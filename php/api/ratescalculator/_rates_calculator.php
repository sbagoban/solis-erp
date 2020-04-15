<?php

function _rates_calculator($con, $arr_params) {
    try {


        //to prevent mysql from truncating group_concat values
        $sql = "SET SESSION group_concat_max_len=10000;";
        $stmt = $con->prepare($sql);
        $stmt->execute();


        $time_pre = microtime(true);


        $_untainted_arr_params = $arr_params; //to be used in SPO to make inner calls of _rates_calculator

        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
        //cleanup:
        $arr_params["contractids"] = trim($arr_params["contractids"]);


        $checkin_dMY = new DateTime($arr_params["checkin_date"]);
        $checkin_dMY = $checkin_dMY->format("d M Y");
        $checkout_dMY = new DateTime($arr_params["checkout_date"]);
        $checkout_dMY = $checkout_dMY->format("d M Y");
        $num_nights = _rates_calculator_get_numnights($checkin_date, $checkout_date);

        if ($num_nights <= 0) {
            $time_post = microtime(true);
            $exec_time = round(($time_post - $time_pre), 2);

            return array("OUTCOME" => "NUMBER OF NIGHTS MUST BE >= 1",
                "DAILY" => array(),
                "NUM NIGHTS" => $num_nights,
                "EXEC_TIME" => $exec_time);
        }

        $arr_params["checkin_dmy"] = $checkin_dMY;
        $arr_params["checkout_dmy"] = $checkout_dMY;
        $arr_params["num_nights"] = $num_nights;


        $arr_params["roll_over"] = false;
        $arr_params["roll_over_basis"] = "";
        $arr_params["roll_over_value"] = "";
        $arr_params["checkin_rollover_dmy"] = "";
        $arr_params["checkout_rollover_dmy"] = "";


        //=========================================================================
        //validate the contracts 

        $arr_days = _rates_get_contract_id($con, $arr_params);
        $arr_outcome = _rates_validate_daily_contract_id($arr_days["DAILY"], false);

        if ($arr_outcome["OUTCOME"] != "OK") {
            if ($arr_outcome["OUTCOME"] == "FAIL_OVERLAPPING_TEST" ||
                    $arr_outcome["OUTCOME"] == "FAIL_MULTILE_PERIODS_TEST") {

                $time_post = microtime(true);
                $exec_time = round(($time_post - $time_pre), 2);

                return array("OUTCOME" => $arr_outcome["OUTCOME"],
                    "DAILY" => $arr_outcome["DETAILS"],
                    "NUM NIGHTS" => $arr_params["num_nights"],
                    "EXEC_TIME" => $exec_time);
            } else if ($arr_outcome["OUTCOME"] == "FAIL_NO_CONTRACT") {

                //check for rollover by setting dtfrom and dtto to last year
                $checkin_rollover = new DateTime($arr_params["checkin_date"]);
                $checkout_rollover = new DateTime($arr_params["checkout_date"]);
                $checkin_rollover = $checkin_rollover->modify('-1 year');
                $checkout_rollover = $checkout_rollover->modify('-1 year');


                $arr_params["checkin_date"] = $checkin_rollover->format("Y-m-d");
                $arr_params["checkout_date"] = $checkout_rollover->format("Y-m-d");

                $arr_params["checkin_rollover_dmy"] = $checkin_rollover->format("d M Y");
                $arr_params["checkout_rollover_dmy"] = $checkout_rollover->format("d M Y");

                //==================================================================
                //retry now
                $arr_days = _rates_get_contract_id($con, $arr_params);
                $arr_outcome = _rates_validate_daily_contract_id($arr_days["DAILY"], true);
                if ($arr_outcome["OUTCOME"] != "OK") {
                    if ($arr_outcome["OUTCOME"] == "FAIL_OVERLAPPING_TEST" ||
                            $arr_outcome["OUTCOME"] == "FAIL_MULTILE_PERIODS_TEST" ||
                            $arr_outcome["OUTCOME"] == "FAIL_NO_CONTRACT") {

                        $time_post = microtime(true);
                        $exec_time = round(($time_post - $time_pre), 2);

                        return array("OUTCOME" => $arr_outcome["OUTCOME"],
                            "DAILY" => $arr_outcome["DETAILS"],
                            "NUM NIGHTS" => $arr_params["num_nights"],
                            "EXEC_TIME" => $exec_time);
                    }
                } else {
                    $arr_params["roll_over"] = true;
                }
                //==================================================================
            }
        }
        //=========================================================================
        //TEST 1: CONTRACT ID:
        $contractid = $arr_days["DAILY"][0]["CONTRACT_ID"][0]; //get the final contract id
        $arr_params["current_contract_id"] = $contractid;



        //get the contract details
        $rw_contract_details = _rates_calculator_get_contract_details($con, $contractid);
        if ($arr_params["roll_over"]) {

            $arr_params["roll_over_basis"] = $rw_contract_details["rollover_basis"];
            $arr_params["roll_over_value"] = $rw_contract_details["rollover_value"];
        }

        //======================================================================

        $currency_sell_arr = _rates_calculator_get_contract_currency_buy_sell($con, $contractid, "SELL");
        $currency_sell = $currency_sell_arr["CODE"];
        $currency_sell_id = $currency_sell_arr["ID"];

        $currency_buy_arr = _rates_calculator_get_contract_currency_buy_sell($con, $contractid, "BUY");
        $currency_buy = $currency_buy_arr["CODE"];
        $currency_buy_id = $currency_buy_arr["ID"];

        $arr_params["contract_details"] = $rw_contract_details;

        $arr_params["currency_sell_code"] = $currency_sell;
        $arr_params["currency_sell_id"] = $currency_sell_id;

        $arr_params["currency_buy_code"] = $currency_buy;
        $arr_params["currency_buy_id"] = $currency_buy_id;
        //======================================================================
        //========================================================================
        //load contract rules, tax and commission into arrays
        $arr_capacity = _contract_capacityarr($con, $arr_params["current_contract_id"]);
        $arr_taxcomm = _contract_taxcommi($con, $arr_params["current_contract_id"]);

        $arr_params["arr_capacity"] = $arr_capacity;
        $arr_params["arr_taxcomm"] = $arr_taxcomm;
        //========================================================================
        //determine the markup basis: is it PPPN or PNI
        $PN_PPN = _rates_calculator_getTaxCommi_AddOn_Basis($arr_params["hotelroom"], $arr_taxcomm);
        $arr_params["TAX_COMMI_BASIS"] = $PN_PPN;
        //========================================================================
        //================================================================
        //create dummy columns needed for MARKUP and COMMISSION
        $arr_columns = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, 0, array("ROOM"), $arr_params["TAX_COMMI_BASIS"]);
        $arr_params["arr_columns"] = $arr_columns;
        //=========================================================================
        //=========================================================================
        //
        //
        //
        //
        //=============== get array of valid spo objects and invalid spos messages ===============
        $arr_spo = _rates_calculator_spo_search($arr_params, $con);
        if ($arr_spo["OUTCOME"] != "OK") {
            throw new Exception($arr_spo["OUTCOME"]);
        }
        $arr_spo_records = $arr_spo["ARR_SPOS"];
        $arr_invalid_spos = $arr_spo["ARR_INVALID_SPOS"];

        //========================================================================================
        //GET ARRAY OF CHOICES OF SPOS
        $arr_my_choices = _rates_calculator_spo_generate_choices($arr_spo_records, $con, $arr_params);
        $arr_choices = $arr_my_choices["CHOICES"];
        $arr_invalid_spos = array_merge($arr_invalid_spos, $arr_my_choices["WARNINGS"]);


        //========================================================================================
        //get the choice of spo to apply
        $arr_spo_discounts = array();

        if (count($arr_choices) > 0) {
            $spo_chosen = "CHOICE";

            if (isset($arr_params["spo_chosen"])) {
                $spo_chosen = $arr_params["spo_chosen"];
            }

            if ($spo_chosen == "CHOICE" || $spo_chosen == "LOWEST") {

                //for each choice in $arr_choices,
                //call rates calculator and save the total price next to each one
                $min_costs = 0;
                $min_choice_id = -1;

                for ($c = 0; $c < count($arr_choices); $c++) {
                    $choice_id = $arr_choices[$c]["LINKID_SPOID"];
                    $_untainted_arr_params["spo_chosen"] = $choice_id;

                    $arr_cost = _rates_calculator($con, $_untainted_arr_params);

                    $arr_total_sell = _rates_calculator_spo_translate_arr_sell($arr_cost);
                    $total_sell = $arr_total_sell["TOTAL_SELL"];
                    $currency_sell = $arr_total_sell["CURRENCY_SELL"];
                    $currency_sell_id = $arr_total_sell["CURRENCY_SELL_ID"];

                    $arr_choices[$c]["TOTAL_SELL"] = $total_sell;
                    $arr_choices[$c]["TOTAL_SELL_DESCRIPTION"] = "$currency_sell " . number_format($total_sell, 2, ".", ",");
                    $arr_choices[$c]["CURRENCY_SELL_CODE"] = $currency_sell;
                    $arr_choices[$c]["CURRENCY_SELL_ID"] = $currency_sell_id;

                    if ($min_choice_id == -1) {
                        $min_choice_id = $choice_id;
                        $min_costs = $total_sell;
                    } else if ($min_costs > $total_sell) {
                        $min_choice_id = $choice_id;
                        $min_costs = $total_sell;
                    }
                }

                //if $spo_chosen == "CHOICE" then return array of choices for user to make
                //else if $spo_chosen == "LOWEST", choose the lowest cost choice and carry on
                if ($spo_chosen == "CHOICE") {
                    return array("OUTCOME" => "OK",
                        "CHOICE_PRICES" => "CHOICES",
                        "CHOICES" => $arr_choices);
                } else {
                    //choose lowest option and return the results
                    $_untainted_arr_params["spo_chosen"] = $min_choice_id;
                    return _rates_calculator($con, $_untainted_arr_params);
                }
            } else if ($spo_chosen == "NONE") {
                //there was a choice but user has chosen no offer to be applied
                $arr_spo_discounts = array(); //no spos to calculate
            } else {
                //a spo or a link has been chosen
                $arr_spo_discounts = _rates_calculator_spo_generate_spo_obj_from_choice($arr_choices, $spo_chosen, $con, $arr_params, $arr_spo_records);
            }
        }


        //CHECK IF THERE ARE FREE NIGHTS AND THEN DETERMINE DATES FOR NIGHTS THAT WILL BE FREE (APPLICABLE FOR FREENIGHT.LOWEST)
        $arr_params["spo_discounts_array"] = $arr_spo_discounts;
        _rates_calculator_process_SPO_free_nights($arr_spo_discounts, $arr_days, $arr_params, $con);
        $arr_params["spo_discounts_array"] = $arr_spo_discounts;

        //======================================================================




        $arr_daily = array();
        for ($idx = 0; $idx < count($arr_days["DAILY"]); $idx++) {
            $this_date = $arr_days["DAILY"][$idx]["DATE"];
            $arr_contract_ids = $arr_days["DAILY"][$idx]["CONTRACT_ID"];
            $arr_lookup_mode = array("TOTAL" => true, "ROOM_ONLY" => false, "LCO" => true, "DISCOUNTS" => true);


            $arr_normal_or_flatrates = _rates_calculator_decide_contract_or_spo_flatrate($arr_params, $this_date);
            $arr_params["flat_rate_spo_apply"] = $arr_normal_or_flatrates;

            rates_calculator_CORE_lookup($arr_daily, $idx, $this_date, $arr_contract_ids, $arr_params, $con, $arr_lookup_mode);
        }

        //done: record time

        $time_post = microtime(true);
        $exec_time = round(($time_post - $time_pre), 2);


        return array("OUTCOME" => "OK",
            "CHOICE_PRICES" => "PRICES",
            "NUM NIGHTS" => $num_nights, "DAILY" => $arr_daily,
            "EXEC_TIME" => $exec_time, "COLUMNS" => $arr_columns,
            "INVALID_SPOS" => $arr_invalid_spos);
    } catch (Exception $ex) {
        return array("OUTCOME" => "_RATES_CALCULATOR: " . $ex->getMessage());
    }
}

function rates_calculator_CORE_lookup(&$arr_daily, $idx, $this_date, $arr_contract_ids,
        $arr_params, $con, $arr_lookup_mode) {


    //this is the core daily lookups for the contract
    //all changes are done to $arr_daily
    //
    //$arr_lookup_mode[TOTAL] = true/false => create total rows or not
    //$arr_lookup_mode[ROOM_ONLY] = true/false => lookup room only or not
    //$arr_lookup_mode[LCO] = true/false => calculate LCO or not
    //$arr_lookup_mode[DISCOUNTS] = true/false => apply discounts or not

    $arr_params["lookupmode"] = $arr_lookup_mode;
    //======================================================================
    //======================================================================
    //======================================================================
    $arr_daily[$idx]["DATE"] = $this_date;
    $arr_daily[$idx]["CONTRACT_ID"] = $arr_contract_ids;
    $arr_daily[$idx]["TAX_COMMI_BASIS"] = $arr_params["TAX_COMMI_BASIS"];

    $arr_daily[$idx]["CURRENCY_SELL_CODE"] = $arr_params["currency_sell_code"];
    $arr_daily[$idx]["CURRENCY_BUY_CODE"] = $arr_params["currency_buy_code"];
    $arr_daily[$idx]["CURRENCY_SELL_ID"] = $arr_params["currency_sell_id"];
    $arr_daily[$idx]["CURRENCY_BUY_ID"] = $arr_params["currency_buy_id"];

    $arr_daily[$idx]["ROLLOVER"] = $arr_params["roll_over"];
    $arr_daily[$idx]["ROLLOVER_BASIS"] = $arr_params["roll_over_basis"];
    $arr_daily[$idx]["ROLLOVER_VALUE"] = $arr_params["roll_over_value"];

    $arr_daily[$idx]["COSTINGS_WORKINGS"] = array();
    $arr_daily[$idx]["STATUS"] = "OK"; //to be used for further checks below

    $arr_taxcomm = $arr_params["arr_taxcomm"];
    $arr_columns = $arr_params["arr_columns"];

    //========================================================================
    //=============================================================================
    //if need to rollover to last year, notify the user
    if ($arr_params["roll_over"]) {

        $msg = "<font color='red'>NO CONTRACTS FOUND FOR <b>" . $arr_params["checkin_dmy"] . " - " . $arr_params["checkout_dmy"] . "</b></font>";
        $msg .= "<br>ROLLING OVER TO: <b>" . $arr_params["checkin_rollover_dmy"] . " - " . $arr_params["checkout_rollover_dmy"] . "</b>";

        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => $msg, "COSTINGS" => array());
    }
    //=============================================================================


    $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 1</font>: FOUND CONTRACT ID: <b>" . $arr_params["current_contract_id"] . "</b>",
        "COSTINGS" => array());

    //perform validations now
    _rates_calculator_validate_reservation($arr_params, $this_date, $con, $arr_daily[$idx]);

    if ($arr_daily[$idx]["STATUS"] == "OK") { //all ok after all validation checks
        //=====================================================================
        //HERE WE GO:
        //
        //
        //
        //=====================================================================
        //================== EARLY CHECKIN ====================================
        $arr_eci = array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
        if ($idx == 0) {
            //CHECK IF THERE IS EARLY CHECK IN ON FIRST NIGHT
            $arr_eci = _rates_calculator_eci_lco("ECI", $arr_params, $this_date);
        }
        //=====================================================================
        //
        //
        //                        
        //=====================================================================
        //=============  GET DAILY ROOM RATES =================================

        $arr = _rates_calculator_lookup_rates($arr_params, $this_date, $con, $arr_eci);
        $arr = _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $arr_params, $this_date);

        $arr = _rates_calculator_calc_rollover_percentage($arr_params, $arr);
        $arr = _rates_calculator_calc_rollover_flat_pni($arr_params, $arr, "ROOM", $this_date);

        $arr = _rates_calculator_calc_discount_PPPN($arr_params, $arr, "ROOM");
        $arr = _rates_calculator_calc_discount_PNI($arr_params, $arr);

        $arr = _rates_calculator_lookup_rates_calc_PPPN($arr, $arr_params, $arr_taxcomm, $con, "ROOM");

        $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr);

        //=====================================================================
        //
        //
        //
        //=====================================================================
        //================== LATE CHECKOUT ====================================
        if ($arr_lookup_mode["LCO"]) {
            $arr_lco = array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
            if ($idx == $arr_params["num_nights"] - 1) {
                //CALCULATE LATE CHECK OUT ON THE DATE OF CHECKOUT

                $lco_applied = false; //flag to determine of LCO has been applied or not
                $checkout_date = new DateTime($this_date);
                $checkout_date = $checkout_date->modify('+1 day');
                $checkout_date = $checkout_date->format("Y-m-d");

                $arr_lco = _rates_calculator_eci_lco("LCO", $arr_params, $checkout_date);

                //get the rates for the date of checkout
                $arr_rates = _rates_calculator_lookup_rates($arr_params, $checkout_date, $con, $arr_eci);
                $arr_rates = _rates_calculator_apply_rates_lco_percentage($arr_rates, $arr_lco, $lco_applied, $arr_params["currency_buy_code"]);
                $arr_rates = _rates_calculator_apply_rates_lco_flat_pni($arr_rates, $arr_lco, $arr_params, $lco_applied, $checkout_date);
                $arr_rates = _rates_calculator_calc_rollover_percentage($arr_params, $arr_rates);

                if ($lco_applied) {
                    //create another index for LCO
                    $arr_daily[$idx + 1] = $arr_daily[$idx]; //copy previous night values to new checkout date
                    $arr_daily[$idx + 1]["DATE"] = $checkout_date;
                    $arr_rates = _rates_calculator_lookup_rates_calc_PPPN($arr_rates, $arr_params, $arr_taxcomm, $con, "ROOM");
                    $arr_daily[$idx + 1]["COSTINGS_WORKINGS"] = $arr_rates;

                    if ($arr_lookup_mode["TOTAL"]) {
                        $arr_daily[$idx + 1]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx + 1]["COSTINGS_WORKINGS"], $arr_columns, "ROOM", $arr_params, $arr_taxcomm, $con);
                        $arr_daily[$idx + 1]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx + 1]["COSTINGS_WORKINGS"], $arr_columns, "NON_ROOM", $arr_params, $arr_taxcomm, $con);
                    }
                }
            }
        }

        //=====================================================================
        //=====================================================================
        //ROOM TOTAL
        if ($arr_lookup_mode["TOTAL"]) {
            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, "ROOM", $arr_params, $arr_taxcomm, $con);
        }


        //=====================================================================
        //================================================================
        //MEAL SUPPLEMENTS
        if (!$arr_lookup_mode["ROOM_ONLY"]) {
            if ($arr_params["supp_mealplan"] != "") {
                //there is a meal supplement
                $arr_meal_supp = _rates_calculator_meal_supp($arr_params, $this_date, $con);
                $arr_meal_supp = _rates_calculator_calc_rollover_percentage($arr_params, $arr_meal_supp);
                $arr_meal_supp = _rates_calculator_meal_supp_PPPN($arr_meal_supp, $arr_params, $arr_taxcomm, $con);
                $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_meal_supp);
            }
        }
        //=====================================================================
        //
        //
        //
        //=====================================================================
        //EXTRA MEAL SUPPLEMENTS
        //CHECK FOR COMPULSORY MEALS
        if (!$arr_lookup_mode["ROOM_ONLY"]) {
            $arr_extra_meal_supp = _rates_calculator_extra_meal_supp($arr_params, $this_date, $con);
            $arr_extra_meal_supp = _rates_calculator_calc_rollover_percentage($arr_params, $arr_extra_meal_supp);
            $arr_extra_meal_supp = _rates_calculator_extra_meal_supp_PPPN($arr_extra_meal_supp, $arr_params, $arr_taxcomm, $con);
            $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_extra_meal_supp);
        }

        //================================================================
        //===============================================================
        //NON ROOM TOTAL
        if ($arr_lookup_mode["TOTAL"]) {
            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, "NON_ROOM", $arr_params, $arr_taxcomm, $con);
        }

        //========================================================================
        //FINALLY, SPLIT THE SELLING PRICE PER PAX IF COSTING IS PNI
        _rates_calculator_split_SP_per_pax($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, $arr_params, "ROOM");
        _rates_calculator_split_SP_per_pax($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, $arr_params, "NON_ROOM");
    }
}

function _rates_calculator_split_SP_per_pax(&$arr, $arr_columns, $arr_params, $category) {

    $PN_PPN = $arr_params["TAX_COMMI_BASIS"];

    if (strpos($PN_PPN, "PNI") !== false) {

        $arr_category_total = _rates_calculator_split_SP_per_pax_get_total($arr, $category);
        $category_initial_total = $arr_category_total["INITIAL_TOTAL"];
        $category_cost_total = $arr_category_total["COST_TOTAL"];
        $category_sp_total = $arr_category_total["SELL_PRICE_TOTAL"];

        $cost_index = $arr_category_total["COST_INDEX"];
        $sprice_index = $arr_category_total["SELL_PRICE_INDEX"];


        //==========================================================
        //first get the number of rows interested in
        $rows_count = 0;
        for ($i = 0; $i < count($arr); $i++) {
            if (isset($arr[$i]["CATEGORY"])) {
                if ($category == $arr[$i]["CATEGORY"]) {
                    $rows_count++;
                }
            }
        }
        //==========================================================
        //now split the total prorata for each pax of that category based on the cost price
        $row_index = 0;

        $cumul_prorata_sp = 0;
        $cumul_prorata_cost = 0;

        for ($i = 0; $i < count($arr); $i++) {
            if (isset($arr[$i]["CATEGORY"])) {
                if ($category == $arr[$i]["CATEGORY"]) {
                    $row_index++;

                    _rates_calculator_split_SP_per_pax_reflect_columns($arr[$i]["COSTINGS"], $arr_columns);

                    $base_initial = $arr[$i]["COSTINGS"][0]["VALUE"];

                    $pro_rata_cost = 0;
                    $pro_rata_sp = 0;

                    if ($row_index == $rows_count) {
                        //last row: place the difference
                        $pro_rata_cost = $category_cost_total - $cumul_prorata_cost;
                        $pro_rata_sp = $category_sp_total - $cumul_prorata_sp;
                    } else {

                        //calculate the values prorata
                        if ($category_initial_total > 0) {

                            $pro_rata_cost = round(($base_initial / $category_initial_total) * $category_cost_total);
                            $cumul_prorata_cost += $pro_rata_cost;

                            $pro_rata_sp = round(($base_initial / $category_initial_total) * $category_sp_total);
                            $cumul_prorata_sp += $pro_rata_sp;
                        }
                    }

                    $arr[$i]["COSTINGS"][$cost_index]["VALUE"] = $pro_rata_cost;
                    $arr[$i]["COSTINGS"][$sprice_index]["VALUE"] = $pro_rata_sp;
                }
            }
        }
    }

    return;
}

function _rates_calculator_split_SP_per_pax_reflect_columns(&$arr_costings, $arr_columns) {
    //make the columns in $arr_costings reflect all the columns in $arr_columns
    $existing_cols = count($arr_costings);

    for ($i = $existing_cols; $i < count($arr_columns); $i++) {
        $arr_costings[] = $arr_columns[$i];
    }

    return;
}

function _rates_calculator_split_SP_per_pax_get_total($arr, $category) {
    $category_sp_total = 0;
    $category_initial_total = 0;
    $category_cost_total = 0;

    $cost_index = 0;
    $final_sp_index = 0;

    for ($i = 0; $i < count($arr); $i++) {
        $item = $arr[$i];
        if (isset($item["CATEGORY"])) {

            $item_category = $item["CATEGORY"];
            if ("$category TOTAL" == $item_category) {
                $arr_costings = $item["COSTINGS"];
                $category_initial_total = $arr_costings[0]["VALUE"];

                for ($j = 0; $j < count($arr_costings); $j++) {
                    $cost_item = $arr_costings[$j];
                    if ($cost_item["CAPTION"] == "FINAL SELLING PRICE") {
                        $category_sp_total = $cost_item["VALUE"];
                        $final_sp_index = $j;
                    } else if ($cost_item["CAPTION"] == "COST PRICE") {
                        $category_cost_total = $cost_item["VALUE"];
                        $cost_index = $j;
                    }
                }
            }
        }
    }

    return array("INITIAL_TOTAL" => $category_initial_total,
        "COST_TOTAL" => $category_cost_total, "COST_INDEX" => $cost_index,
        "SELL_PRICE_TOTAL" => $category_sp_total, "SELL_PRICE_INDEX" => $final_sp_index);
}

function _rate_calculator_taxcommi_for_room($arr_buy_sell, $hotelroom) {
    for ($i = 0; $i < count($arr_buy_sell); $i++) {
        if ($arr_buy_sell[$i]["room_id"] == $hotelroom && $arr_buy_sell[$i]["room_hasexception"] == "YES") {
            return $arr_buy_sell[$i]; //SPECIFIC ROOM
        }
    }

    return $arr_buy_sell[0]; //GENERAL 
}

function _rates_calculator_eci_lco_lookup_charge($checkinout_time_dt, $arr_policies, $eci_lco) {

    $charge_type = "";
    $limit_time = "";
    $charge_value = "";
    //========================== ECI ==================
    if ($eci_lco == "ECI") {
        for ($i = 0; $i < count($arr_policies); $i++) {

            //go from small to big
            if ($eci_lco == $arr_policies[$i]["checkinout_policytype"]) {

                $charge_type = $arr_policies[$i]["checkinout_charge_type"];
                $limit_time = $arr_policies[$i]["checkinout_checkinout_time"];
                $limit_time_dt = new DateTime($limit_time);
                $charge_value = $arr_policies[$i]["checkinout_charge_value"];

                if ($checkinout_time_dt < $limit_time_dt) {

                    return array("CHARGE_TYPE" => $charge_type, "LIMIT_TIME" => $limit_time, "CHARGE_VALUE" => $charge_value);
                }
            }
        }
    }

    //========================== LCO ==================
    else if ($eci_lco == "LCO") {

        for ($i = count($arr_policies) - 1; $i >= 0; $i--) {
            if ($eci_lco == $arr_policies[$i]["checkinout_policytype"]) {

                $charge_type = $arr_policies[$i]["checkinout_charge_type"];
                $limit_time = $arr_policies[$i]["checkinout_checkinout_time"];
                $limit_time_dt = new DateTime($limit_time);
                $charge_value = $arr_policies[$i]["checkinout_charge_value"];

                if ($eci_lco == "LCO") {
                    if ($checkinout_time_dt > $limit_time_dt) {
                        return array("CHARGE_TYPE" => $charge_type, "LIMIT_TIME" => $limit_time, "CHARGE_VALUE" => $charge_value);
                    }
                }
            }
        }
    }


    //there are no issues
    return array("CHARGE_TYPE" => "", "LIMIT_TIME" => "", "CHARGE_VALUE" => "");
}

function _rates_calculator_eci_lco($eci_lco, $arr_params, $this_date) {

    $workings = "";


    $hotelroom = $arr_params["hotelroom"];
    $arr_capacity = $arr_params["arr_capacity"];
    $flg_found = false;

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        //so bypass contract ECI and LCO policies and apply SPO ECI and LCO policies
        $arr_capacity = $arr_params["flat_rate_spo_apply"]["RATES"];
        $workings .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];

        $arr_eci_lco_params = _rates_calculator_eci_lco_lookup_params($arr_params, $eci_lco, $arr_capacity, $workings, $hotelroom, $this_date, $flg_found);
    }
    //=============================================================================

    if (!$flg_found) {
        //fall back to contract level
        $arr_capacity = $arr_params["arr_capacity"];
        if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
            $workings .= "<font color='orange'><b>$eci_lco</b> POLICIES NOT DEFINED IN SPO.</font> REVERTING TO CONTRACT POLICIES...<br>";
        }

        $arr_eci_lco_params = _rates_calculator_eci_lco_lookup_params($arr_params, $eci_lco, $arr_capacity, $workings, $hotelroom, $this_date, $flg_found);
    }

    return $arr_eci_lco_params;
}

function _rates_calculator_eci_lco_lookup_params($arr_params, $eci_lco, $arr_capacity, $workings, $hotelroom, $this_date, &$flg_found) {
    $flg_found = false;

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

    if (!is_null($rules)) {

        $arr_policies = $rules["date_policies_checkinout"];

        if ($eci_lco == "ECI") {

            //EARLY CHECK IN 
            $checkin_time_dt = trim($arr_params["checkin_time"]);

            if ($checkin_time_dt != "" && $checkin_time_dt != "__:__") {

                $checkin_time_dt = new DateTime($checkin_time_dt);
                $arr_values = _rates_calculator_eci_lco_lookup_charge($checkin_time_dt, $arr_policies, $eci_lco);
                $charge_type = $arr_values["CHARGE_TYPE"];
                $limit_time = $arr_values["LIMIT_TIME"];
                $charge_value = $arr_values["CHARGE_VALUE"];

                if ($charge_type != "") {

                    //there is an early check in here!
                    $flg_found = true;
                    $workings .= "<b><font color='blue'>EARLY CHECK IN</font></b>: before $limit_time ";
                    return array("WORKINGS" => $workings, "CHARGE_TYPE" => $charge_type, "CHARGE_VALUE" => $charge_value);
                }
            }
        } else if ($eci_lco == "LCO") {
            //LATE CHECK OUT

            $checkout_time_dt = trim($arr_params["checkout_time"]);
            if ($checkout_time_dt != "" && $checkout_time_dt != "__:__") {

                $checkout_time_dt = new DateTime($arr_params["checkout_time"]);
                $arr_values = _rates_calculator_eci_lco_lookup_charge($checkout_time_dt, $arr_policies, $eci_lco);
                $charge_type = $arr_values["CHARGE_TYPE"];
                $limit_time = $arr_values["LIMIT_TIME"];
                $charge_value = $arr_values["CHARGE_VALUE"];

                if ($charge_type != "") {

                    //there is a late checkout here!
                    $flg_found = true;
                    $workings .= "<b><font color='blue'>LATE CHECK OUT</font></b>: after $limit_time";
                    return array("WORKINGS" => $workings, "CHARGE_TYPE" => $charge_type, "CHARGE_VALUE" => $charge_value);
                }
            }
        }
    }

    return array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
}

function _rates_calculator_test_children_ages($arr_params, $con) {
    try {

        $children = $arr_params["children"];

        $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, "CONTRACT");


        for ($a = 0; $a < count($arr_age_groups); $a++) {
            $age_from = $arr_age_groups[$a]["AGEFROM"];
            $age_to = $arr_age_groups[$a]["AGETO"];

            for ($i = 0; $i < count($children); $i++) {
                if ($age_from <= $children[$i]["age"] && $children[$i]["age"] <= $age_to) {
                    $children[$i]["age"] = "OK";
                }
            }
        }

        //now check if there are ages outside the contract definition
        for ($i = 0; $i < count($children); $i++) {
            if ($children[$i]["age"] != "OK") {
                return $children[$i]["age"] . "YRS OUTSIDE ALLOWABLE AGE RANGES";
            }
        }


        return "OK";
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_TEST_CHILDREN_AGES: " . $ex->getMessage();
    }
}

function _rates_calculator_min_stay_nights($arr_params, $this_date, &$flg_min_test) {
    //return OK if min stay is satisfied, minstay nights otherwise

    try {

        $arr_capacity = $arr_params["arr_capacity"];
        $hotelroom = $arr_params["hotelroom"];

        $checkin_date = new DateTime($arr_params["checkin_date"]); //yyyy-mm-dd
        $checkout_date = new DateTime($arr_params["checkout_date"]); //yyyy-mm-dd

        $contract_active_from = $arr_params["contract_details"]["active_from"];
        $contract_active_to = $arr_params["contract_details"]["active_to"];


        $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);
        if (!is_null($rules)) {

            //========================================================
            $rules_date_from = $rules["date_dtfrom"];
            $rules_date_to = $rules["date_dtto"];

            if ($rules_date_from == "") {
                $rules_date_from = $contract_active_from;
            }
            if ($rules_date_to == "") {
                $rules_date_to = $contract_active_to;
            }

            $rules_date_from = new DateTime($rules_date_from);
            $rules_date_to = new DateTime($rules_date_to);

            //========================================================
            //get the number of nights spent in the period $rules_date_from - $rules_date_to
            $overlapping_nights = 0;
            if ($rules_date_from < $checkout_date && $rules_date_to > $checkin_date) {
                $overlapping_nights = min($rules_date_to, $checkout_date)->diff(max($checkin_date, $rules_date_from))->days + 1;
            }

            $arr_minstay_rules = $rules["date_minstay_rules"];
            for ($i = 0; $i < count($arr_minstay_rules); $i++) {
                $minstay_duration = $arr_minstay_rules[$i]["minstay_duration"];

                //if ($num_nights < $minstay_duration) {
                if ($checkout_date <= $rules_date_to) {
                    //this is the last period
                    $overlapping_nights--;
                }

                if ($overlapping_nights < $minstay_duration) {
                    $flg_min_test = false;
                    return "PERIOD <b>" . $rules_date_from->format("d M Y") . " - " . $rules_date_to->format("d M Y") . "</b> : STAYED <b>$overlapping_nights</b> NIGHTS &lt; <b>$minstay_duration</b> NIGHTS";
                }
            }
        }
        $flg_min_test = true;
        return "PERIOD <b>" . $rules_date_from->format("d M Y") . " - " . $rules_date_to->format("d M Y") . "</b> : STAYED <b>$overlapping_nights</b> NIGHTS &ge; <b>$minstay_duration</b> NIGHTS";
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_MIN_STAY_NIGHTS: " . $ex->getMessage();
    }
}

function _rates_calculator_ch_own_capacity($arr_params, $this_date) {
    //OWN ROOM  children
    //return OK if capacity is satisfied, error message otherwise
    try {

        if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
            //clearly SPO FLAT RATES will take over for this date
            //so bypass the check
            //because this check has already been done at another level before reaching here
            return array("MSG" => "OK", "INDEX" => "<font color='#BB3C94'>SEE FLAT RATES SPO</font>");
        }

        $hotelroom = $arr_params["hotelroom"];
        $arr_capacity = $arr_params["arr_capacity"];

        $room_details = _rates_calculator_get_room_details($arr_params);
        $room_type = $room_details["room_variants"]; //"PERSONS", "UNITS"

        if ($room_type == "UNITS") {
            //this check is not applicable here!
            return array("MSG" => "OK", "INDEX" => "CHECK NOT APPLICABLE FOR UNIT ROOM");
        }

        $children = array();
        for ($i = 0; $i < count($arr_params["children"]); $i++) {
            if ($arr_params["children"][$i]["sharing_own"] == "OWN") {
                //filter only those children that are in own room
                $children[] = $arr_params["children"][$i];
            }
        }

        if (count($children) == 0) {
            return array("MSG" => "OK", "INDEX" => "NO CHILDREN IN OWN ROOM"); //there is NO children in own room to test here
        }

        $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

        if (!is_null($rules)) {

            $date_rwid = $rules["date_rwid"];

            //generate the combinations for that room and date
            $arr_combinations = _contract_combinations_rooms($arr_capacity, $hotelroom, $date_rwid);

            $adult = 0; //test with zero adult
            $combi_index = _rates_calculator_test_capacity_adch_combii($arr_combinations, $adult, $children);

            if ($combi_index != -1) {
                //successful capacity check
                return array("MSG" => "OK", "INDEX" => $combi_index);
            }

            //======================================================
            //if we are here means that no rules satisfy
            return array("MSG" => "NO CHILDREN OWN ROOM CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT");
        } else {
            return array("MSG" => "NO CHILDREN OWN ROOM CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT");
        }
    } catch (Exception $ex) {
        return array("MSG" => "_RATES_CALCULATOR_ADCH_CAPACITY: " . $ex->getMessage());
    }
}

function _rates_calculator_adch_capacity($arr_params, $this_date) {

    //test adult + SHARING children
    //return OK if capacity is satisfied, error message otherwise
    try {

        if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
            //clearly SPO FLAT RATES will take over for this date
            //so bypass the check
            //because this check has already been done at another level before reaching here
            return array("MSG" => "OK", "INDEX" => "<font color='#BB3C94'>SEE FLAT RATES SPO</font>");
        }

        //if we are here means that there are no SPO Flat Rates for this date
        //apply check as per normal contract

        $arr_capacity = $arr_params["arr_capacity"];
        $hotelroom = $arr_params["hotelroom"];

        $room_details = _rates_calculator_get_room_details($arr_params);

        $children = array();
        for ($i = 0; $i < count($arr_params["children"]); $i++) {
            if ($arr_params["children"][$i]["sharing_own"] == "SHARING") {
                //filter only those children that are sharing
                $children[] = $arr_params["children"][$i];
            }
        }

        $adult = count($arr_params["adults"]);

        if ($adult == 0) {
            //means that we are going towards child in OWN room
            return array("MSG" => "OK", "INDEX" => "<font color='green'>GOING TO CHILD IN OWN ROOM...</font>");
        }

        $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

        if (!is_null($rules) && !is_null($room_details)) {
            $date_rwid = $rules["date_rwid"];

            //generate the combinations for that room and date
            $arr_combinations = _contract_combinations_rooms($arr_capacity, $hotelroom, $date_rwid);

            $combi_index = _rates_calculator_test_capacity_adch_combii($arr_combinations, $adult, $children);

            if ($combi_index != -1) {
                //successful capacity check
                return array("MSG" => "OK", "INDEX" => $combi_index);
            }

            //======================================================
            //if we are here means that no rules satisfy
            return array("MSG" => "ADULT AND CHILDREN CAPACITY <B>NOT</B> SATISFIED");
        } else {
            return array("MSG" => "NO ADULT AND CHILDREN CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT");
        }
    } catch (Exception $ex) {
        return array("MSG" => "_RATES_CALCULATOR_ADCH_CAPACITY: " . $ex->getMessage());
    }
}

function _rates_calculator_test_capacity_adch_combii($arr_room_combinations, $adult, $children) {
    $arr_date_combinations = $arr_room_combinations["room_combinations"];
    for ($cix = 0; $cix < count($arr_date_combinations); $cix++) {
        $arr_combinations = $arr_date_combinations[$cix]["combinations_array"];

        //returns the index of the combination that matched the search
        for ($i = 0; $i < count($arr_combinations); $i++) {
            $combo = $arr_combinations[$i];
            if (_rates_calculator_test_capacity_adch_combii_combo($combo, $adult, $children)) {
                return ($i + 1); //return the index of the successful combination. start from 1
            }
        }
        return -1;
    }
}

function _rates_calculator_test_capacity_adch_combii_combo($combo, $adult, $children) {
    //return true if combination a valid one
    //return false otherwise
    //deduct adult first
    $found = true;

    if ($adult > 0) {
        $found = false;

        for ($i = 0; $i < count($combo); $i++) {
            $agfrom = $combo[$i]["AGEFROM"];
            $agto = $combo[$i]["AGETO"];

            if ($agfrom == -1 && $agto == -1) {
                //adult
                if ($combo[$i]["No"] >= $adult) {
                    $combo[$i]["No"] -= $adult;
                    $found = true;
                }
            }
        }
    }

    if (!$found) {
        return false; //adult check failed
    }

    //================================
    for ($j = 0; $j < count($children); $j++) {
        $found = false;

        $age = $children[$j]["age"];

        for ($i = 0; $i < count($combo); $i++) {
            $agfrom = $combo[$i]["AGEFROM"];
            $agto = $combo[$i]["AGETO"];

            if ($agfrom <= $age && $age <= $agto) {
                if ($combo[$i]["No"] > 0) {
                    $found = true;
                    $combo[$i]["No"]--; //decrement the children count
                }
            }
        }

        if (!$found) {
            return false;
        }
    }


    //now assess the combo: the total pax should be zero
    $sum = 0;
    for ($i = 0; $i < count($combo); $i++) {
        $sum += $combo[$i]["No"];
    }

    if ($sum != 0) {
        return false;
    }

    return true;
}

function _rates_calculator_get_capacity_rule_units_getoccupation($rule_capacity, $category) {
    $arr = array();

    for ($i = 0; $i < count($rule_capacity); $i++) {
        if ($rule_capacity[$i]["capacity_action"] != "DELETE") {
            if ($rule_capacity[$i]["capacity_category"] == $category) {

                $capacity_minpax = $rule_capacity[$i]["capacity_minpax"];
                $capacity_maxpax = $rule_capacity[$i]["capacity_maxpax"];
                $capacity_child_agefrom = $rule_capacity[$i]["capacity_child_agefrom"];
                $capacity_child_ageto = $rule_capacity[$i]["capacity_child_ageto"];

                $arr[] = array("AGE_FROM" => $capacity_child_agefrom,
                    "AGE_TO" => $capacity_child_ageto,
                    "MIN" => $capacity_minpax,
                    "MAX" => $capacity_maxpax);
            }
        }
    }

    return $arr;
}

function _rates_calculator_test_capacity_rule_units_extra_child(&$std_max, &$arr_group_children) {
    //decrement children from the standard allocation to see what children are left as extra

    $i = count($arr_group_children) - 1;
    while ($i >= 0) {
        while (count($arr_group_children[$i]["CHILDREN"]) > 0 && $std_max > 0) {
            unset($arr_group_children[$i]["CHILDREN"][0]);
            $arr_group_children[$i]["CHILDREN"] = array_values($arr_group_children[$i]["CHILDREN"]);
            $std_max--;
        }
        $i--;
    }

    return;
}

function _rates_calculator_test_capacity_rule_units_extra_adult(&$std_max, &$adult) {

    //deduct a max adults from std capacity first

    if ($adult > $std_max) {
        $adult -= $std_max;
        $std_max = 0;
    } else {
        $std_max -= $adult;
        $adult = 0;
    }

    return;
}

function _rates_calculator_get_room_details($arr_params) {

    $roomid = $arr_params["hotelroom"];
    $arr_capacity = $arr_params["arr_capacity"];

    for ($i = 0; $i < count($arr_capacity); $i++) {
        //get the room
        if ($roomid == $arr_capacity[$i]["room_id"]) {
            return $arr_capacity[$i];
        }
    }

    return null;
}

function _rates_calculator_get_arrcapacity_daterange($arr_capacity, $roomid, $thedate) {
    $thedate = date("Y-m-d", strtotime($thedate));

    for ($i = 0; $i < count($arr_capacity); $i++) {
        //get the room
        if ($roomid == $arr_capacity[$i]["room_id"]) {
            $arr_dates = $arr_capacity[$i]["room_dates"];
            for ($j = 0; $j < count($arr_dates); $j++) {

                $date_dtfrom = $arr_dates[$j]["date_dtfrom"];
                $date_dtto = $arr_dates[$j]["date_dtto"];

                if ($date_dtfrom != "" && $date_dtto != "") {
                    $date_dtfrom = date("Y-m-d", strtotime($date_dtfrom));
                    $date_dtto = date("Y-m-d", strtotime($date_dtto));
                    if (($thedate >= $date_dtfrom) && ($thedate <= $date_dtto)) {
                        return $arr_dates[$j];
                    }
                } else if ($date_dtfrom == "" && $date_dtto != "") {
                    $date_dtto = date("Y-m-d", strtotime($date_dtto));
                    if ($thedate <= $date_dtto) {
                        return $arr_dates[$j];
                    }
                } else if ($date_dtfrom != "" && $date_dtto == "") {
                    $date_dtfrom = date("Y-m-d", strtotime($date_dtfrom));
                    if ($thedate >= $date_dtfrom) {
                        return $arr_dates[$j];
                    }
                }
                if ($date_dtfrom == "" && $date_dtto == "") {

                    return $arr_dates[$j];
                }
            }
        }
    }

    return null;
}

function _rates_calculator_getcontracts_for_the_date($arr_params, $thedate, $con) {

    //return list of contract ID(s) that fall within the parameters
    //$thedate in yyyy-mm-dd

    $country = $arr_params["country"];
    $hotel = $arr_params["hotel"];
    $hotelroom = $arr_params["hotelroom"];
    $mealplan = $arr_params["mealplan"];
    $rate = $arr_params["rate"];
    $touroperator = $arr_params["touroperator"];

    $sql = "SELECT * FROM tblservice_contract WHERE deleted = 0 AND active_internal = 1";


    //======================= HOTEL =========================
    $sql .= " AND hotelfk = :hotelfk ";

    //======================= HOTEL ROOM =========================
    $sql .= " AND id IN 
                  (SELECT servicecontractfk FROM 
                   tblservice_contract_rooms 
                   WHERE roomfk=:roomfk
                  )";

    //======================= COUNTRY =========================
    $sql .= " AND id IN 
                  (SELECT service_contract_fk FROM 
                   tblservice_contract_countries 
                   WHERE countryfk=:countryfk 
                  )";

    //======================= MEAL PLAN =========================
    $sql .= " AND mealplan_fk = :mealplan_fk ";

    //======================= RATES =========================
    $sql .= " AND id IN 
                  (SELECT service_contract_fk FROM 
                   tblservice_contract_rates 
                   WHERE ratefk=:ratefk 
                  )";

    //======================= TOUR OPERATORS =========================
    $sql .= " AND id IN 
                  (SELECT service_contract_fk FROM 
                   tblservice_contract_touroperator 
                   WHERE tofk=:tofk 
                  )";


    //======================= THE DATE =========================
    $sql .= " AND :the_date BETWEEN active_from AND active_to ";

    $query = $con->prepare($sql);
    $query->execute(array(":the_date" => $thedate, ":tofk" => $touroperator,
        ":ratefk" => $rate, ":mealplan_fk" => $mealplan, ":hotelfk" => $hotel,
        ":countryfk" => $country, ":roomfk" => $hotelroom));

    $arrids = array();
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arrids[] = $rw["id"];
    }

    return $arrids;
}

function _rates_calculator_lookup_rates($arr_params, $this_date, $con, $arr_eci) {

    $room_details = _rates_calculator_get_room_details($arr_params);
    $room_type = $room_details["room_variants"]; //"PERSONS", "UNITS"

    if ($room_type == "PERSONS") {
        return _rates_calculator_lookup_rates_persons($arr_params, $this_date, $con, $arr_eci);
    } else if ($room_type == "UNITS") {
        return _rates_calculator_lookup_rates_units($arr_params, $this_date, $con, $arr_eci);
    }
}

function _rates_calculator_lookup_rates_units($arr_params, $this_date, $con, $arr_eci) {
    $arr = array();

    $arr_capacity = $arr_params["arr_capacity"];

    $spo_contract = "CONTRACT"; //load children ages at contract level

    $flat_rate_comments = "";
    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $arr_capacity = $arr_params["flat_rate_spo_apply"]["RATES"];
        $flat_rate_comments .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
        $spo_contract = "SPO";
    }
    //=============================================================================


    $adult = count($arr_params["adults"]);
    $children = $arr_params["children"];

    $total_children = count($arr_params["children"]);

    $currency_buy = $arr_params["currency_buy_code"];

    //get the rules occupation rules
    $hotelroom = $arr_params["hotelroom"];
    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);


    if (!is_null($rules)) {
        $arr_capacity_rules = $rules["date_capacity_rules"];

        $rule_capacity = $arr_capacity_rules[0]["rule_capacity"]; //only one rule                
        $arr_std_occup = _rates_calculator_get_capacity_rule_units_getoccupation($rule_capacity, "STANDARDOCCUPATION");

        $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract);

        $std_max = $arr_std_occup[0]["MAX"];

        //get the unit normal price first
        $arr_normal = _rates_calculator_lookup_rates_units_standard($rules, $arr_params);
        $normal_rates = $arr_normal["RATES_STANDARD"];
        $normal_workings = $arr_normal["WORKINGS_STANDARD"];


        //get extra adults and children
        _rates_calculator_test_capacity_rule_units_extra_adult($std_max, $adult);
        _rates_calculator_test_capacity_rule_units_extra_child($std_max, $arr_group_children);


        $flg_extra_people = false;

        $arr_spo_summary_applied = array(); //array to count number of times applied for adults/children per SPO
        //for each extra adult and extra children, calculate the price
        //===========================================================
        if ($adult > 0) {
            //there is extra adult!
            $arr_extra_adult = _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params,
                    $adult, $normal_rates,
                    $arr_eci, $this_date,
                    $arr_spo_summary_applied);
            $arr = array_merge($arr, $arr_extra_adult);
            $flg_extra_people = true;
        }

        //===========================================================
        //===========================================================
        //for each extra children, calculate the price    
        $extra_children = _rates_calculator_getchildrencount_inagegroup_array($arr_group_children);
        if ($extra_children > 0) {
            $arr_extra_children = _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params,
                    $arr_group_children,
                    $normal_rates, $arr_eci,
                    $this_date,
                    $extra_children,
                    $total_children,
                    $arr_spo_summary_applied);
            $arr = array_merge($arr, $arr_extra_children);
            $flg_extra_people = true;
        }


        //===========================================================
        //finally get the normal standard unit price and split it per person
        //reload the $arr_group_children
        $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract);


        $num_persons = 0;
        if ($flg_extra_people) {
            $num_persons = $arr_std_occup[0]["MAX"];
        } else {
            $num_persons = count($arr_params["adults"]) + count($arr_params["children"]);
        }


        $per_person_buyprice = 0;
        if ($num_persons > 0) {
            $per_person_buyprice = round($normal_rates / $num_persons);
        }

        $cumul_buyprice = 0;
        $ad_total_index = 0;
        $ch_total_index = 0;


        for ($adinx = 1; $adinx <= $num_persons; $adinx++) {

            if ($adinx == $num_persons) {
                $per_person_buyprice = $normal_rates - $cumul_buyprice;
            } else {
                $cumul_buyprice += $per_person_buyprice;
            }

            $pax = _rates_calculator_lookup_rates_units_get_pax_details($adinx, $arr_params, $arr_group_children);


            //===========
            $index_to_use = 0;
            if ($pax["adch"] == "ADULT") {
                $ad_total_index++;
                $index_to_use = $ad_total_index;
            } else {
                $ch_total_index++;
                $index_to_use = $ch_total_index;
            }
            //===========

            $_workings = "$normal_workings => " . substr($pax["adch"], 0, 2) . " #$index_to_use " . $pax["adch"] . " <b>" . $pax["age"] . "yrs</b> = $currency_buy $per_person_buyprice";

            //record adult rates for each adult:
            //apply eci for that pax if any
            $per_person_after_eci = $per_person_buyprice;
            _rates_calculator_apply_rates_eci_percentage($per_person_after_eci, $arr_eci, $_workings, $currency_buy);

            //apply spo percent discount for that pax if any                    
            _rates_calculator_apply_spo_discount_percentage($per_person_after_eci, $_workings, $arr_params,
                    $pax["adch"], $pax["age"], $pax["bride_groom"], "ROOM",
                    $this_date, $index_to_use, $arr_spo_summary_applied);


            $arr[] = array("MSG" => $_workings, "COSTINGS" => $per_person_after_eci,
                "ADCH" => $pax["adch"],
                "AGE" => $pax["age"],
                "BRIDEGROOM" => $pax["bride_groom"]);
        }
    }


    return $arr;
}

function _rates_calculator_lookup_rates_units_get_pax_details($adinx, $arr_params, $arr_group_children) {

    //get adults first
    //then get the children

    $arr_adults = $arr_params["adults"];

    if ($adinx <= count($arr_adults)) {
        $pax = $arr_adults[$adinx - 1];
        $pax["adch"] = "ADULT";
        $pax["idx"] = $adinx;
        return $pax;
    }

    $adinx -= count($arr_adults);

    //=============
    $a = count($arr_group_children) - 1;
    while ($a >= 0) {
        $arr_temp_children = $arr_group_children[$a]["CHILDREN"];

        if ($adinx <= count($arr_temp_children)) {
            $pax = $arr_temp_children[$adinx - 1];
            $pax["adch"] = "CHILDREN";
            $pax["bride_groom"] = "";
            $pax["idx"] = $adinx;
            return $pax;
        } else {
            $adinx -= count($arr_temp_children);
        }

        $a--;
    }
}

function _rates_calculator_lookup_rates_units_standard($rules, $arr_params) {
    $rates = 0;
    $workings = "";

    $currency_buy = $arr_params["currency_buy_code"];
    $arr_adult_rules = $rules["date_adultpolicies_rules"];

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================



    $rates = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "unit_price", 0);
    $workings .= "<b>UNIT PRICE</b>: $currency_buy $rates";

    if ($rates == "") {
        $rates = 0;
        $workings .= "NO UNIT PRICE";
    }

    return array("RATES_STANDARD" => $rates, "WORKINGS_STANDARD" => $workings);
}

function _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params, $arr_group_children,
        $normal_rates, $arr_eci, $this_date,
        $extra_children, $total_children,
        &$arr_spo_summary_applied) {
    $arr = array();
    $currency_buy = $arr_params["currency_buy_code"];

    $arr_children_rules = $rules["date_childpolicies_rules"];

    $workings_children_spo = "";

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings_children_spo = $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================

    $child_index_to_use = $total_children - $extra_children + 1;

    for ($i = 0; $i < count($arr_group_children); $i++) {
        $arr_children = $arr_group_children[$i]["CHILDREN"];
        $age_from = $arr_group_children[$i]["AGFROM"];
        $age_to = $arr_group_children[$i]["AGTO"];

        for ($idx = 1; $idx <= count($arr_children); $idx++) {

            $child_age = $arr_children[$idx - 1]["age"];

            $basis = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "basis", $idx, $age_from, $age_to);
            $value = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "value", $idx, $age_from, $age_to);


            if ($basis == "%") {
                $percentage = 0;
                if ($value > 0) {
                    $percentage = round(($value / 100) * $normal_rates, 2);
                }

                $rates_children = $percentage;
                $workings_children = "$workings_children_spo <b>EXTRA PAX</b>: Ch #$child_index_to_use (<b>$child_age yrs</b>) = <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any                    
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children,
                        $arr_params, "CHILDREN", $child_age, "", "ROOM",
                        $this_date, $child_index_to_use, $arr_spo_summary_applied);


                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "EXTRA" => "YES",
                    "BRIDEGROOM" => "");
            } else if ($basis == "FLAT") {

                $rates_children = $value;
                $workings_children = "$workings_children_spo <b>EXTRA PAX</b>: Ch #$child_index_to_use (<b>$child_age yrs</b>) = $currency_buy $value";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any 
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children,
                        $arr_params, "CHILDREN", $child_age, "", "ROOM",
                        $this_date, $child_index_to_use,
                        $arr_spo_summary_applied);

                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "EXTRA" => "YES",
                    "BRIDEGROOM" => "");
            } else {
                $rates_children = 0;
                $workings_children = "$workings_children_spo <b>EXTRA PAX</b>: Ch #$child_index_to_use (<b>$child_age yrs</b>) <font color='orange'>NO RATES DEFINED...</font>";

                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "EXTRA" => "YES",
                    "BRIDEGROOM" => "");
            }

            $child_index_to_use++;
        }
    }

    return $arr;
}

function _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates,
        $arr_eci, $this_date, &$arr_spo_summary_applied) {
    $arr = array();
    $rates = 0;
    $workings_flat_rates = "";

    $currency_buy = $arr_params["currency_buy_code"];
    $arr_adults = $arr_params["adults"];

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings_flat_rates = $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================


    $arr_adult_rules = $rules["date_adultpolicies_rules"];

    $adult_index = count($arr_adults);

    //get the basis and value for each extra adult
    for ($i = 1; $i <= $adult; $i++) {

        $workings = "";

        $basis = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_basis", $i);
        $value = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_value", $i);


        $adult_pax = $arr_adults[$adult_index - 1];

        if ($basis == "%") {
            $percentage = 0;
            if ($value > 0) {
                $percentage = round(($value / 100) * $normal_rates, 2);
            }
            $rates = $percentage;
            $workings = "$workings_flat_rates <b>EXTRA PAX</b>: Ad #$adult_index: <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";
        } else if ($basis == "FLAT") {
            $rates = $value;
            $workings = "$workings_flat_rates <b>EXTRA PAX</b>: Ad #$adult_index: $currency_buy $value";
        } else {
            $rates = 0;
            $workings = "$workings_flat_rates <b>EXTRA PAX</b>: Ad #$adult_index: <font color='orange'>NO RATES DEFINED...</font>";
        }

        //apply eci for that extra adult if any
        _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

        //apply spo percent discount for that extra adult if any 
        _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT",
                $adult_pax["age"], $adult_pax["bride_groom"],
                "ROOM", $this_date, $adult_index, $arr_spo_summary_applied);

        $adult_index--;

        $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
            "ADCH" => "ADULT",
            "AGE" => $adult_pax["age"],
            "EXTRA" => "YES",
            "BRIDEGROOM" => $adult_pax["bride_groom"]);
    }

    return $arr;
}

function _rates_calculator_lookup_rates_units_lookup_children_rates($arr_rule, $value_category, $rule_category, $age_from, $age_to) {
    for ($r = 0; $r < count($arr_rule); $r++) {
        if ($arr_rule[$r]["rule_category"] == $rule_category &&
                $arr_rule[$r]["rule_sharing_single"] == "SHARING") {
            $arr_rule_policy = $arr_rule[$r]["rule_policy"];
            for ($i = 0; $i < count($arr_rule_policy); $i++) {

                $policy_category = $arr_rule_policy[$i]["policy_category"];
                $policy_agfrom = $arr_rule_policy[$i]["policy_units_additional_child_agefrom"];
                $policy_agto = $arr_rule_policy[$i]["policy_units_additional_child_ageto"];

                if ($policy_category == $value_category &&
                        $age_from == $policy_agfrom &&
                        $age_to == $policy_agto) {
                    $arr_policy_values = $arr_rule_policy[$i]["policy_values"];
                    for ($j = 0; $j < count($arr_policy_values); $j++) {
                        if ($arr_policy_values[$j]["value_basis"] == $value_category) {
                            return $arr_policy_values[$j]["value_value"];
                        }
                    }
                }
            }
        }
    }

    return "";
}

function _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_rule, $value_basis, $rule_category) {
    for ($r = 0; $r < count($arr_rule); $r++) {
        if ($arr_rule[$r]["rule_category"] == $rule_category) {
            $arr_rule_policy = $arr_rule[$r]["rule_policy"];
            for ($i = 0; $i < count($arr_rule_policy); $i++) {
                $policy_basis = $arr_rule_policy[$i]["policy_basis"];
                if ($policy_basis == $value_basis) {
                    $arr_policy_values = $arr_rule_policy[$i]["policy_values"];
                    for ($j = 0; $j < count($arr_policy_values); $j++) {
                        if ($arr_policy_values[$j]["value_basis"] == $value_basis) {
                            return $arr_policy_values[$j]["value_value"];
                        }
                    }
                }
            }
        }
    }

    return "";
}

function _rates_calculator_lookup_rates_persons($arr_params, $this_date, $con, $arr_eci) {

    $adult = $arr_params["adults"];
    $count_adult = count($adult);
    $children = $arr_params["children"];
    $flg_got_rates = false; //to be passed by reference below
    $flg_looked_at_single_parent_first = false;
    $arr = array();


    if ($count_adult == 1 && count($children) > 0) {
        //does single parent policy exists?
        $flg_looked_at_single_parent_first = true;
        $arr = _rates_calculator_lookup_rates_single_parent($arr_params, $this_date, $con, $flg_got_rates, $arr_eci);
    }
    if (!$flg_got_rates) {
        //no single parent rates defined?! then lookup in normal rates
        $arr = _rates_calculator_lookup_rates_normal($arr_params, $this_date, $con, $arr_eci, $flg_looked_at_single_parent_first);
    }



    return $arr;
}

function _rates_calculator_calc_rollover_flat_pni($arr_params, $arr, $this_date) {

    $roll_over_flg = $arr_params["roll_over"];

    if (!$roll_over_flg) {
        return $arr;
    }

    $currency_buy = $arr_params["currency_buy_code"];
    $roll_over_basis = $arr_params["roll_over_basis"];
    $roll_over_value = $arr_params["roll_over_value"];


    if ($roll_over_basis != "add_per_night") {
        return $arr;
    }


    //split rollover equally between Non FOC pax

    $num_non_foc_pax = 0;

    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i]["COSTINGS"] > 0) {
            $num_non_foc_pax++;
        }
    }

    //split the roll over now
    $pax_rollover = 0;
    if ($num_non_foc_pax > 0) {
        $pax_rollover = round($roll_over_value / $num_non_foc_pax, 2);
    }


    //now apply it to the costings per each pax
    $cumul_applied = 0;
    $applied_to = 1;
    $ad_total_index = 0;
    $ch_total_index = 0;
    $arr_spo_summary_applied = array(); //count the summary of adults/children applied to SPOs

    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $adch = $arr[$i]["ADCH"];
        $age = $arr[$i]["AGE"];
        $bridegroom = $arr[$i]["BRIDEGROOM"];

        //===========
        $index_to_use = 0;
        if ($adch == "ADULT") {
            $ad_total_index++;
            $index_to_use = $ad_total_index;
        } else {
            $ch_total_index++;
            $index_to_use = $ch_total_index;
        }
        //===========

        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_rollover;
            } else {
                $pax_rollover = $roll_over_value - $cumul_applied;
            }
            $applied_to++;


            $msg .= "<br> + (<font color='blue'><b>ROLLOVER</b>: </font> FLAT PNI : $currency_buy $roll_over_value &#247; $num_non_foc_pax = $currency_buy $pax_rollover per Non FOC pax)";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_rollover, $msg, $arr_params, $adch,
                    $age, $bridegroom, "ROOM", $this_date,
                    $index_to_use, $arr_spo_summary_applied);

            $costings += $pax_rollover;

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_calc_rollover_percentage($arr_params, $arr) {

    $roll_over_flg = $arr_params["roll_over"];

    if (!$roll_over_flg) {
        return $arr;
    }

    $currency_buy = $arr_params["currency_buy_code"];
    $roll_over_basis = $arr_params["roll_over_basis"];
    $roll_over_value = $arr_params["roll_over_value"];


    if ($roll_over_basis == "percentage") {

        //percentage applied on both ROOM and NON_ROOM items
        //add roll over as a percentage of all items 
        for ($i = 0; $i < count($arr); $i++) {
            $msg = $arr[$i]["MSG"];
            $costings = $arr[$i]["COSTINGS"];
            $addon = 0;
            if ($roll_over_value > 0) {
                $addon = round(($roll_over_value / 100) * $costings);
            }

            $msg .= "<br> + (<font color='blue'><b>ROLLOVER</b>: </font> $roll_over_value % of $currency_buy $costings = $currency_buy $addon)";
            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] += $addon;
        }
    }


    return $arr;
}

function _rates_calculator_group_sharing_own_children_ageranges($rules, $sharing_own) {

    //create an array of rule_age_ranges

    $arr_group_age_ranges = array();

    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE" &&
                $rules[$i]["rule_sharing_single"] == $sharing_own) {

            $rule_ageranges = $rules[$i]["rule_ageranges"];
            if (!in_array($rule_ageranges, $arr_group_age_ranges)) {
                $arr_group_age_ranges[] = $rule_ageranges;
            }
        }
    }

    return $arr_group_age_ranges;
}

function _rates_calculator_calculate_sharing_own_children_rates($arr_childrenpolicies_rules,
        $arr_adultpolicies_rules, $arr_params,
        $arr_eci, $this_date,
        $flg_looked_at_single_parent_first, $con,
        $spo_contract, $sharing_own) {
    $arr = array();


    //==============================================================
    //filter only these children that are SHARING/OWN room
    $children = array();
    for ($i = 0; $i < count($arr_params["children"]); $i++) {

        if (($arr_params["children"][$i]["sharing_own"] == "OWN" && $sharing_own == "SINGLE") ||
                $arr_params["children"][$i]["sharing_own"] == "SHARING" && $sharing_own == "SHARING") {

            //filter only those children that are sharing/own
            $children[] = $arr_params["children"][$i];
        }
    }

    if (count($children) == 0) {
        //no children for that SHARING/SINGLE lookup
        //return a blank array
        return array();
    }


    //==============================================================
    //for each age group, get the children that fall within that range
    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract);

    $arr_age_ranges = _rates_calculator_group_sharing_own_children_ageranges($arr_childrenpolicies_rules, $sharing_own);


    //============================================================================================
    //for each age_range in arr_age_ranges, check if children ages match        
    //=================================================
    //first check for EXACT match
    $the_age_range = _rates_calculator_agerange_exact_match_children($arr_age_ranges, $children);

    if ($the_age_range == "") {
        //if no EXACT match found, look for NEXT BEST match
        $the_age_range = _rates_calculator_agerange_nextbest_match_children($arr_age_ranges, $children);

        if ($the_age_range == "") {
            //if no NEXT BEST match found, look for ALLOWABLE match
            $the_age_range = _rates_calculator_agerange_allowable_match_children($arr_age_ranges, $children);
        }
    }

    //================================================



    $flg_got_rates = true;
    $arr_spo_summary_applied = array(); //summary of adults/childen per SPO
    $rules_age_range = _rates_calculator_children_get_rules_by_agerange($arr_childrenpolicies_rules, $the_age_range);

    //calculate sharing/own children rates
    $arr_children_rates = _rates_calculator_lookup_sharing_own_children_rates($arr_group_children, $rules_age_range, $arr_params, $arr_adultpolicies_rules, $arr_eci, $this_date, $arr_spo_summary_applied, $flg_looked_at_single_parent_first, $sharing_own);
    $arr = array_merge($arr, $arr_children_rates);


    return $arr;
}

function _rates_calculator_lookup_sharing_own_children_rates_index($index, $rules, $age_from, $age_to, $cat_basis_val, $sharing_own) {
    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE" &&
                $rules[$i]["rule_sharing_single"] == $sharing_own &&
                $rules[$i]["rule_category"] == $index) {

            $arrpolicies = $rules[$i]["rule_policy"];

            for ($p = 0; $p < count($arrpolicies); $p++) {
                if ($arrpolicies[$p]["policy_action"] != "DELETE") {

                    if ($arrpolicies[$p]["policy_category"] == $cat_basis_val &&
                            $arrpolicies[$p]["policy_units_additional_child_agefrom"] == $age_from &&
                            $arrpolicies[$p]["policy_units_additional_child_ageto"] == $age_to) {

                        $arrvalues = $arrpolicies[$p]["policy_values"];

                        for ($v = 0; $v < count($arrvalues); $v++) {
                            if ($arrvalues[$v]["value_action"] != "DELETE") {
                                return $arrvalues[$v]["value_value"];
                            }
                        }
                    }
                }
            }
        }
    }

    return "";
}

function _rates_calculator_lookup_sharing_own_children_rates($arr_group_children, $rules, $arr_params,
        $arr_adultpolicies_rules, $arr_eci, $this_date,
        &$arr_spo_summary_applied,
        $flg_looked_at_single_parent_first,
        $sharing_own) {

    $arr_final = array();
    $rates = 0;
    $workings = "";
    $workings_spo = "";


    $single_parent_comments = "";
    if ($flg_looked_at_single_parent_first) {
        $single_parent_comments = "<font color='orange'>NO <b>SINGLE PARENT</b> RATES FOUND!</font> REVERTING TO <B>NORMAL RATES</B>...<br>";
    }

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings_spo = $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================

    $currency_buy = $arr_params["currency_buy_code"];

    //for each child in $arr_group_children, get the agerange and the index
    //for that age range and index, lookup the category, basis and value
    $child_count_index = 1;

    for ($a = 0; $a < count($arr_group_children); $a++) {


        $arr_children = $arr_group_children[$a]["CHILDREN"];
        $age_from = $arr_group_children[$a]["AGFROM"];
        $age_to = $arr_group_children[$a]["AGTO"];

        //======================================================================

        $arr = array();
        $child_index = count($arr_children);
        while ($child_index > 0) {
            $child_age = $arr_children[$child_index - 1]["age"];

            $basis = _rates_calculator_lookup_sharing_own_children_rates_index($child_index, $rules, $age_from, $age_to, "basis", $sharing_own);
            //SINGLE, %, FLAT, DOUBLE, TRIPLE

            $value = _rates_calculator_lookup_sharing_own_children_rates_index($child_index, $rules, $age_from, $age_to, "value", $sharing_own);

            if ($basis == "") {
                $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index {$child_age}yr <font color='orange'>NO RATES</font>) => ";

                $arr[] = array("WORKINGS" => $workings, "RATES" => 0, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                //go to next child if possible
                if ($child_index == 1) {
                    $workings .= "END";

                    $arr[count($arr) - 1]["WORKINGS"] = $workings;
                    //stop here
                    break;
                }
            } else {
                if ($basis == "SINGLE") {
                    //here we are just taking the value attached to the child
                    $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index {$child_age}yr SNGL $currency_buy $value) ";
                    $rates_children = $value;

                    $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                    //stop here
                    break;
                } else if ($basis == "FLAT") {
                    //here just take the flat rate
                    $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index {$child_age}yr FLAT $currency_buy $value) ";
                    $rates_children = $value;
                    $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                    //may need to go to next child if possible
                } else if ($basis == "%") {

                    //here just calculate a percentage value of adult.index
                    //get adult rates
                    $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
                    $arr_adult_workings = _rates_calculator_calc_adult_recur($child_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];

                    $child_rate_value = 0;
                    if ($value > 0) {
                        $child_rate_value = round(($value / 100) * $rates_adult, 2);
                    }

                    $rates_children = $child_rate_value;
                    $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index {$child_age}yr $value% of AD $currency_buy $rates_adult = $currency_buy $child_rate_value)";

                    $arr[] = array("WORKINGS" => $workings, "RATES" => $child_rate_value, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                    //stop here
                    break;
                } else if ($basis == "DOUBLE") {
                    //implies take the adult double rate
                    //here we are just taking the value attached to the child (good?)

                    $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index $basis {$child_age}yr DBL $currency_buy $value) ";
                    $rates_children = $value;

                    $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 2);

                    //stop here
                    break;
                } else if ($basis == "TRIPLE") {
                    //implies take the adult double rate
                    //here we are just taking the value attached to the child (good?)

                    $workings = "$workings_spo $single_parent_comments $sharing_own (CH #$child_index $basis {$child_age}yr TRPL $currency_buy $value) ";
                    $rates_children = $value;

                    $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 3);
                    //stop here
                    break;
                }
            }

            $child_index--;
        }

        //======================================================================
        //======================================================================
        //finally merge into final array
        //now need to know if there is a splitting of children fees between DOUBLE,1/2 DBL or TRIPLE children
        $child_count_index = 1;

        $temp_arr = array();

        for ($i = 0; $i < count($arr); $i++) {
            $work = $arr[$i]["WORKINGS"];
            $rates = $arr[$i]["RATES"];
            $childindex = $arr[$i]["CHILDINDEX"];
            $split_between = $arr[$i]["TO_SPLIT_BETWEEN"];
            $child_age = $arr_children[$childindex - 1]["age"];

            if ($split_between == 0) {
                //apply eci percentage if any for that kiddo
                _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $work, $currency_buy);

                //apply spo percent discount for that kiddo if any
                _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params,
                        "CHILDREN", $child_age, "", "ROOM",
                        $this_date, $child_count_index, $arr_spo_summary_applied);
                $child_count_index++;

                $temp_arr[] = array("MSG" => $work, "COSTINGS" => $rates,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            } else {
                //need to split
                $ch_buyprice = round($rates / $split_between);
                $cumul_buyprice = 0;

                for ($chinx = 1; $chinx <= $split_between; $chinx++) {

                    if ($chinx == $split_between) {
                        $ch_buyprice = $rates - $cumul_buyprice;
                    } else {
                        $cumul_buyprice += $ch_buyprice;
                    }

                    $msg = "$work => Ch #$childindex => $currency_buy $ch_buyprice";

                    //apply eci percentage if any for that kiddo
                    _rates_calculator_apply_rates_eci_percentage($ch_buyprice, $arr_eci, $msg, $currency_buy);

                    //apply spo percent discount for that kiddo if any
                    _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params,
                            "CHILDREN", $child_age, "", "ROOM",
                            $this_date, $child_count_index, $arr_spo_summary_applied);

                    $child_count_index++;


                    $temp_arr[] = array("MSG" => $msg, "COSTINGS" => $ch_buyprice,
                        "ADCH" => "CHILDREN",
                        "AGE" => $child_age,
                        "BRIDEGROOM" => "");

                    $childindex--;
                }
            }
        }

        //finally merge into final array
        $arr_final = array_merge($arr_final, $temp_arr);
    }

    return $arr_final;
}

function _rates_calculator_lookup_rates_single_parent($arr_params, $this_date, $con, &$flg_got_rates, $arr_eci) {

    $arr = array();

    $children = $arr_params["children"];
    $hotelroom = $arr_params["hotelroom"];
    $arr_capacity = $arr_params["arr_capacity"];

    $spo_contract = "CONTRACT"; //load children ages at contract level initially
    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        //so bypass contract ECI and LCO policies and apply SPO ECI and LCO policies
        $arr_capacity = $arr_params["flat_rate_spo_apply"]["RATES"];
        $spo_contract = "SPO";
    }
    //=============================================================================
    //for each age group, get the children that fall within that range
    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract);

    //get the date rules for that date
    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

    if (!is_null($rules)) {
        $arr_singleparent_rules = $rules["date_singleparentpolicies_rules"];
        $arr_adultpolicies_rules = $rules["date_adultpolicies_rules"];

        $arr_age_ranges = _rates_calculator_group_single_parent_ageranges($arr_singleparent_rules);


        //for each age_range in arr_age_ranges, check if children ages match        
        //=================================================
        //first check for EXACT match
        $the_age_range = _rates_calculator_agerange_exact_match_children($arr_age_ranges, $children);

        if ($the_age_range == "") {
            //if no EXACT match found, look for NEXT BEST match
            $the_age_range = _rates_calculator_agerange_nextbest_match_children($arr_age_ranges, $children);

            if ($the_age_range == "") {
                //if no NEXT BEST match found, look for ALLOWABLE match
                $the_age_range = _rates_calculator_agerange_allowable_match_children($arr_age_ranges, $children);
            }
        }

        //================================================

        if ($the_age_range == "") {
            //really no rates defined for single parent!
            $flg_got_rates = false;
            $arr[] = array("MSG" => "<font color='orange'>NO SINGLE PARENT RATES</font>", "COSTINGS" => array());
        } else {

            $flg_got_rates = true;
            $arr_spo_summary_applied = array(); //summary of adults/childen per SPO
            $rules_age_range = _rates_calculator_children_get_rules_by_agerange($arr_singleparent_rules, $the_age_range);

            //calculate children rates
            $arr_children_rates = _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules_age_range, $arr_params, $arr_adultpolicies_rules, $arr_eci, $this_date, $arr_spo_summary_applied);
            $arr = array_merge($arr, $arr_children_rates);

            //calculate adult rates
            $arr_adult_rates = _rates_calculator_lookup_single_parent_parent_rates($rules_age_range, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci, $this_date, $arr_spo_summary_applied);
            $arr = array_merge($arr, $arr_adult_rates);
        }
    } else {
        $flg_got_rates = false;
        $arr[] = array("MSG" => "NO SINGLE PARENT RATES", "COSTINGS" => array());
    }

    return $arr;
}

function _rates_calculator_lookup_rates_normal($arr_params, $this_date, $con, $arr_eci, $flg_looked_at_single_parent_first) {

    //$flg_looked_at_single_parent_first = true/false : showing if
    //searched rates from single parent first

    $arr = array();

    $spo_contract = "CONTRACT"; //to distinguish between loading children ages from CONTRACT or SPO

    $single_parent_comments = "";
    if ($flg_looked_at_single_parent_first) {
        $single_parent_comments = "<font color='orange'>NO <b>SINGLE PARENT</b> RATES FOUND!</font> REVERTING TO <B>NORMAL RATES</B>...<br>";
    }

    $arr_capacity = $arr_params["arr_capacity"];
    $flat_rate_comments = "";
    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $arr_capacity = $arr_params["flat_rate_spo_apply"]["RATES"];
        $flat_rate_comments .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
        $spo_contract = "SPO";
    }
    //=============================================================================


    $adult = count($arr_params["adults"]);
    $children = $arr_params["children"];
    $hotelroom = $arr_params["hotelroom"];
    $currency_buy = $arr_params["currency_buy_code"];

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

    if (!is_null($rules)) {
        $arr_adultpolicies_rules = $rules["date_adultpolicies_rules"];
        $arr_childrenpolicies_rules = $rules["date_childpolicies_rules"];


        //======================================================================
        //calculate adult rates ================================================
        //then split the adult rates for each adult
        $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");

        $arr_adult_workings = _rates_calculator_calc_adult_recur($adult, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
        $rates = $arr_adult_workings["RATES_ADULT"];
        $workings = $arr_adult_workings["WORKINGS_ADULT"];

        $adult_buyprice = 0;
        if ($adult > 0) {
            $adult_buyprice = round($rates / $adult); //split the adult rates per person
        }

        $cumul_buyprice = 0;

        $arr_spo_summary_applied = array(); //count the summary of adults/children applied to SPOs
        //splitting the adult rates for each adult
        for ($adinx = 1; $adinx <= $adult; $adinx++) {
            if ($adinx == $adult) {
                $adult_buyprice = $rates - $cumul_buyprice;
            } else {
                $cumul_buyprice += $adult_buyprice;
            }

            $_workings = "$flat_rate_comments $single_parent_comments $workings => Ad #$adinx = $currency_buy $adult_buyprice";

            //apply eci for that adult if any
            _rates_calculator_apply_rates_eci_percentage($adult_buyprice, $arr_eci, $_workings, $currency_buy);

            //apply spo percent discount for that adult if any
            $pax = $arr_params["adults"][($adinx - 1)];
            _rates_calculator_apply_spo_discount_percentage($adult_buyprice, $_workings, $arr_params, "ADULT",
                    $pax["age"], $pax["bride_groom"], "ROOM", $this_date,
                    $adinx, $arr_spo_summary_applied);

            //record adult rates for each adult
            $arr[] = array("MSG" => $_workings,
                "COSTINGS" => $adult_buyprice,
                "ADCH" => "ADULT",
                "AGE" => $pax["age"],
                "BRIDEGROOM" => $pax["bride_groom"]);
        }


        //======================================================================
        //======================================================================
        //
        //calculate children rates =============================================
        $arr_rates_children = _rates_calculator_calc_children($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $con, $arr_eci, $this_date, $flg_looked_at_single_parent_first, $spo_contract);
        $arr = array_merge($arr, $arr_rates_children);

        //======================================================================
        //======================================================================
    } else {

        $arr[] = array("MSG" => "<font color='orange'>NO RATES FOUND FOR THIS DATE</font>", "COSTINGS" => array());
    }


    return $arr;
}

function _rates_calculator_calc_children($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $con, $arr_eci, $this_date, $flg_looked_at_single_parent_first, $spo_contract) {

    //regroup each child in $children by age groups defined in the contract
    $arr = array();

    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract);

    //======================================================================================
    //now calculate rates for own room children
    $_arr = _rates_calculator_calculate_sharing_own_children_rates($arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first, $con, $spo_contract, "SINGLE");
    $arr = array_merge($arr, $_arr);
    //======================================================================================
    //======================================================================================
    //now calculate rates for sharing children
    $_arr = _rates_calculator_calculate_sharing_own_children_rates($arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first, $con, $spo_contract, "SHARING");
    $arr = array_merge($arr, $_arr);
    //======================================================================================

    return $arr;
}

function _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract) {
    $arr_age_groups = array();

    //need to know if to take ages group from contract level or from spo level
    //$spo_contract = CONTRACT / SPO

    $query = null;

    if ($spo_contract == "CONTRACT") {
        //load age ranges from CONTRACT level
        $contractid = $arr_params["current_contract_id"];

        //return an array of age groups for that contract
        $sql = "SELECT ca.agefrom, ca.ageto
                FROM tblservice_contract_childages scc
                INNER JOIN tblchildrenagerange ca ON scc.child_age_fk = ca.id
                WHERE service_contract_fk = :contractid
                GROUP BY ca.agefrom, ca.ageto
                ORDER BY ca.agefrom, ca.ageto";

        $query = $con->prepare($sql);
        $query->execute(array(":contractid" => $contractid));
    } else if ($spo_contract == "SPO") {
        //load age ranges from SPO level
        $spoid = $arr_params["flat_rate_spo_apply"]["SPO_ID"];

        //return an array of age groups for that SPO
        $sql = "SELECT cr.id, cr.agefrom, cr.ageto
                FROM tblspecial_offer_flatrate_group_validity_period  sofgvp
                INNER JOIN tblspecial_offer_flatrate_group_validity_period_childages sofgvpc 
                ON sofgvp.id = sofgvpc.spo_fltrate_grp_valid_period_fk
                INNER JOIN tblchildrenagerange cr on sofgvpc.childage_fk = cr.id
                WHERE sofgvp.spo_fk = :spoid
                GROUP BY cr.agefrom, cr.id, cr.ageto
                ORDER BY cr.agefrom, cr.ageto";

        $query = $con->prepare($sql);
        $query->execute(array(":spoid" => $spoid));
    }


    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr_age_groups[] = array("AGEFROM" => $rw["agefrom"], "AGETO" => $rw["ageto"]);
    }

    return $arr_age_groups;
}

function _rates_calculator_calc_adult_recur($adult, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params) {
    if ($adult == 0) {
        //base case
        return array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "<font color='orange'>NO RATES DEFINED</font>");
    } else {

        $arr = _rates_calculator_lookup_adult_rates($adult, $arr_adultpolicies_rules, $arr_params);
        $val = $arr["VAL"];
        $msg = $arr["MSG"];
        $recur = $arr["RECUR"];

        $arr_adult_workings["RATES_ADULT"] += $val;
        $arr_adult_workings["WORKINGS_ADULT"] .= "$msg";

        if ($recur) {
            $adult--;
            return _rates_calculator_calc_adult_recur($adult, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
        } else {
            return $arr_adult_workings;
        }
    }
}

function _rates_calculator_lookup_adult_rates($adultcount, $arr_adultpolicies_rules, $arr_params) {
    $rates = 0;
    $recur = false;
    $workings = "";

    $currency_buy = $arr_params["currency_buy_code"];

    for ($i = 0; $i < count($arr_adultpolicies_rules); $i++) {
        if ($adultcount == $arr_adultpolicies_rules[$i]["rule_category"]) {
            $arr_rule_policy = $arr_adultpolicies_rules[$i]["rule_policy"];

            $value = _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, "value");
            $basis = _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, "basis");

            if ($basis == "") {
                //happpens when there is only 1 adult
                $rates = $value;
                $workings = "($adultcount AD = $currency_buy $value = $currency_buy $rates)";
            } else if ($basis == "1/n") {
                $rates = $value * $adultcount;
                $workings = "($adultcount AD * $currency_buy $value = $currency_buy $rates)";
            } else if ($basis == "n") {
                $rates = $value;
                $workings = "($adultcount AD = $currency_buy $rates)";
            } else if ($basis == "ADD") {
                $rates = $value;
                $recur = true; //make call to recur to adultcount - 1
                $workings = "$currency_buy $value + ";
            }
        }
    }

    return array("VAL" => $rates, "MSG" => $workings, "RECUR" => $recur);
}

function _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, $value_basis) {
    for ($i = 0; $i < count($arr_rule_policy); $i++) {
        $policy_basis = $arr_rule_policy[$i]["policy_basis"];
        if ($policy_basis == $value_basis) {
            $arr_policy_values = $arr_rule_policy[$i]["policy_values"];
            for ($j = 0; $j < count($arr_policy_values); $j++) {
                if ($arr_policy_values[$j]["value_basis"] == $value_basis) {
                    return $arr_policy_values[$j]["value_value"];
                }
            }
        }
    }

    return "";
}

function _rates_calculator_get_contract_details($con, $contractid) {
    $sql = "SELECT * FROM tblservice_contract
            WHERE id = :contractid ";

    $query = $con->prepare($sql);
    $query->execute(array(":contractid" => $contractid));

    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return $rw;
    }

    return null;
}

function _rates_calculator_get_contract_currency_buy_sell($con, $contractid, $buy_sell) {
    $sql = "select c.currency_code, c.id
            FROM tblservice_contract sc
            INNER JOIN tblservice_contract_currency scc 
            ON sc.id = scc.service_contract_fk
            INNER JOIN tblcurrency c on scc.currencyfk = c.id
            WHERE sc.id = :contractid AND scc.buy_sell = '$buy_sell'";

    $query = $con->prepare($sql);
    $query->execute(array(":contractid" => $contractid));

    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return array("ID" => $rw["id"], "CODE" => $rw["currency_code"]);
    }

    return array("ID" => "-1", "CODE" => "");
}

function _rates_calculator_extra_meal_supp($arr_params, $this_date, $con) {

    $hotelroom = $arr_params["hotelroom"];
    $arr_capacity = $arr_params["arr_capacity"];

    $this_date = date("Y-m-d", strtotime($this_date)); //convert into yyyy-mm-dd

    $arr = array();
    $workings = "";

    $adult = count($arr_params["adults"]);
    $children = $arr_params["children"];

    $currency_buy = $arr_params["currency_buy_code"];

    //check if for this date there is a mandatory Extra Supplement Meal
    //if yes, then add the fees to adults and children

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);
    if (!is_null($rules)) {
        $arr_extra_mealsupplement_rules = $rules["date_mealextrasupplement_rules"];

        for ($i = 0; $i < count($arr_extra_mealsupplement_rules); $i++) {
            $rules = $arr_extra_mealsupplement_rules[$i];
            if ($rules["extra_mandatory"] == 1 &&
                    $rules["extra_extra_date"] == $this_date) {
                //here! got an extra compulsory meal

                $extra_extra_name = $rules["extra_extra_name"];

                //determine if extra meal is SPO deductable or not
                $extra_spo_deductable = $rules["extra_spo_deductable"];

                $workings = "<font color='blue'><b>EXTRA MANDATORY MEAL</b>: $extra_extra_name : </font>";

                if ($extra_spo_deductable == 1) {
                    $workings .= " (SPO DEDUCTABLE) ";
                }
                //============= now adult ==========

                $arr_spo_summary_applied = array(); //count the summary of adults/children applied to SPOs

                for ($a = 1; $a <= $adult; $a++) {

                    $extra_adult = $rules["extra_adult_count"];

                    $ad_pax = $arr_params["adults"][$a - 1];

                    $msg = "$workings Ad #{$a} = $currency_buy $extra_adult";

                    //apply any percentage discount if applicable
                    if ($extra_spo_deductable == 1) {
                        _rates_calculator_apply_spo_discount_percentage($extra_adult, $msg, $arr_params,
                                "ADULT", $ad_pax["age"], $ad_pax["bride_groom"],
                                "EXTRA_MEAL_SUPPLEMENT", $this_date, $a,
                                $arr_spo_summary_applied);
                    }


                    $arr[] = array("MSG" => $msg, "COSTINGS" => $extra_adult,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }



                //============= and now children ==========
                $children_rules = $rules["extra_children"];
                $arr_children_result = _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name, $this_date, $arr_spo_summary_applied, $extra_spo_deductable);
                $arr = array_merge($arr, $arr_children_result);
            }
        }
    }


    return $arr;
}

function _rates_calculator_get_meal_name($supp_mealplan, $con) {
    $sql = "SELECT * FROM tblmealplans WHERE id=:id";

    $query = $con->prepare($sql);
    $query->execute(array(":id" => $supp_mealplan));

    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return $rw["meal"];
    }

    return "";
}

function _rates_calculator_meal_supp($arr_params, $this_date, $con) {


    $workings_flat_rate = "";

    $arr_capacity = $arr_params["arr_capacity"];

    $flg_found_meal_supp = false;
    $arr = array();

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        //so bypass contract MEAL SUPP policies and apply SPO policies
        $arr_capacity = $arr_params["flat_rate_spo_apply"]["RATES"];
        $workings_flat_rate .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];

        $arr = _rates_calculator_meal_supp_lookup($arr_capacity, $workings_flat_rate, $arr_params, $this_date, $con, $flg_found_meal_supp, "SPO");
    }
    //=============================================================================

    if (!$flg_found_meal_supp) {
        //fall back to contract level
        $arr_capacity = $arr_params["arr_capacity"];
        if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
            $workings_flat_rate .= "<font color='orange'><b>$flg_found_meal_supp</b> MEAL SUPPLEMENT POLICIES NOT DEFINED IN SPO.</font> REVERTING TO CONTRACT POLICIES...<br>";
        }
        $arr = _rates_calculator_meal_supp_lookup($arr_capacity, $workings_flat_rate, $arr_params, $this_date, $con, $flg_found_meal_supp, "CONTRACT");
    }


    return $arr;
}

function _rates_calculator_meal_supp_lookup($arr_capacity, $workings_flat_rate, $arr_params, $this_date, $con, &$flg_found_meal_supp, $spo_contract) {

    //$spo_contract = SPO / CONTRACT

    $arr = array();

    $hotelroom = $arr_params["hotelroom"];
    $supp_mealplan = $arr_params["supp_mealplan"];
    $children = $arr_params["children"];

    $currency_buy = $arr_params["currency_buy_code"];

    $meal_supplement_caption = _rates_calculator_get_meal_name($supp_mealplan, $con);
    //======================



    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);
    if (!is_null($rules)) {
        $arr_mealsupplement_rules = $rules["date_mealsupplement_rules"];

        for ($i = 0; $i < count($arr_mealsupplement_rules); $i++) {
            $rules = $arr_mealsupplement_rules[$i];

            if ($rules["meal_ismain"] == 0 && $rules["meal_mealplanfk"] == $supp_mealplan) {

                //get the meal supplement caption
                $flg_found_meal_supp = true;
                $arr_spo_summary_applied = array(); //stores a summary of adult/children count per SPO
                //=========================================================================
                //=========================================================================
                //for each adult:

                for ($a = 1; $a <= count($arr_params["adults"]); $a++) {

                    $adult_supp_price = $rules["meal_adult_count"];

                    $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font> $workings_flat_rate  Ad #$a => $currency_buy $adult_supp_price";

                    //apply spo percent discount if any for that pax
                    $ad_pax = $arr_params["adults"][$a - 1];
                    _rates_calculator_apply_spo_discount_percentage($adult_supp_price, $workings, $arr_params,
                            "ADULT", $ad_pax["age"], $ad_pax["bride_groom"],
                            "MEAL_SUPPLEMENT", $this_date, $a,
                            $arr_spo_summary_applied);


                    $arr[] = array("MSG" => $workings,
                        "COSTINGS" => $adult_supp_price,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }

                //============= and now for each children ==========
                $children_rules = $rules["meal_children"];
                $arr_children_result = _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption, $this_date, $workings_flat_rate, $spo_contract, $arr_spo_summary_applied);
                $arr = array_merge($arr, $arr_children_result);


                //=========================================================================
                //=========================================================================
            }
        }
    }



    return $arr;
}

function _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name, $this_date, &$arr_spo_summary_applied, $extra_spo_deductable) {
    $workings = "";
    $arr = array();

    $workings = "<font color='blue'><b>EXTRA MANDATORY MEAL</b>: $extra_extra_name : </font>";

    if ($extra_spo_deductable == 1) {
        $workings .= " (SPO DEDUCTABLE) ";
    }

    $currency_buy = $arr_params["currency_buy_code"];

    //extra meal supplement always at CONTRACT level
    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, "CONTRACT");

    //for each age group, get children that fall within that range

    $child_total_index = 1;

    for ($a = 0; $a < count($arr_age_groups); $a++) {
        $age_from = $arr_age_groups[$a]["AGEFROM"];
        $age_to = $arr_age_groups[$a]["AGETO"];

        $arr_temp_children = array();
        for ($i = 0; $i < count($children); $i++) {
            if ($age_from <= $children[$i]["age"] && $children[$i]["age"] <= $age_to) {
                $arr_temp_children[] = $children[$i];
            }
        }

        if (count($arr_temp_children) > 0) {
            //get the fees from $children_rules
            for ($r = 0; $r < count($children_rules); $r++) {
                $r_child_agefrom = $children_rules[$r]["child_agefrom"];
                $r_child_ageto = $children_rules[$r]["child_ageto"];

                if ($age_from == $r_child_agefrom && $age_to == $r_child_ageto) {

                    for ($ch = 1; $ch <= count($arr_temp_children); $ch++) {

                        $age = $arr_temp_children[$ch - 1]["age"];
                        $child_meal_rate = $children_rules[$r]["child_count"];

                        $msg = "$workings Ch #{$ch} ({$age_from}-{$age_to}yrs) = $currency_buy $child_meal_rate";

                        //apply any percentage discount if applicable
                        if ($extra_spo_deductable == 1) {
                            _rates_calculator_apply_spo_discount_percentage($child_meal_rate, $msg, $arr_params,
                                    "CHILDREN", $age, "",
                                    "EXTRA_MEAL_SUPPLEMENT", $this_date, $child_total_index,
                                    $arr_spo_summary_applied);
                        }


                        $arr[] = array("MSG" => $msg, "COSTINGS" => $child_meal_rate,
                            "ADCH" => "CHILDREN",
                            "AGE" => $age,
                            "BRIDEGROOM" => "");

                        $child_total_index++;
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption, $this_date, $workings_flat_rate, $spo_contract, &$arr_spo_summary_applied) {

    $arr = array();

    $currency_buy = $arr_params["currency_buy_code"];

    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract);

    //for each age group, get children that fall within that range

    $child_total_index = 1;

    for ($a = 0; $a < count($arr_age_groups); $a++) {
        $age_from = $arr_age_groups[$a]["AGEFROM"];
        $age_to = $arr_age_groups[$a]["AGETO"];

        $arr_temp_children = array();
        for ($i = 0; $i < count($children); $i++) {
            if ($age_from <= $children[$i]["age"] && $children[$i]["age"] <= $age_to) {
                $arr_temp_children[] = $children[$i];
            }
        }


        if (count($arr_temp_children) > 0) {
            //get the fees from $children_rules
            for ($r = 0; $r < count($children_rules); $r++) {
                $r_child_agefrom = $children_rules[$r]["child_agefrom"];
                $r_child_ageto = $children_rules[$r]["child_ageto"];

                if ($age_from == $r_child_agefrom && $age_to == $r_child_ageto) {


                    //for each children in that age group
                    for ($ec = 1; $ec <= count($arr_temp_children); $ec++) {

                        $age = $arr_temp_children[$ec - 1]["age"];
                        $child_meal_rate = $children_rules[$r]["child_count"];

                        $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font> $workings_flat_rate" .
                                " Ch #$ec {$age_from}-{$age_to}yrs => $currency_buy $child_meal_rate";

                        //apply spo percent discount if any for that pax
                        _rates_calculator_apply_spo_discount_percentage($child_meal_rate, $workings, $arr_params,
                                "CHILDREN", $age, "", "MEAL_SUPPLEMENT", $this_date, $child_total_index,
                                $arr_spo_summary_applied);


                        $arr[] = array("MSG" => $workings, "COSTINGS" => $child_meal_rate,
                            "ADCH" => "CHILDREN",
                            "AGE" => $age,
                            "BRIDEGROOM" => "");

                        $child_total_index++;
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_group_single_parent_ageranges($rules) {

    //create an array of rule_age_ranges

    $arr_group_age_ranges = array();

    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE") {
            $rule_ageranges = $rules[$i]["rule_ageranges"];
            if (!in_array($rule_ageranges, $arr_group_age_ranges)) {
                $arr_group_age_ranges[] = $rule_ageranges;
            }
        }
    }

    return $arr_group_age_ranges;
}

function _rates_calculator_children_get_rules_by_agerange($rules, $ag) {
    $arr_rules = array();

    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE") {
            $rule_ageranges = $rules[$i]["rule_ageranges"];
            if ($ag == $rule_ageranges) {
                $arr_rules[] = $rules[$i];
            }
        }
    }

    return $arr_rules;
}

function _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract) {

    //for each age group, get children that fall within that range
    $arr_group_children = array();

    //group children by age groups in the contract
    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract);


    for ($a = 0; $a < count($arr_age_groups); $a++) {
        $age_from = $arr_age_groups[$a]["AGEFROM"];
        $age_to = $arr_age_groups[$a]["AGETO"];

        $arr_temp_children = array();
        for ($i = 0; $i < count($children); $i++) {
            if ($age_from <= $children[$i]["age"] && $children[$i]["age"] <= $age_to) {
                $arr_temp_children[] = $children[$i];
            }
        }

        $arr_group_children[$a]["CHILDREN"] = $arr_temp_children;
        $arr_group_children[$a]["AGFROM"] = $age_from;
        $arr_group_children[$a]["AGTO"] = $age_to;
    }

    return $arr_group_children;
}

function _rates_calculator_process_age_range($this_age_range) {
    //converts a string like:
    //0_1:0^2 ; 2_3:1^3
    //into an array like:
    //[0][agefrom] = 0
    //   [ageto] = 1
    //   [min] = 0
    //   [max] = 2
    //[1][agefrom] = 2
    //   [ageto] = 3
    //   [min] = 1
    //   [max] = 3

    $arr = array();

    $arr_ranges = explode(";", $this_age_range);
    for ($i = 0; $i < count($arr_ranges); $i++) {
        $str_age_range = trim($arr_ranges[$i]);

        if ($str_age_range != "") {
            $arr_age_and_minmax = explode(":", $str_age_range);
            $arr_ages = explode("_", $arr_age_and_minmax[0]);
            $arr_minmax = explode("^", $arr_age_and_minmax[1]);

            $age_from = $arr_ages[0];
            $age_to = $arr_ages[1];

            $min = $arr_minmax[0];
            $max = $arr_minmax[1];

            $arr[] = array("agefrom" => $age_from, "ageto" => $age_to, "min" => $min, "max" => $max);
        }
    }

    return $arr;
}

function _rates_calculator_get_children_count_by_age(&$children, $age_from, $age_to, $destructive) {
    //returns the count of children that fall within the age range provided
    //if destructive is true, 
    //then set the age of the children who have been accounted for to -1

    $count = 0;
    for ($i = 0; $i < count($children); $i++) {
        $age = $children[$i]["age"];
        if ($age_from <= $age && $age <= $age_to) {
            $count++;
            if ($destructive) {
                $children[$i]["age"] = -1;
            }
        }
    }

    return $count;
}

function _rates_calculator_agerange_exact_match_children($arr_age_ranges, $children) {

    //search for the exact allowable match
    //an example of an exact allowable match is:
    //booking has: child 1 yr + teen 15 yrs
    //look for age_ranges with ranges that are exactly the ages in the booking
    //age_ranges MUST NOT contain other age ranges of children that are not in the booking


    for ($i = 0; $i < count($arr_age_ranges); $i++) {

        $arr_temp_children = $children;

        $this_age_range = $arr_age_ranges[$i]; //0_1:0^2 ; 2_3:1^3        
        $this_age_range_passed_test = true;

        $arr_age_minmax = _rates_calculator_process_age_range($this_age_range);

        //================================================================
        //for each age_min_max range, get the count of children
        for ($g = 0; $g < count($arr_age_minmax); $g++) {
            $age_from = $arr_age_minmax[$g]["agefrom"];
            $age_to = $arr_age_minmax[$g]["ageto"];

            $min = $arr_age_minmax[$g]["min"];
            $max = $arr_age_minmax[$g]["max"];

            $count = _rates_calculator_get_children_count_by_age($arr_temp_children, $age_from, $age_to, true);
            if ($count == 0) {
                //there are no children in this age range in the booking
                $this_age_range_passed_test = false;
            } else if ($count != $min || $count != $max) {
                //there is no exact match
                $this_age_range_passed_test = false;
            }
        }

        //================================================================
        //now test that all children have been accounted for
        for ($c = 0; $c < count($arr_temp_children); $c++) {
            $age = $arr_temp_children[$c]["age"];
            if ($age != -1) {
                $this_age_range_passed_test = false;
            }
        }
        //================================================================
        //now test if not failed
        if ($this_age_range_passed_test) {
            return $this_age_range;
        }
    }

    return "";
}

function _rates_calculator_agerange_allowable_match_children($arr_age_ranges, $children) {
    //search for the nearest allowable match after next best match has failed
    //an allowable match is:
    //booking has: child 1 yr + teen 15 yrs
    //look for age_ranges with ranges that cover the ages in the booking
    //age_ranges MAY contain age ranges of children not in the booking

    for ($i = 0; $i < count($arr_age_ranges); $i++) {

        $arr_temp_children = $children;

        $this_age_range = $arr_age_ranges[$i];
        $this_age_range_passed_test = true;

        $arr_age_minmax = _rates_calculator_process_age_range($this_age_range);

        //================================================================
        //for each age_min_max range, get the count of children
        for ($g = 0; $g < count($arr_age_minmax); $g++) {
            $age_from = $arr_age_minmax[$g]["agefrom"];
            $age_to = $arr_age_minmax[$g]["ageto"];

            $min = $arr_age_minmax[$g]["min"];
            $max = $arr_age_minmax[$g]["max"];

            $count = _rates_calculator_get_children_count_by_age($arr_temp_children, $age_from, $age_to, true);
            if (!($min <= $count && $count <= $max)) {
                //check if the count match the range allowable
                $this_age_range_passed_test = false;
            }
        }

        //================================================================
        //now test that all children have been accounted for
        for ($c = 0; $c < count($arr_temp_children); $c++) {
            $age = $arr_temp_children[$c]["age"];
            if ($age != -1) {
                $this_age_range_passed_test = false;
            }
        }
        //================================================================
        //now test if not failed
        if ($this_age_range_passed_test) {
            return $this_age_range;
        }
    }

    return "";
}

function _rates_calculator_agerange_nextbest_match_children($arr_age_ranges, $children) {

    //search for the next best match after exact match has failed
    //next best match is:
    //booking has: child 1 yr + teen 15 yrs
    //look for age_ranges with ranges that cover the ages in the booking
    //age_ranges MUST NOT contain age ranges of children not in the booking


    for ($i = 0; $i < count($arr_age_ranges); $i++) {

        $arr_temp_children = $children;

        $this_age_range = $arr_age_ranges[$i];
        $this_age_range_passed_test = true;

        $arr_age_minmax = _rates_calculator_process_age_range($this_age_range);

        //================================================================
        //for each age_min_max range, get the count of children
        for ($g = 0; $g < count($arr_age_minmax); $g++) {
            $age_from = $arr_age_minmax[$g]["agefrom"];
            $age_to = $arr_age_minmax[$g]["ageto"];

            $min = $arr_age_minmax[$g]["min"];
            $max = $arr_age_minmax[$g]["max"];

            $count = _rates_calculator_get_children_count_by_age($arr_temp_children, $age_from, $age_to, true);
            if ($count == 0) {
                //there are no children in this age range
                $this_age_range_passed_test = false;
            } else if (!($min <= $count && $count <= $max)) {
                //check if the count match the range allowable
                $this_age_range_passed_test = false;
            }
        }

        //================================================================
        //now test that all children have been accounted for
        for ($c = 0; $c < count($arr_temp_children); $c++) {
            $age = $arr_temp_children[$c]["age"];
            if ($age != -1) {
                $this_age_range_passed_test = false;
            }
        }
        //================================================================
        //now test if not failed
        if ($this_age_range_passed_test) {
            return $this_age_range;
        }
    }

    return "";
}

function _rates_calculator_lookup_single_parent_parent_rates($rules, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci, $this_date, &$arr_spo_summary_applied) {

    $arr = array();
    $rates = 0;

    $workings = "";

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================

    $workings .= " SINGLE PARENT: AD: ";

    $currency_buy = $arr_params["currency_buy_code"];

    $num_children = count($children);
    $basis = "";
    $category = "";
    $value = 0;

    if ($num_children == 1) {
        $basis = _rates_calculator_lookup_single_parent_rules_cells($rules, "basis", $num_children);
        $category = _rates_calculator_lookup_single_parent_rules_cells($rules, "category", $num_children);
        $value = _rates_calculator_lookup_single_parent_rules_cells($rules, "value", $num_children);
    } else {
        while ($basis == "" && $num_children >= 1) {
            $basis = _rates_calculator_lookup_single_parent_rules_cells($rules, "basis", $num_children);
            $category = _rates_calculator_lookup_single_parent_rules_cells($rules, "category", $num_children);
            $value = _rates_calculator_lookup_single_parent_rules_cells($rules, "value", $num_children);
            $num_children--;
        }
    }

    $single_pax = $arr_params["adults"][0];

    if ($category == "SINGLE") {
        if ($basis == "FLAT") {
            //take the rate as it is
            $workings .= "$category $currency_buy $value";

            //take price as it is
            $rates = $value;

            //apply eci percentage for that single parent if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

            //apply spo percent discount for that single parent if any            
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params,
                    "ADULT", $single_pax["age"], $single_pax["bride_groom"],
                    "ROOM", $this_date, 1, $arr_spo_summary_applied);

            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        } else if ($basis == "%") {
            //calculate as a percentage of adult single

            $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
            $adult_index = 1;
            $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
            $rates_adult = $arr_adult_workings["RATES_ADULT"];
            $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

            $workings .= " $category {$value}% of ";
            if (trim($workings_adult) != "") {
                $workings .= "$workings_adult";
            }

            $fees = 0;
            if ($value > 0) {
                $fees = round(($value / 100) * $rates_adult, 2);
            }
            $rates = $fees;

            $workings .= " = $currency_buy $fees";

            //apply eci percentage for that single parent if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

            //apply spo percent discount for that single parent if any
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params,
                    "ADULT", $single_pax["age"], $single_pax["bride_groom"],
                    "ROOM", $this_date, 1, $arr_spo_summary_applied);


            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        }
    } else if ($category == "1/2 DBL") {
        if ($basis == "FLAT") {
            //take the rate as it is
            $workings .= "$category $currency_buy $value";

            //take price as it is
            $rates = $value;


            //apply eci percentage for that single parent if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

            //apply spo percent discount for that single parent if any
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params,
                    "ADULT", $single_pax["age"], $single_pax["bride_groom"],
                    "ROOM", $this_date, 1, $arr_spo_summary_applied);


            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        } else if ($basis == "%") {
            //calculate as a percentage of adult 1/2 DBL
            //if there is no adult 1/2 DBL, take adult DOUBLE and divide by 2
            //TODO

            $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
            $adult_index = 2;
            $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
            $rates_adult = $arr_adult_workings["RATES_ADULT"];
            $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

            $workings .= "$category {$value}% of ";
            if (trim($workings_adult) != "") {
                $workings .= "$workings_adult";
            }

            $fees = 0;
            if ($value > 0) {
                $fees = round(($value / 100) * $rates_adult, 2);
            }
            $rates = $fees;

            $workings .= " = $currency_buy $fees";

            //apply eci percentage for that single parent if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

            //apply spo percent discount for that single parent if any
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT",
                    $single_pax["age"], $single_pax["bride_groom"],
                    "ROOM", $this_date, 1, $arr_spo_summary_applied);

            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        }
    }

    return $arr;
}

function _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules, $arr_params, $arr_adultpolicies_rules, $arr_eci, $this_date, &$arr_spo_summary_applied) {

    $arr_final = array();
    $rates = 0;
    $workings = "";

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================

    $currency_buy = $arr_params["currency_buy_code"];

    //for each child in $arr_group_children, get the agerange and the index
    //for that age range and index, lookup the category, basis and value
    $child_count_index = 1;

    for ($a = 0; $a < count($arr_group_children); $a++) {


        $arr_children = $arr_group_children[$a]["CHILDREN"];
        $age_from = $arr_group_children[$a]["AGFROM"];
        $age_to = $arr_group_children[$a]["AGTO"];

        $_arr = array();
        $index = count($arr_children);
        while ($index > 0) {
            $child_age = $arr_children[$index - 1]["age"];
            $category = _rates_calculator_lookup_single_parent_children_rates_index($index, $rules, $age_from, $age_to, "category");
            //SINGLE, DOUBLE, 1/2 DBL, TRPL, SHARING

            $basis = _rates_calculator_lookup_single_parent_children_rates_index($index, $rules, $age_from, $age_to, "basis");
            //%, FLAT

            $value = _rates_calculator_lookup_single_parent_children_rates_index($index, $rules, $age_from, $age_to, "value");


            if ($basis == "FLAT") {
                $workings = "SINGLE PARENT: (CH #$child_count_index {$child_age}yr $category $currency_buy $value)";

                //take price as it is
                $rates = $value;

                if ($category == "SINGLE") {

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "1/2 DBL") {
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "DOUBLE") {
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 2);
                    break;
                } else if ($category == "TRPL") {
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 3);
                    break;
                } else if ($category == "SHARING") {
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                }
            } else if ($basis == "%") {
                $workings = "";
                if ($category == "SINGLE") {

                    //get price from adult for that index and category
                    $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
                    $adult_index = 1;
                    $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

                    $workings .= "SINGLE PARENT: (CH #$child_count_index $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;

                    $workings .= " = $currency_buy $fees)";
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);

                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "1/2 DBL") {
                    //get price from adult for that index and category
                    //get the price of adult 1/2 DBL
                    $adult_index = 2;
                    $adult_basis = "1/n";
                    $arr_adult_workings = _rates_calculator_lookup_single_parent_children_adult_rates($arr_adultpolicies_rules, $adult_index, $arr_params, $adult_basis, "1/2 DBL");
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];


                    $workings .= "SINGLE PARENT: (CH #$child_count_index $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;
                    $workings .= " = $currency_buy $fees)";

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);

                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "DOUBLE") {
                    //get price from adult for that index and category

                    $adult_index = 2;
                    $adult_basis = "n";
                    $arr_adult_workings = _rates_calculator_lookup_single_parent_children_adult_rates($arr_adultpolicies_rules, $adult_index, $arr_params, $adult_basis, "DBL");

                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];


                    $workings .= "SINGLE PARENT: (CH #$child_count_index $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;
                    $workings .= " = $currency_buy $fees)";

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);

                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "TRPL") {

                    //get price from adult for that index and category
                    $adult_index = 3;
                    $adult_basis = "n";
                    $arr_adult_workings = _rates_calculator_lookup_single_parent_children_adult_rates($arr_adultpolicies_rules, $adult_index, $arr_params, $adult_basis, "TRPL");

                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

                    $workings .= "SINGLE PARENT: (CH #$child_count_index $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;
                    $workings .= " = $currency_buy $fees)";

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);

                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "SHARING") {
                    //this is not possible
                    $workings .= "SINGLE PARENT: (CH #$child_count_index {$child_age}yr : SHARING % IS NOT POSSIBLE)";
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => 0, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                }
            }

            $index--;
            $child_count_index++;
        }

        //now need to know if there is a splitting of children fees between DOUBLE,1/2 DBL or TRIPLE children
        $child_count_index = 1;

        $temp_arr = array();
        for ($i = 0; $i < count($_arr); $i++) {
            $work = $_arr[$i]["WORKINGS"];
            $rates = $_arr[$i]["RATES"];
            $childindex = $_arr[$i]["CHILDINDEX"];
            $split_between = $_arr[$i]["TO_SPLIT_BETWEEN"];
            $child_age = $arr_children[$childindex - 1]["age"];

            if ($split_between == 0) {
                //apply eci percentage if any for that kiddo
                _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $work, $currency_buy);

                //apply spo percent discount for that kiddo if any
                _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params,
                        "CHILDREN", $child_age, "", "ROOM",
                        $this_date, $child_count_index, $arr_spo_summary_applied);
                $child_count_index++;

                $temp_arr[] = array("MSG" => $work, "COSTINGS" => $rates,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            } else {
                //need to split
                $ch_buyprice = round($rates / $split_between);
                $cumul_buyprice = 0;

                for ($chinx = 1; $chinx <= $split_between; $chinx++) {

                    if ($chinx == $split_between) {
                        $ch_buyprice = $rates - $cumul_buyprice;
                    } else {
                        $cumul_buyprice += $ch_buyprice;
                    }

                    $msg = "$work => Ch #$childindex => $currency_buy $ch_buyprice";

                    //apply eci percentage if any for that kiddo
                    _rates_calculator_apply_rates_eci_percentage($ch_buyprice, $arr_eci, $msg, $currency_buy);

                    //apply spo percent discount for that kiddo if any
                    _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params,
                            "CHILDREN", $child_age, "", "ROOM",
                            $this_date, $child_count_index, $arr_spo_summary_applied);

                    $child_count_index++;


                    $temp_arr[] = array("MSG" => $msg, "COSTINGS" => $ch_buyprice,
                        "ADCH" => "CHILDREN",
                        "AGE" => $child_age,
                        "BRIDEGROOM" => "");

                    $childindex--;
                }
            }
        }

        //finally merge into final array
        $arr_final = array_merge($arr_final, $temp_arr);
    }

    return $arr_final;
}

function _rates_calculator_lookup_single_parent_children_adult_rates($arr_adultpolicies_rules, $adultcount, $arr_params, $adult_basis, $description) {
    $rates = 0;
    $workings = "";

    $currency_buy = $arr_params["currency_buy_code"];

    for ($i = 0; $i < count($arr_adultpolicies_rules); $i++) {
        if ($adultcount == $arr_adultpolicies_rules[$i]["rule_category"]) {
            $arr_rule_policy = $arr_adultpolicies_rules[$i]["rule_policy"];

            $value = _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, "value");
            $basis = _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, "basis");

            if ($basis == $adult_basis) {
                $rates = $value;
                $workings = "(AD $description = $currency_buy $value)";
            }
        }
    }

    return array("RATES_ADULT" => $rates, "WORKINGS_ADULT" => $workings);
}

function _rates_calculator_lookup_single_parent_rules_cells($rules, $cat_basis_val, $num_children) {

    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE") {
            if ($rules[$i]["rule_category"] == $num_children) {
                $arrpolicies = $rules[$i]["rule_policy"];
                for ($p = 0; $p < count($arrpolicies); $p++) {
                    if ($arrpolicies[$p]["policy_action"] != "DELETE") {
                        if ($arrpolicies[$p]["policy_adult_child"] == "ADULT" &&
                                $arrpolicies[$p]["policy_category"] == $cat_basis_val) {
                            $arrvalues = $arrpolicies[$p]["policy_values"];
                            for ($v = 0; $v < count($arrvalues); $v++) {
                                if ($arrvalues[$v]["value_action"] != "DELETE") {
                                    return $arrvalues[$v]["value_value"];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return "";
}

function _rates_calculator_lookup_single_parent_children_rates_index($index, $rules, $age_from, $age_to, $cat_basis_val) {
    for ($i = 0; $i < count($rules); $i++) {
        if ($rules[$i]["rule_action"] != "DELETE") {
            if ($rules[$i]["rule_category"] == $index) {
                $arrpolicies = $rules[$i]["rule_policy"];
                for ($p = 0; $p < count($arrpolicies); $p++) {
                    if ($arrpolicies[$p]["policy_action"] != "DELETE") {
                        if ($arrpolicies[$p]["policy_adult_child"] == "CHILD" &&
                                $arrpolicies[$p]["policy_category"] == $cat_basis_val &&
                                $arrpolicies[$p]["policy_child_agefrom"] == $age_from &&
                                $arrpolicies[$p]["policy_child_ageto"] == $age_to) {
                            $arrvalues = $arrpolicies[$p]["policy_values"];
                            for ($v = 0; $v < count($arrvalues); $v++) {
                                if ($arrvalues[$v]["value_action"] != "DELETE") {
                                    return $arrvalues[$v]["value_value"];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return "";
}

function _rates_calculator_get_numnights($checkin_date, $checkout_date) {
    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);
    //$checkout = $checkout->modify('+1 day');

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($checkin, $interval, $checkout);
    $num_nights = iterator_count($period);

    return $num_nights;
}

function _rates_calculator_getTaxCommi_AddOn_Basis($hotelroom, $arr_taxcomm) {
    //get the basis for ADDON items for that room
    //check for room exception first

    for ($i = 0; $i < count($arr_taxcomm); $i++) {
        if ($arr_taxcomm[$i]["room_id"] == $hotelroom &&
                $arr_taxcomm[$i]["room_hasexception"] == "YES") {
            $arr_buy = $arr_taxcomm[$i]["buying_settings"];
            for ($j = 0; $j < count($arr_buy); $j++) {
                if ($arr_buy[$j]["setting_core_addon"] == "ADDON") {
                    $basis = trim($arr_buy[$j]["setting_basis"]);
                    if ($basis != "") {
                        $basis = str_replace("% ", "", $basis);
                        $basis = str_replace("FLAT ", "", $basis);

                        return $basis;
                    }
                }
            }

            //no basis in buy? then look in sell
            $arr_sell = $arr_taxcomm[$i]["selling_settings"];
            for ($j = 0; $j < count($arr_sell); $j++) {
                $basis = trim($arr_sell[$j]["setting_basis"]);
                if ($basis != "") {
                    $basis = str_replace("% ", "", $basis);
                    $basis = str_replace("FLAT ", "", $basis);

                    return $basis;
                }
            }

            //no basis in buy or sell? then return default
            return "PNI ALL";
        }
    }

    //==============================================
    //if we are here, means that there are no room exceptions
    //look for general settings

    for ($i = 0; $i < count($arr_taxcomm); $i++) {
        if ($arr_taxcomm[$i]["room_id"] == "GENERAL") {
            $arr_buy = $arr_taxcomm[$i]["buying_settings"];
            for ($j = 0; $j < count($arr_buy); $j++) {
                if ($arr_buy[$j]["setting_core_addon"] == "ADDON") {
                    $basis = trim($arr_buy[$j]["setting_basis"]);
                    if ($basis != "") {
                        $basis = str_replace("% ", "", $basis);
                        $basis = str_replace("FLAT ", "", $basis);

                        return $basis;
                    }
                }
            }

            //no basis in buy? then look in sell
            $arr_sell = $arr_taxcomm[$i]["selling_settings"];
            for ($j = 0; $j < count($arr_sell); $j++) {
                $basis = trim($arr_sell[$j]["setting_basis"]);
                if ($basis != "") {
                    $basis = str_replace("% ", "", $basis);
                    $basis = str_replace("FLAT ", "", $basis);

                    return $basis;
                }
            }

            //no basis in buy or sell? then return default
            return "PNI ALL";
        }
    }

    //==============================================
    //if we are here means that no addons have been defined in general settings
    //return default 
    return "PNI ALL";
}

function _rates_calculator_lookup_spo($arr_params, $con) {
    //returns an array of spos for that hotel 
    //that match the conditions of the contract
    /*
      $adult = count($arr_params["adults"]);
      $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
      $checkin_time = $arr_params["checkin_time"];
      $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
      $checkout_time = $arr_params["checkout_time"];
      $children = $arr_params["children"];
      $country = $arr_params["country"];
      $hotel = $arr_params["hotel"];
      $hotelroom = $arr_params["hotelroom"];
      $mealplan = $arr_params["mealplan"];
      $rate = $arr_params["rate"];
      $touroperator = $arr_params["touroperator"];
      $contractid = $arr_params["contractids"];
     */
}

function _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, $buyprice, $arr_items, $isPN_PPPN) {
    $arr = array();

    global $__arr_alphabets;

    $contractid = $arr_params["current_contract_id"];
    $hotelroom = $arr_params["hotelroom"];
    $PN_PPN = $arr_params["TAX_COMMI_BASIS"];

    $arr_taxcomm_room = _rate_calculator_taxcommi_for_room($arr_taxcomm, $hotelroom);
    $exchgrates = _contract_exchangerates($con, $contractid); //TO BE OPTIMISED

    $currency_sell_id = $arr_params["currency_sell_id"];
    $currency_buy_id = $arr_params["currency_buy_id"];

    $currency_sell = $arr_params["currency_sell_code"];
    $currency_buy = $arr_params["currency_buy_code"];

    $arr[] = array("CAPTION" => "BUY PRICE", "CURRENCY_ID" => $currency_buy_id,
        "CURRENCY_CODE" => $currency_buy, "VALUE" => $buyprice);

    if (strpos($PN_PPN, $isPN_PPPN) !== false) {
        $arr_calculations = _contract_calculatesp($con, $buyprice, $currency_buy_id, $currency_sell_id, $arr_taxcomm_room["buying_settings"], $arr_taxcomm_room["selling_settings"], $exchgrates, $__arr_alphabets, $arr_items);
        for ($i = 0; $i < count($arr_calculations); $i++) {
            $item_name = $arr_calculations[$i]["setting_item_name"];
            $item_name = strtoupper($item_name);
            $value = $arr_calculations[$i]["my_calculated_value"];
            $buysell = $arr_calculations[$i]["setting_buying_selling"];

            $curid = "";
            $cur = "";

            if ($buysell == "BUYING") {
                $curid = $currency_buy_id;
                $cur = $currency_buy;
            } else {
                $curid = $currency_sell_id;
                $cur = $currency_sell;
            }

            $arr[] = array("CAPTION" => $item_name, "CURRENCY_ID" => $curid,
                "CURRENCY_CODE" => $cur, "VALUE" => $value);
        }
    }


    return $arr;
}

function _rates_calculator_apply_rates_lco_flat_pni($arr, $arr_lco, $arr_params, &$lco_applied, $this_date) {
    //$lco_applied = false;
    //this will only apply if late check out FLAT PNI, then:
    //share the lco costs between Non FOC pax

    $currency_buy = $arr_params["currency_buy_code"];
    $workings = $arr_lco["WORKINGS"];
    $charge_type = $arr_lco["CHARGE_TYPE"];
    $charge_value = $arr_lco["CHARGE_VALUE"];

    if ($charge_type == "" && $workings == "" && $charge_value == "") {
        return $arr; //no lco!
    }

    if ($charge_type != "FLAT PNI") {
        return $arr; // only applicable if eci is flat per night
    }

    $lco_applied = true;

    $num_non_foc_pax = 0;

    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i]["COSTINGS"] > 0) {
            $num_non_foc_pax++;
        }
    }

    //split the lco now
    $pax_lco = 0;
    if ($num_non_foc_pax > 0) {
        $pax_lco = round($charge_value / $num_non_foc_pax, 2);
    }


    //now apply it to the costings per each pax
    $cumul_applied = 0;
    $applied_to = 1;
    $ad_total_index = 0;
    $ch_total_index = 0;
    $arr_spo_summary_applied = array(); //stores a summary of adult/children counts per SPO

    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $pax_adch = $arr[$i]["ADCH"];
        $pax_age = $arr[$i]["AGE"];
        $pax_bridegroom = $arr[$i]["BRIDEGROOM"];

        //===========
        $index_to_use = 0;
        if ($pax_adch == "ADULT") {
            $ad_total_index++;
            $index_to_use = $ad_total_index;
        } else {
            $ch_total_index++;
            $index_to_use = $ch_total_index;
        }
        //===========

        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_lco;
            } else {
                $pax_lco = $charge_value - $cumul_applied;
            }

            $applied_to++;

            $msg .= " <br> ($workings = FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_lco per <b>Non FOC</b> pax) ";
            $costings = $pax_lco;

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($costings, $msg, $arr_params, $pax_adch,
                    $pax_age, $pax_bridegroom, "ROOM",
                    $this_date, $index_to_use, $arr_spo_summary_applied);

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $arr_params, $this_date) {

//this will only apply if early check in FLAT PNI, then:
    //share the eci costs between Non FOC pax
    $currency_buy = $arr_params["currency_buy_code"];

    $workings = $arr_eci["WORKINGS"];
    $charge_type = $arr_eci["CHARGE_TYPE"];
    $charge_value = $arr_eci["CHARGE_VALUE"];

    if ($charge_type == "" && $workings == "" && $charge_value == "") {
        return $arr; //no eci!
    }

    if ($charge_type != "FLAT PNI") {
        return $arr; // only applicable if eci is flat per night
    }

    $num_non_foc_pax = 0;

    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i]["COSTINGS"] > 0) {
            $num_non_foc_pax++;
        }
    }

    //split the eci now
    $pax_eci = 0;
    if ($num_non_foc_pax > 0) {
        $pax_eci = round($charge_value / $num_non_foc_pax, 2);
    }


    //now apply it to the costings per each pax
    $cumul_applied = 0;
    $applied_to = 1;
    $ad_total_index = 0;
    $ch_total_index = 0;
    $arr_spo_summary_applied = array(); //stores a count of adult/child per SPO

    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $pax_adch = $arr[$i]["ADCH"];
        $pax_age = $arr[$i]["AGE"];
        $pax_bridegroom = $arr[$i]["BRIDEGROOM"];

        //==============
        $index_to_use = 0;
        if ($pax_adch == "ADULT") {
            $ad_total_index++;
            $index_to_use = $ad_total_index;
        } else {
            $ch_total_index++;
            $index_to_use = $ch_total_index;
        }
        //==============

        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_eci;
            } else {
                $pax_eci = $charge_value - $cumul_applied;
            }

            $applied_to++;

            $msg .= "<br> + ($workings =  FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_eci per <b>Non FOC</b> pax) ";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_eci, $msg, $arr_params, $pax_adch, $pax_age,
                    $pax_bridegroom, "ROOM", $this_date, $index_to_use,
                    $arr_spo_summary_applied);

            $costings += $pax_eci;

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_calc_discount_PNI($arr_params, $arr) {
    //this discount is split equally per pax and is applicable for all pax irrespective of age, bridegroom

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $currency_buy = $arr_params["currency_buy_code"];
    $apply_discounts = $arr_params["lookupmode"]["DISCOUNTS"]; //true or false

    $num_pax = count($arr);

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount
        //finally apply the discount when it flat PPPN

        if ($apply_discounts) {
            if ($disc_value > 0) {

                if ($disc_type == "FLAT_PNI") {

                    $disc_amt = $disc_value;

                    //split the discount PNI over now
                    $per_pax_discount = round($disc_amt / $num_pax, 2);

                    //now apply it to the discount to each pax
                    $cumul_applied = 0;
                    $applied_to = 1;

                    for ($p = 0; $p < count($arr); $p++) {
                        $costings = $arr[$p]["COSTINGS"];
                        $msg = $arr[$p]["MSG"];

                        if ($costings > 0) {

                            if ($applied_to < $num_pax) {
                                $cumul_applied += $per_pax_discount;
                            } else {
                                $per_pax_discount = $disc_amt - $cumul_applied;
                            }
                            $applied_to++;


                            $msg .= "<br><font color='#BB3C94'> - (<b>SPO</b> => [ID:$spo_id $spo_type - $spo_name] : FLAT PNI : $currency_buy $disc_amt &#247; $num_pax = $currency_buy $per_pax_discount per pax)</font>";

                            $costings -= $per_pax_discount;

                            if ($costings < 0) {
                                $costings = 0;
                            }

                            $arr[$p]["MSG"] = $msg;
                            $arr[$p]["COSTINGS"] = $costings;
                        }
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_calc_discount_PPPN($arr_params, $arr, $room_nonroom) {
    // $room_nonroom = {ROOM, NONROOM}
    //for each person in $arr, decide the discount to be applied if all criteria meet up
    $arr_spo_summary_applied = array();

    for ($i = 0; $i < count($arr); $i++) {
        $pax = $arr[$i];
        $rates = $pax["COSTINGS"];
        $msg = $pax["MSG"];
        $adch = $pax["ADCH"];
        $pax_age = $pax["AGE"];
        $pax_bridegroom = $pax["BRIDEGROOM"];

        _rates_calculator_apply_spo_discount_PPPN($rates, $msg, $arr_params, $adch, $pax_age, $pax_bridegroom, $room_nonroom, $arr_spo_summary_applied);

        $arr[$i]["COSTINGS"] = $rates;
        $arr[$i]["MSG"] = $msg;
    }

    return $arr;
}

function _rates_calculator_apply_spo_discount_PPPN(&$rates, &$msg, $arr_params, $adult_children,
        $pax_age, $pax_bridegroom, $room_nonroom,
        &$arr_spo_summary_applied) {
    // $adult_child = {ADULT, CHILDREN}
    // $pax_age is the age of the pax in question
    // $pax_bridegroom is the marital status of the pax in question
    // $room_nonroom = {ROOM, NONROOM}
    // $applied_discount is set to TRUE if discount is applied 
    // if is CHILDREN then child age is within SHARING/OWN age ranges (if applicable)


    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $apply_discounts = $arr_params["lookupmode"]["DISCOUNTS"]; //true or false

    $currency_buy = $arr_params["currency_buy_code"];

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $disc_ad_ch = $discount_item["AD_CH"]; //is discount for children or adults or both

        $disc_max_ad = $discount_item["MAX_AD"]; //max adult discount is applicable to
        $disc_max_ad_category = $discount_item["MAX_AD_CATEGORY"];
        $disc_max_ch = $discount_item["MAX_CH"]; //max children discount is applicable to
        $disc_max_ch_category = $discount_item["MAX_CH_CATEGORY"];


        $disc_bd_gm = $discount_item["BRIDE_GROOM"]; //is discount for bride, groom or both
        $disc_ag_frm = $discount_item["AGE_FROM"]; //is discount for a specific age group
        $disc_ag_to = $discount_item["AGE_TO"]; //is discount for a specific age group
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount
        $disc_sharing_age_ranges = $discount_item["SHARING_AGE_RANGES"]; //children sharing age ranges
        $disc_own_age_ranges = $discount_item["OWN_AGE_RANGES"]; //children own age ranges
        //==========================================================
        //==========================================================
        //test if adult or children max applicable passed
        $flg_index_passed = true;
        if ($adult_children == "ADULT") {
            if ($disc_max_ad != "" && $disc_max_ad_category == "APPLICABLE") {
                $ad_count = _rates_calculator_get_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);
                if ($ad_count >= $disc_max_ad) {
                    $flg_index_passed = false;
                }
            }
        } else if ($adult_children == "CHILDREN") {
            if ($disc_max_ch != "" && $disc_max_ch_category == "APPLICABLE") {
                $ch_count = _rates_calculator_get_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);
                if ($ch_count >= $disc_max_ch) {
                    $flg_index_passed = false;
                }
            }
        }



        if ($apply_discounts) {
            if ($flg_index_passed) {
                if (($adult_children == "ADULT" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "ADULT")) ||
                        $adult_children == "CHILDREN" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "CHILDREN") ||
                        $disc_ad_ch == "") {
                    //passed check for adult or children applicable discounts

                    if (_rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to)) {
                        //passed age limits check

                        if (_rates_calculator_apply_spo_discount_test_sharingown_children_age($adult_children, $pax_age, $disc_sharing_age_ranges, $disc_own_age_ranges, $arr_params)) {
                            //passed children sharing/own age limits check

                            if (_rates_calculator_apply_spo_discount_test_bride_groom($pax_bridegroom, $disc_bd_gm, $adult_children)) {

                                //passed bride groom checks
                                //finally apply the discount when it flat PPPN
                                if ($disc_value > 0) {

                                    if (($disc_type == "FLAT_PPPN" && $room_nonroom == "ROOM")) {

                                        //discount applied
                                        //update the counter in $arr_spo_summary_applied

                                        _rates_calculator_update_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);


                                        $disc_amt = $disc_value;
                                        $msg .= "<br><font color='#BB3C94'> - (<b>SPO</b> => [ID:$spo_id $spo_type - $spo_name] : PPPN : $currency_buy $disc_amt)</font>";
                                        $rates -= $disc_amt;

                                        if ($rates < 0) {
                                            $rates = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return;
}

function _rates_calculator_apply_spo_discount_percentage(&$rates, &$msg, $arr_params, $adult_children,
        $pax_age, $pax_bridegroom, $room_nonroom, $this_date, $ad_ch_index,
        &$arr_spo_summary_applied) {

    // $adult_child = {ADULT, CHILDREN}
    // $pax_age is the age of the pax in question
    // $pax_bridegroom is the marital status of the pax in question
    // $room_nonroom = {ROOM, NONROOM}
    // $this_date = yyyy-mm-dd : the date being calculated on
    // $ad_ch_index : the index of the adult or child to whom discount is being applied
    // $arr_spo_summary_applied is an array the counts the number of times an spo has been applied to an adult or children
    // if is CHILDREN then child age is within SHARING/OWN age ranges (if applicable)

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $currency_buy = $arr_params["currency_buy_code"];
    $apply_discounts = $arr_params["lookupmode"]["DISCOUNTS"]; //true or false
    //==========================================================
    $cumul_room_percentage = 0;
    $cumul_room_workings = array();
    $workings_room = "";

    $cumul_nonroom_percentage = 0;
    $cumul_nonroom_workings = array();
    $workings_non_room = "";

    $arr_individual_percentage = array();
    $arr_individual_workings = array();

    //==========================================================

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {

        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];

        $disc_ad_ch = $discount_item["AD_CH"]; //is discount for children or adults or both

        $disc_max_ad = $discount_item["MAX_AD"]; //max adult discount is applicable to
        $disc_max_ad_category = $discount_item["MAX_AD_CATEGORY"];
        $disc_max_ch = $discount_item["MAX_CH"]; //max children discount is applicable to
        $disc_max_ch_category = $discount_item["MAX_CH_CATEGORY"];

        $disc_bd_gm = $discount_item["BRIDE_GROOM"]; //is discount for bride, groom or both

        $disc_ag_frm = $discount_item["AGE_FROM"]; //is discount for a specific age group
        $disc_ag_to = $discount_item["AGE_TO"]; //is discount for a specific age group
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount
        $disc_sharing_age_ranges = $discount_item["SHARING_AGE_RANGES"]; //children sharing age ranges
        $disc_own_age_ranges = $discount_item["OWN_AGE_RANGES"]; //children own age ranges

        $disc_is_cumulative = $discount_item["IS_CUMULATIVE"]; //is discount cumulative or not

        $apply_to_dates = $discount_item["APPLY_TO_DATES"]; //any array of date filters?


        if ($apply_discounts) {

            //passed apply discount check

            if (_rates_calculator_apply_spo_discount_test_dates($apply_to_dates, $this_date)) {

                //passed date check for SPO

                if (_rates_calculator_apply_spo_discount_test_pax_index($adult_children, $disc_max_ad, $disc_max_ad_category, $disc_max_ch, $disc_max_ch_category, $arr_spo_summary_applied, $spo_id)) {

                    //passed adult/children index check for SPO

                    if (_rates_calculator_apply_spo_discount_test_ad_ch($adult_children, $disc_ad_ch)) {

                        //passed check for adult or children applicable discounts

                        if (_rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to)) {

                            //passed age limits check

                            if (_rates_calculator_apply_spo_discount_test_sharingown_children_age($adult_children, $pax_age, $disc_sharing_age_ranges, $disc_own_age_ranges, $arr_params)) {

                                //passed children sharing/own age limits check

                                if (_rates_calculator_apply_spo_discount_test_bride_groom($pax_bridegroom, $disc_bd_gm, $adult_children)) {

                                    //passed bride groom checks
                                    //finally apply the discount when it is percetage only

                                    if ($disc_value > 0) {

                                        if ($disc_is_cumulative == 1) {
                                            if ($disc_type == "%ALL") {
                                                //update the counter in $arr_spo_summary_applied
                                                _rates_calculator_update_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);


                                                //discount applied                                                
                                                $cumul_room_percentage += $disc_value;
                                                $cumul_room_workings[] = "[SPO - ID:$spo_id $spo_type - $spo_name : $disc_value%]";

                                                $cumul_nonroom_percentage += $disc_value;
                                                $cumul_nonroom_workings[] = "[SPO - ID:$spo_id $spo_type - $spo_name : $disc_value%]";
                                            } else if ($disc_type == "%ROOM" && $room_nonroom == "ROOM") {
                                                //update the counter in $arr_spo_summary_applied
                                                _rates_calculator_update_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);


                                                //discount applied                                                
                                                $cumul_room_percentage += $disc_value;
                                                $cumul_room_workings[] = "[SPO - ID:$spo_id $spo_type - $spo_name : $disc_value%]";
                                            }
                                        } else {
                                            if (($disc_type == "%ROOM" && $room_nonroom == "ROOM") ||
                                                    ($disc_type == "%ALL")) {

                                                //update the counter in $arr_spo_summary_applied
                                                _rates_calculator_update_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);

                                                $arr_individual_percentage[] = array("SPO" => "[SPO - ID:$spo_id $spo_type - $spo_name : $disc_value%]", "DISCOUNT_PER" => $disc_value);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //=========================================
    $_temp_workings = "";

    //apply the discounts now
    if ($cumul_room_percentage > 0 && $room_nonroom == "ROOM") {
        $workings_room = implode("<br> + ", $cumul_room_workings);
        $workings_room = "<br><font color='#BB3C94'> - <b>(CUMULATIVE</b> :<br>$workings_room<b>)</b>";

        $_rates_before = $rates;
        $disc_amt = round(($cumul_room_percentage / 100) * $rates, 2);
        $rates -= $disc_amt;
        if ($rates < 0) {
            $rates = 0;
        }

        $workings_room .= " = $cumul_room_percentage% of $currency_buy $_rates_before = $currency_buy $disc_amt</font>";
    } else if ($cumul_nonroom_percentage > 0 && $room_nonroom != "ROOM") {
        $workings_non_room = implode(" + <br>", $cumul_nonroom_workings);
        $workings_non_room = "<br><font color='#BB3C94'> - <b>(CUMULATIVE</b> :<br>$workings_non_room<b>)</b>";

        $_rates_before = $rates;
        $disc_amt = round(($cumul_nonroom_percentage / 100) * $rates, 2);
        $rates -= $disc_amt;
        if ($rates < 0) {
            $rates = 0;
        }

        $workings_non_room .= " = $cumul_nonroom_percentage% of $currency_buy $_rates_before = $currency_buy $disc_amt</font>";
    }

    //===============================
    //apply individual discounts
    for ($i = 0; $i < count($arr_individual_percentage); $i++) {
        $sponame = $arr_individual_percentage[$i]["SPO"];
        $disc_value = $arr_individual_percentage[$i]["DISCOUNT_PER"];

        $_rates_before = $rates;
        $disc_amt = round(($disc_value / 100) * $rates, 2);
        $rates -= $disc_amt;
        if ($rates < 0) {
            $rates = 0;
        }

        $arr_individual_workings[] = "<font color='#BB3C94'> - (<b>NON-CUMULATIVE</b> : $sponame = $currency_buy $rates)";
    }

    $workings_individual = implode("<br>", $arr_individual_workings);
    //===============================
    $_temp_workings .= $workings_room;

    if ($workings_non_room != "") {
        if ($_temp_workings != "") {
            $_temp_workings .= "<br>";
        }
        $_temp_workings .= $workings_non_room;
    }

    if ($workings_individual != "") {
        if ($_temp_workings != "") {
            $_temp_workings .= "<br>";
        }
        $_temp_workings .= $workings_individual;
    }

    $msg .= $_temp_workings;

    return;
}

function _rates_calculator_update_spo_counter($spo_id, $adult_children, &$arr_spo_summary_applied) {
    if (!isset($arr_spo_summary_applied[$spo_id])) {
        $arr_spo_summary_applied[$spo_id] = array("CHILDREN" => 0, "ADULT" => 0);
    }

    $arr_spo_summary_applied[$spo_id][$adult_children]++;
    return;
}

function _rates_calculator_get_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied) {
    if (!isset($arr_spo_summary_applied[$spo_id])) {
        return 0;
    } else {
        return $arr_spo_summary_applied[$spo_id][$adult_children];
    }


    return;
}

function _rates_calculator_apply_spo_discount_test_sharingown_children_age($adult_children, $pax_age, $disc_sharing_age_ranges, $disc_own_age_ranges, $arr_params) {
    //this test is applicable for children only
    //if child and sharing, make sure age in within $disc_sharing_age_ranges
    //if child and own, make sure age in within $disc_own_age_ranges

    if ($adult_children == "CHILDREN") {
        //determine if sharing or own
        //get the sharing_own status of the first child
        //first child is enough because, remember, cannot have both SHARING and OWN in the same grid
        $sharing_own = $arr_params["children"][0]["sharing_own"];
        $arr = null;

        if ($sharing_own == "SHARING") {
            $arr = $disc_sharing_age_ranges;
        } else if ($sharing_own == "OWN") {
            $arr = $disc_own_age_ranges;
        }

        if (is_null($arr)) {
            return false;
        }

        if (count($arr) == 0) {
            return true; //no conditions applicable
        }

        for ($j = 0; $j < count($arr); $j++) {
            $age_from = $arr[$j]["AGE_FROM"];
            $age_to = $arr[$j]["AGE_TO"];
            if ($age_from <= $pax_age && $pax_age <= $age_to) {
                return true;
            }
        }

        return false; //failed!
    }

    return true;
}

function _rates_calculator_apply_spo_discount_test_bride_groom($pax_bride_groom, $disc_bd_gm, $adult_children) {

    $disc_bd_gm = strtoupper(trim($disc_bd_gm));
    $pax_bride_groom = strtoupper($pax_bride_groom);

    if ($disc_bd_gm == "") {
        return true; //no bride groom filter check
    } else if ($adult_children == "CHILDREN" && $disc_bd_gm != "") {
        return false; //interested in adults only
    }

    if ($adult_children == "ADULT" && ($disc_bd_gm == "BOTH")) {
        return true;
    }

    if ($adult_children == "ADULT" && $pax_bride_groom == $disc_bd_gm) {
        return true;
    }

    return false;
}

function _rates_calculator_apply_spo_discount_test_dates($apply_to_dates, $this_date) {
    //if $apply_to_dates is not empty, then check if this_date is in it
    //test if dates check passed

    if (count($apply_to_dates) > 0) {
        //check if ALL in array
        //else check for filters
        if (in_array("ALL", $apply_to_dates)) {
            return true;
        } else {
            if (in_array($this_date, $apply_to_dates)) {
                return true;
            }
        }
    }

    return false;
}

function _rates_calculator_apply_spo_discount_test_pax_index($adult_children, $disc_max_ad,
        $disc_max_ad_category, $disc_max_ch,
        $disc_max_ch_category, $arr_spo_summary_applied,
        $spo_id) {
    //test if adult or children max applicable passed
    $flg_index_passed = true;
    if ($adult_children == "ADULT") {
        if ($disc_max_ad != "" && $disc_max_ad_category == "APPLICABLE") {
            $ad_count = _rates_calculator_get_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);
            if ($ad_count >= $disc_max_ad) {
                return false;
            }
        }
    } else if ($adult_children == "CHILDREN") {
        if ($disc_max_ch != "" && $disc_max_ch_category == "APPLICABLE") {
            $ch_count = _rates_calculator_get_spo_counter($spo_id, $adult_children, $arr_spo_summary_applied);
            if ($ch_count >= $disc_max_ch) {
                return false;
            }
        }
    }

    return true;
}

function _rates_calculator_apply_spo_discount_test_ad_ch($adult_children, $disc_ad_ch) {
    if ($disc_ad_ch == "" || $disc_ad_ch == "BOTH") {
        return true; //discount applicable to both adults and children
    }

    if ($adult_children == "ADULT" && $disc_ad_ch == "AD") {
        return true;
    }

    if ($adult_children == "CHILDREN" && $disc_ad_ch == "CH") {
        return true;
    }

    return false;
}

function _rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to) {
    if ($disc_ag_frm == -1 && $disc_ag_to == -1) {
        return true; //no age checks needed
    } else if ($disc_ag_frm == "" && $disc_ag_to == "") {
        return true; //no age checks needed
    } else if ($disc_ag_frm != -1 && $disc_ag_to != -1) {
        if ($disc_ag_frm <= $pax_age && $pax_age <= $disc_ag_to) {
            return true;
        }
    } else if ($disc_ag_frm != -1 && $disc_ag_to == -1) {
        if ($disc_ag_frm <= $pax_age) {
            return true;
        }
    } else if ($disc_ag_frm == -1 && $disc_ag_to != -1) {
        if ($pax_age <= $disc_ag_to) {
            return true;
        }
    }

    return false;
}

function _rates_calculator_apply_rates_eci_percentage(&$rates, $arr_eci, &$msg, $currency_buy) {

    //this will only apply if early check in is a percentage, then apply it to rates
    $workings = $arr_eci["WORKINGS"];
    $charge_type = $arr_eci["CHARGE_TYPE"];
    $charge_value = $arr_eci["CHARGE_VALUE"];

    if ($charge_type == "" && $workings == "" && $charge_value == "") {
        return;
    }

    //cater only for percentage charges

    if ($charge_type == "%D") {

        $discount_fees = 0;

        if ($charge_value > 0) {
            $discount_fees = round(($charge_value / 100) * $rates, 2);
        }

        $eci_fees = $rates - $discount_fees;
        if ($eci_fees < 0) {
            $eci_fees = 0;
        }

        $msg .= "<br> + <b>(</b>$workings:$currency_buy $rates - Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $discount_fees => $currency_buy $eci_fees<b>)</b>";
        $rates += $eci_fees;
    } else if ($charge_type == "%C") {
        $fees = 0;
        if ($charge_value > 0) {
            $fees = round(($charge_value / 100) * $rates, 2);
        }

        $msg .= "<br> + <b>(</b>$workings Charge <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees<b>)</b> ";
        $rates += $fees;
    }
}

function _rates_calculator_apply_rates_lco_percentage($arr_rates, $arr_lco, &$lco_applied, $currency_buy) {
    $lco_applied = false;

    $workings = $arr_lco["WORKINGS"];
    $charge_type = $arr_lco["CHARGE_TYPE"];
    $charge_value = $arr_lco["CHARGE_VALUE"];

    if ($charge_type == "" && $workings == "" && $charge_value == "") {
        return $arr_rates;
    }

    $lco_applied = true;

    for ($i = 0; $i < count($arr_rates); $i++) {
        //cater only for percentage charges
        $rates = $arr_rates[$i]["COSTINGS"];
        $msg = $arr_rates[$i]["MSG"];
        $fees = 0;

        if ($charge_type == "%D") {

            $discount_fees = 0;

            if ($charge_value > 0) {
                $discount_fees = round(($charge_value / 100) * $rates, 2);
            }

            $lco_fees = $rates - $discount_fees;

            if ($lco_fees < 0) {
                $lco_fees = 0;
            }

            $msg .= "<br> => ($workings: $currency_buy $rates - Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $discount_fees) = $currency_buy $lco_fees";
            $rates = $lco_fees;
        } else if ($charge_type == "%C") {
            $fees = 0;
            if ($charge_value > 0) {
                $fees = round(($charge_value / 100) * $rates, 2);
            }

            $msg .= "<br> => ($workings Charge <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
            $rates = $fees;
        }

        $arr_rates[$i]["COSTINGS"] = $rates;
        $arr_rates[$i]["MSG"] = $msg;
    }
    return $arr_rates;
}

function _rates_calculator_lookup_rates_calc_PPPN($arr, $arr_params, $arr_taxcomm, $con, $category) {
    $arr_items = array("ROOM", "ECI", "LCO");

    for ($i = 0; $i < count($arr); $i++) {
        $rates = $arr[$i]["COSTINGS"];
        $arr[$i]["COSTINGS"] = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, $rates, $arr_items, "PPPN");
        $arr[$i]["CATEGORY"] = $category;
    }
    return $arr;
}

function _rates_calculator_meal_supp_PPPN($arr, $arr_params, $arr_taxcomm, $con) {
    $arr_items = array("MEAL_SUPP");

    for ($i = 0; $i < count($arr); $i++) {
        $rates = $arr[$i]["COSTINGS"];
        $arr[$i]["COSTINGS"] = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, $rates, $arr_items, "PPPN");
        $arr[$i]["CATEGORY"] = "NON_ROOM";
    }
    return $arr;
}

function _rates_calculator_extra_meal_supp_PPPN($arr, $arr_params, $arr_taxcomm, $con) {
    $arr_items = array("MEAL_EXTRA_SUPP");

    for ($i = 0; $i < count($arr); $i++) {
        $rates = $arr[$i]["COSTINGS"];
        $arr[$i]["COSTINGS"] = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, $rates, $arr_items, "PPPN");
        $arr[$i]["CATEGORY"] = "NON_ROOM";
    }
    return $arr;
}

function _rates_calculator_sum_daily_total($arr, $arr_columns, $category, $arr_params, $arr_taxcomm, $con) {

    //calulate the daily costings for the night
    //apply the markup if needed for PNI
    $PN_PPN = $arr_params["TAX_COMMI_BASIS"];
    $arr_total = array();
    $arr_items = array();

    if ($category == "ROOM") {
        $arr_items = array("ROOM", "ECI", "LCO");
    } else if ($category == "NON_ROOM") {
        $arr_items = array("MEAL_EXTRA_SUPP", "MEAL_SUPP");
    }

    for ($c = 0; $c < count($arr_columns); $c++) {

        $arr_total[$c] = $arr_columns[$c];
        $arr_total[$c]["VALUE"] = 0; //override value
    }

    //======== sum up values for the night ================
    for ($i = 0; $i < count($arr); $i++) {
        //for that specific day
        if (isset($arr[$i]["CATEGORY"])) {
            if ($arr[$i]["CATEGORY"] == $category) {
                $arr_lines = $arr[$i]["COSTINGS"];

                for ($c = 0; $c < count($arr_lines); $c++) {

                    $arr_total[$c]["VALUE"] += $arr_lines[$c]["VALUE"];
                }
            }
        }
    }

    //=================================================
    //calculate the costings
    $rates = $arr_total[0]["VALUE"]; //index 0 = BUY PRICE
    $_arr_total = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, $rates, $arr_items, "PNI");

    //if PPPN then use $arr_total
    //else if PNI, use $_arr_total
    if (strpos($PN_PPN, "PPPN") !== false) {
        return array("MSG" => "$category TOTAL", "COSTINGS" => $arr_total, "CATEGORY" => "$category TOTAL");
    } else {
        //PNI
        return array("MSG" => "$category TOTAL", "COSTINGS" => $_arr_total, "CATEGORY" => "$category TOTAL");
    }
}

function _rates_calculator_calc_free_nights($num_nights, $arr_stays, $is_cumulative) {
    //calculates the number of free nights based on params $arr_stays and $is_cumulative
    //$arr_stays is an array of free nights rules [0]["STAYS"]
    //                                               ["PAYS]
    //$num_nights is the number of nights in the reservation

    if (count($arr_stays) == 0) {
        return 0;
    }

    //==========================================================================
    //NON CUMULATIVE
    if ($is_cumulative == 0) {
        $idx = 1;
        $stay = 0;
        $pay = 0;
        while (true) {
            $arr_stays_pays = _rates_calculator_calc_free_nights_lookup_night($arr_stays, $idx);
            if ($arr_stays_pays["STAYS"] == -1 && $arr_stays_pays["PAYS"] == -1) {
                //not found
                $stay++;
                $pay++;
            } else {
                $stay = $arr_stays_pays["STAYS"];
                $pay = $arr_stays_pays["PAYS"];
            }

            if ($idx == $num_nights) {
                return array("STAYS" => $stay, "PAYS" => $pay, "FREE" => ($stay - $pay)); //free nights
            }

            $idx++;
        }
    }
    //==========================================================================
    //CUMULATIVE
    else {
        $idx = count($arr_stays) - 1;
        while ($idx >= 0) {
            $arr_stays_pays = $arr_stays[$idx];
            if ($arr_stays_pays["STAYS"] == $num_nights) {
                return array("STAYS" => $arr_stays_pays["STAYS"], "PAYS" => $arr_stays_pays["PAYS"], "FREE" => ($arr_stays_pays["STAYS"] - $arr_stays_pays["PAYS"])); //free nights
            } else if ($arr_stays_pays["STAYS"] < $num_nights) {
                if ($num_nights % $arr_stays_pays["STAYS"] == 0) {
                    //no remainder: multiple
                    $factor = $num_nights / $arr_stays_pays["STAYS"];
                    $pays = $arr_stays_pays["PAYS"] * $factor;
                    return array("STAYS" => $num_nights, "PAYS" => $pays, "FREE" => ($num_nights - $pays)); //free nights
                }
            }

            $idx--;
        }

        //===============
        //if we are here, means that no multiples found in cumulative version
        //find free nights in non cumulative version now
        return _rates_calculator_calc_free_nights($num_nights, $arr_stays, 0);
    }
}

function _rates_calculator_calc_free_nights_lookup_night($arr_stays, $x) {
    $stays = -1;
    $pays = -1;

    for ($i = 0; $i < count($arr_stays); $i++) {
        if ($x == $arr_stays[$i]["STAYS"]) {
            return $arr_stays[$i];
        }
    }

    return array("STAYS" => $stays, "PAYS" => $pays);
}

function _rates_calculator_validate_reservation($arr_params, $this_date, $con, &$arr_daily_idx) {

    //========================================================
    //TEST 2: MINIMUM STAY 
    $flg_min_test = true;
    $min_test_msg = _rates_calculator_min_stay_nights($arr_params, $this_date, $flg_min_test);
    if (!$flg_min_test) {
        $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 2</font>: $min_test_msg",
            "COSTINGS" => array());
        $arr_daily_idx["STATUS"] = "MIN_STAY_FAIL";
        return;
    }

    $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 2</font>: MIN STAY $min_test_msg",
        "COSTINGS" => array());

    //========================================================
    //========================================================
    //TEST 3: CHILDREN AGES
    $children_age_test = _rates_calculator_test_children_ages($arr_params, $con);
    if ($children_age_test != "OK") {
        $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 3</font>: CHILDREN AGES: $children_age_test",
            "COSTINGS" => array());
        $arr_daily_idx["STATUS"] = "CHILDREN_AGE_FAIL";
        return;
    }

    $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 3</font>: CHILDREN AGES",
        "COSTINGS" => array());
    //========================================================
    //========================================================
    //TEST 4: ADULT AND CHILDREN CAPACITY
    $capacity_test_adch = _rates_calculator_adch_capacity($arr_params, $this_date);
    if ($capacity_test_adch["MSG"] != "OK") {
        $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 4</font>: CAPACITY TEST ADULT + CHILDREN SHARING: " . $capacity_test_adch["MSG"],
            "COSTINGS" => array());
        $arr_daily_idx["STATUS"] = "CAPACITY_FAIL";

        return;
    }

    $test_4_index = $capacity_test_adch["INDEX"];

    $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 4</font>: CAPACITY ADULT + CHILDREN SHARING. COMBINATION INDEX: <b>" . $test_4_index . "</b>",
        "COMBINATION_INDEX" => $test_4_index,
        "COSTINGS" => array());
    //========================================================
    //========================================================
    //TEST 5: CHILDREN IN OWN ROOM
    $capacity_test_ch_ownroom = _rates_calculator_ch_own_capacity($arr_params, $this_date);
    if ($capacity_test_ch_ownroom["MSG"] != "OK") {
        $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 5</font>: CAPACITY TEST CHILDREN OWN ROOM: " . $capacity_test_ch_ownroom["MSG"],
            "COSTINGS" => array());
        $arr_daily_idx["STATUS"] = "CHILDREN_OWN_ROOM_FAIL";

        return;
    }

    $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 5</font>: CAPACITY CHILDREN OWN ROOM. COMBINATION INDEX: <b>" . $capacity_test_ch_ownroom["INDEX"] . "</b>",
        "COMBINATION_INDEX" => $capacity_test_ch_ownroom["INDEX"],
        "COSTINGS" => array());
    //========================================================
    //all ok finally
    return;
}

function rates_calculator_get_nights_with_lowest_rates($arr_daily_free_nights, $free_nights_num_nights, $arr_params, $arr_possible_dates) {
    $num_nights = $arr_params["num_nights"];

    $arr_night_total = array();


    for ($i = 0; $i < $num_nights; $i++) {
        $night_total = 0;
        $arr_cost_work = $arr_daily_free_nights[$i]["COSTINGS_WORKINGS"];
        $the_date = $arr_daily_free_nights[$i]["DATE"];

        for ($j = 0; $j < count($arr_cost_work); $j++) {
            $arr_costings = $arr_cost_work[$j]["COSTINGS"];
            for ($k = 0; $k < count($arr_costings); $k++) {
                $caption = $arr_costings[$k]["CAPTION"];
                $value = $arr_costings[$k]["VALUE"];
                if ($caption == "BUY PRICE") {
                    $night_total += $value;
                }
            }
        }

        $arr_night_total[] = array("NIGHT_INDEX" => $i, "NIGHT_VALUE" => $night_total, "THE_DATE" => $the_date);
    }


    $night_value = array();
    $night_idx = array();
    foreach ($arr_night_total as $key => $row) {
        $night_value[$key] = $row["NIGHT_VALUE"];
        $night_idx[$key] = $row["NIGHT_INDEX"];
    }

    // Sort the data with $night_value ascending
    array_multisort($night_value, SORT_ASC, $night_idx, SORT_ASC, $arr_night_total);


    //now get the first $free_nights_num_nights index of nights
    $arr = array();
    $idx = 0;
    while ($idx < count($arr_night_total)) {

        $this_date = $arr_night_total[$idx]["THE_DATE"];
        $idx = $arr_night_total[$idx]["NIGHT_INDEX"];

        if (_rates_calculator_is_date_inarray($this_date, $arr_possible_dates) && $free_nights_num_nights > 0) {
            $arr[] = $this_date;
            $free_nights_num_nights--;
        }

        $idx++;
    }

    return $arr;
}

function _rates_calculator_is_date_inarray($this_date, $arr_possible_dates) {
    for ($i = 0; $i < count($arr_possible_dates); $i++) {
        if ($arr_possible_dates[$i] == $this_date) {
            return true;
        }
    }

    return false;
}

function _rates_calculator_process_SPO_free_nights(&$arr_spo_discounts, $arr_days, $arr_params, $con) {
    //HAVE WE GOT FREE NIGHTS SPO?
    $arr_dates_free_nights = array();
    $free_nights_num_nights = 0;
    $spo_free_nights_start_end = "START";
    $spo_FN_index = -1;

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $arr_item = $arr_spo_discounts[$i];
        if ($arr_item["SPO_TYPE"] == "FREE_NIGHTS") {
            $spo_FN_index = $i;
            $free_nights_num_nights = $arr_item["FREE_NIGHTS"];
            $spo_free_nights_start_end = $arr_item["FREE_NIGHTS_START_END"];
        }
    }

    if ($spo_FN_index == -1 || $free_nights_num_nights == "") {
        return; //NO Free Nights SPO to be taken into consideration
    }


    //==================================================================
    //okay, got a Free Night SPO, need to know which nights will be free
    //get array of all possible nights
    $arr_possible_dates = $arr_spo_discounts[$spo_FN_index]["APPLY_TO_DATES"];

    if ($free_nights_num_nights > 0) {
        if ($spo_free_nights_start_end == "START") {




            usort($arr_possible_dates, '_rates_calculator_sort_date_compare_asc');

            //get first n nights based on date
            //then place nights as first n nights
            for ($idx = 0; $idx < count($arr_days["DAILY"]); $idx++) {
                $this_date = $arr_days["DAILY"][$idx]["DATE"];

                if (_rates_calculator_is_date_inarray($this_date, $arr_possible_dates) && $free_nights_num_nights > 0) {
                    $arr_dates_free_nights[] = $this_date;
                    $free_nights_num_nights--;
                }
            }
        } else if ($spo_free_nights_start_end == "END") {



            usort($arr_possible_dates, '_rates_calculator_sort_date_compare_desc');

            //last n nights based on date
            //then place nights as last n nights

            $idx = count($arr_days["DAILY"]) - 1;
            while ($idx >= 0) {
                $this_date = $arr_days["DAILY"][$idx]["DATE"];

                if (_rates_calculator_is_date_inarray($this_date, $arr_possible_dates) && $free_nights_num_nights > 0) {
                    $arr_dates_free_nights[] = $this_date;
                    $free_nights_num_nights--;
                }
                $idx--;
            }
        } else if ($spo_free_nights_start_end == "LOWEST") {
            //run the rates_calculator_CORE_lookup with ROOM only to get 
            //the night with the lowest rate
            $arr_daily_free_nights = array();

            for ($idx = 0; $idx < count($arr_days["DAILY"]); $idx++) {
                $this_date = $arr_days["DAILY"][$idx]["DATE"];
                $arr_contract_ids = $arr_days["DAILY"][$idx]["CONTRACT_ID"];

                //free nights applicable to ROOM rates only. Skip Total, LCO and DISCOUNTS
                $arr_lookup_mode = array("TOTAL" => false, "ROOM_ONLY" => true, "LCO" => false, "DISCOUNTS" => false);

                //use SPO Flat Rate or normal Contract Rates?
                $arr_normal_or_flatrates = _rates_calculator_decide_contract_or_spo_flatrate($arr_params, $this_date);
                $arr_params["flat_rate_spo_apply"] = $arr_normal_or_flatrates;

                //proceed with rates lookup
                rates_calculator_CORE_lookup($arr_daily_free_nights, $idx, $this_date, $arr_contract_ids, $arr_params, $con, $arr_lookup_mode);
            }

            //get the nights (index and date) with the lowest rates
            $arr_dates_free_nights = rates_calculator_get_nights_with_lowest_rates($arr_daily_free_nights, $free_nights_num_nights, $arr_params, $arr_possible_dates);
        }
    }

    //place the array of free dates into the Free Night SPO
    $arr_spo_discounts[$spo_FN_index]["APPLY_TO_DATES"] = $arr_dates_free_nights; //recall nights Free
}

function _rates_calculator_decide_contract_or_spo_flatrate($arr_params, $this_date) {

    //check if for this date, there is a flat rate SPO to be applied

    $arr_spo_discounts = $arr_params["spo_discounts_array"];

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $apply_to_dates = $discount_item["APPLY_TO_DATES"]; //any array of date filters?


        if ($spo_type == "FLAT_RATES") {
            for ($x = 0; $x < count($apply_to_dates); $x++) {
                if ($apply_to_dates[$x] == $this_date) {

                    //return SPO Flat Rates
                    return array("RATES" => $discount_item["FLAT_RATE_CAPACITY_ARRAY"],
                        "SPO_ID" => $spo_id, "SPO_NAME" => $spo_name, "SPO_TYPE" => $spo_type,
                        "COMMENTS" => "<font color='#BB3C94'><b>SPO </b>[ID:$spo_id $spo_type - $spo_name]</font><br>",
                        "APPLY_SPO_FLAT_RATE" => true);
                }
            }
        }
    }

    return array("RATES" => "", "COMMENTS" => "", "APPLY_SPO_FLAT_RATE" => false); //return normal contract rates
}

function _rates_calculator_spo_remove_duplicates($arr_spoids, $arr_spos, $types) {
    //for each spoid in $arr_spoids,
    //  check the type of the spo in $arr_spos
    //  if type of spo is of type to be removed, then 
    //      remove a duplicate of types found in $types
    $arr_final_spoids = array();

    $arr_type_counters = array();
    for ($i = 0; $i < count($arr_spoids); $i++) {

        $spoid = $arr_spoids[$i];
        $spo_rec = _rates_calculator_spo_lookup_spo_record_by_id($spoid, $arr_spos);

        $spo_rw = $spo_rec["SPO_RW"];
        $spotemplate = $spo_rw["template"];

        if (isset($arr_type_counters[$spotemplate])) {
            $arr_type_counters[$spotemplate]++;

            //check if need to remove
            if (!in_array($spotemplate, $types)) {
                //can add freely
                $arr_final_spoids[] = $spoid;
            }
        } else {
            $arr_type_counters[$spotemplate] = 0;
            $arr_final_spoids[] = $spoid;
        }
    }


    return $arr_final_spoids;
}

function _rates_calculator_spo_search($arr_params, $con) {
    try {

        //will return a list of valid spos and a list of invalid spos with reasons why they failed

        $arr_spos = array();
        $arr_invalid_spos = array();

        //TODO: 
        //1. MINIMUM STAY OVERWRITING

        $arr_test_spos = _rates_calculator_getSPO_first_test($arr_params, $con);
        $arr_spos = $arr_test_spos["VALID_SPOS"];
        $arr_invalid_spos = $arr_test_spos["INVALID_SPOS"];

        if (count($arr_spos) == 0) {
            //there are no SPOs for that booking
            return array("OUTCOME" => "OK", "ARR_SPOS" => $arr_spos, "ARR_INVALID_SPOS" => $arr_invalid_spos);
        }


        //=======================================================================================
        //=======================================================================================
        //there is at least one SPO! continue with 2nd check

        $arr_test_spos = _rates_calculator_getSPO_second_test($arr_params, $con, $arr_spos);
        $arr_spos = $arr_test_spos["VALID_SPOS"];
        $arr_invalid_spos = array_merge($arr_invalid_spos, $arr_test_spos["INVALID_SPOS"]);

        if (count($arr_spos) == 0) {
            //there are no SPOs followng 2nd test
            return array("OUTCOME" => "OK", "ARR_SPOS" => $arr_spos, "ARR_INVALID_SPOS" => $arr_invalid_spos);
        }


        return array("OUTCOME" => "OK", "ARR_SPOS" => $arr_spos, "ARR_INVALID_SPOS" => $arr_invalid_spos);
    } catch (Exception $ex) {
        return array("OUTCOME" => "SPO_RATES_CALCULATOR: " . $ex->getMessage());
    }
}

function _rates_calculator_create_spos($arr_single_spos, $con, $arr_params) {

    //create an array of spo objects based on the array $arr_single_spos
    //family_offer <-- careful, may need to further split by age groups
    //honeymoon <-- careful, may need to further split if bride and groom different basis
    //wedding_anniversary <-- careful, may need to further split if bride and groom different basis
    //wedding_party  <-- careful, may need to further split if bride and groom different basis

    $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
    $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
    $num_nights = _rates_calculator_get_numnights($checkin_date, $checkout_date);

    $arr_final_single_spos = array();

    for ($i = 0; $i < count($arr_single_spos); $i++) {
        $dates = $arr_single_spos[$i]["DATES"];
        $spo_rw = $arr_single_spos[$i]["SPO_RW"];
        $template = $spo_rw["template"];
        $id = $spo_rw["id"];
        $sponame = $spo_rw["sponame"];
        $spocode = $spo_rw["spocode"];
        $max_adult = $spo_rw["adult_max"];
        $max_adult_category = $spo_rw["adult_max_category"];
        $max_children = $spo_rw["children_max"];
        $max_children_category = $spo_rw["children_max_category"];
        $iscumulative = $spo_rw["iscumulative"];


        //====================================================================
        if ($template == "meals_upgrade") {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_UPGRADES", "",
                    "", -1, -1, "", 0, $dates, 0, "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);
            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "free_upgrade") {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_UPGRADES", "", "", -1, -1, "",
                    0, $dates, 0, "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "free_nights") {
            $spo_free_nights_start_end = strtoupper($spo_rw["free_nights_placed_at"]);
            $free_nights_num_nights = _rates_calculator_spo_calculate_num_free_nights($spo_rw, $con, $num_nights);

            //$dates : to be reordered later on depending on place start,place end, place lowest
            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_NIGHTS", "BOTH",
                    "", -1, -1, "%ROOM", 100, $dates, $free_nights_num_nights,
                    $spo_free_nights_start_end, array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);
            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "flat_rate") {

            $arr_fr = _spo_loadspo($con, $id, $spo_rw["hotel_fk"]);

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FLAT_RATES", "",
                    "", -1, -1, "", 0, $dates, "",
                    "", $arr_fr["FLAT_RATES_CAPACITY"],
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "discount") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value, $dates, "",
                    "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "early_booking") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value, $dates, "",
                    "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "family_offer") {
            //CAREFUL
            //WILL NEED TO SPLIT IF MORE THAN ON AGE GROUPS
            $arr_family_offer = _rates_calculator_create_spos_family_offer($arr_single_spos[$i], $con);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_family_offer);
        }
        //====================================================================
        else if ($template == "honeymoon") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATELY
            $arr_wedding_offer = _rates_calculator_create_spos_wedding_offer($arr_single_spos[$i], $con);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
        //====================================================================
        else if ($template == "long_stay") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];


            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value,
                    $dates, "", "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "senior_offer") {
            $discount_basis = $spo_rw["senior_discount_basis"];
            $discount_value = $spo_rw["senior_discount_value"];

            $senior_guests_aged_from = $spo_rw["senior_guests_aged_from"];
            if ($senior_guests_aged_from == "") {
                $senior_guests_aged_from = -1;
            }

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                    "", $senior_guests_aged_from, -1, $discount_basis,
                    $discount_value, $dates, "",
                    "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "wedding_anniversary") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATELY
            $arr_wedding_offer = _rates_calculator_create_spos_wedding_offer($arr_single_spos[$i], $con);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
        //====================================================================
        else if ($template == "wedding_party") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATELY
            $arr_wedding_offer = _rates_calculator_create_spos_wedding_offer($arr_single_spos[$i], $con);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
    }

    return $arr_final_single_spos;
}

function _rates_calculator_create_spos_wedding_offer($the_wedding_spo, $con) {
    $arr_spo_return = array();

    $dates = $the_wedding_spo["DATES"];
    $rw = $the_wedding_spo["SPO_RW"];
    $template = $rw["template"];
    $id = $rw["id"];
    $sponame = $rw["sponame"];
    $spocode = $rw["spocode"];
    $iscumulative = $rw["iscumulative"];

    $max_adult = $rw["adult_max"];
    $max_adult_category = $rw["adult_max_category"];
    $max_children = $rw["children_max"];
    $max_children_category = $rw["children_max_category"];

    $wedding_apply_discount_both = $rw["wedding_apply_discount_both"];
    $wedding_apply_discount_bride = $rw["wedding_apply_discount_bride"];
    $wedding_apply_discount_groom = $rw["wedding_apply_discount_groom"];

    $wedding_apply_discount_both_basis = $rw["wedding_apply_discount_both_basis"];
    $wedding_apply_discount_bride_basis = $rw["wedding_apply_discount_bride_basis"];
    $wedding_apply_discount_groom_basis = $rw["wedding_apply_discount_groom_basis"];

    $wedding_apply_discount_both_value = $rw["wedding_apply_discount_both_value"];
    $wedding_apply_discount_bride_value = $rw["wedding_apply_discount_bride_value"];
    $wedding_apply_discount_groom_value = $rw["wedding_apply_discount_groom_value"];

    if ($wedding_apply_discount_both == 1) {

        $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                "BOTH", -1, -1, $wedding_apply_discount_both_basis,
                $wedding_apply_discount_both_value, $dates, "",
                "", array(),
                $max_adult, $max_adult_category,
                $max_children, $max_children_category, $iscumulative);
        $arr_spo_return[] = $s;
    } else {
        //will need to split into two separate offers

        if ($wedding_apply_discount_bride == 1) {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                    "BRIDE", -1, -1, $wedding_apply_discount_bride_basis,
                    $wedding_apply_discount_bride_value, $dates, "",
                    "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_spo_return[] = $s;
        }
        if ($wedding_apply_discount_groom == 1) {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                    "GROOM", -1, -1, $wedding_apply_discount_groom_basis,
                    $wedding_apply_discount_groom_value, $dates, "",
                    "", array(),
                    $max_adult, $max_adult_category,
                    $max_children, $max_children_category, $iscumulative);

            $arr_spo_return[] = $s;
        }
    }

    return $arr_spo_return;
}

function _rates_calculator_create_spos_family_offer($the_family_spo, $con) {
    $arr_spo_return = array();

    $dates = $the_family_spo["DATES"];
    $rw = $the_family_spo["SPO_RW"];
    $template = $rw["template"];
    $id = $rw["id"];
    $sponame = $rw["sponame"];
    $spocode = $rw["spocode"];
    $iscumulative = $rw["iscumulative"];

    $max_adult = $rw["adult_max"];
    $max_adult_category = $rw["adult_max_category"];
    $max_children = $rw["children_max"];
    $max_children_category = $rw["children_max_category"];

    $sql = "select * from 
            tblspecial_offer_familyoffer_childage_discount 
            where spo_fk = :spoid";

    $query = $con->prepare($sql);
    $query->execute(array(":spoid" => $id));
    while ($rwch = $query->fetch(PDO::FETCH_ASSOC)) {

        $age_from = $rwch["child_age_from"];
        $age_to = $rwch["child_age_to"];
        $discount_basis = $rwch["discount_percentage_value"];
        $discount_value = $rwch["discount_value"];

        $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "CH",
                "", $age_from, $age_to, $discount_basis,
                $discount_value, $dates, "", "", array(),
                $max_adult, $max_adult_category,
                $max_children, $max_children_category, $iscumulative);

        $arr_spo_return[] = $s;
    }


    return $arr_spo_return;
}

function _rates_calculator_spo_get_combinable_links($con, $str_ids, $arr_params) {
    //returns an array of combinable SPOs
    $arr = array();
    $sql = "select sol.id, sol.description, 
            group_concat(sols.spofk SEPARATOR ',') as spoids
            from tblspecial_offer_link_spos sols
            inner join tblspecial_offer_link sol on sols.linkfk = sol.id
            where sol.deleted = 0 and sol.hotel_fk = :hotelfk and sol.active=1
            and sols.spofk in ($str_ids)
            group by sol.id, sol.description;";

    $hotel = $arr_params["hotel"];

    $query = $con->prepare($sql);
    $query->execute(array(":hotelfk" => $hotel));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $arr_spo = explode(",", $rw["spoids"]);
        $arr[] = array("LINK_ID" => $rw["id"], "DESCRIPTION" => $rw["description"], "ARR_SPO_IDS" => $arr_spo);
    }
    return $arr;
}

function _rates_calculator_spo_generate_spo_obj_from_choice($arr_choices, $spo_chosen, $con,
        $arr_params, $arr_spo_records) {

    $arr_spo_records_selected = array();
    $spo_records_selected_idx = 0;

    //1. create an array of spo records
    //choice can be either SPO_spoid or LINK_linkid

    $arr_choice_details = explode("_", $spo_chosen);
    if ($arr_choice_details[0] == "LINK") {

        //LINKED SPOs
        for ($i = 0; $i < count($arr_choices); $i++) {
            if ($arr_choices[$i]["SINGLE_COMBINED"] == "COMBINED" &&
                    $arr_choices[$i]["LINKID_SPOID"] == $spo_chosen) {

                $arr_link_spos = $arr_choices[$i]["ARR_SPOS"];
                for ($j = 0; $j < count($arr_link_spos); $j++) {
                    $_spoid = $arr_link_spos[$j]["SPOID"];
                    $_iscumulative = $arr_link_spos[$j]["ISCUMULATIVE"];

                    //add all spos belonging to that Link to the array $arr_spo_records_selected
                    $arr_spo_records_selected[$spo_records_selected_idx] = _rates_calculator_spo_lookup_spo_record_by_id($_spoid, $arr_spo_records);

                    //push cumulative attribute to that spo
                    $arr_spo_records_selected[$spo_records_selected_idx]["SPO_RW"]["iscumulative"] = $_iscumulative;

                    $spo_records_selected_idx++;
                }
            }
        }
    } else {
        //SINGLE SPO
        $spoid = $arr_choice_details[1]; //get the spoid
        //add the selected spo to the array $arr_spo_records_selected
        $arr_spo_records_selected[$spo_records_selected_idx] = _rates_calculator_spo_lookup_spo_record_by_id($spoid, $arr_spo_records);

        //push cumulative attribute to that spo locked to 0 by default
        $arr_spo_records_selected[$spo_records_selected_idx]["SPO_RW"]["iscumulative"] = 0;

        $spo_records_selected_idx++;
    }

    //2. convert SPO records in objects  
    $arr_spo_discounts = _rates_calculator_create_spos($arr_spo_records_selected, $con, $arr_params);

    return $arr_spo_discounts;
}

function _rates_calculator_spo_generate_choices($arr_spos, $con, $arr_params) {

    //will create a series of choices possible from the list of valid spos provided
    //will also return an array of warnings generated while created combined spos (if any)

    $arr_choices = array();
    $arr_warnings = array();

    if (count($arr_spos) == 0) {
        return array("CHOICES" => $arr_choices, "WARNINGS" => $arr_warnings);
    }

    //==========================================================================================
    //==========================================================================================
    //1. get list of spos ids comma separated

    $str_ids = "";
    $first = true;
    for ($i = 0; $i < count($arr_spos); $i++) {
        $spo_rw = $arr_spos[$i]["SPO_RW"];
        $spoid = $spo_rw["id"];
        if (!$first) {
            $str_ids .= ",";
        }
        $str_ids .= $spoid;
        $first = false;
    }

    //==========================================================================================
    //==========================================================================================
    //2. get list of spo_links for these spos from id above
    $arr_links = array();
    $arr_links = _rates_calculator_spo_get_combinable_links($con, $str_ids, $arr_params);

    //3. for each link in arrlinks, check if there have been any SPOs that have been skipped
    //if yes, then flag warning

    for ($i = 0; $i < count($arr_links); $i++) {

        $linkid = $arr_links[$i]["LINK_ID"];
        $arr_spoids = $arr_links[$i]["ARR_SPO_IDS"];
        $link_description = $arr_links[$i]["DESCRIPTION"];


        //======================================================================
        // must have at most one 1 free night spo in the list. 
        //other free nights spo are discarded
        $types = array("free_nights");
        $arr_spoids = _rates_calculator_spo_remove_duplicates($arr_spoids, $arr_spos, $types);
        //======================================================================

        $outcome = _rates_calculator_spo_validate_combinable_link($con, $linkid, $arr_spoids);
        if ($outcome != "OK") {
            //flag any error if spo missing from the linking
            $arr_warnings[] = $outcome;
        }

        //array of spos for that link
        $arr_spos_of_link = array();
        for ($j = 0; $j < count($arr_spoids); $j++) {

            $spoid = $arr_spoids[$j];
            $is_cumulative = _rates_calculator_spo_determine_cumulative($con, $linkid, $spoid);

            $spo_rec = _rates_calculator_spo_lookup_spo_record_by_id($spoid, $arr_spos);
            $spo_rw = $spo_rec["SPO_RW"];
            $arr_spos_of_link[] = array("SPOID" => $spoid, "NAME" => $spo_rw["sponame"], "ISCUMULATIVE" => $is_cumulative);
        }
        //4. add link to array of choices
        $arr_choices[] = array("SINGLE_COMBINED" => "COMBINED",
            "DESCRIPTION" => $link_description,
            "LINKID_SPOID" => "LINK_$linkid",
            "ARR_SPOS" => $arr_spos_of_link);
    }

    //get spos that are not in any links and then add them up as separate choices
    $arr_spo_individual_choices = array();

    for ($i = 0; $i < count($arr_spos); $i++) {
        $spoid = $arr_spos[$i]["SPO_RW"]["id"];

        //search in link choices first
        $flg_found = false;
        for ($j = 0; $j < count($arr_choices); $j++) {
            $arr_link_spos = $arr_choices[$j]["ARR_SPOS"];
            for ($k = 0; $k < count($arr_link_spos); $k++) {
                if ($arr_link_spos[$k]["SPOID"] == $spoid) {
                    $flg_found = true;
                }
            }
        }

        if (!$flg_found) {
            //this is a SPO that does not belong to any link! add it up
            $spo_rec = _rates_calculator_spo_lookup_spo_record_by_id($spoid, $arr_spos);
            $spo_rw = $spo_rec["SPO_RW"];

            $arr_spo_individual_choices[] = array("SINGLE_COMBINED" => "SINGLE",
                "DESCRIPTION" => $spo_rw["sponame"],
                "LINKID_SPOID" => "SPO_$spoid",
                "ARR_SPOS" => array(array("SPOID" => $spoid, "NAME" => $spo_rw["sponame"], "ISCUMULATIVE" => 0)));
        }
    }

    $arr_choices = array_merge($arr_spo_individual_choices, $arr_choices);

    $arr_choices[] = array("SINGLE_COMBINED" => "NONE",
        "DESCRIPTION" => "NO OFFER",
        "LINKID_SPOID" => "NONE",
        "ARR_SPOS" => array());


    return array("CHOICES" => $arr_choices, "WARNINGS" => $arr_warnings);
}

function _rates_calculator_spo_determine_cumulative($con, $linkid, $spoid) {
    //returns 1/0 if spo attached to link is cumulative or not
    $sql = "select * from tblspecial_offer_link_spos
            where linkfk = :linkid and spofk = :spoid";
    $query = $con->prepare($sql);
    $query->execute(array(":linkid" => $linkid, ":spoid" => $spoid));
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return $rw["cumulative"];
    }

    return 0;
}

function _rates_calculator_spo_validate_combinable_link($con, $linkid, $arr_listed_spos) {
    //$spoids is comma separated
    $outcome = "";

    //get all spos that are attached to the link
    //check if each spo id in $arr_listed_spos
    //if not flag error message

    $sql = "select sol.description, so.sponame, so.spocode, so.template, so.id
            from tblspecial_offer_link sol
            inner join tblspecial_offer_link_spos sols on sol.id = sols.linkfk
            inner join tblspecial_offer so on sols.spofk = so.id
            where sol.id = :linkid and so.deleted = 0 and so.active_external = 1";

    $query = $con->prepare($sql);
    $query->execute(array(":linkid" => $linkid));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $spo_to_search = $rw["id"];
        if (!in_array($spo_to_search, $arr_listed_spos)) {
            $outcome = (trim($outcome) == "" ? $outcome : $outcome . "<br>");
            $outcome .= "INVALID COMBINABLE SPOS: MISSING SPO '" . $rw["spocode"] . " - " . $rw["sponame"] . "' IN THE LINK '" . $rw["description"] . "'";
        }
    }


    if (trim($outcome) == "") {
        return "OK";
    } else {
        return $outcome;
    }
}

function _rates_calculator_spo_lookup_spo_record_by_id($id, $arr_spos) {
    //searches for a SPO object from an array of SPO objects
    //returns object found, null otherwise

    for ($i = 0; $i < count($arr_spos); $i++) {
        $spo_rw = $arr_spos[$i]["SPO_RW"];
        $spoid = $spo_rw["id"];
        if ($spoid == $id) {
            return $arr_spos[$i];
        }
    }
    return null;
}

function _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates) {
    //return true if spo nights within min stay rules 
    //return false otherwise

    if ($spo_minstay_priority == "NONE") {
        //no need to worry
        return "OK";
    }

    $num_spo_nights = count($arr_dates);

    if ($spo_minstay_from != "" && $spo_minstay_to != "") {
        if ($spo_minstay_from <= $num_spo_nights && $num_spo_nights <= $spo_minstay_to) {
            return "OK";
        } else {
            return " $num_spo_nights NIGHT(S) SHOULD BE BETWEEN SPO $spo_minstay_from - $spo_minstay_to NIGHTS";
        }
    } else if ($spo_minstay_from == "" && $spo_minstay_to != "") {
        if ($num_spo_nights <= $spo_minstay_to) {
            return "OK";
        } else {
            return " $num_spo_nights NIGHT(S) SHOULD BE &le; SPO $spo_minstay_to NIGHTS";
        }
    } else if ($spo_minstay_from != "" && $spo_minstay_to == "") {
        if ($spo_minstay_from <= $num_spo_nights) {
            return "OK";
        } else {
            return " $num_spo_nights NIGHT(S) SHOULD BE &ge; SPO $spo_minstay_from NIGHTS";
        }
    }

    return false;
}

function _rates_calculator_getSPO_second_test($arr_params, $con, $arr_spos) {

    $arr_final_spos = array();
    $arr_invalid_spos = array();

    for ($i = 0; $i < count($arr_spos); $i++) {
        $spo_rw = $arr_spos[$i]["SPO_RW"];
        $spoid = $spo_rw["id"];
        $sponame = $spo_rw["sponame"];
        $spotemplate = $spo_rw["template"];
        $spo_minstay_from = $spo_rw["min_stay_from"];
        $spo_minstay_to = $spo_rw["min_stay_to"];
        $spo_minstay_priority = $spo_rw["min_stay_priority"];

        $arr_dates = $arr_spos[$i]["DATES"];

        $flg_passed = false;

        //=======================================================
        if ($spotemplate == "discount") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED: $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "early_booking") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "family_offer") {
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_applicable_on_room = _rates_calculator_validate_SPO_family_offer_applicable_room($arr_params, $spo_rw["family_offer_room_applicable"]);

            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_num_night == "OK") {
                if ($flg_applicable_on_room) {
                    if ($flg_adults_check == "OK") {
                        if ($flg_children_check == "OK") {
                            $arr_final_spos[] = $arr_spos[$i];
                        } else {
                            $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                        }
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : APPLICABLE ON ROOM FAILED";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_num_night";
            }
        }
        //=======================================================
        else if ($spotemplate == "flat_rate") {
            $arr_dates = _rates_calculator_validate_SPO_flat_rate_grpvalidity($spoid, $arr_dates, $con);
            $arr_dates = _rates_calculator_validate_SPO_flat_rate_validate_capacity($spoid, $arr_params, $arr_dates, $con);
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if (count($arr_dates) > 0) {
                if ($flg_num_night == "OK") {
                    if ($flg_adults_check == "OK") {
                        if ($flg_children_check == "OK") {
                            $arr_final_spos[] = $arr_spos[$i];
                        } else {
                            $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                        }
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_num_night";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : GROUP DATE VALIDITY FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "free_nights") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "free_upgrade") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "honeymoon") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "long_stay") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED . $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "meals_upgrade") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "senior_offer") {
            $flg_min_guests = _rates_calculator_validate_SPO_min_max_guests($arr_params, $spo_rw["senior_min_guests"], "");
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if ($flg_num_night == "OK") {
                if ($flg_min_guests) {
                    if ($flg_adults_check == "OK") {
                        if ($flg_children_check == "OK") {
                            $arr_final_spos[] = $arr_spos[$i];
                        } else {
                            $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                        }
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN GUESTS FAILED";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_num_night";
            }
        }
        //=======================================================
        else if ($spotemplate == "wedding_anniversary") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);

            if ($flg_passed == "OK") {
                if ($flg_adults_check == "OK") {
                    if ($flg_children_check == "OK") {
                        $arr_final_spos[] = $arr_spos[$i];
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_passed";
            }
        }
        //=======================================================
        else if ($spotemplate == "wedding_party") {

            $flg_min_guests = _rates_calculator_validate_SPO_min_max_guests($arr_params, $spo_rw["wedding_min_guests"], $spo_rw["wedding_max_guests"]);
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_adults_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["adults"], $spo_rw["adult_min"], $spo_rw["adult_max"], $spo_rw["adult_max_category"]);
            $flg_children_check = _rates_calculator_validate_SPO_ad_ch_qty($arr_params["children"], $spo_rw["children_min"], $spo_rw["children_max"], $spo_rw["children_max_category"]);


            if ($flg_num_night == "OK") {
                if ($flg_min_guests) {
                    if ($flg_adults_check == "OK") {
                        if ($flg_children_check == "OK") {
                            $arr_final_spos[] = $arr_spos[$i];
                        } else {
                            $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : CHILDREN CHECK FAILED: $flg_children_check";
                        }
                    } else {
                        $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : ADULTS CHECK FAILED: $flg_adults_check";
                    }
                } else {
                    $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN GUESTS FAILED";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: $spoid - $sponame : MIN NIGHTS FAILED : $flg_num_night";
            }
        }
    }

    return array("VALID_SPOS" => $arr_final_spos, "INVALID_SPOS" => $arr_invalid_spos);
}

function _rates_calculator_validate_SPO_flat_rate_validate_capacity($spoid, $arr_params, $spo_arr_dates, $con) {
    //for each date in $spo_arr_dates, check capacity validation
    //dates that fail check are to be skipped
    $arr_spo_details = _spo_loadspo($con, $spoid, $arr_params["hotel"]);

    //overwrite the local variable $arr_params["arr_capacity"] with the capacity of the SPO
    $arr_params["arr_capacity"] = $arr_spo_details["FLAT_RATES_CAPACITY"];

    $arr = array();

    for ($i = 0; $i < count($spo_arr_dates); $i++) {
        $this_date = $spo_arr_dates[$i];

        //test children sharing and parents
        $capacity_test_adch = _rates_calculator_adch_capacity($arr_params, $this_date);

        //test children in own room
        $capacity_test_ch_ownroom = _rates_calculator_ch_own_capacity($arr_params, $this_date);

        if ($capacity_test_ch_ownroom["MSG"] == "OK" && $capacity_test_adch["MSG"] == "OK") {
            $arr[] = $this_date;
        }
    }

    return $arr;
}

function _rates_calculator_validate_SPO_flat_rate_grpvalidity($spoid, $spo_arr_dates, $con) {
    //for each date in $spo_arr_dates, check if it is in the group validity periods of the SPO
    //dates that are not valid are to be skipped

    $arr = array();
    for ($i = 0; $i < count($spo_arr_dates); $i++) {
        $this_date = $spo_arr_dates[$i];

        $sql = "select * from tblspecial_offer_flatrate_group_validity_period
                where spo_fk = :id and :dt between dt_from and dt_to";
        $query = $con->prepare($sql);

        $query->execute(array(":id" => $spoid, ":dt" => $this_date));

        if ($rwdt = $query->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $this_date;
        }
    }

    return $arr;
}

function _rates_calculator_validate_SPO_min_max_guests($arr_params, $min_guests, $max_guests) {
    $children = count($arr_params["children"]);
    $adults = count($arr_params["adults"]);
    $num_other_pax = ($arr_params["spo_party_pax"] == "" ? 0 : $arr_params["spo_party_pax"]);

    $total_pax = $children + $adults + $num_other_pax;

    if ($min_guests != "" && $max_guests != "") {
        if ($min_guests <= $total_pax && $total_pax <= $max_guests) {
            return true;
        }
    } else if ($min_guests != "" && $max_guests == "") {
        if ($min_guests <= $total_pax) {
            return true;
        }
    } else if ($min_guests == "" && $max_guests != "") {
        if ($total_pax <= $max_guests) {
            return true;
        }
    } else if ($min_guests == "" && $max_guests == "") {
        return true;
    }

    return false;
}

function _rates_calculator_validate_SPO_ad_ch_qty($arr_ad_ch, $min, $max, $category) {
    $adch_count = count($arr_ad_ch);

    if ($min != "") {
        if ($adch_count < $min) {
            return "MINIMUM $min PAX ALLOWABLE! CURRENTLY IS $adch_count PAX";
        }
    }
    if ($max != "") {
        if ($adch_count > $max && $category == "LIMIT") {
            return "MAXIMUM $max ALLOWABLE! CURRENTLY IS $adch_count PAX";
        }
    }
    return "OK";
}

function _rates_calculator_validate_SPO_family_offer_applicable_room($arr_params, $room_applicable) {
    //$room_applicable = both, share, own
    if ($room_applicable == "both") {
        return true;
    } else if ($room_applicable == "share") {
        //cannot have children.own

        $children = $arr_params["children"];

        for ($i = 0; $i < count($children); $i++) {
            if ($children[$i]["sharing_own"] == "OWN") {
                return false;
            }
        }
    } else if ($room_applicable == "own") {
        //cannot have children.share or adults

        $arr_adults = $arr_params["adults"];
        if (count($arr_adults) > 0) {
            return false;
        }

        $children = $arr_params["children"];
        for ($i = 0; $i < count($children); $i++) {
            if ($children[$i]["sharing_own"] == "OWN") {
                return false;
            }
        }
    }

    return true;
}

function _rates_calculator_getSPO_first_test($arr_params, $con) {
    //get all spos that hotel satisfy the criteria of:
    //1. hotel_room
    //2. meal_plan
    //3. rate
    //4. touroperator
    //5. country
    //6. spo type


    $country = $arr_params["country"];
    $hotel = $arr_params["hotel"];
    $hotelroom = $arr_params["hotelroom"];
    $mealplan = $arr_params["mealplan"];
    $rate = $arr_params["rate"];
    $touroperator = $arr_params["touroperator"];
    $spo_type = $arr_params["spo_type"];
    $wedding_interested = $arr_params["spo_chk_is_wedding"];


    $sql = "SELECT * FROM tblspecial_offer WHERE deleted = 0 
            and active_internal = 1 ";


    //======================= HOTEL =========================
    $sql .= " AND hotel_fk = :hotelfk ";

    //======================= HOTEL ROOM =========================
    $sql .= " AND id IN 
                  (SELECT spo_fk
                   FROM tblspecial_offer_rooms
                   WHERE roomfk=:roomfk
                  )";

    //======================= COUNTRY =========================
    $sql .= " AND id IN 
                  (SELECT spo_fk FROM 
                   tblspecial_offer_countries 
                   WHERE country_fk=:countryfk 
                  )";

    //======================= MEAL PLAN =========================
    $sql .= " AND id IN (SELECT spo_fk FROM 
              tblspecial_offer_mealplan 
              WHERE mealplanfk = :mealplan_fk
              )";

    //======================= RATES =========================
    $sql .= " AND rate_fk=:ratefk   ";

    //======================= TOUR OPERATORS =========================
    $sql .= " AND id IN 
                  (SELECT spofk FROM 
                   tblspecial_offer_touroperator 
                   WHERE tofk=:tofk 
                  )";

    //======================= SPO TYPE =================================
    if (strtoupper($spo_type) != "BOTH") {
        $sql .= " AND spo_type = '$spo_type' ";
    }

    //======================= WEDDING SPO INTERESTED ===================
    if ($wedding_interested == 0) {
        //skip wedding offers on purpose
        $sql .= " AND template NOT IN ('wedding_anniversary','wedding_party','honeymoon') ";
    }



    $query = $con->prepare($sql);
    $query->execute(array(":tofk" => $touroperator,
        ":ratefk" => $rate, ":mealplan_fk" => $mealplan, ":hotelfk" => $hotel,
        ":countryfk" => $country, ":roomfk" => $hotelroom));

    $arr_spos = array();
    $arr_invalid_spos = array();

    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        //================================================================
        //APPLY BOOKING DATE AND DAYS FILTER IF APPLICABLE
        $flg_book_date_chk = _rates_calculator_spo_check_booking_date($arr_params, $rw);
        $flg_book_days_chk = _rates_calculator_spo_check_booking_days($arr_params, $rw);

        //generate array of dates that are within SPO.validityperiod
        $arr_spo_dates = _rates_calculator_spo_generate_valid_dates($arr_params, $rw, $con);
        $flg_spo_dates_valid = (count($arr_spo_dates) > 0 ? true : false);

        if ($flg_book_date_chk == "OK") {
            if ($flg_book_days_chk == "OK") {

                if ($flg_spo_dates_valid) {
                    $arr_spos[] = array("SPO_RW" => $rw,
                        "TEMPLATE" => $rw["template"],
                        "DATES" => $arr_spo_dates); //spo passed initial tests! remember it
                } else {
                    $arr_invalid_spos[] = $rw["sponame"] . " : <br> NO VALID BOOKING DATES WITHIN VALIDITY PERIODS";
                }
            } else {
                $arr_invalid_spos[] = "SPO ID: " . $rw["id"] . " - " . $rw["sponame"] . ": <br> BOOKING DAYS FAILED: $flg_book_days_chk";
            }
        } else {
            $arr_invalid_spos[] = "SPO ID: " . $rw["id"] . " - " . $rw["sponame"] . ": <br> BOOKING DATE FAILED: $flg_book_date_chk";
        }
    }

    return array("VALID_SPOS" => $arr_spos, "INVALID_SPOS" => $arr_invalid_spos);
}

function _rates_calculator_spo_generate_valid_dates($arr_params, $rw, $con) {
    //for each date in the booking,
    //return an array of dates that can within the SPO validity periods

    $arr = array();

    $spoid = $rw["id"];

    $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
    $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd

    $checkin = new DateTime($checkin_date);
    $checkout = new DateTime($checkout_date);

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($checkin, $interval, $checkout);

    foreach ($period as $dt) {
        $this_date = $dt->format("Y-m-d");

        $sql = "select * from tblspecial_offer_validityperiods
                where spo_fk = :id
                and :dt between valid_from and valid_to";
        $query = $con->prepare($sql);
        $query->execute(array(":id" => $spoid, ":dt" => $this_date));

        if ($rwdt = $query->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $this_date;
        }
    }

    return $arr;
}

function _rates_calculator_spo_check_child_age_sharing_own($spoid, $sharing_own, $con) {
    //if SHARING then
    //return array of children.SHARING.ages_ranges for the SPO
    //if OWN then
    //return array of children.OWN.ages_ranges for the SPO

    $sql = "select soapc.*, ca.agefrom, ca.ageto from 
            tblspecial_offer_applicable_childsupp_" . strtolower($sharing_own) . " soapc
            inner join tblchildrenagerange ca on soapc.child_age_fk = ca.id
            where soapc.spo_fk = :id
            order by ca.agefrom, ca.ageto";

    $query = $con->prepare($sql);
    $query->execute(array(":id" => $spoid));

    $arr_acceptable_age_ranges = array();

    //make sure the age is within the range
    while ($rwage = $query->fetch(PDO::FETCH_ASSOC)) {
        $age_from = $rwage["agefrom"];
        $age_to = $rwage["ageto"];
        $arr_acceptable_age_ranges[] = array("AGE_FROM" => $age_from, "AGE_TO" => $age_to);
    }

    return $arr_acceptable_age_ranges;
}

function _rates_calculator_spo_check_booking_date($arr_params, $rw) {
    $spo_booking_date = $arr_params["spo_booking_date"];

    if ($spo_booking_date == "") {
        return "NO BOOKING DATE PROVIDED!";
    }


    //====================================================================================

    if ($rw["booking_before_date_from"] != "" && $rw["booking_before_date_to"] != "") {
        if ($rw["booking_before_date_from"] <= $spo_booking_date &&
                $spo_booking_date <= $rw["booking_before_date_to"]) {
            return "OK";
        } else {
            return "BOOKING DATE " . date("d-m-Y", strtotime($spo_booking_date)) .
                    " SHOULD BE BETWEEN SPO BOOKING DATES " .
                    date("d-m-Y", strtotime($rw["booking_before_date_from"])) . " AND " .
                    date("d-m-Y", strtotime($rw["booking_before_date_to"]));
        }
    } else if ($rw["booking_before_date_from"] == "" && $rw["booking_before_date_to"] != "") {
        if ($spo_booking_date <= $rw["booking_before_date_to"]) {
            return "OK";
        } else {
            return "BOOKING DATE " . date("d-m-Y", strtotime($spo_booking_date)) .
                    " SHOULD BE &le; SPO BOOKING DATE" .
                    date("d-m-Y", strtotime($rw["booking_before_date_to"]));
        }
    } else if ($rw["booking_before_date_from"] != "" && $rw["booking_before_date_to"] == "") {
        //dtto <= date
        if ($rw["booking_before_date_from"] <= $spo_booking_date) {
            return "OK";
        } else {
            return "BOOKING DATE " . date("d-m-Y", strtotime($spo_booking_date)) .
                    " SHOULD BE &ge; SPO BOOKING DATE" .
                    date("d-m-Y", strtotime($rw["booking_before_date_from"]));
        }
    } else if ($rw["booking_before_date_from"] == "" && $rw["booking_before_date_to"] == "") {
        return "OK";
    }

    return "ERROR";
}

function _rates_calculator_spo_check_booking_days($arr_params, $rw) {
    $spo_booking_date = $arr_params["spo_booking_date"];
    $spo_travel_date = $arr_params["spo_travel_date"];

    if ($spo_booking_date == "") {
        return "NO BOOKING DATE PROVIDED!";
    }

    if ($spo_travel_date == "") {
        return "NO TRAVEL DATE PROVIDED!";
    }

    $days_booking = utils_days_diff($spo_booking_date, $spo_travel_date);

    if ($rw["booking_before_days_from"] != "" && $rw["booking_before_days_to"] != "") {
        if ($rw["booking_before_days_from"] <= $days_booking &&
                $days_booking <= $rw["booking_before_days_to"]) {
            return "OK";
        } else {
            return "TRAVEL DATE - BOOKING DATE = $days_booking DAY(S) " .
                    "SHOULD BE BETWEEN SPO BOOKING DAYS " .
                    $rw["booking_before_days_from"] . " AND " .
                    $rw["booking_before_days_to"] . " DAY(S)";
        }
    } else if ($rw["booking_before_days_from"] == "" && $rw["booking_before_days_to"] != "") {
        //date <= dtfrom
        if ($days_booking <= $rw["booking_before_days_to"]) {
            return "OK";
        } else {
            return "TRAVEL DATE - BOOKING DATE = $days_booking DAY(S) " .
                    "SHOULD BE &le; " .
                    $rw["booking_before_days_to"] . " DAY(S)";
        }
    } else if ($rw["booking_before_days_from"] != "" && $rw["booking_before_days_to"] == "") {
        //dtto <= date
        if ($rw["booking_before_days_from"] <= $days_booking) {
            return "OK";
        } else {
            return "TRAVEL DATE - BOOKING DATE = $days_booking DAY(S) " .
                    "SHOULD BE &ge; " .
                    $rw["booking_before_days_from"] . " DAY(S)";
        }
    } else if ($rw["booking_before_days_from"] == "" && $rw["booking_before_days_to"] == "") {
        return "OK";
    }

    return "ERROR";
}

function _rates_calculator_spo_calculate_num_free_nights($rw, $con, $num_nights) {

    $cumulative = $rw["free_nights_cumulative"];

    $sql = "SELECT * FROM tblspecial_offer_freenights 
            WHERE spo_fk = :spoid order by stay_nights, pay_nights";

    $id = $rw["id"];

    $arr_stay_pay = array();

    $query = $con->prepare($sql);
    $query->execute(array(":spoid" => $id));
    while ($rwfn = $query->fetch(PDO::FETCH_ASSOC)) {

        $stay = $rwfn["stay_nights"];
        $pay = $rwfn["pay_nights"];

        $arr_stay_pay[] = array("STAYS" => $stay, "PAYS" => $pay);
    }

    $arr_free_nights = _rates_calculator_calc_free_nights($num_nights, $arr_stay_pay, $cumulative);

    $stay = $arr_free_nights["STAYS"];
    $pay = $arr_free_nights["PAYS"];
    $free = $arr_free_nights["FREE"];

    return $free;
}

function _rates_calculator_create_spo_obj($con, $spoid, $sponame, $spo_type, $ad_ch,
        $bride_groom, $age_from, $age_to, $room_all_flat,
        $value, $apply_to_dates, $free_nights,
        $free_nights_start_end, $flat_rate_capacity_array,
        $max_adult, $max_adult_category,
        $max_children, $max_children_category, $iscumulative) {

    $arr_sharing_age_ranges = _rates_calculator_spo_check_child_age_sharing_own($spoid, "SHARING", $con);
    $arr_own_age_ranges = _rates_calculator_spo_check_child_age_sharing_own($spoid, "OWN", $con);

    $arr_item = array(
        "SPO_ID" => $spoid,
        "SPO_NAME" => $sponame,
        "SPO_TYPE" => $spo_type,
        "AD_CH" => $ad_ch,
        "MAX_AD" => $max_adult,
        "MAX_AD_CATEGORY" => $max_adult_category,
        "MAX_CH" => $max_children,
        "MAX_CH_CATEGORY" => $max_children_category,
        "BRIDE_GROOM" => $bride_groom,
        "SHARING_AGE_RANGES" => $arr_sharing_age_ranges,
        "OWN_AGE_RANGES" => $arr_own_age_ranges,
        "AGE_FROM" => $age_from,
        "AGE_TO" => $age_to,
        "ROOM_ALL_FLAT" => $room_all_flat,
        "VALUE" => $value,
        "APPLY_TO_DATES" => $apply_to_dates,
        "IS_CUMULATIVE" => $iscumulative,
        "FREE_NIGHTS" => $free_nights,
        "FREE_NIGHTS_START_END" => $free_nights_start_end,
        "FLAT_RATE_CAPACITY_ARRAY" => $flat_rate_capacity_array);

    return $arr_item;
}

function _rates_calculator_getchildrencount_inagegroup_array($arr_group_children) {
    $total = 0;

    for ($a = 0; $a < count($arr_group_children); $a++) {
        $arr_children = $arr_group_children[$a]["CHILDREN"];
        $total += count($arr_children);
    }

    return $total;
}

function _rates_calculator_spo_translate_arr_sell($arr_cost) {
    $total_sell = 0;
    $currency_sell_code = "";
    $currency_sell_id = -1;

    //returns the total cost found in $arr_cost
    if ($arr_cost["OUTCOME"] == "OK") {
        $arr = $arr_cost["DAILY"];

        for ($i = 0; $i < count($arr); $i++) {
            $currency_sell_code = $arr[$i]["CURRENCY_SELL_CODE"];
            $currency_sell_id = $arr[$i]["CURRENCY_SELL_ID"];

            $arr_costings_workings = $arr[$i]["COSTINGS_WORKINGS"];
            for ($j = 0; $j < count($arr_costings_workings); $j++) {
                $arr_columns = $arr_costings_workings[$j]["COSTINGS"];

                $num_cols = count($arr_columns);
                if ($num_cols > 0) {
                    $colidx = $num_cols - 1;
                    if (isset($arr_columns[$colidx])) {
                        if (isset($arr_columns[$colidx]["VALUE"])) {
                            $msg = $arr_costings_workings[$j]["MSG"];
                            if (strpos($msg, "TOTAL") !== false) {
                                $total_sell += $arr_columns[$colidx]["VALUE"]; //the last value for total
                            }
                        }
                    }
                }
            }
        }
    }

    return array("TOTAL_SELL" => $total_sell, "CURRENCY_SELL" => $currency_sell_code,
        "CURRENCY_SELL_ID" => $currency_sell_id);
}

function _rates_calculator_sort_date_compare_asc($a, $b) {
    //sort the array as ascending
    $t1 = strtotime($a);
    $t2 = strtotime($b);
    return $t1 - $t2;
}

function _rates_calculator_sort_date_compare_desc($a, $b) {
    //sort the array as descending
    $t1 = strtotime($a);
    $t2 = strtotime($b);
    return $t2 - $t1;
}

function _rates_calculator_reservation_gen_room_combination($con, $contractid, $arr_params_resa) {

    /**
     * Summary.
     *
     * generates all applicable combinations of ad/teen/ch/inf for the room 
     * limits the combinations to the max pax provided
     * returns an array of combinations
     *
     *
     * @param PDOConnection  $con PDO Connection Object
     * @param Integer $contractid contract id
     * @param array $arr_params_resa {
     *     Array of parameters from reservation
     *
     *     @type Integer    $mealplan       meal plan id 
     *     @type Integer    $touroperator   tour operator id
     *     @type Integer    $hotel          hotel id
     *     @type Integer    $hotelroom      hotel room id
     *     @type Date       $checkin_date   checkin date in yyyy-mm-dd
     *     @type Date       $checkout_date  checkout date in yyyy-mm-dd
     *     @type Date       $booking_date   booking date in yyyy-mm-dd
     *     @type Date       $travel_date    travel date in yyyy-mm-dd
     *     @type Integer    $max_pax        maximum passengers in reservation
     * 
     * }
     * @return array An array of combinations
     */
    $max_pax = $arr_params_resa["max_pax"];

    $hotelroom = $arr_params_resa["hotelroom"];
    $checkin_date = $arr_params_resa["checkin_date"]; //yyyy-mm-dd

    $arr_capacity = _contract_capacityarr($con, $contractid);

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $checkin_date);

    $arr_combinations = array("OUTCOME" => "", "ROOM_TYPE" => "", "COMBINATIONS" => array());

    if (!is_null($rules)) {

        $date_rwid = $rules["date_rwid"];

        $arr_combii = _contract_combinations_rooms($arr_capacity, $hotelroom, $date_rwid);

        //generate the combinations for that room and date
        $arr_combinations["OUTCOME"] = "OK";
        $arr_combinations["ROOM_TYPE"] = $arr_combii["room_variants"];

        $combinations = $arr_combii["room_combinations"];
        $combinations = _rates_calculator_reservation_filter_room_combination($combinations, $max_pax);

        $arr_combinations["COMBINATIONS"] = $combinations;
    } else {
        $arr_combinations["OUTCOME"] = "NO COMBINATIONS DEFINED FOR THE ROOM FOR THAT CHECKIN PERIOD";
    }

    return $arr_combinations;
}

function _rates_calculator_reservation_filter_room_combination($combinations, $max_pax) {
    $arr = array();

    if (count($combinations) > 0) {
        $combinations_array = $combinations[0]["combinations_array"];

        for ($i = 0; $i < count($combinations_array); $i++) {
            $the_combii = $combinations_array[$i];

            //get the total pax in that combination
            $sum = 0;
            for ($j = 0; $j < count($the_combii); $j++) {
                $sum += $the_combii[$j]["No"];
            }
            if ($sum <= $max_pax) {
                //got an interesting combination here
                $arr[] = $the_combii;
            }
        }
    }

    return $arr;
}

function _rates_calculator_reservation_get_applicable_spos($con, $arr_params) {
    /**
     * Summary.
     *
     * generates choice of all applicable SPOs for reservation parameters
     * returns an array of SPOs with the LOWEST claim price for each option
     *
     * */
    //now launch the rates calculator to lookup SPO choices
    $arr_params["spo_chosen"] = "CHOICE";

    $arr_outcome = _rates_calculator($con, $arr_params);
    $outcome = $arr_outcome["OUTCOME"];

    if ($outcome != "OK") {
        return array("OUTCOME" => $outcome);
    }


    $arr_choices = array();
    if (isset($arr_outcome["CHOICES"])) {
        $arr_choices = $arr_outcome["CHOICES"];
    }

    //get the lowest SPO combination
    $index = -1;
    $lowest_total = 0;
    for ($i = 0; $i < count($arr_choices); $i++) {
        $single_combined = $arr_choices[$i]["SINGLE_COMBINED"];
        if ($single_combined != "NONE") {
            if ($index == -1) {
                $index = $i;
                $lowest_total = $arr_choices[$i]["TOTAL_SELL"];
            } else {
                if ($arr_choices[$i]["TOTAL_SELL"] < $lowest_total) {
                    $lowest_total = $arr_choices[$i]["TOTAL_SELL"];
                    $index = $i;
                }
            }
        }
    }

    //====================================
    //now get the list of spos with lowest price
    $arr_spos = array("DISCOUNTED_CLAIM_AMOUNT" => null, "SPOS" => array());
    if ($index >= 0) {
        $arr_spos["TOTAL_DISCOUNTED_CLAIM_AMOUNT"] = $lowest_total;

        $arr_list_spos = $arr_choices[$index]["ARR_SPOS"];
        for ($i = 0; $i < count($arr_list_spos); $i++) {
            $arr_spos["SPOS"][] = array("SPO_ID" => $arr_list_spos[$i]["SPOID"],
                "SPO_NAME" => $arr_list_spos[$i]["NAME"]);
        }
    }


    $arr_return = array("OUTCOME" => $outcome, "SPOS" => $arr_spos);

    return $arr_return;
}

function _rates_calculator_reservation_get_cost_claim($con, $contractid, $arr_params_resa) {

    try {
        //get the cost and claim amount per pax

        /**
         * Summary.
         *
         * returns an array of cost/claim amount per pax for the parameters provided
         *
         *
         * @param PDOConnection  $con PDO Connection Object
         * @param Integer $contractid contract id    
         * @param array $arr_params_resa {
         *     Array of parameters from reservation
         *
         *     @type Integer    $mealplan           meal plan id 
         *     @type Integer    $suppmealplan       supplement meal plan id (optional: can be blank string)
         *     @type Integer    $touroperator       tour operator id
         *     @type Integer    $hotel              hotel id
         *     @type Integer    $hotelroom          hotel room id
         *     @type Date       $checkin_date       checkin date in yyyy-mm-dd
         *     @type Date       $checkout_date      checkout date in yyyy-mm-dd
         *     @type Time       $checkin_time       checkin time in HH:mm (optional: can be blank string)
         *     @type Time       $checkout_time      checkout time in HH:mm (optional: can be blank string)
         *     @type Date       $booking_date       booking date in yyyy-mm-dd
         *     @type Date       $travel_date        travel date in yyyy-mm-dd
         *     @type Integer    $max_pax            maximum passengers in reservation
         *     @type Boolean    $wedding_interested if interested in wedding SPOS (1/0)
         *     @type array      $arr_pax {
         *          Array of adults/children details mixed together
         *          @type Integer $count        index of child/adult
         *          @type Integer $age          age of the child/adult. If no age, then is adult
         *          @type String  $bride_groom  if adult if bride or groom or none. values = {"BRIDE","GROOM",""}
         *     }      
         * }
         * @return array An array of cost/claim amounts per pax for above parameters
         * 
         */
        $mealplan = $arr_params_resa["mealplan"];
        $suppmealplan = $arr_params_resa["suppmealplan"];
        $touroperator = $arr_params_resa["touroperator"];
        $hotel = $arr_params_resa["hotel"];
        $hotelroom = $arr_params_resa["hotelroom"];
        $checkin_date = $arr_params_resa["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params_resa["checkout_date"]; //yyyy-mm-dd
        $booking_date = $arr_params_resa["booking_date"]; //yyyy-mm-dd
        $travel_date = $arr_params_resa["travel_date"]; //yyyy-mm-dd
        $checkin_time = $arr_params_resa["checkin_time"]; //HH:mm
        $checkout_time = $arr_params_resa["checkout_time"]; //HH:mm
        $wedding_interested = $arr_params_resa["wedding_interested"]; //1 or 0
        $max_pax = $arr_params_resa["max_pax"];
        $arr_pax = $arr_params_resa["arr_pax"];

        $arr_limits = array();

        $resa_rates = -1; //to be decided below:
        //=========================================
        //get the country of the TO
        $countryid = -1;
        $sql = "select * from tblto_countries where tofk = :toid limit 1";
        $query = $con->prepare($sql);
        $query->execute(array(":toid" => $touroperator));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $countryid = $row["countryfk"];
        }
        //=========================================
        //=========================================
        //lookup the special and standard rates of that tour operator
        $special_rate_id = -1;
        $standard_rate_id = -1;

        $sql = "select ratecode,specialratecode, 
            ifnull(rc_std.id,-1) as stdid, ifnull(rc_spec.id,-1) as specid
            from tbltouroperator tourop
            left join tblratecodes rc_std on ratecode = rc_std.ratecodes
            left join tblratecodes rc_spec on specialratecode = rc_spec.ratecodes
            where tourop.id = :toid";

        $query = $con->prepare($sql);
        $query->execute(array(":toid" => $touroperator));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $special_rate_id = $row["specid"];
            $standard_rate_id = $row["stdid"];
        }
        //============================================
        //test which rate is the one that belongs to the contract
        $sql = "select * from tblservice_contract_rates where 
            service_contract_fk = :contractid and ratefk = :rateid";

        $query = $con->prepare($sql);
        $query->execute(array(":contractid" => $contractid, ":rateid" => $special_rate_id));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $resa_rates = $special_rate_id;
        } else {
            $resa_rates = $standard_rate_id;
        }


        //============================================
        //=========================================
        //reset my parameters
        $arr_params["checkin_date"] = $checkin_date;
        $arr_params["checkout_date"] = $checkout_date;
        $arr_params["checkin_time"] = $checkin_time;
        $arr_params["checkout_time"] = $checkout_time;
        $arr_params["country"] = $countryid;
        $arr_params["hotel"] = $hotel;
        $arr_params["hotelroom"] = $hotelroom;
        $arr_params["mealplan"] = $mealplan;
        $arr_params["supp_mealplan"] = $suppmealplan;
        $arr_params["touroperator"] = $touroperator;
        $arr_params["contractids"] = "";
        $arr_params["rate"] = $resa_rates;
        $arr_params["spo_type"] = "both";
        $arr_params["spo_booking_date"] = $booking_date;
        $arr_params["spo_travel_date"] = $travel_date;
        $arr_params["spo_party_pax"] = $max_pax;
        $arr_params["spo_chk_is_wedding"] = $wedding_interested;

        //================================================================
        //get age policies
        $_arr_params = $arr_params;
        $_arr_params["current_contract_id"] = $contractid;
        $arr_age_policies = _rates_calculator_get_children_agegroups($_arr_params, $con, "CONTRACT");
        //===========================================================
        //
        //split the pax array into adults and children
        $arr_ad_ch = _rates_calculator_reservation_split_pax_arrays($arr_pax, $arr_age_policies);
        $arr_params["adults"] = $arr_ad_ch["ADULTS"];
        $arr_params["children"] = $arr_ad_ch["CHILDREN"];
        //===========================================================
        //==========================================
        //get the SPO 
        $arr_params["spo_chosen"] = "CHOICE";
        $arr_spos = _rates_calculator_reservation_get_applicable_spos($con, $arr_params);
        if ($arr_spos["OUTCOME"] != "OK") {
            return $arr_spos["OUTCOME"];
        }

        //===============================================================
        //get the room type: UNITS or PAX
        $room_variant = "PERSONS";
        $sql = "select * from tblservice_contract_roomcapacity 
            where service_contract_fk = :contractid and roomfk = :roomid";
        $query = $con->prepare($sql);
        $query->execute(array(":contractid" => $contractid, ":roomid" => $hotelroom));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $room_variant = $row["variant"];
        }
        //===========================================================
        //get the contract Cost Currency Id
        $currency_cost_id = "-1";
        $sql = "select * from tblservice_contract where id = :contractid";
        $query = $con->prepare($sql);
        $query->execute(array(":contractid" => $contractid));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $currency_cost_id = $row["mycostprice_currencyfk"];
        }
        //===========================================================
        //get the limits possibilities for the adults and children provided
        $arr_limits = _rates_calculator_get_pax_limits($arr_params, $con, $contractid, $room_variant);
        //===========================================================
        //now launch the rates calculator with/without SPO:
        //run rates calculator with LOWEST SPO applied
        $arr_params["spo_chosen"] = "LOWEST";
        $arr_outcome_with_spo = _rates_calculator($con, $arr_params);
        $outcome = $arr_outcome_with_spo["OUTCOME"];

        if ($outcome != "OK") {
            return array("OUTCOME" => $outcome);
        }

        //run rates calculator without SPO applied
        $arr_params["spo_chosen"] = "NONE";
        $arr_outcome_without_spo = _rates_calculator($con, $arr_params);
        $outcome = $arr_outcome_without_spo["OUTCOME"];

        if ($outcome != "OK") {
            return array("OUTCOME" => $outcome);
        }


        //==========================================================
        //success in lookup
        //proceed with extracting the amounts

        $currency_sell_id = $arr_outcome_without_spo["DAILY"][0]["CURRENCY_SELL_ID"];

        $arr_colidx = _rates_calculator_reservation_get_columns_cost_sp_colindex($arr_outcome_without_spo["COLUMNS"]);
        $cost_colidx = $arr_colidx["COST_IDX"];
        $sp_colidx = $arr_colidx["SP_IDX"];

        //=====================================================

        $arr_daily_status = _rates_calculator_reservation_get_daily_status($arr_outcome_without_spo["DAILY"]);

        //=====================================================
        //for each adult in arr_adults, lookup the amounts
        $arr_adult_rates = _rates_calculator_reservation_get_cost_claim_per_pax(
                $arr_outcome_without_spo["DAILY"][0]["COSTINGS_WORKINGS"],
                $arr_outcome_with_spo["DAILY"][0]["COSTINGS_WORKINGS"],
                "ROOM",
                "ADULT", $cost_colidx, $sp_colidx);


        //for each child in $arr_children, lookup the amounts
        $arr_children_rates = _rates_calculator_reservation_get_cost_claim_per_pax(
                $arr_outcome_without_spo["DAILY"][0]["COSTINGS_WORKINGS"],
                $arr_outcome_with_spo["DAILY"][0]["COSTINGS_WORKINGS"],
                "ROOM",
                "CHILDREN", $cost_colidx, $sp_colidx);



        $arr_amounts = _rates_calculator_reservation_organise_costs($arr_adult_rates, $arr_children_rates, $room_variant);


        $arr_return = array("OUTCOME" => "OK",
            "CONTRACT_ID" => $contractid,
            "ROOM_TYPE" => $room_variant,
            "ROOM_ID" => $hotelroom,
            "PAX_LIMITS_POSSIBILITIES" => $arr_limits,
            "DAILY_STATUS" => $arr_daily_status,
            "COST_CURRENCY_ID" => $currency_cost_id,
            "CLAIM_CURRENCY_ID" => $currency_sell_id,
            "COST_CLAIM_AMOUNTS" => $arr_amounts,
            "AGE_POLICIES" => $arr_age_policies,
            "SPECIAL_OFFERS" => $arr_spos["SPOS"]);



        return $arr_return;
    } catch (Exception $ex) {
        $arr_return = array("OUTCOME" => $ex->getMessage());
        return $arr_return;
    }
}

function _rates_calculator_reservation_get_daily_status($arr_daily) {
    $arr_status = array();

    for ($i = 0; $i < count($arr_daily); $i++) {
        $arr_status[] = array("DATE" => $arr_daily[$i]["DATE"], "STATUS" => $arr_daily[$i]["STATUS"]);
    }

    return $arr_status;
}

function _rates_calculator_reservation_split_pax_arrays($arr_pax, $arr_age_policies) {
    $arr_adults = array();
    $arr_children = array();


    //==================================================
    //get the max age for a child
    $max_age = -1;
    for ($i = 0; $i < count($arr_age_policies); $i++) {
        $age_to = $arr_age_policies[$i]["AGETO"];
        if ($max_age == -1) {
            $max_age = $age_to;
        } else if ($max_age < $age_to) {
            $max_age = $age_to;
        }
    }
    //==================================================     
    $age_adult = $max_age + 1;

    //now split the array into adults and children categories
    $adult_count = 0;
    $child_count = 0;

    for ($i = 0; $i < count($arr_pax); $i++) {
        $age = trim($arr_pax[$i]["age"]);
        $bride_groom = $arr_pax[$i]["bride_groom"];

        if ($age == "" || $age >= $age_adult) {
            $adult_count++;
            $arr_adults[] = array("count" => $adult_count, "age" => $age, "bride_groom" => $bride_groom);
        } else {
            $child_count++;
            $arr_children[] = array("count" => $child_count, "age" => $age, "sharing_own" => "");
        }
    }

    //==================================================
    //preformat the $arr_children to know if SHARING or OWN
    $flg_sharing_own = "SHARING";
    if (count($arr_adults) == 0) {
        $flg_sharing_own = "OWN";
    }
    for ($i = 0; $i < count($arr_children); $i++) {
        $arr_children[$i]["sharing_own"] = $flg_sharing_own;
    }
    //==================================================

    return array("ADULTS" => $arr_adults, "CHILDREN" => $arr_children);
}

function _rates_calculator_reservation_organise_costs($arr_adult_rates, $arr_children_rates, $room_variant) {
    if ($room_variant == "PERSONS") {
        $arr_adults = array();
        $adults_index = 0;

        $arr_children = array();
        $children_index = 0;

        //============== adults ================
        for ($i = 0; $i < count($arr_adult_rates); $i++) {

            $adults_index++;
            $arr_adults[] = array("INDEX" => $adults_index,
                "BRIDE_GROOM" => $arr_adult_rates[$i]["BRIDE_GROOM"],
                "COST" => $arr_adult_rates[$i]["COST"],
                "CLAIM_WITHOUT_SPO" => $arr_adult_rates[$i]["CLAIM_WITHOUT_SPO"],
                "CLAIM_WITH_SPO" => $arr_adult_rates[$i]["CLAIM_WITH_SPO"]);
        }

        //========== children ==============
        for ($i = 0; $i < count($arr_children_rates); $i++) {

            $children_index++;
            $arr_children[] = array("INDEX" => $children_index,
                "AGE" => $arr_children_rates[$i]["AGE"],
                "COST" => $arr_children_rates[$i]["COST"],
                "CLAIM_WITHOUT_SPO" => $arr_children_rates[$i]["CLAIM_WITHOUT_SPO"],
                "CLAIM_WITH_SPO" => $arr_children_rates[$i]["CLAIM_WITH_SPO"]);
        }

        return array("ADULTS" => $arr_adults,
            "CHILDREN" => $arr_children);
    } else {
        //UNITS
        //merge the unit costs and claim
        //also extract the extra adults and children amounts
        $unit_cost = 0;
        $unit_claim_before_spo = 0;
        $unit_claim_after_spo = 0;


        $arr_extra_adults = array();
        $extra_adults_index = 0;

        $arr_extra_children = array();
        $extra_children_index = 0;

        //============== adults ================
        for ($i = 0; $i < count($arr_adult_rates); $i++) {
            if ($arr_adult_rates[$i]["EXTRA"] == "") {
                $unit_cost += $arr_adult_rates[$i]["COST"];
                $unit_claim_before_spo += $arr_adult_rates[$i]["CLAIM_WITHOUT_SPO"];
                $unit_claim_after_spo += $arr_adult_rates[$i]["CLAIM_WITH_SPO"];
            } else {
                $extra_adults_index++;
                $arr_extra_adults[] = array("INDEX" => $extra_adults_index,
                    "COST" => $arr_adult_rates[$i]["COST"],
                    "CLAIM_WITHOUT_SPO" => $arr_adult_rates[$i]["CLAIM_WITHOUT_SPO"],
                    "CLAIM_WITH_SPO" => $arr_adult_rates[$i]["CLAIM_WITH_SPO"]);
            }
        }

        //========== children ==============
        for ($i = 0; $i < count($arr_children_rates); $i++) {
            if ($arr_children_rates[$i]["EXTRA"] == "") {
                $unit_cost += $arr_children_rates[$i]["COST"];
                $unit_claim_before_spo += $arr_children_rates[$i]["CLAIM_WITHOUT_SPO"];
                $unit_claim_after_spo += $arr_children_rates[$i]["CLAIM_WITH_SPO"];
            } else {
                $extra_children_index++;
                $arr_extra_children[] = array("INDEX" => $extra_children_index,
                    "AGE" => $arr_children_rates[$i]["AGE"],
                    "COST" => $arr_children_rates[$i]["COST"],
                    "CLAIM_WITHOUT_SPO" => $arr_children_rates[$i]["CLAIM_WITHOUT_SPO"],
                    "CLAIM_WITH_SPO" => $arr_children_rates[$i]["CLAIM_WITH_SPO"]);
            }
        }

        return array("UNIT_COST" => $unit_cost,
            "UNIT_CLAIM_WITHOUT_SPO" => $unit_claim_before_spo,
            "UNIT_CLAIM_WITH_SPO" => $unit_claim_after_spo,
            "EXTRA_ADULTS" => $arr_extra_adults,
            "EXTRA_CHILDREN" => $arr_extra_children);
    }
}

function _rates_calculator_reservation_get_columns_cost_sp_colindex($columns) {
    $sp_colidx = 0;
    $cost_colidx = 0;

    for ($i = 0; $i < count($columns); $i++) {
        $colitem = $columns[$i];
        if ($colitem["CAPTION"] == "COST PRICE") {
            $cost_colidx = $i;
        } else if ($colitem["CAPTION"] == "FINAL SELLING PRICE") {
            $sp_colidx = $i;
        }
    }

    return array("COST_IDX" => $cost_colidx, "SP_IDX" => $sp_colidx);
}

function _rates_calculator_reservation_get_cost_claim_per_pax($arr_before_spo,
        $arr_afer_spo, $category, $adch,
        $cost_colidx, $sp_colidx) {
    $pax_arr = array();


    for ($i = 0; $i < count($arr_before_spo); $i++) {
        if (isset($arr_before_spo[$i]["CATEGORY"])) {
            if ($category == $arr_before_spo[$i]["CATEGORY"]) {
                if ($arr_before_spo[$i]["ADCH"] == $adch) {
                    $age = $arr_before_spo[$i]["AGE"];

                    $bridegroom = $arr_before_spo[$i]["BRIDEGROOM"];
                    if ($bridegroom != "BRIDE" && $bridegroom != "GROOM") {
                        $bridegroom = "";
                    }

                    $extra = "";
                    if (isset($arr_before_spo[$i]["EXTRA"])) {
                        $extra = $arr_before_spo[$i]["EXTRA"];
                    }
                    
                    $cost_value = $arr_before_spo[$i]["COSTINGS"][$cost_colidx]["VALUE"];
                    $sp_value_without_spo = $arr_before_spo[$i]["COSTINGS"][$sp_colidx]["VALUE"];
                    $sp_value_with_spo = $arr_afer_spo[$i]["COSTINGS"][$sp_colidx]["VALUE"];

                    if ($adch == "ADULT") {
                        $pax_arr[] = array("AGE" => $age, "EXTRA" => $extra, "BRIDE_GROOM" => $bridegroom,
                            "COST" => $cost_value, "CLAIM_WITH_SPO" => $sp_value_with_spo,
                            "CLAIM_WITHOUT_SPO" => $sp_value_without_spo);
                    } else {
                        $pax_arr[] = array("AGE" => $age, "EXTRA" => $extra,
                            "COST" => $cost_value, "CLAIM_WITH_SPO" => $sp_value_with_spo,
                            "CLAIM_WITHOUT_SPO" => $sp_value_without_spo);
                    }
                }
            }
        }
    }

    return $pax_arr;
}

function _rates_calculator_reservation_get_ad_ch_categories($con, $contractid, $arr_params_resa) {
    //get the cost and claim amount per pax

    /**
     * Summary.
     *
     * returns an array of cost/claim amount per pax for the parameters provided
     *
     *
     * @param PDOConnection  $con PDO Connection Object
     * @param Integer $contractid contract id    
     * @param array $arr_params_resa {
     *     Array of parameters from reservation
     *
     *     @type Integer    $hotelroom          hotel room id
     *     @type Date       $checkin_date       checkin date in yyyy-mm-dd
     *     @type Date       $checkout_date      checkout date in yyyy-mm-dd
     * }
     * @return array of adult/children single/double/triple....
     * 
     */
    $hotelroom = $arr_params_resa["hotelroom"];
    $checkin_date = $arr_params_resa["checkin_date"]; //yyyy-mm-dd
    $checkout_date = $arr_params_resa["checkout_date"]; //yyyy-mm-dd



    $arr_capacity = _contract_capacityarr($con, $contractid);

    //reformat my parameters
    $arr_params["arr_capacity"] = $arr_capacity;
    $arr_params["hotelroom"] = $hotelroom;

    $room_details = _rates_calculator_get_room_details($arr_params);

    //get the room type
    $room_variant = $room_details["room_variants"];

    //get the arr_capacity for that date
    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $checkin_date);


    if (is_null($rules)) {

        $arr_return = array("OUTCOME" => "NO DEFINITIONS FOUND FOR THIS DATE",
            "ROOM_TYPE" => $room_variant, "ADULT" => array());
    }

    //=================================================
    //=================================================
    //GENERATE THE ADULT GRID
    $arr_adults = array();

    $arr_adultpolicies_rules = $rules["date_adultpolicies_rules"];

    //get the adult max and min
    if ($room_variant == "PERSONS") {
        $adult_max = _rates_calculator_reservation_get_ad_max($rules["date_capacity_rules"], "ADULT");

        for ($i = 1; $i <= $adult_max; $i++) {
            //===========================

            $basis = "";
            for ($b = 0; $b < count($arr_adultpolicies_rules); $b++) {
                if ($i == $arr_adultpolicies_rules[$b]["rule_category"]) {
                    $arr_rule_policy = $arr_adultpolicies_rules[$b]["rule_policy"];
                    $basis = _rates_calculator_lookup_rates_valuebasis($arr_rule_policy, "basis");

                    if ($basis == "1/n") {
                        $basis = "1/$i";
                    } else if ($basis == "n") {
                        $basis = "$i";
                    }
                }
            }

            //===========================

            if ($i == 1) {
                $arr_adults[] = array("INDEX" => $i, "CAPTION" => "SINGLE", "BASIS" => $basis);
            } else if ($i == 2) {
                $arr_adults[] = array("INDEX" => $i, "CAPTION" => "DOUBLE", "BASIS" => $basis);
            } else if ($i == 3) {
                $arr_adults[] = array("INDEX" => $i, "CAPTION" => "TRIPLE", "BASIS" => $basis);
            } else if ($i == 4) {
                $arr_adults[] = array("INDEX" => $i, "CAPTION" => "QUADRUPLE", "BASIS" => $basis);
            } else if ($i >= 5) {
                $arr_adults[] = array("INDEX" => $i, "CAPTION" => "$i-PAX", "BASIS" => $basis);
            }

            //===========================
        }
    } else {
        $adult_max = _rates_calculator_reservation_get_ad_max($rules["date_capacity_rules"], "ADDITIONALPERSONS");

        for ($i = 1; $i <= $adult_max; $i++) {
            $arr_adults[] = array("INDEX" => $i, "CAPTION" => "EXTRA ADULT $i");
        }
    }


    $arr_return = array("OUTCOME" => "OK",
        "ROOM_TYPE" => $room_variant, "ADULT" => $arr_adults);


    return $arr_return;
}

function _rates_calculator_reservation_get_ad_max($rules, $category) {
    $max_adult_count = 0;

    for ($j = 0; $j < count($rules); $j++) {

        $arrrule_capacity = $rules[$j]["rule_capacity"];
        for ($k = 0; $k < count($arrrule_capacity); $k++) {

            $capacity_category = $arrrule_capacity[$k]["capacity_category"];
            $capacity_minpax = $arrrule_capacity[$k]["capacity_minpax"];
            $capacity_maxpax = $arrrule_capacity[$k]["capacity_maxpax"];

            if ($capacity_category == $category) {
                if ($max_adult_count < $capacity_minpax) {
                    $max_adult_count = $capacity_minpax;
                }
                if ($max_adult_count < $capacity_maxpax) {
                    $max_adult_count = $capacity_maxpax;
                }
            }
        }
    }

    return $max_adult_count;
}

function _rates_calculator_get_pax_limits($arr_params, $con, $contractid, $room_variant) {
    //for the number of adults and children in the parameters
    //determine the max for adults and each age group provided

    $arr_limits = array();

    $arr_adults = $arr_params["adults"];
    $arr_children = $arr_params["children"];

    $adult_count = count($arr_adults);

    //get the contract age groups
    $arr_params["current_contract_id"] = $contractid;

    //$arr_resa_children_ages = $arr_params["children_ages"];
    //regroup the children in the reservation by the age groups above
    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $arr_children, $con, "CONTRACT");

    //load the capacity
    $arr_capacity = _contract_capacityarr($con, $contractid);

    $hotelroom = $arr_params["hotelroom"];
    $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $checkin_date);

    if (is_null($rules)) {
        return $arr_limits;
    }

    $arr_capacity_rules = $rules["date_capacity_rules"];

    if ($room_variant == "PERSONS") {
        $arr_limits = _rates_calculator_get_pax_limits_persons($arr_capacity_rules, $adult_count, $arr_group_children);
    } else {

        $arr_limits = _rates_calculator_get_pax_limits_units($arr_capacity_rules);
    }

    return $arr_limits;
}

function _rates_calculator_get_pax_limits_units($rule) {


    $arr_rules_returned = array();

    for ($i = 0; $i < count($rule); $i++) {
        $rule_line = $rule[$i]["rule_capacity"];
        $arr_rule_presentable = array();

        for ($j = 0; $j < count($rule_line); $j++) {
            $capacity_category = $rule_line[$j]["capacity_category"];
            $capacity_minpax = $rule_line[$j]["capacity_minpax"];
            $capacity_maxpax = $rule_line[$j]["capacity_maxpax"];
            $capacity_child_agefrom = $rule_line[$j]["capacity_child_agefrom"];
            $capacity_child_ageto = $rule_line[$j]["capacity_child_ageto"];

            if ($capacity_category == "STANDARDOCCUPATION") {
                $arr_rule_presentable[] = array("CATEGORY" => "STANDARD", "MIN_PAX" => $capacity_minpax,
                    "MAX_PAX" => $capacity_maxpax);
            } else if ($capacity_category == "ADDITIONALPERSONS") {
                $arr_rule_presentable[] = array("CATEGORY" => "ADDITIONAL_ADULTS", "MIN_PAX" => $capacity_minpax,
                    "MAX_PAX" => $capacity_maxpax);
            } else if ($capacity_category == "CH") {
                $arr_rule_presentable[] = array("CATEGORY" => "ADDITIONAL_CHILDREN", "MIN_PAX" => $capacity_minpax,
                    "MAX_PAX" => $capacity_maxpax,
                    "AGE_FROM" => $capacity_child_agefrom,
                    "AGE_TO" => $capacity_child_ageto);
            }
        }
    }

    return $arr_rule_presentable;
}

function _rates_calculator_get_pax_limits_persons_format_rule($rule) {
    $arr_rule_presentable = array();

    for ($i = 0; $i < count($rule); $i++) {
        $capacity_category = $rule[$i]["capacity_category"];
        $capacity_minpax = $rule[$i]["capacity_minpax"];
        $capacity_maxpax = $rule[$i]["capacity_maxpax"];
        $capacity_child_agefrom = $rule[$i]["capacity_child_agefrom"];
        $capacity_child_ageto = $rule[$i]["capacity_child_ageto"];

        if ($capacity_category == "ADULT") {
            $arr_rule_presentable[] = array("CATEGORY" => "ADULT", "MIN_PAX" => $capacity_minpax, "MAX_PAX" => $capacity_maxpax);
        } else if ($capacity_category == "CHILD") {
            $arr_rule_presentable[] = array("CATEGORY" => "CHILD", "MIN_PAX" => $capacity_minpax,
                "MAX_PAX" => $capacity_maxpax,
                "AGE_FROM" => $capacity_child_agefrom,
                "AGE_TO" => $capacity_child_ageto);
        }
    }

    return $arr_rule_presentable;
}

function _rates_calculator_get_pax_limits_persons($arr_capacity_rules, $adult_count, $arr_group_children) {

    $arr_limits = array();

    for ($i = 0; $i < count($arr_capacity_rules); $i++) {

        $therule = $arr_capacity_rules[$i];

        //test the rule respects children ages limits
        //also test the rule that its adult ranges is within the adult count
        $flg = _rates_calculator_get_pax_limits_persons_test_rule($therule["rule_capacity"], $adult_count, $arr_group_children);

        if ($flg) {
            $arr_limits[] = _rates_calculator_get_pax_limits_persons_format_rule($therule["rule_capacity"]);
        }
    }

    return $arr_limits;
}

function _rates_calculator_get_pax_limits_persons_test_rule($therule_capacity, $adult_count, $arr_group_children) {


    //===========================================================================
    //now more specific check: see if the pax ranges for adults and children are respected in the rule
    $flg_seen_adult = false;
    $flg_seen_children = false;


    for ($i = 0; $i < count($therule_capacity); $i++) {
        $capacity_category = $therule_capacity[$i]["capacity_category"];
        $min_pax = $therule_capacity[$i]["capacity_minpax"];
        $max_pax = $therule_capacity[$i]["capacity_maxpax"];

        if ($min_pax == "") {
            $min_pax = 0;
        }
        if ($max_pax == "") {
            $max_pax = 0;
        }

        //====================================================

        if ($capacity_category == "ADULT") {
            $flg_seen_adult = true;
            if ($adult_count < $min_pax || $adult_count > $max_pax) {
                return false;
            }
        }

        //====================================================
        else if ($capacity_category == "CHILD") {
            $capacity_child_agefrom = $therule_capacity[$i]["capacity_child_agefrom"];
            $capacity_child_ageto = $therule_capacity[$i]["capacity_child_ageto"];

            //rule: 0-11
            //resa: 0-1; 2-11<-- 12-17
            //return the total children $arr_group_children where age_from and age_to match
            $ch_count = _rates_calculator_get_pax_limits_persons_test_rule_ages_get_index($capacity_child_agefrom, $capacity_child_ageto, $arr_group_children);

            if ($ch_count < $min_pax || $ch_count > $max_pax) {
                return false;
            }
        }

        //====================================================
    }

    //====================================================
    //was expecting to see an adult but current rule tested as an own room one
    if (!$flg_seen_adult && $adult_count > 0) {
        return false;
    }

    //====================================================
    //now test that all age ranges in $arr_group_children have been processed from $therule_capacity
    for ($j = 0; $j < count($arr_group_children); $j++) {

        $age_from = $arr_group_children[$j]["AGFROM"];
        $age_to = $arr_group_children[$j]["AGTO"];

        if (count($arr_group_children[$j]["CHILDREN"]) > 0) {
            return false;
        }
    }

    return true;
}

function _rates_calculator_get_pax_limits_persons_test_rule_ages_get_index($capacity_child_agefrom, $capacity_child_ageto, &$arr_group_children) {

    $total = 0;
    for ($j = 0; $j < count($arr_group_children); $j++) {

        $age_from = $arr_group_children[$j]["AGFROM"];
        $age_to = $arr_group_children[$j]["AGTO"];

        if ($capacity_child_agefrom <= $age_from && $age_to <= $capacity_child_ageto) {
            $total += count($arr_group_children[$j]["CHILDREN"]);

            $arr_group_children[$j]["CHILDREN"] = array(); //reset the array because this age group has been seen
        }
    }

    return $total;
}
?>


