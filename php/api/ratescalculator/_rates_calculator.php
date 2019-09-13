<?php

function _rates_calculator($con, $arr_params) {
    try {

        $time_pre = microtime(true);

        //=============== get array of valid and invalid spos ===============
        $arr_spo = _rates_calculator_spo_search($arr_params, $con);
        if ($arr_spo["OUTCOME"] != "OK") {
            throw new Exception($arr_spo["OUTCOME"]);
        }
        $arr_spo_discounts = $arr_spo["ARR_SPOS"];
        $arr_invalid_spos = $arr_spo["ARR_INVALID_SPOS"];
        //=================================================


        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
        //cleanup:
        $arr_params["contractids"] = trim($arr_params["contractids"]);


        $checkin_dMY = new DateTime($arr_params["checkin_date"]);
        $checkin_dMY = $checkin_dMY->format("d M Y");
        $checkout_dMY = new DateTime($arr_params["checkout_date"]);
        $checkout_dMY = $checkout_dMY->format("d M Y");
        $num_nights = _rates_calculator_get_numnights($checkin_date, $checkout_date);

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
        //==========================================================
        //=========================================================================
        //CHECK IF THERE ARE FREE NIGHTS AND THEN SEARCH FOR NIGHTS THAT WILL BE FREE
        $arr_params["spo_discounts_array"] = $arr_spo_discounts;
        _rates_calculator_process_SPO_free_nights($arr_spo_discounts, $arr_days, $arr_params, $con, $arr_capacity);
        $arr_params["spo_discounts_array"] = $arr_spo_discounts;
        //=========================================================================


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


        return array("OUTCOME" => "OK", "NUM NIGHTS" => $num_nights, "DAILY" => $arr_daily,
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
    }
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

        if ($adult == 0 && count($children) == 0) {
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
    for ($cix = 0; $cix < count($arr_date_combinations); $cix ++) {
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
    //====== now test children =======
    for ($j = 0; $j < count($children); $j++) {
        $found = false;

        $age = $children[$j]["age"];

        for ($i = 0; $i < count($combo); $i++) {
            $agfrom = $combo[$i]["AGEFROM"];
            $agto = $combo[$i]["AGETO"];

            if ($agfrom <= $age && $age <= $agto) {
                if ($combo[$i]["No"] > 0) {
                    $found = true;
                    $combo[$i]["No"] --; //decrement the children count
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
            //$arr_group_children[$i]["CHILDREN"] = array_splice($arr_group_children[$i]["CHILDREN"], 0, 1);
            $std_max --;
        }
        $i --;
    }

    return;
}

function _rates_calculator_test_capacity_rule_units_extra_adult(&$std_max, &$adult) {
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

        //for each extra adult and extra children, calculate the price
        //===========================================================
        if ($adult > 0) {
            //there is extra adult
            $arr_extra_adult = _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates, $arr_eci, $this_date);
            $arr = array_merge($arr, $arr_extra_adult);
            $flg_extra_people = true;
        }
        //===========================================================
        //===========================================================
        //for each extra children, calculate the price                
        $arr_extra_children = _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params, $arr_group_children, $normal_rates, $arr_eci, $flg_extra_people, $this_date);
        $arr = array_merge($arr, $arr_extra_children);

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

        for ($adinx = 1; $adinx <= $num_persons; $adinx++) {
            if ($adinx == $num_persons) {
                $per_person_buyprice = $normal_rates - $cumul_buyprice;
            } else {
                $cumul_buyprice += $per_person_buyprice;
            }

            $pax = _rates_calculator_lookup_rates_units_get_pax_details($adinx, $arr_params, $arr_group_children);

            $_workings = "$normal_workings => Pax #$adinx " . $pax["adch"] . " <b>" . $pax["age"] . "yrs</b> = $currency_buy $per_person_buyprice";

            //record adult rates for each adult:
            //apply eci for that pax if any
            _rates_calculator_apply_rates_eci_percentage($per_person_buyprice, $arr_eci, $_workings, $currency_buy);

            //apply spo percent discount for that pax if any                    
            _rates_calculator_apply_spo_discount_percentage($per_person_buyprice, $_workings, $arr_params, $pax["adch"], $pax["age"], $pax["bride_groom"], "ROOM", $this_date);


            $arr[] = array("MSG" => $_workings, "COSTINGS" => $per_person_buyprice,
                "ADCH" => $pax["adch"],
                "AGE" => $pax["age"],
                "BRIDEGROOM" => $pax["bride_groom"]);
        }
    }


    return $arr;
}

function _rates_calculator_lookup_rates_units_get_pax_details($adinx, $arr_params, $arr_group_children) {
    $arr_adults = $arr_params["adults"];

    if ($adinx <= count($arr_adults)) {
        $pax = $arr_adults[$adinx - 1];
        $pax["adch"] = "ADULT";
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
            return $pax;
        } else {
            $adinx -= count($arr_temp_children);
        }

        $a --;
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

function _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params, $arr_group_children, $normal_rates, $arr_eci, &$flg_extra_people, $this_date) {
    $arr = array();
    $currency_buy = $arr_params["currency_buy_code"];

    $arr_children_rules = $rules["date_childpolicies_rules"];

    $workings_children = "";

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings_children .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================


    for ($i = 0; $i < count($arr_group_children); $i++) {
        $arr_children = $arr_group_children[$i]["CHILDREN"];
        $age_from = $arr_group_children[$i]["AGFROM"];
        $age_to = $arr_group_children[$i]["AGTO"];

        for ($idx = 1; $idx <= count($arr_children); $idx++) {
            $flg_extra_people = true;

            $child_age = $arr_children[$idx - 1]["age"];

            $basis = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "basis", $idx, $age_from, $age_to);
            $value = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "value", $idx, $age_from, $age_to);


            if ($basis == "%") {
                $percentage = 0;
                if ($value > 0) {
                    $percentage = round(($value / 100) * $normal_rates, 2);
                }

                $rates_children = $percentage;
                $workings_children .= "<b>EXTRA PAX</b>: Ch #$idx (<b>$child_age yrs</b>) = <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any                    
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);


                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            } else if ($basis == "FLAT") {

                $rates_children = $value;
                $workings_children .= "<b>EXTRA PAX</b>: Ch #$idx (<b>$child_age yrs</b>) = $currency_buy $value";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any                    
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);

                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            } else {
                $rates_children = 0;
                $workings_children .= "<b>EXTRA PAX</b>: Ch #$idx (<b>$child_age yrs</b>) <font color='orange'>NO RATES DEFINED...</font>";

                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            }
        }
    }

    return $arr;
}

function _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates, $arr_eci, $this_date) {
    $arr = array();
    $rates = 0;
    $workings = "";

    $currency_buy = $arr_params["currency_buy_code"];
    $arr_adults = $arr_params["adults"];

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================



    $arr_adult_rules = $rules["date_adultpolicies_rules"];

    //get the basis and value for each extra adult
    for ($i = 1; $i <= $adult; $i++) {

        $basis = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_basis", $i);
        $value = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_value", $i);

        $adult_index = count($arr_adults);
        $adult_pax = $arr_adults[$adult_index - 1];

        if ($basis == "%") {
            $percentage = 0;
            if ($value > 0) {
                $percentage = round(($value / 100) * $normal_rates, 2);
            }
            $rates = $percentage;
            $workings .= "<b>EXTRA PAX</b>: Ad #$adult_index: <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";
        } else if ($basis == "FLAT") {
            $rates = $value;
            $workings .= "<b>EXTRA PAX</b>: Ad #$adult_index: $currency_buy $value";
        }

        //apply eci for that extra adult if any
        _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

        //apply spo percent discount for that extra adult if any                    
        _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $adult_pax["age"], $adult_pax["bride_groom"], "ROOM", $this_date);


        $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
            "ADCH" => "ADULT",
            "AGE" => $adult_pax["age"],
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
            $num_non_foc_pax ++;
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
    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $adch = $arr[$i]["ADCH"];
        $age = $arr[$i]["AGE"];
        $bridegroom = $arr[$i]["BRIDEGROOM"];


        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_rollover;
            } else {
                $pax_rollover = $roll_over_value - $cumul_applied;
            }
            $applied_to ++;


            $msg .= "<br> + (<font color='blue'><b>ROLLOVER</b>: </font> FLAT PNI : $currency_buy $roll_over_value &#247; $num_non_foc_pax = $currency_buy $pax_rollover per Non FOC pax)";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_rollover, $msg, $arr_params, $adch, $age, $bridegroom, "ROOM", $this_date);

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


    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con, $spo_contract);
    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);

    if (!is_null($rules)) {
        $arr_singleparent_rules = $rules["date_singleparentpolicies_rules"];
        $arr_adultpolicies_rules = $rules["date_adultpolicies_rules"];

        $arr_age_ranges = _rates_calculator_group_single_parent_ageranges($arr_singleparent_rules);

        //for each age_range in arr_age_ranges, check if children ages match        
        //first check for exact match
        $the_age_range = _rates_calculator_single_parent_exact_match_children($arr_age_ranges, $arr_group_children);
        if ($the_age_range == "") {
            //if no exact match found, look for next best match
            $the_age_range = _rates_calculator_single_parent_nextbest_match_children($arr_age_ranges, $arr_group_children);
        }


        if ($the_age_range == "") {
            //really no rates defined for single parent!
            $flg_got_rates = false;
            $arr[] = array("MSG" => "NO SINGLE PARENT RATES", "COSTINGS" => array());
        } else {

            $flg_got_rates = true;
            $rules_age_range = _rates_calculator_single_parent_get_rules_by_agerange($arr_singleparent_rules, $the_age_range);

            //calculate children rates
            $arr_children_rates = _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules_age_range, $arr_params, $arr_adultpolicies_rules, $arr_eci, $this_date);
            $arr = array_merge($arr, $arr_children_rates);

            //calculate adult rates
            $arr_adult_rates = _rates_calculator_lookup_single_parent_parent_rates($rules_age_range, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci, $this_date);
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
            _rates_calculator_apply_spo_discount_percentage($adult_buyprice, $_workings, $arr_params, "ADULT", $pax["age"], $pax["bride_groom"], "ROOM", $this_date);

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

        $arr[] = array("MSG" => "NO RATES FOUND FOR THIS DATE", "COSTINGS" => array());
    }


    return $arr;
}

function _rates_calculator_calc_children($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $con, $arr_eci, $this_date, $flg_looked_at_single_parent_first, $spo_contract) {

    //regroup each child in $children by age groups defined in the contract
    $arr = array();

    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract);

    //for each age group, get children that fall within that range
    for ($a = 0; $a < count($arr_age_groups); $a++) {
        $age_from = $arr_age_groups[$a]["AGEFROM"];
        $age_to = $arr_age_groups[$a]["AGETO"];

        $arr_temp_children = array();
        for ($i = 0; $i < count($children); $i++) {
            if ($age_from <= $children[$i]["age"] && $children[$i]["age"] <= $age_to) {
                $arr_temp_children[] = $children[$i];
            }
        }

        //now calculate the rates for all children in that age group
        if (count($arr_temp_children) > 0) {
            $_arr = _rates_calculator_calc_children_by_agegroup($arr_temp_children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first);
            $arr = array_merge($arr, $_arr);
        }
    }

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

function _rates_calculator_calc_children_by_agegroup($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first) {



    $arr_sharing = array();
    $arr_single = array();
    $arr_final = array();

    //===============================================================
    //split children sharing or single
    for ($i = 0; $i < count($children); $i++) {
        if ($children[$i]["sharing_own"] == "SHARING") {
            $arr_sharing[] = $children[$i];
        } else {
            $arr_single[] = $children[$i];
        }
    }

    //===============================================================
    //get sharing children rates
    $_arr = _rates_calculator_calculate_children_rates("SHARING", $arr_sharing, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first);
    $arr_final = array_merge($arr_final, $_arr);

    //===============================================================
    //get single children rates    
    $_arr = _rates_calculator_calculate_children_rates("SINGLE", $arr_single, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first);
    $arr_final = array_merge($arr_final, $_arr);
    //===========================================================================

    return $arr_final;
}

function _rates_calculator_calculate_children_rates($sharing_single, $arr_children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci, $this_date, $flg_looked_at_single_parent_first) {

    $single_parent_comments = "";
    if ($flg_looked_at_single_parent_first) {
        $single_parent_comments = "<font color='orange'>NO <b>SINGLE PARENT</b> RATES FOUND!</font> REVERTING TO <B>NORMAL RATES</B>...<br>";
    }

    $currency_buy = $arr_params["currency_buy_code"];

    $arr = array();
    $workings_spo = "";
    $child_index = count($arr_children);
    $rates_children = 0;

    //==============================FLAT RATE SPO? ================================
    if ($arr_params["flat_rate_spo_apply"]["APPLY_SPO_FLAT_RATE"]) {
        //clearly SPO FLAT RATES will take over for this date
        $workings_spo .= $arr_params["flat_rate_spo_apply"]["COMMENTS"];
    }
    //=============================================================================


    while ($child_index > 0) {
        $child_age = $arr_children[$child_index - 1]["age"];
        //get the basis and value for that childindex + age combination

        $arr_lkup = _rates_calculator_lookup_child_basis_value($sharing_single, $child_index, $child_age, $arr_childrenpolicies_rules);
        $basis = $arr_lkup["BASIS"];
        $value = $arr_lkup["VALUE"];
        $status = $arr_lkup["STATUS"];

        if ($status == "NO_RATES") {
            $workings = "$workings_spo $single_parent_comments (CH #$child_index {$child_age}yr NO RATES) => ";

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
                $workings = "$workings_spo $single_parent_comments (CH #$child_index {$child_age}yr SNGL $currency_buy $value) ";
                $rates_children = $value;

                $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                //stop here
                break;
            } else if ($basis == "FLAT") {
                //here just take the flat rate
                $workings = "$workings_spo $single_parent_comments (CH #$child_index {$child_age}yr FLAT $currency_buy $value) ";
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
                $workings = "$workings_spo $single_parent_comments (CH #$child_index {$child_age}yr $value% of AD $currency_buy $rates_adult = $currency_buy $child_rate_value)";

                $arr[] = array("WORKINGS" => $workings, "RATES" => $child_rate_value, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                //stop here
                break;
            } else if ($basis == "DOUBLE") {
                //implies take the adult double rate
                //here we are just taking the value attached to the child (good?)

                $workings = "$workings_spo $single_parent_comments (CH #$child_index $basis {$child_age}yr DBL $currency_buy $value) ";
                $rates_children = $value;

                $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 2);

                //stop here
                break;
            } else if ($basis == "TRIPLE") {
                //implies take the adult double rate
                //here we are just taking the value attached to the child (good?)

                $workings = "$workings_spo $single_parent_comments (CH #$child_index $basis {$child_age}yr TRPL $currency_buy $value) ";
                $rates_children = $value;

                $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 3);
                //stop here
                break;
            }
        }

        $child_index --;
    }

    //need to split where to split:

    $temp_arr = array();
    for ($i = 0; $i < count($arr); $i++) {
        $work = $arr[$i]["WORKINGS"];
        $rates = $arr[$i]["RATES"];
        $childindex = $arr[$i]["CHILDINDEX"];
        $split_between = $arr[$i]["TO_SPLIT_BETWEEN"];
        $child_age = $arr_children[$childindex - 1]["age"];
        
        if ($split_between == 0) {

            //apply eci for that child if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $work, $currency_buy);

            //apply spo percent discount for that child if any
            _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);


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

                //apply eci percentage for that child if any
                _rates_calculator_apply_rates_eci_percentage($ch_buyprice, $arr_eci, $msg, $currency_buy);

                //apply spo percent discount for that child if any
                _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);

                $temp_arr[] = array("MSG" => $msg,
                    "COSTINGS" => $ch_buyprice,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");

                $childindex --;
            }
        }
    }


    return $temp_arr;
}

function _rates_calculator_lookup_child_basis_value($sharing_single, $child_index, $child_age, $arr_childrenpolicies_rules) {
    $arr = array("BASIS" => "", "VALUE" => 0, "STATUS" => "NO_RATES");

    for ($i = 0; $i < count($arr_childrenpolicies_rules); $i++) {
        if ($arr_childrenpolicies_rules[$i]["rule_sharing_single"] == $sharing_single &&
                $arr_childrenpolicies_rules[$i]["rule_category"] == $child_index) {
            $arr_rule_policy = $arr_childrenpolicies_rules[$i]["rule_policy"];

            for ($j = 0; $j < count($arr_rule_policy); $j++) {
                $policy_category = strtoupper($arr_rule_policy[$j]["policy_category"]);
                $agfrom = $arr_rule_policy[$j]["policy_units_additional_child_agefrom"];
                $agto = $arr_rule_policy[$j]["policy_units_additional_child_ageto"];

                if ($agfrom <= $child_age && $child_age <= $agto) {
                    $arr_policy_values = $arr_rule_policy[$j]["policy_values"];
                    for ($k = 0; $k < count($arr_policy_values); $k++) {
                        $value_value = $arr_policy_values[$k]["value_value"];
                        $arr[$policy_category] = $value_value;

                        $basis = trim($arr["BASIS"]);
                        $basis = str_replace(chr(194) . chr(160), "", $basis);
                        if ($basis != "") {
                            $arr["STATUS"] = "OK";
                        }

                        //return $arr;
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_calc_adult_recur($adult, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params) {
    if ($adult == 0) {
        //base case
        return array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "NO RATES DEFINED");
    } else {

        $arr = _rates_calculator_lookup_adult_rates($adult, $arr_adultpolicies_rules, $arr_params);
        $val = $arr["VAL"];
        $msg = $arr["MSG"];
        $recur = $arr["RECUR"];

        $arr_adult_workings["RATES_ADULT"] += $val;
        $arr_adult_workings["WORKINGS_ADULT"] .= "$msg";

        if ($recur) {
            $adult --;
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

                $workings = "<font color='blue'><b>EXTRA MANDATORY MEAL</b>: $extra_extra_name : </font>";

                //============= now adult ==========
                $extra_adult = $rules["extra_adult_count"];
                for ($a = 1; $a <= $adult; $a++) {

                    $ad_pax = $arr_params["adults"][$a - 1];

                    $msg = "$workings Ad #{$a} = $currency_buy $extra_adult";

                    //apply any percentage discount if applicable
                    _rates_calculator_apply_spo_discount_percentage($extra_adult, $msg, $arr_params,
                            "ADULT", $ad_pax["age"], $ad_pax["bride_groom"],
                            "EXTRA_MEAL_SUPPLEMENT", $this_date);

                    $arr[] = array("MSG" => $msg, "COSTINGS" => $extra_adult,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }



                //============= and now children ==========
                $children_rules = $rules["extra_children"];
                $arr_children_result = _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name, $this_date);
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
                $adult_supp_price = $rules["meal_adult_count"];

                //=========================================================================
                //=========================================================================
                //for each adult:

                for ($a = 1; $a <= count($arr_params["adults"]); $a++) {

                    $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font> $workings_flat_rate  Ad #$a => $currency_buy $adult_supp_price";

                    //apply spo percent discount if any for that pax
                    $ad_pax = $arr_params["adults"][$a - 1];
                    _rates_calculator_apply_spo_discount_percentage($adult_supp_price, $workings, $arr_params,
                            "ADULT", $ad_pax["age"], $ad_pax["bride_groom"], "MEAL_SUPPLEMENT", $this_date);


                    $arr[] = array("MSG" => $workings,
                        "COSTINGS" => $adult_supp_price,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }

                //============= and now for each children ==========
                $children_rules = $rules["meal_children"];
                $arr_children_result = _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption, $this_date, $workings_flat_rate, $spo_contract);
                $arr = array_merge($arr, $arr_children_result);


                //=========================================================================
                //=========================================================================
            }
        }
    }



    return $arr;
}

function _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name, $this_date) {
    $workings = "";
    $arr = array();

    $workings = "<font color='blue'><b>EXTRA MANDATORY MEAL</b>: $extra_extra_name : </font>";

    $currency_buy = $arr_params["currency_buy_code"];

    //extra meal supplement always at CONTRACT level
    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, "CONTRACT");

    //for each age group, get children that fall within that range


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
                        _rates_calculator_apply_spo_discount_percentage($child_meal_rate, $msg, $arr_params,
                                "CHILDREN", $age, "",
                                "EXTRA_MEAL_SUPPLEMENT", $this_date);

                        $arr[] = array("MSG" => $msg, "COSTINGS" => $children_rules[$r]["child_count"],
                            "ADCH" => "CHILDREN",
                            "AGE" => $age,
                            "BRIDEGROOM" => "");
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption, $this_date, $workings_flat_rate, $spo_contract) {

    $arr = array();

    $currency_buy = $arr_params["currency_buy_code"];

    $arr_age_groups = _rates_calculator_get_children_agegroups($arr_params, $con, $spo_contract);

    //for each age group, get children that fall within that range


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
                    for ($ec = 1; $ec <= count($arr_temp_children); $ec ++) {

                        $age = $arr_temp_children[$ec - 1]["age"];
                        $child_meal_rate = $children_rules[$r]["child_count"];

                        $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font> $workings_flat_rate" .
                                " Ch #$ec {$age_from}-{$age_to}yrs => $currency_buy $child_meal_rate";

                        //apply spo percent discount if any for that pax
                        _rates_calculator_apply_spo_discount_percentage($child_meal_rate, $workings, $arr_params,
                                "CHILDREN", $age, "", "MEAL_SUPPLEMENT", $this_date);


                        $arr[] = array("MSG" => $workings, "COSTINGS" => $child_meal_rate,
                            "ADCH" => "CHILDREN",
                            "AGE" => $age,
                            "BRIDEGROOM" => "");
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_group_single_parent_ageranges($rules) {

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

function _rates_calculator_single_parent_get_rules_by_agerange($rules, $ag) {
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

function _rates_calculator_single_parent_exact_match_children($arr_age_ranges, $arr_group_children) {

    for ($i = 0; $i < count($arr_age_ranges); $i++) {

        $this_age_range = $arr_age_ranges[$i];

        $str = ";";
        for ($g = 0; $g < count($arr_group_children); $g++) {
            $arrchildren = $arr_group_children[$g]["CHILDREN"];
            if (count($arrchildren) > 0) {
                $agfrom = $arr_group_children[$g]["AGFROM"];
                $agto = $arr_group_children[$g]["AGTO"];
                $str .= "{$agfrom}_$agto;";
            }
        }

        if ($this_age_range == $str) {
            return $this_age_range;
        }
    }

    return "";
}

function _rates_calculator_single_parent_nextbest_match_children($arr_age_ranges, $arr_group_children) {
    for ($i = 0; $i < count($arr_age_ranges); $i++) {

        $this_age_range = $arr_age_ranges[$i];

        $flg_ok = true;
        for ($g = 0; $g < count($arr_group_children); $g++) {
            //for each children, check if the age range is satisfied in the $this_age_range

            $agfrom = $arr_group_children[$g]["AGFROM"];
            $agto = $arr_group_children[$g]["AGTO"];
            if (count($arr_group_children[$g]["CHILDREN"]) > 0) {
                //check if the agefrom and ageto are in $this_age_range
                $flg_ok = _rates_calculator_single_parent_nextbest_match_children_in_range($agfrom, $agto, $this_age_range);
            }
        }

        if ($flg_ok) {
            return $this_age_range;
        }
    }

    return "";
}

function _rates_calculator_single_parent_nextbest_match_children_in_range($agfrom, $agto, $this_age_range) {

    //$this_age_range can be in format: ;0_2;3_11;

    $arr_age_ranges = explode(";", $this_age_range);
    for ($i = 0; $i < count($arr_age_ranges); $i++) {
        $inner_age_range = trim($arr_age_ranges[$i]);
        if ($inner_age_range != "") {
            $arr_age_from_to = explode("_", $inner_age_range);
            if (count($arr_age_from_to) == 2) {
                $_agfrom = $arr_age_from_to[0];
                $_agto = $arr_age_from_to[1];

                if ($_agfrom <= $agfrom && $agto <= $_agto) {
                    return true;
                }
            }
        }
    }


    return false;
}

function _rates_calculator_lookup_single_parent_parent_rates($rules, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci, $this_date) {

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
            $num_children --;
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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM", $this_date);

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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM", $this_date);


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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM", $this_date);


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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM", $this_date);

            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        }
    }

    return $arr;
}

function _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules, $arr_params, $arr_adultpolicies_rules, $arr_eci, $this_date) {

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

            $index --;
            $child_count_index ++;
        }

        //now need to know if there is a splitting of children fees between DOUBLE,1/2 DBL or TRIPLE children

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
                _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);


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
                    _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params, "CHILDREN", $child_age, "", "ROOM", $this_date);


                    $temp_arr[] = array("MSG" => $msg, "COSTINGS" => $ch_buyprice,
                        "ADCH" => "CHILDREN",
                        "AGE" => $child_age,
                        "BRIDEGROOM" => "");

                    $childindex --;
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
            $num_non_foc_pax ++;
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
    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $pax_adch = $arr[$i]["ADCH"];
        $pax_age = $arr[$i]["AGE"];
        $pax_bridegroom = $arr[$i]["BRIDEGROOM"];

        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_lco;
            } else {
                $pax_lco = $charge_value - $cumul_applied;
            }

            $applied_to ++;

            $msg .= " <br> ($workings = FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_lco per <b>Non FOC</b> pax) ";
            $costings = $pax_lco;

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($costings, $msg, $arr_params, $pax_adch, $pax_age, $pax_bridegroom, "ROOM", $this_date);

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
            $num_non_foc_pax ++;
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
    for ($i = 0; $i < count($arr); $i++) {
        $costings = $arr[$i]["COSTINGS"];
        $pax_adch = $arr[$i]["ADCH"];
        $pax_age = $arr[$i]["AGE"];
        $pax_bridegroom = $arr[$i]["BRIDEGROOM"];

        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_eci;
            } else {
                $pax_eci = $charge_value - $cumul_applied;
            }

            $applied_to ++;

            $msg .= "<br> + ($workings =  FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_eci per <b>Non FOC</b> pax) ";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_eci, $msg, $arr_params, $pax_adch, $pax_age, $pax_bridegroom, "ROOM", $this_date);

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
                            $applied_to ++;


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
    for ($i = 0; $i < count($arr); $i++) {
        $pax = $arr[$i];
        $rates = $pax["COSTINGS"];
        $msg = $pax["MSG"];
        $adch = $pax["ADCH"];
        $pax_age = $pax["AGE"];
        $pax_bridegroom = $pax["BRIDEGROOM"];

        _rates_calculator_apply_spo_discount_PPPN($rates, $msg, $arr_params, $adch, $pax_age, $pax_bridegroom, $room_nonroom);

        $arr[$i]["COSTINGS"] = $rates;
        $arr[$i]["MSG"] = $msg;
    }

    return $arr;
}

function _rates_calculator_apply_spo_discount_PPPN(&$rates, &$msg, $arr_params, $adult_children, $pax_age, $pax_bridegroom, $room_nonroom) {
    // $adult_child = {ADULT, CHILDREN}
    // $pax_age is the age of the pax in question
    // $pax_bridegroom is the marital status of the pax in question
    // $room_nonroom = {ROOM, NONROOM}

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $apply_discounts = $arr_params["lookupmode"]["DISCOUNTS"]; //true or false

    $currency_buy = $arr_params["currency_buy_code"];

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $disc_ad_ch = $discount_item["AD_CH"]; //is discount for children or adults or both
        $disc_bd_gm = $discount_item["BRIDE_GROOM"]; //is discount for bride, groom or both
        $disc_ag_frm = $discount_item["AGE_FROM"]; //is discount for a specific age group
        $disc_ag_to = $discount_item["AGE_TO"]; //is discount for a specific age group
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount


        if ($apply_discounts) {
            if (($adult_children == "ADULT" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "ADULT")) ||
                    $adult_children == "CHILDREN" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "CHILDREN") ||
                    $disc_ad_ch == "") {
                //passed check for adult or children applicable discounts

                if (_rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to)) {
                    //passed age limits check

                    if (_rates_calculator_apply_spo_discount_test_bride_groom($pax_bridegroom, $disc_bd_gm, $adult_children)) {

                        //passed bride groom checks
                        //finally apply the discount when it flat PPPN
                        if ($disc_value > 0) {

                            if (($disc_type == "FLAT_PPPN" && $room_nonroom == "ROOM")) {

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

    return;
}

function _rates_calculator_apply_spo_discount_percentage(&$rates, &$msg, $arr_params, $adult_children, $pax_age, $pax_bridegroom, $room_nonroom, $this_date) {
    // $adult_child = {ADULT, CHILDREN}
    // $pax_age is the age of the pax in question
    // $pax_bridegroom is the marital status of the pax in question
    // $room_nonroom = {ROOM, NONROOM}
    // $this_date = yyyy-mm-dd : the date being calculated on

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $currency_buy = $arr_params["currency_buy_code"];
    $apply_discounts = $arr_params["lookupmode"]["DISCOUNTS"]; //true or false

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $disc_ad_ch = $discount_item["AD_CH"]; //is discount for children or adults or both
        $disc_bd_gm = $discount_item["BRIDE_GROOM"]; //is discount for bride, groom or both
        $disc_ag_frm = $discount_item["AGE_FROM"]; //is discount for a specific age group
        $disc_ag_to = $discount_item["AGE_TO"]; //is discount for a specific age group
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount
        $apply_to_dates = $discount_item["APPLY_TO_DATES"]; //any array of date filters?
        //if $apply_to_dates is not blank, then check if this_date is in it
        $flg_date_check_passed = false;

        if (count($apply_to_dates) > 0) {
            //check if ALL in array
            //else check for filters

            if (in_array("ALL", $apply_to_dates)) {
                $flg_date_check_passed = true;
            } else {

                for ($x = 0; $x < count($apply_to_dates); $x++) {
                    if ($apply_to_dates[$x] == $this_date) {
                        $flg_date_check_passed = true;
                    }
                }
            }
        }

        if ($apply_discounts) {
            if ($flg_date_check_passed) {
                if (($adult_children == "ADULT" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "ADULT")) ||
                        $adult_children == "CHILDREN" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "CHILDREN") ||
                        $disc_ad_ch == "") {
                    //passed check for adult or children applicable discounts

                    if (_rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to)) {
                        //passed age limits check

                        if (_rates_calculator_apply_spo_discount_test_bride_groom($pax_bridegroom, $disc_bd_gm, $adult_children)) {

                            //passed bride groom checks
                            //finally apply the discount when it is percetage only
                            if ($disc_value > 0) {

                                if (($disc_type == "%ROOM" && $room_nonroom == "ROOM") ||
                                        ($disc_type == "%ALL")) {

                                    $disc_amt = round(($disc_value / 100) * $rates, 2);
                                    $msg .= "<br><font color='#BB3C94'> - (<b>SPO</b> => [ID:$spo_id $spo_type - $spo_name] = $disc_value% of $currency_buy $rates = $currency_buy $disc_amt)</font>";
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

    return;
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

function _rates_calculator_apply_spo_discount_test_age($pax_age, $disc_ag_frm, $disc_ag_to) {
    if ($disc_ag_frm == -1 && $disc_ag_to == -1) {
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
                $stay ++;
                $pay ++;
            } else {
                $stay = $arr_stays_pays["STAYS"];
                $pay = $arr_stays_pays["PAYS"];
            }

            if ($idx == $num_nights) {
                return array("STAYS" => $stay, "PAYS" => $pay, "FREE" => ($stay - $pay)); //free nights
            }

            $idx ++;
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

            $idx --;
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

    $arr_daily_idx["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 4</font>: CAPACITY ADULT + CHILDREN SHARING. COMBINATION INDEX: <b>" . $capacity_test_adch["INDEX"] . "</b>",
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
            $free_nights_num_nights --;
        }

        $idx ++;
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

function _rates_calculator_process_SPO_free_nights(&$arr_spo_discounts, $arr_days, $arr_params, $con, $arr_capacity) {
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

            //sort the array as ascending
            function __sort_date_compare($a, $b) {
                $t1 = strtotime($a['datetime']);
                $t2 = strtotime($b['datetime']);
                return $t1 - $t2;
            }

            usort($arr_possible_dates, '__sort_date_compare');

            //get first n nights based on date
            //then place nights as first n nights
            for ($idx = 0; $idx < count($arr_days["DAILY"]); $idx++) {
                $this_date = $arr_days["DAILY"][$idx]["DATE"];

                if (_rates_calculator_is_date_inarray($this_date, $arr_possible_dates) && $free_nights_num_nights > 0) {
                    $arr_dates_free_nights[] = $this_date;
                    $free_nights_num_nights --;
                }
            }
        } else if ($spo_free_nights_start_end == "END") {

            //sort the array as descending
            function __sort_date_compare($a, $b) {
                $t1 = strtotime($a['datetime']);
                $t2 = strtotime($b['datetime']);
                return $t2 - $t1;
            }

            usort($arr_possible_dates, '__sort_date_compare');

            //last n nights based on date
            //then place nights as last n nights

            $idx = count($arr_days["DAILY"]) - 1;
            while ($idx >= 0) {
                $this_date = $arr_days["DAILY"][$idx]["DATE"];

                if (_rates_calculator_is_date_inarray($this_date, $arr_possible_dates) && $free_nights_num_nights > 0) {
                    $arr_dates_free_nights[] = $this_date;
                    $free_nights_num_nights --;
                }
                $idx --;
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

function _rates_calculator_getSPO_remove_duplicates($arr_spos, $types) {
    //remove a duplicate of types found in $types
    $arr_final_spos = array();

    $arr_type_counters = array();
    for ($i = 0; $i < count($arr_spos); $i++) {
        $template = $arr_spos[$i]["TEMPLATE"];
        if (isset($arr_type_counters["TEMPLATE"])) {
            $arr_type_counters[$template] ++;

            //check if need to remove
            if (!in_array($template, $types)) {
                //can add freely
                $arr_final_spos[] = $arr_spos[$i];
            }
        } else {
            $arr_type_counters[$template] = 0;
            $arr_final_spos[] = $arr_spos[$i];
        }
    }


    return $arr_final_spos;
}

function _rates_calculator_spo_search($arr_params, $con) {
    try {

        //will return a list of valid spos and a list of invalid spos with reasons why they failed

        $arr_spos = array();
        $arr_invalid_spos = array();

        //TODO: 
        //1. MINIMUM STAY OVERWRITING
        //2. SHOW MESSAGES FOR SPO

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
        $arr_invalid_spos = $arr_test_spos["INVALID_SPOS"];

        if (count($arr_spos) == 0) {
            //there are no SPOs followng 2nd test
            return array("OUTCOME" => "OK", "ARR_SPOS" => $arr_spos, "ARR_INVALID_SPOS" => $arr_invalid_spos);
        }


        //finally got list of all valid spos
        //last processing
        //1. can have at most one 1 free night spo in the list. other free nights spo are discarded
        $types = array("free_nights");
        $arr_spos = _rates_calculator_getSPO_remove_duplicates($arr_spos, $types);


        //2. get array of single spo and array of spos to be merged
        $arr_single_merged = _rates_calculator_getSPO_merge_cumulative($arr_spos, $con);
        $arr_single_spos = $arr_single_merged["SINGLE"];
        $arr_merged_spos = $arr_single_merged["MERGED"];
        $arr_invalid_merged_spos = $arr_single_merged["INVALID_MERGED"];

        //3. present SPO in array of objects as:    
        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
        $num_nights = _rates_calculator_get_numnights($checkin_date, $checkout_date);

        //create SPO objects from single spos     
        $arr_final_spos = array();
        $arr_final_single_spos = _rates_calculator_create_single_spos($arr_single_spos, $con, $num_nights);
        $arr_final_spos = array_merge($arr_final_spos, $arr_final_single_spos);


        //create SPO objects from merged spos
        $arr_final_merged_spos = _rates_calculator_create_merged_spos($arr_merged_spos, $con, $arr_spos);
        $arr_final_spos = array_merge($arr_final_spos, $arr_final_merged_spos);

        
        $arr_invalid_spos = array_merge($arr_invalid_spos,$arr_invalid_merged_spos);
        
        return array("OUTCOME" => "OK", "ARR_SPOS" => $arr_final_spos, "ARR_INVALID_SPOS" => $arr_invalid_spos);
    } catch (Exception $ex) {
        return array("OUTCOME" => "SPO_RATES_CALCULATOR: " . $ex->getMessage());
    }
}

function _rates_calculator_create_merged_spos($arr_merged_spos, $con, $arr_spos) {
    $arr_final_merged_spos = array();

    //remember, merged spos are of type:
    //discount + is percentage
    //early_booking + is percentage
    //family_offer + is percentage <-- careful, may need to further split by age groups
    //honeymoon + is percentage <-- careful, may need to further split if bride and groom different basis
    //long_stay + is percentage
    //senior_offer + is percentage
    //wedding_anniversary + is percentage <-- careful, may need to further split if bride and groom different basis
    //wedding_party + is percentage <-- careful, may need to further split if bride and groom different basis
    
    //Matrix looks like this:
    //Col A: Wedding anniversary, Wedding Party, Honey Moon
    //Col B: Discount, Early Booking, Long Stay, Senior Offer
    //Col C: Family Offers
    
    //Merge Case 1: create SPOs with sum % discounts for Adults.Groom AndOr Bride
    //              => ColA.wedding_anniversary.Groom AndOr Bride.% +
    //                 ColA.wedding_party.Groom AndOr Bride.% +
    //                 ColA.honeymoon.Groom AndOr Bride.% +
    //                 ColB.discount.% +
    //                 ColB.early_booking.% +
    //                 ColB.long_stay.% +
    //                 ColB.senior_offer.%
    //                
    //Merge Case 2: create SPOs with sum % discounts for Adults.NonGroomAndBride
    //              => ColB.discount.% +
    //                 ColB.early_booking.% +
    //                 ColB.long_stay.% +
    //                 ColB.senior_offer.%
    // 
    //Merge Case 3: Merge all % discounts for children
    //              => ColB.discount.% +
    //                 ColB.early_booking.% +
    //                 ColB.long_stay.% +
    //                 ColC.family_offer.%.split_per_age
    //                 
    //Merge Case 4: Merge all Value discounts for Adults.Groom AndOr Bride
    //              => ColA.wedding_anniversary.Groom AndOr Bride.Value +
    //                 ColA.wedding_party.Groom AndOr Bride.Value +
    //                 ColA.honeymoon.Groom AndOr Bride.Value
    //                 
    //                 
    //Merge Case 5: Merge all Value discounts for children
    //              => ColC.family_offer.Value.split_per_age
    //=========================================================================================================
    //CASE 1 AND 2 AND 3

    $arr_param_wedding = array("", "BOTH", "GROOM", "BRIDE"); //note that blank refers to NO wedding scenarios
    $arr_param_basis = array("%ROOM", "%ALL"); //all possible percentage basis
    $arr_param_ad_ch = array("AD", "CH"); //adult only, or children only or both

    for ($a = 0; $a < count($arr_param_wedding); $a++) {
        $pa = $arr_param_wedding[$a];

        for ($b = 0; $b < count($arr_param_basis); $b++) {
            $pb = $arr_param_basis[$b];

            for ($c = 0; $c < count($arr_param_ad_ch); $c++) {
                $pc = $arr_param_ad_ch[$c];

                $x = _rates_calculator_create_merged_spos_case123($arr_merged_spos, $arr_spos, $pa, $pb, $pc, $con);
                $arr_final_merged_spos = array_merge($arr_final_merged_spos, $x);
            }
        }
    }

    //=========================================================================================================
    //=========================================================================================================
    //CASE 4 AND 5
    $arr_param_wedding = array("", "BOTH", "GROOM", "BRIDE");
    $arr_param_basis = array("FLAT_PNI", "FLAT_PPPN"); //all possible flat basis
    $arr_param_ad_ch = array("AD", "CH"); //adult only, or children only or both

    for ($a = 0; $a < count($arr_param_wedding); $a++) {
        $pa = $arr_param_wedding[$a];

        for ($b = 0; $b < count($arr_param_basis); $b++) {
            $pb = $arr_param_basis[$b];

            for ($c = 0; $c < count($arr_param_ad_ch); $c++) {
                $pc = $arr_param_ad_ch[$c];

                $x = _rates_calculator_create_merged_spos_case45($arr_merged_spos, $arr_spos, $pa, $pb, $pc, $con);
                $arr_final_merged_spos = array_merge($arr_final_merged_spos, $x);
            }
        }
    }


    return $arr_final_merged_spos;
}

function _rates_calculator_create_merged_spos_case45($arr_merged_spos, $arr_spos, $both_groom_bride, $basis, $adch, $con) {
    //$basis is either FLAT_PNI or FLAT_PPPN
    //$both_groom_bride = "","BOTH", "GROOM", "BRIDE"
    //$adch = "AD","CH"
    //Merge Case 4,5

    $arr_final_merged_spos = array();

    for ($i = 0; $i < count($arr_merged_spos); $i++) {

        $sum_flat_discount = 0;
        $arr_spo_ids = explode(",",$arr_merged_spos[$i]["SPOIDS"]);
        $arr_spo_dates = $arr_merged_spos[$i]["DATES"];
        $arr_family_conditions = array();


        $merged_name = "";
        $merged_id = "";

        for ($j = 0; $j < count($arr_spo_ids); $j++) {
            $id = $arr_spo_ids[$j];
            $spo = _rates_calculator_lookupSPO_by_id($id, $arr_spos);
            if (!is_null($spo)) {
                $template = $spo["SPO_RW"]["template"];
                $name = $spo["SPO_RW"]["sponame"];

                if (($template == "wedding_anniversary" ||
                        $template == "wedding_party" ||
                        $template == "honeymoon") && $both_groom_bride != "" &&
                        $adch == "AD") {
                    if ($both_groom_bride == "BOTH" &&
                            $spo["SPO_RW"]["wedding_apply_discount_both_basis"] == $basis &&
                            $spo["SPO_RW"]["wedding_apply_discount_both"] == 1) {
                        $sum_flat_discount += $spo["SPO_RW"]["wedding_apply_discount_both_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    } else if ($both_groom_bride == "GROOM" &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom"] == 1 &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom_basis"] == $basis) {
                        $sum_flat_discount += $spo["SPO_RW"]["wedding_apply_discount_groom_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    } else if ($both_groom_bride == "BRIDE" &&
                            $spo["SPO_RW"]["wedding_apply_discount_bride"] == 1 &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom_basis"] == $basis) {
                        $sum_flat_discount += $spo["SPO_RW"]["wedding_apply_discount_bride_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    }
                } else if ($template == "family_offer" && $adch == "CH" && $both_groom_bride == "") {
                    $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                    $merged_id .= ($merged_id != "" ? "," : "") . $id;

                    //split spo for each condition with a percentage
                    $arr_family_conditions = _rates_calculator_create_merged_spos_case_family_offer($con, $id, $basis);
                }
            }
        }


        if ($sum_flat_discount > 0) {

            if (count($arr_family_conditions == 0)) {
                //there are no family splits

                $s = _rates_calculator_create_spo_obj($con, $merged_id, $merged_name, "DISCOUNT", $adch,
                        $both_groom_bride, -1, -1, $basis,
                        $sum_flat_discount, $arr_spo_dates, 0,
                        "", array(), true);
                $arr_final_merged_spos[] = $s;
            } else {
                //family splits detected, need to replicate the spo for each age range
                for ($f = 0; $f < count($arr_family_conditions); $f++) {
                    $agefrom = $arr_family_conditions[$f]["AGEFROM"];
                    $ageto = $arr_family_conditions[$f]["AGETO"];
                    $amt = $arr_family_conditions[$f]["AMT"];
                    $total_flat_discount = $sum_flat_discount + $amt;

                    $s = _rates_calculator_create_spo_obj($con, $merged_id, $merged_name, "DISCOUNT", "CH",
                            $both_groom_bride, $agefrom, $ageto, $basis,
                            $total_flat_discount, $arr_spo_dates, 0,
                            "", array(), true);
                    $arr_final_merged_spos[] = $s;
                }
            }
        }
    }

    return $arr_final_merged_spos;
}

function _rates_calculator_create_merged_spos_case123($arr_merged_spos, $arr_spos, $both_groom_bride, $basis, $adch, $con) {
    //$basis is either %ROOM or % ALL
    //$both_groom_bride = "", "BOTH", "GROOM", "BRIDE"
    //$adch = "AD","CH"
    //Merge Case 1,2,3

    $arr_final_merged_spos = array();

    for ($i = 0; $i < count($arr_merged_spos); $i++) {

        $sum_per_discount = 0;
        $arr_spo_ids = explode(",", $arr_merged_spos[$i]["SPOIDS"]);
        $arr_spo_dates = $arr_merged_spos[$i]["DATES"];
        $senior_aged_from = -1;
        $arr_family_conditions = array();


        $merged_name = "";
        $merged_id = "";

        for ($j = 0; $j < count($arr_spo_ids); $j++) {
            $id = $arr_spo_ids[$j];
            $spo = _rates_calculator_lookupSPO_by_id($id, $arr_spos);
            if (!is_null($spo)) {
                $template = $spo["SPO_RW"]["template"];
                $name = $spo["SPO_RW"]["sponame"];

                //=======================================================================
                if (($template == "discount" || $template == "early_booking" ||
                        $template == "long_stay") &&
                        $spo["SPO_RW"]["discount_basis"] == $basis) {
                    $sum_per_discount += $spo["SPO_RW"]["discount_value"];

                    $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                    $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    
                } 
                //=======================================================================
                else if ($template == "senior_offer" &&
                        $spo["SPO_RW"]["senior_discount_basis"] == $basis &&
                        $adch == "AD") {
                    $sum_per_discount += $spo["SPO_RW"]["senior_discount_value"];
                    if ($spo["SPO_RW"]["senior_guests_aged_from"] != "") {
                        $senior_aged_from = $spo["SPO_RW"]["senior_guests_aged_from"];
                    }

                    $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                    $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    
                } 
                //=======================================================================
                else if (($template == "wedding_anniversary" ||
                        $template == "wedding_party" ||
                        $template == "honeymoon") && $both_groom_bride != "" &&
                        $adch == "AD") {
                    
                    if ($both_groom_bride == "BOTH" &&
                            $spo["SPO_RW"]["wedding_apply_discount_both_basis"] == $basis &&
                            $spo["SPO_RW"]["wedding_apply_discount_both"] == 1) {
                        $sum_per_discount += $spo["SPO_RW"]["wedding_apply_discount_both_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    } else if ($both_groom_bride == "GROOM" &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom"] == 1 &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom_basis"] == $basis) {
                        $sum_per_discount += $spo["SPO_RW"]["wedding_apply_discount_groom_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    } else if ($both_groom_bride == "BRIDE" &&
                            $spo["SPO_RW"]["wedding_apply_discount_bride"] == 1 &&
                            $spo["SPO_RW"]["wedding_apply_discount_groom_basis"] == $basis) {
                        $sum_per_discount += $spo["SPO_RW"]["wedding_apply_discount_bride_value"];

                        $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                        $merged_id .= ($merged_id != "" ? "," : "") . $id;
                    }
                } 
                //=======================================================================
                else if ($template == "family_offer" && $adch == "CH" &&
                        $both_groom_bride == "") {
                    $merged_name .= ($merged_name != "" ? " + " : "") . $name;
                    $merged_id .= ($merged_id != "" ? "," : "") . $id;

                    //split spo for each condition with a percentage
                    $arr_family_conditions = _rates_calculator_create_merged_spos_case_family_offer($con, $id, $basis);
                }
            }
        }


        if ($sum_per_discount > 0) {
            $sum_per_discount = ($sum_per_discount > 100 ? 100 : $sum_per_discount);

            if (count($arr_family_conditions == 0)) {
                //there are no family splits


                $s = _rates_calculator_create_spo_obj($con, $merged_id, $merged_name,
                        "DISCOUNT", $adch,
                        $both_groom_bride, $senior_aged_from,
                        -1, $basis,
                        $sum_per_discount, $arr_spo_dates, 0,
                        "", array(), true);
                $arr_final_merged_spos[] = $s;
            } else {
                //family splits detected, need to replicate the spo for each age range
                for ($f = 0; $f < count($arr_family_conditions); $f++) {
                    $agefrom = $arr_family_conditions[$f]["AGEFROM"];
                    $ageto = $arr_family_conditions[$f]["AGETO"];
                    $percentage = $arr_family_conditions[$f]["AMT"];
                    $percentage_discount = $sum_per_discount + $percentage;

                    $percentage_discount = ($percentage_discount > 100 ? 100 : $percentage_discount);




                    $s = _rates_calculator_create_spo_obj($con, $merged_id, $merged_name, "DISCOUNT",
                            "CH",
                            $both_groom_bride, $agefrom, $ageto, $basis,
                            $sum_per_discount, $arr_spo_dates, 0,
                            "", array(), true);
                    $arr_final_merged_spos[] = $s;
                }
            }
        }
    }

    return $arr_final_merged_spos;
}

function _rates_calculator_create_single_spos($arr_single_spos, $con, $num_nights) {

    //create an array of spo objects based on the array $arr_single_spos
    //family_offer <-- careful, may need to further split by age groups
    //honeymoon <-- careful, may need to further split if bride and groom different basis
    //wedding_anniversary <-- careful, may need to further split if bride and groom different basis
    //wedding_party  <-- careful, may need to further split if bride and groom different basis

    $arr_final_single_spos = array();

    for ($i = 0; $i < count($arr_single_spos); $i++) {
        $dates = $arr_single_spos[$i]["DATES"];
        $spo_rw = $arr_single_spos[$i]["SPO_RW"];
        $template = $spo_rw["template"];
        $id = $spo_rw["id"];
        $sponame = $spo_rw["sponame"];
        $spocode = $spo_rw["spocode"];

        //====================================================================
        if ($template == "meals_upgrade") {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_UPGRADES", "",
                    "", -1, -1, "", 0, $dates, 0, "", array(), false);
            $arr_spo_discounts[] = $s;
        }
        //====================================================================
        else if ($template == "free_upgrade") {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_UPGRADES", "", "", -1, -1, "",
                    0, $dates, 0, "", array(), false);

            $arr_spo_discounts[] = $s;
        }
        //====================================================================
        else if ($template == "free_nights") {
            $spo_free_nights_start_end = strtoupper($spo_rw["free_nights_placed_at"]);
            $free_nights_num_nights = _rates_calculator_spo_calculate_num_free_nights($spo_rw, $con, $num_nights);

            //$dates : to be reordered later on depending on place start,place end, place lowest
            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FREE_NIGHTS", "BOTH",
                    "", -1, -1, "%ROOM", 100, $dates, $free_nights_num_nights,
                    $spo_free_nights_start_end, array(), false);
            $arr_spo_discounts[] = $s;
        }
        //====================================================================
        else if ($template == "flat_rate") {

            $arr_fr = _spo_loadspo($con, $id, $spo_rw["hotel_fk"]);

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "FLAT_RATES", "",
                    "", -1, -1, "", 0, $dates, "",
                    "", $arr_fr["FLAT_RATES_CAPACITY"], false);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "discount") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value, $dates, "", "", array(), false);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "early_booking") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value, $dates, "", "", array(), false);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "family_offer") {
            //CAREFUL
            //WILL NEED TO SPLIT IF MORE THAN ON AGE GROUPS
            $arr_family_offer = _rates_calculator_create_single_spos_family_offer($arr_single_spos[$i], $con);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_family_offer);
        }
        //====================================================================
        else if ($template == "honeymoon") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATE
            $arr_wedding_offer = _rates_calculator_create_single_spos_wedding_offer($arr_single_spos[$i]);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
        //====================================================================
        else if ($template == "long_stay") {
            $discount_basis = $spo_rw["discount_basis"];
            $discount_value = $spo_rw["discount_value"];


            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "",
                    "", -1, -1, $discount_basis, $discount_value, $dates, "", "", array(), false);

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
                    "", array(), false);

            $arr_final_single_spos[] = $s;
        }
        //====================================================================
        else if ($template == "wedding_anniversary") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATE
            $arr_wedding_offer = _rates_calculator_create_single_spos_wedding_offer($arr_single_spos[$i]);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
        //====================================================================
        else if ($template == "wedding_party") {
            //CAREFUL
            //WILL NEED TO SPLIT IF BRIDE AND GROOM SEPARATE
            $arr_wedding_offer = _rates_calculator_create_single_spos_wedding_offer($arr_single_spos[$i]);
            $arr_final_single_spos = array_merge($arr_final_single_spos, $arr_wedding_offer);
        }
    }

    return $arr_final_single_spos;
}

function _rates_calculator_create_single_spos_wedding_offer($the_wedding_spo) {
    $arr_spo_return = array();

    $dates = $the_wedding_spo["DATES"];
    $rw = $the_wedding_spo["SPO_RW"];
    $template = $rw["template"];
    $id = $rw["id"];
    $sponame = $rw["sponame"];
    $spocode = $rw["spocode"];

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
                "", array(), false);
        $arr_spo_return[] = $s;
    } else {
        //will need to split into two separate offers

        if ($wedding_apply_discount_bride == 1) {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                    "BRIDE", -1, -1, $wedding_apply_discount_bride_basis,
                    $wedding_apply_discount_bride_value, $dates, "",
                    "", array(), false);

            $arr_spo_return[] = $s;
        }
        if ($wedding_apply_discount_groom == 1) {

            $s = _rates_calculator_create_spo_obj($con, $id, $sponame, "DISCOUNT", "AD",
                    "GROOM", -1, -1, $wedding_apply_discount_groom_basis,
                    $wedding_apply_discount_groom_value, $dates, "",
                    "", array(), false);

            $arr_spo_return[] = $s;
        }
    }

    return $arr_spo_return;
}

function _rates_calculator_create_merged_spos_case_family_offer($con, $id, $basis) {
    $arr_ranges = array();

    $sql = "select * from 
            tblspecial_offer_familyoffer_childage_discount 
            where spo_fk = :spoid AND discount_percentage_value =:basis";

    $query = $con->prepare($sql);
    $query->execute(array(":spoid" => $id, ":basis" => $basis));

    while ($rwch = $query->fetch(PDO::FETCH_ASSOC)) {

        $age_from = $rwch["child_age_from"];
        $age_to = $rwch["child_age_to"];
        $discount_value = $rwch["discount_value"];

        $arr_ranges[] = array("AGEFROM" => $age_from,
            "AGETO" => $age_to,
            "AMT" => $discount_value);
    }

    return $arr_ranges;
}

function _rates_calculator_create_single_spos_family_offer($the_family_spo, $con) {
    $arr_spo_return = array();

    $dates = $the_family_spo["DATES"];
    $rw = $the_family_spo["SPO_RW"];
    $template = $rw["template"];
    $id = $rw["id"];
    $sponame = $rw["sponame"];
    $spocode = $rw["spocode"];

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
                "", $age_from, $age_to, $discount_basis, $discount_value, $dates, "", "", array(), false);

        $arr_spo_return[] = $s;
    }


    return $arr_spo_return;
}

function _rates_calculator_getSPO_cumulative_eligible_family_offer($spoid, $con) {
    //check if there is at least one children age discounts with percentage
    $sql = "select * from 
            tblspecial_offer_familyoffer_childage_discount 
            where spo_fk = :spoid
            and discount_percentage_value IN ('%ROOM', '%ALL')";

    $query = $con->prepare($sql);
    $query->execute(array(":spoid" => $spoid));
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return true;
    }

    return false;
}

function _rates_calculator_getSPO_cumulative_eligible($spo_rw, $con) {
    //only:
    //discount + is percentage
    //early_booking + is percentage
    //family_offer + is percentage <-- careful, may need to further split by age groups
    //honeymoon + is percentage <-- careful, may need to further split if bride and groom different basis
    //long_stay + is percentage
    //senior_offer + is percentage
    //wedding_anniversary + is percentage <-- careful, may need to further split if bride and groom different basis
    //wedding_party + is percentage <-- careful, may need to further split if bride and groom different basis
    
    
    $template = $spo_rw["template"];

    //====================================================================
    if ($template == "discount") {
        $discount_basis = $spo_rw["discount_basis"];
        if (strpos($discount_basis, "%") !== false) {
            return true;
        }
    }
    //====================================================================
    else if ($template == "early_booking") {
        $discount_basis = $spo_rw["discount_basis"];
        if (strpos($discount_basis, "%") !== false) {
            return true;
        }
    }
    //====================================================================
    else if ($template == "family_offer") {
        return _rates_calculator_getSPO_cumulative_eligible_family_offer($spo_rw["id"], $con);
    }
    //====================================================================
    else if ($template == "honeymoon") {
        return _rates_calculator_getSPO_cumulative_eligible_wedding($spo_rw);
    }
    //====================================================================
    else if ($template == "long_stay") {
        $discount_basis = $spo_rw["discount_basis"];
        if (strpos($discount_basis, "%") !== false) {
            return true;
        }
    }
    //====================================================================
    else if ($template == "senior_offer") {
        $discount_basis = $spo_rw["senior_discount_basis"];
        if (strpos($discount_basis, "%") !== false) {
            return true;
        }
    }
    //====================================================================
    else if ($template == "wedding_anniversary") {
        return _rates_calculator_getSPO_cumulative_eligible_wedding($spo_rw);
    }
    //====================================================================
    else if ($template == "wedding_party") {
        return _rates_calculator_getSPO_cumulative_eligible_wedding($spo_rw);
    }
    //====================================================================

    return false;
}

function _rates_calculator_getSPO_cumulative_eligible_wedding($spo_rw) {
    $wedding_apply_discount_both = $spo_rw["wedding_apply_discount_both"];
    $wedding_apply_discount_bride = $spo_rw["wedding_apply_discount_bride"];
    $wedding_apply_discount_groom = $spo_rw["wedding_apply_discount_groom"];
    $wedding_apply_discount_both_basis = $spo_rw["wedding_apply_discount_both_basis"];
    $wedding_apply_discount_bride_basis = $spo_rw["wedding_apply_discount_bride_basis"];
    $wedding_apply_discount_groom_basis = $spo_rw["wedding_apply_discount_groom_basis"];

    if ($wedding_apply_discount_both == 1) {
        if (strpos($wedding_apply_discount_both_basis, "%") !== false) {
            return true;
        }
    } else {

        if ($wedding_apply_discount_bride == 1) {
            if (strpos($wedding_apply_discount_bride_basis, "%") !== false) {
                return true;
            }
        }
        if ($wedding_apply_discount_groom == 1) {
            if (strpos($wedding_apply_discount_groom_basis, "%") !== false) {
                return true;
            }
        }
    }

    return false;
}

function _rates_calculator_getSPO_merge_cumulative($arr_spos, $con) {

    //will return an array of single spos and an array of spos to be merged

    if (count($arr_spos) == 0) {
        return array("SINGLE" => $arr_spos, "MERGED" => array(), "INVALID_MERGED"=>array()); //no spos to be merged at all
    }

    //==========================================================================================
    //==========================================================================================
    
    //1. get list of eligible spoids where template is only:
    //discount + is percentage
    //early_booking + is percentage
    //family_offer + is percentage <-- careful, may need to further split by age groups
    //honeymoon + is percentage <-- careful, may need to further split if bride and groom different basis
    //long_stay + is percentage
    //senior_offer + is percentage
    //wedding_anniversary + is percentage <-- careful, may need to further split if bride and groom different basis
    //wedding_party + is percentage <-- careful, may need to further split if bride and groom different basis


    $str_ids = "";
    $first = true;
    for ($i = 0; $i < count($arr_spos); $i++) {
        $spoid = $arr_spos[$i]["SPO_RW"]["id"];
        if (_rates_calculator_getSPO_cumulative_eligible($arr_spos[$i]["SPO_RW"], $con)) {
            if (!$first) {
                $str_ids .= ",";
            }
            $str_ids .= $spoid;
            $first = false;
        }
    }


    //===========================================================
    if (trim($str_ids) == "") {
        //no eligible spos with desired template to merge
        return array("SINGLE" => $arr_spos, "MERGED" => array(), "INVALID_MERGED"=>array()); //no spos to be merged at all
    }
    
    //==========================================================================================
    //==========================================================================================
    //2. get list of spo_links for these spos from id above
    $sql = "select ols.linkfk, group_concat(ols.spofk SEPARATOR ',') as spoids
            FROM tblspecial_offer_link_spos ols
            INNER JOIN tblspecial_offer_link sol on ols.linkfk = sol.id
            WHERE spofk in ($str_ids) 
            AND sol.active = 1 
            AND sol.deleted = 0
            AND ols.cumulative = 1
            GROUP BY ols.linkfk
            ORDER BY ols.linkfk
            ";

    $query = $con->prepare($sql);
    $query->execute();
    $arr_links = array();
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $arr_links[] = array("LINKID" => $rw["linkfk"], "SPOIDS" => $rw["spoids"]);
    }

    //==========================================================================================
    //==========================================================================================

    if (count($arr_links) == 0) {
        //no links to process!
        //return the list of spos as they are
        return array("SINGLE" => $arr_spos, "MERGED" => array(), "INVALID_MERGED"=>array()); //no spos to be merged at all
    }
    
    //==========================================================================================
    //==========================================================================================
    $arr_spo_common_dates = array(); // this is the array that will store all 
    $arr_spo_invalid_links = array(); //this array will store lists of invalid links
    
    // spos to be merged cumulatively
    //3. for each link in arrlinks,

    for ($i = 0; $i < count($arr_links); $i++) {

        //==========================================================
        //test the link to see if any spo is missing
        $outcome = _rates_calculator_getSPO_validate_merge_link($con,$arr_links[$i]["LINKID"],$arr_links[$i]["SPOIDS"]);
        if($outcome != "OK")
        {
            //flag any error if spo missing from the linking
            $arr_spo_invalid_links[] = $outcome;
        }
        
        //==========================================================
        //now, get the common dates for the spos in that link
        $arr_link_spos_common = _rates_calculator_getSPO_common_dates($arr_links[$i]["SPOIDS"], $arr_spos);
        if (!is_null($arr_link_spos_common)) {
            $arr_spo_common_dates = array_merge($arr_spo_common_dates, $arr_link_spos_common);
        }
        
    }
    
    //==========================================================================================
    //==========================================================================================
    //4. now remove the common dates found in $arr_spo_common_dates from $arr_spos        
    $arr_spos = _rates_calculator_getSPO_remove_common_dates($arr_spos, $arr_spo_common_dates);
    $arr_spos = _rates_calculator_getSPO_cleanup_without_dates($arr_spos);


    return array("SINGLE" => $arr_spos, "MERGED" => $arr_spo_common_dates, "INVALID_MERGED"=>$arr_spo_invalid_links); //no spos to be merged at all
}

function _rates_calculator_getSPO_validate_merge_link($con,$linkid,$spoids)
{
    //$spoids is comma separated
    $outcome = "";
    
    $arr_listed_spos = explode(",", $spoids);
    
    //get all spos that are attached to the link
    //check if each spo id in $arr_listed_spos
    //if not flag error message
    
    $sql = "select sol.description, so.sponame, so.spocode, so.template, so.id
            from tblspecial_offer_link sol
            inner join tblspecial_offer_link_spos sols on sol.id = sols.linkfk
            inner join tblspecial_offer so on sols.spofk = so.id
            where sol.id = :linkid and so.deleted = 0 and so.active_external = 1";
    
    $query = $con->prepare($sql);
    $query->execute(array(":linkid"=>$linkid));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $spo_to_search = $rw["id"];
        if (!in_array($spo_to_search, $arr_listed_spos))
        {
            $outcome = (trim($outcome) == "" ? $outcome : $outcome . "<br>");
            $outcome .= "INVALID COMBINABLE SPOS: MISSING SPO '" . $rw["spocode"] . " - " . $rw["sponame"] . "' IN THE LINK '". $rw["description"] . "'";
        }
    }
    
    
    if(trim($outcome) == "")
    {
        return "OK";
    }
    else
    {
        return $outcome;
    }
}

function _rates_calculator_getSPO_cleanup_without_dates($arr_spos) {
    //searches for all spos without dates and then removes them from the array
    $arr_index_to_delete = array();
    for ($i = 0; $i < count($arr_spos); $i++) {
        $arr_dates = $arr_spos[$i]["DATES"];
        if (count($arr_dates) == 0) {
            $arr_index_to_delete[] = $i;
        }
    }

    //now delete the items
    for ($i = 0; $i < count($arr_index_to_delete); $i++) {
        $index = $arr_index_to_delete[$i];
        unset($arr_spos[$index]);
    }

    $arr_spos = array_values($arr_spos);

    return $arr_spos;
}

function _rates_calculator_getSPO_remove_common_dates($arr_spos, $arr_spo_common_dates) {
    //remove the dates of each spo in $arr_spo_common_dates from $arr_spos
    for ($i = 0; $i < count($arr_spo_common_dates); $i++) {
        $merged_spos = $arr_spo_common_dates[$i];
        $arr_dates = $merged_spos["DATES"];
        $arr_spoids = explode(",",$merged_spos["SPOIDS"]);

        for ($j = 0; $j < count($arr_spoids); $j++) {
            $spoid = $arr_spoids[$j];
            $rw_spo = _rates_calculator_lookupSPO_by_id($spoid, $arr_spos);
            $arr_dates_to_remove_from = $rw_spo["DATES"];
            $arr_dates_to_remove_from = \array_diff($arr_dates_to_remove_from, $arr_dates);
            $arr_dates_to_remove_from = array_values($arr_dates_to_remove_from);

            //replace the array into $arr_spos
            _rates_calculator_updateSPO_dates_by_id($spoid, $arr_spos, $arr_dates_to_remove_from);
        }
    }

    return $arr_spos;
}

function _rates_calculator_getSPO_common_dates($list_spo_ids, $arr_spos) {


    //$list_spo_ids = comma separated list of spo for a specific link
    //$arr_spos = full list of spos 
    //return an array that looks like
    //[0][spoid] = 1,2,3
    //   [dates] = d1,d2,d3
    //[1][spoid] = 1,2
    //   [dates] = d4
    //[2][spoid] = 2,3
    //   [dates] = d5
    //[3][spoid] = 1
    //   [dates] = d6,d7
    //step 1: get a list of all spos in that links
    //also get the dates of each spo

    $arr_dates_wip = array();
    $arr_common_spos = explode(",", $list_spo_ids);

    for ($i = 0; $i < count($arr_common_spos); $i++) {
        $id = trim($arr_common_spos[$i]);
        if ($id != "") {
            $_spo_arr = _rates_calculator_lookupSPO_by_id($id, $arr_spos);
            if (!is_null($_spo_arr)) {
                $arr_dates_wip[] = array("DATES" => $_spo_arr["DATES"], "SPOIDS" => array($id));
            }
        }
    }

    //good, now get common dates 
    $count = count($arr_dates_wip);
    if ($count == 0) {
        return null; //no dates in common at all 
    } else if ($count == 1) {
        //there is only one spo in that link => simply return the spo with all its dates 
        return $arr_dates_wip;
    } else {
        //count > 1
        //process the merging of dates if applicable for more than one spo in the link
        //get an array of dates first
        $arr_array_by_dates = array();
        for ($i = 0; $i < count($arr_dates_wip); $i++) {
            $_dates = $arr_dates_wip[$i]["DATES"];
            for ($j = 0; $j < count($_dates); $j++) {
                $_dt = $_dates[$j];
                if (!in_array($_dt, $arr_array_by_dates)) {
                    $arr_array_by_dates[] = $_dt;
                }
            }
        }

        //now for each date in the array $arr_array_by_dates, get all spos that belong to it
        $arr_date_summary = array();
        for ($i = 0; $i < count($arr_array_by_dates); $i++) {
            $_arr_spos_for_that_date = array();
            $dt = $arr_array_by_dates[$i];

            for ($j = 0; $j < count($arr_dates_wip); $j++) {
                $_dates = $arr_dates_wip[$j]["DATES"];
                $spoid = $arr_dates_wip[$j]["SPOIDS"][0];

                if (in_array($dt, $_dates)) {
                    $_arr_spos_for_that_date[] = $spoid;
                }
            }

            $arr_date_summary[] = array("THE_DATE" => $dt, "SPOIDS" => implode(",", $_arr_spos_for_that_date));
        }

        //great, now we got an array of merged spos for any specific date
        //now we got to use the merged_ids of spos as the key of dates
        //ie arr[1,2] = d1,d2
        //   arr[1] = d3
        //   arr[1,3] = d4

        $arr_merged_ids_as_key = array();
        for ($i = 0; $i < count($arr_date_summary); $i++) {
            $spoids = $arr_date_summary[$i]["SPOIDS"];
            $dt = $arr_date_summary[$i]["THE_DATE"];

            if (isset($arr_merged_ids_as_key[$spoids])) {
                $arr_merged_ids_as_key[$spoids][] = $dt; //push date into already created array
            } else {
                $arr_merged_ids_as_key[$spoids] = array($dt); //create array with date
            }
        }



        //nearly there
        //now convert the array from 
        //   arr[1,2] = d1,d2
        //   arr[1] = d3
        //   arr[1,3] = d4
        //
        //to
        //
        //
        //[0][spoid] = 1,2
        //   [dates] = d1,d2
        //[1][spoid] = 1
        //   [dates] = d3
        //[2][spoid] = 1,3
        //   [dates] = d4


        $arr_spo_common_dates = array();

        $arr_keys = array_keys($arr_merged_ids_as_key);

        for ($i = 0; $i < count($arr_keys); $i++) {
            $spoids = $arr_keys[$i];
            $dates = $arr_merged_ids_as_key[$spoids];

            $arr_spo_common_dates[] = array("DATES" => $dates, "SPOIDS" => $spoids);
        }

        return $arr_spo_common_dates;
    }
}

function _rates_calculator_updateSPO_dates_by_id($id, &$arr_spos, $arr_dates) {
    for ($i = 0; $i < count($arr_spos); $i++) {
        if ($arr_spos[$i]["SPO_RW"]["id"] == $id) {
            $arr_spos[$i]["DATES"] = $arr_dates;
        }
    }
    return null;
}

function _rates_calculator_lookupSPO_by_id($id, $arr_spos) {
    for ($i = 0; $i < count($arr_spos); $i++) {
        if ($arr_spos[$i]["SPO_RW"]["id"] == $id) {
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
        return true;
    }

    $num_spo_nights = count($arr_dates);

    if ($spo_minstay_from != "" && $spo_minstay_to != "") {
        if ($spo_minstay_from <= $num_spo_nights && $num_spo_nights <= $spo_minstay_to) {
            return true;
        }
    } else if ($spo_minstay_from == "" && $spo_minstay_to != "") {
        if ($num_spo_nights <= $spo_minstay_to) {
            return true;
        }
    } else if ($spo_minstay_from != "" && $spo_minstay_to == "") {
        if ($spo_minstay_from <= $num_spo_nights) {
            return true;
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
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "early_booking") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "family_offer") {
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            $flg_applicable_on_room = _rates_calculator_validate_SPO_family_offer_applicable_room($arr_params, $spo_rw["family_offer_room_applicable"]);
            //??
            $flg_adults_check = _rates_calculator_validate_SPO_family_offer_adults($arr_params, $spo_rw["family_offer_adult_min"], $spo_rw["family_offer_adult_max"]);
            $flg_children_check = _rates_calculator_validate_SPO_family_offer_children($arr_params, $spo_rw["family_offer_children_min"], $spo_rw["family_offer_children_max"]);

            if ($flg_num_night) {
                if ($flg_applicable_on_room) {
                    if ($flg_adults_check) {
                        if ($flg_children_check) {
                            $arr_final_spos[] = $arr_spos[$i];
                        } else {
                            $arr_invalid_spos[] = "$sponame : CHILDREN CHECK FAILED";
                        }
                    } else {
                        $arr_invalid_spos[] = "$sponame : ADULT CHECK FAILED";
                    }
                } else {
                    $arr_invalid_spos[] = "$sponame : APPLICABLE ON ROOM FAILED";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "flat_rate") {
            $arr_dates = _rates_calculator_validate_SPO_flat_rate_grpvalidity($spoid, $arr_dates, $con);
            $arr_dates = _rates_calculator_validate_SPO_flat_rate_validate_capacity($spoid, $arr_params, $arr_dates, $con);
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);

            if (count($arr_dates) > 0) {
                if ($flg_num_night) {
                    $arr_final_spos[] = $arr_spos[$i];
                } else {
                    $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : GROUP DATE VALIDITY FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "free_nights") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "free_upgrade") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "honeymoon") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "long_stay") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "meals_upgrade") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "senior_offer") {
            $flg_min_guests = _rates_calculator_validate_SPO_min_max_guests($arr_params, $spo_rw["senior_min_guests"], "");
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);

            if ($flg_num_night) {
                if ($flg_min_guests) {
                    $arr_final_spos[] = $arr_spos[$i];
                } else {
                    $arr_invalid_spos[] = "$sponame : MIN GUESTS FAILED";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "wedding_anniversary") {
            $flg_passed = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);
            if ($flg_passed) {
                $arr_final_spos[] = $arr_spos[$i];
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
            }
        }
        //=======================================================
        else if ($spotemplate == "wedding_party") {

            $flg_min_guests = _rates_calculator_validate_SPO_min_max_guests($arr_params, $spo_rw["wedding_min_guests"], $spo_rw["wedding_max_guests"]);
            $flg_num_night = _rates_calculator_validate_SPO_minnights($spo_minstay_priority, $spo_minstay_from, $spo_minstay_to, $arr_dates);

            if ($flg_num_night) {
                if ($flg_min_guests) {
                    $arr_final_spos[] = $arr_spos[$i];
                } else {
                    $arr_invalid_spos[] = "$sponame : MIN GUESTS FAILED";
                }
            } else {
                $arr_invalid_spos[] = "$sponame : MIN NIGHTS FAILED";
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

function _rates_calculator_validate_SPO_family_offer_children($arr_params, $children_min, $children_max) {
    $children_count = count($arr_params["children"]);

    if ($children_min != "" && $children_max != "") {
        if ($children_min <= $children_count && $children_count <= $children_max) {
            return true;
        }
    } else if ($children_min != "" && $children_max == "") {
        if ($children_min <= $children_count) {
            return true;
        }
    } else if ($children_min == "" && $children_max != "") {
        if ($children_count <= $children_max) {
            return true;
        }
    } else if ($children_min == "" && $children_max == "") {
        return true;
    }

    return false;
}

function _rates_calculator_validate_SPO_family_offer_adults($arr_params, $adult_min, $adult_max) {
    $adult_count = count($arr_params["adults"]);

    if ($adult_min != "" && $adult_max != "") {
        if ($adult_min <= $adult_count && $adult_count <= $adult_max) {
            return true;
        }
    } else if ($adult_min != "" && $adult_max == "") {
        if ($adult_min <= $adult_count) {
            return true;
        }
    } else if ($adult_min == "" && $adult_max != "") {
        if ($adult_count <= $adult_max) {
            return true;
        }
    } else if ($adult_min == "" && $adult_max == "") {
        return true;
    }

    return false;
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
    if ($spo_type != "BOTH") {
        $sql .= " AND spo_type = '$spo_type' ";
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
        $arr_spo_dates = _rates_calculator_spo_validity_dates($arr_params, $rw, $con);
        $flg_spo_dates_valid = (count($arr_spo_dates) > 0 ? true : false);

        if ($flg_book_date_chk) {
            if ($flg_book_days_chk) {

                if ($flg_spo_dates_valid) {
                    $arr_spos[] = array("SPO_RW" => $rw,
                        "TEMPLATE" => $rw["template"],
                        "DATES" => $arr_spo_dates); //spo passed initial tests! remember it
                } else {
                    $arr_invalid_spos[] = $rw["sponame"] . " : NO DATES WITHIN VALIDITY PERIODS";
                }
            } else {
                $arr_invalid_spos[] = $rw["sponame"] . " : BOOKING DAYS FAILED";
            }
        } else {
            $arr_invalid_spos[] = $rw["sponame"] . ": BOOKING DATE FAILED";
        }
    }

    return array("VALID_SPOS" => $arr_spos, "INVALID_SPOS" => $arr_invalid_spos);
}

function _rates_calculator_spo_validity_dates($arr_params, $rw, $con) {
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
        return true;
    }

    if ($rw["booking_before_date_from"] != "" && $rw["booking_before_date_to"] != "") {
        if ($rw["booking_before_date_from"] <= $spo_booking_date &&
                $spo_booking_date <= $rw["booking_before_date_to"]) {
            return true;
        }
    } else if ($rw["booking_before_date_from"] == "" && $rw["booking_before_date_to"] != "") {
        //date <= dtfrom
        if ($spo_booking_date <= $rw["booking_before_date_to"]) {
            return true;
        }
    } else if ($rw["booking_before_date_from"] != "" && $rw["booking_before_date_to"] == "") {
        //dtto <= date
        if ($rw["booking_before_date_from"] <= $spo_booking_date) {
            return true;
        }
    } else if ($rw["booking_before_date_from"] == "" && $rw["booking_before_date_to"] == "") {
        return true;
    }

    return false;
}

function _rates_calculator_spo_check_booking_days($arr_params, $rw) {
    $spo_booking_date = $arr_params["spo_booking_date"];
    $spo_travel_date = $arr_params["spo_travel_date"];

    if ($spo_booking_date == "") {
        return true;
    }

    if ($spo_travel_date == "") {
        return true;
    }

    $days_booking = utils_days_diff($spo_booking_date, $spo_travel_date);

    if ($rw["booking_before_days_from"] != "" && $rw["booking_before_days_to"] != "") {
        if ($rw["booking_before_days_from"] <= $days_booking &&
                $days_booking <= $rw["booking_before_days_to"]) {
            return true;
        }
    } else if ($rw["booking_before_days_from"] == "" && $rw["booking_before_days_to"] != "") {
        //date <= dtfrom
        if ($days_booking <= $rw["booking_before_days_to"]) {
            return true;
        }
    } else if ($rw["booking_before_days_from"] != "" && $rw["booking_before_days_to"] == "") {
        //dtto <= date
        if ($rw["booking_before_days_from"] <= $days_booking) {
            return true;
        }
    } else if ($rw["booking_before_days_from"] == "" && $rw["booking_before_days_to"] == "") {
        return true;
    }

    return false;
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
        $free_nights_start_end, $flat_rate_capacity_array, $ismerged) {

    $arr_sharing_age_ranges = array();
    $arr_own_age_ranges = array();

    if (!$ismerged) {
        $arr_sharing_age_ranges = _rates_calculator_spo_check_child_age_sharing_own($spoid, "SHARING", $con);
        $arr_own_age_ranges = _rates_calculator_spo_check_child_age_sharing_own($spoid, "OWN", $con);
    } else {
        //get common dates for the sharing and own age ranges if any        
        $arr_sharing_age_ranges = _rates_calculator_spo_check_child_age_sharing_own_merged($spoid, "SHARING", $con);
        $arr_own_age_ranges = _rates_calculator_spo_check_child_age_sharing_own_merged($spoid, "OWN", $con);

        if (is_null($arr_sharing_age_ranges)) {
            //the merged spos disagree on sharing or own age ranges
            //this merged spo cannot be valid
            //simply place a value that is impossible to meet
            $arr_sharing_age_ranges = array();
            $arr_sharing_age_ranges[] = array("agefrom" => 1000000, "ageto" => -1000000);
        }
        if (is_null($arr_own_age_ranges)) {
            //the merged spos disagree on sharing or own age ranges
            //this merged spo cannot be valid
            //simply place a value that is impossible to meet
            $arr_own_age_ranges = array();
            $arr_own_age_ranges[] = array("agefrom" => 1000000, "ageto" => -1000000);
        }
    }



    $arr_item = array(
        "SPO_ID" => $spoid,
        "SPO_NAME" => $sponame,
        "SPO_TYPE" => $spo_type,
        "AD_CH" => $ad_ch,
        "BRIDE_GROOM" => $bride_groom,
        "SHARING_AGE_RANGES" => $arr_sharing_age_ranges,
        "OWN_AGE_RANGES" => $arr_own_age_ranges,
        "AGE_FROM" => $age_from,
        "AGE_TO" => $age_to,
        "ROOM_ALL_FLAT" => $room_all_flat,
        "VALUE" => $value,
        "APPLY_TO_DATES" => $apply_to_dates,
        "FREE_NIGHTS" => $free_nights,
        "FREE_NIGHTS_START_END" => $free_nights_start_end,
        "FLAT_RATE_CAPACITY_ARRAY" => $flat_rate_capacity_array);

    return $arr_item;
}

function _rates_calculator_spo_check_child_age_sharing_own_merged($spoids, $sharing_own, $con) {
    $arr_spo_ids = explode(",", $spoids);

    $arr_all_ranges = array();

    for ($i = 0; $i < count($arr_spo_ids); $i++) {
        $spoid = $arr_spo_ids[$i];
        if ($spoid != "") {
            $arr_age_ranges = _rates_calculator_spo_check_child_age_sharing_own($spoid, $sharing_own, $con);
            $arr_all_ranges[] = $arr_age_ranges;
        }
    }

    //================================================================
    //get the min and max age values
    $min = 1000000;
    $max = -1000000;

    for ($i = 0; $i < count($arr_all_ranges); $i++) {
        $spo_arr_range = $arr_all_ranges[$i];

        for ($j = 0; $j < count($spo_arr_range); $j++) {
            $age_from = $spo_arr_range[$j]["AGE_FROM"];
            $age_to = $spo_arr_range[$j]["AGE_TO"];

            if ($min > $age_from) {
                $min = $age_from;
            }

            if ($max < $age_to) {
                $max = $age_to;
            }
        }
    }

    //================================================================
    if ($min == 1000000 && $max == -1000000) {
        //all spos had no applicable age ranges 
        //return blank array
        return array();
    }
    //================================================================
    //now for each age in range min to max
    $final_age_array = array();
    $flg_found_intersection_age = false;

    for ($cur_age = $min; $cur_age <= $max; $cur_age++) {
        //check if age i is within all the age ranges in $arr_all_ranges of each spo

        for ($i = 0; $i < count($arr_all_ranges); $i++) {
            $spo_arr_range = $arr_all_ranges[$i];

            for ($j = 0; $j < count($spo_arr_range); $j++) {
                $age_from = $spo_arr_range[$j]["AGE_FROM"];
                $age_to = $spo_arr_range[$j]["AGE_TO"];
                if ($age_from <= $cur_age && $cur_age <= $age_to) {
                    $flg_found_intersection_age = true;
                    $final_age_array[] = array("AGE_FROM" => $cur_age, "AGE_TO" => $cur_age);
                }
            }
        }
    }

    if ($flg_found_intersection_age) {
        return $final_age_array;
    } else {
        return null; //all ages ranges in each spo are as a disjoint set
    }
}
?>

