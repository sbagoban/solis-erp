<?php

function _rates_calculator($con, $arr_params) {
    try {

        $time_pre = microtime(true);

        //================================================================
        ////TEMP ========================================================
        //temporarily create a discount array for testing
        $arr_spo_discounts = array();

        $discount_percnt_room = $arr_params["spo_discount_room_percentage"];
        $discount_percnt_all = $arr_params["spo_discount_all_percentage"];
        $discount_flat_pppn = $arr_params["spo_discount_PPPN"];
        $discount_flat_pni = $arr_params["spo_discount_PNI"];


        $arr_item = array(
            "SPO_ID" => 1, "SPO_NAME" => "TESTING 1", "SPO_TYPE" => "DISCOUNT",
            "AD_CH" => "BOTH", "BRIDE_GROOM" => "",
            "AGE_FROM" => -1, "AGE_TO" => -1,
            "ROOM_ALL_FLAT" => "%ALL", "VALUE" => $discount_percnt_all);
        $arr_spo_discounts[] = $arr_item;

        $arr_item = array(
            "SPO_ID" => 2, "SPO_NAME" => "TESTING 2", "SPO_TYPE" => "DISCOUNT",
            "AD_CH" => "BOTH", "BRIDE_GROOM" => "",
            "AGE_FROM" => -1, "AGE_TO" => -1,
            "ROOM_ALL_FLAT" => "%ROOM", "VALUE" => $discount_percnt_room);
        $arr_spo_discounts[] = $arr_item;

        $arr_item = array(
            "SPO_ID" => 3, "SPO_NAME" => "TESTING 3", "SPO_TYPE" => "DISCOUNT",
            "AD_CH" => "BOTH", "BRIDE_GROOM" => "",
            "AGE_FROM" => -1, "AGE_TO" => -1,
            "ROOM_ALL_FLAT" => "FLAT_PPPN", "VALUE" => $discount_flat_pppn);
        $arr_spo_discounts[] = $arr_item;

        $arr_item = array(
            "SPO_ID" => 3, "SPO_NAME" => "TESTING 3", "SPO_TYPE" => "DISCOUNT",
            "AD_CH" => "BOTH", "BRIDE_GROOM" => "",
            "AGE_FROM" => -1, "AGE_TO" => -1,
            "ROOM_ALL_FLAT" => "FLAT_PNI", "VALUE" => $discount_flat_pni);
        $arr_spo_discounts[] = $arr_item;

        $arr_params["spo_discounts_array"] = $arr_spo_discounts;

        ////TEMP ========================================================
        //=================================================================        


        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
        $hotelroom = $arr_params["hotelroom"];
        $supp_mealplan = $arr_params["supp_mealplan"];

        //cleanup:
        $arr_params["contractids"] = trim($arr_params["contractids"]);


        $checkin_dMY = new DateTime($arr_params["checkin_date"]);
        $checkin_dMY = $checkin_dMY->format("d M Y");
        $checkout_dMY = new DateTime($arr_params["checkout_date"]);
        $checkout_dMY = $checkout_dMY->format("d M Y");

        $num_nights = _rates_calculator_get_numnights($checkin_date, $checkout_date);


        $roll_over_flg = false;
        $roll_over_basis = "";
        $roll_over_value = "";
        $checkin_rollover_dMY = "";
        $checkout_rollover_dMY = "";


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
                    "NUM NIGHTS" => $num_nights,
                    "EXEC_TIME" => $exec_time);
            } else if ($arr_outcome["OUTCOME"] == "FAIL_NO_CONTRACT") {

                //check for rollover by setting dtfrom and dtto to last year
                $checkin_rollover = new DateTime($arr_params["checkin_date"]);
                $checkout_rollover = new DateTime($arr_params["checkout_date"]);
                $checkin_rollover = $checkin_rollover->modify('-1 year');
                $checkout_rollover = $checkout_rollover->modify('-1 year');


                $arr_params["checkin_date"] = $checkin_rollover->format("Y-m-d");
                $arr_params["checkout_date"] = $checkout_rollover->format("Y-m-d");

                $checkin_rollover_dMY = $checkin_rollover->format("d M Y");
                $checkout_rollover_dMY = $checkout_rollover->format("d M Y");

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
                            "NUM NIGHTS" => $num_nights,
                            "EXEC_TIME" => $exec_time);
                    }
                } else {
                    $roll_over_flg = true;
                }
                //==================================================================
            }
        }
        //=========================================================================

        $contractid = $arr_days["DAILY"][0]["CONTRACT_ID"][0]; //get the final contract id
        $arr_params["current_contract_id"] = $contractid;

        $arr_params["roll_over"] = $roll_over_flg;
        $arr_params["roll_over_basis"] = $roll_over_basis;
        $arr_params["roll_over_value"] = $roll_over_value;


        //get the contract details
        $rw_contract_details = _rates_calculator_get_contract_details($con, $contractid);
        if ($roll_over_flg) {
            $roll_over_basis = $rw_contract_details["rollover_basis"];
            $roll_over_value = $rw_contract_details["rollover_value"];

            $arr_params["roll_over"] = $roll_over_flg;
            $arr_params["roll_over_basis"] = $roll_over_basis;
            $arr_params["roll_over_value"] = $roll_over_value;
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


        $arr_daily = array();

        for ($idx = 0; $idx < count($arr_days["DAILY"]); $idx++) {


            $this_date = $arr_days["DAILY"][$idx]["DATE"];

            //======================================================================
            $arr_daily[$idx]["DATE"] = $this_date;
            $arr_daily[$idx]["ROLLOVER"] = $roll_over_flg;
            $arr_daily[$idx]["ROLLOVER_BASIS"] = $roll_over_basis;
            $arr_daily[$idx]["ROLLOVER_VALUE"] = $roll_over_value;
            $arr_daily[$idx]["TAX_COMMI_BASIS"] = "";
            $arr_daily[$idx]["CONTRACT_ID"] = $arr_days["DAILY"][$idx]["CONTRACT_ID"];
            $arr_daily[$idx]["COSTINGS_WORKINGS"] = array();
            $arr_daily[$idx]["STATUS"] = "OK"; //to be used for further checks below
            //========================================================================

            $arr_daily[$idx]["CURRENCY_SELL_CODE"] = $currency_sell;
            $arr_daily[$idx]["CURRENCY_BUY_CODE"] = $currency_buy;


            //=============================================================================
            //if rollover to last year, notify the user
            if ($roll_over_flg) {

                $msg = "<font color='red'>NO CONTRACTS FOUND FOR <b>$checkin_dMY - $checkout_dMY</b></font>";
                $msg .= "<br>ROLLING OVER TO: <b>$checkin_rollover_dMY - $checkout_rollover_dMY</b>";

                $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => $msg, "COSTINGS" => array());
            }
            //=============================================================================


            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 1</font>: FOUND CONTRACT ID: $contractid",
                "COSTINGS" => array());

            //========================================================================
            //load contract rules, tax and commission into array
            $arr_capacity = _contract_capacityarr($con, $contractid);
            $arr_taxcomm = _contract_taxcommi($con, $contractid);
            //========================================================================

            $PN_PPN = _rates_calculator_getTaxCommi_AddOn_Basis($hotelroom, $arr_taxcomm);
            $arr_daily[$idx]["TAX_COMMI_BASIS"] = $PN_PPN;
            $arr_params["TAX_COMMI_BASIS"] = $PN_PPN;
            //========================================================================
            //================================================================
            //create dummy columns needed for MARKUP and COMMISSION
            $arr_columns = _rates_calculator_prepare_costings_array($con, $arr_taxcomm, $arr_params, 0, array("ROOM"), $arr_params["TAX_COMMI_BASIS"]);
            //==========================================================
            //TEST 2: MINIMUM STAY 
            $min_test = _rates_calculator_min_stay_nights($arr_capacity, $arr_params, $this_date, $num_nights);
            if ($min_test != "OK") {
                $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 2</font>: MIN STAY $min_test NIGHTS",
                    "COSTINGS" => array());

                $arr_daily[$idx]["STATUS"] = "MIN_STAY_FAIL";
            } else {

                $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 2</font>: MIN STAY",
                    "COSTINGS" => array());


                //TEST 3: CHILDREN AGES
                $children_age_test = _rates_calculator_test_children_ages($arr_params, $con);
                if ($children_age_test != "OK") {
                    $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 3</font>: CHILDREN AGES: $children_age_test",
                        "COSTINGS" => array());
                    $arr_daily[$idx]["STATUS"] = "CHILDREN_AGE_FAIL";
                } else {

                    $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 3</font>: CHILDREN AGES",
                        "COSTINGS" => array());

                    //TEST 4: ADULT AND CHILDREN CAPACITY
                    $capacity_test_adch = _rates_calculator_adch_capacity($arr_capacity, $arr_params, $this_date);
                    if ($capacity_test_adch["MSG"] != "OK") {
                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 4</font>: CAPACITY TEST ADULT + CHILDREN SHARING: " . $capacity_test_adch["MSG"],
                            "COSTINGS" => array());

                        $arr_daily[$idx]["STATUS"] = "CAPACITY_FAIL";
                    } else {

                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 4</font>: CAPACITY ADULT + CHILDREN SHARING. COMBINATION INDEX: " . ($capacity_test_adch["INDEX"]),
                            "COSTINGS" => array());

                        //TEST 5: CHILDREN IN OWN ROOM
                        $capacity_test_ch_ownroom = _rates_calculator_ch_own_capacity($arr_capacity, $arr_params, $this_date);
                        if ($capacity_test_ch_ownroom["MSG"] != "OK") {
                            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 5</font>: CAPACITY TEST CHILDREN OWN ROOM: " . $capacity_test_ch_ownroom["MSG"],
                                "COSTINGS" => array());

                            $arr_daily[$idx]["STATUS"] = "CHILDREN_OWN_ROOM_FAIL";
                        } else {
                            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 5</font>: CAPACITY CHILDREN OWN ROOM. COMBINATION INDEX:" . ($capacity_test_adch["INDEX"]),
                                "COSTINGS" => array());
                        }

                        //=====================================================================
                        //HERE WE GO:
                        //
                        //
                        //
                        //=====================================================================
                        //================== EARLY CHECKIN ====================================
                        $arr_eci = array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
                        if ($idx == 0) {
                            //CHECK IF THERE IS EARLY CHECK IN
                            $arr_eci = _rates_calculator_eci_lco("ECI", $arr_capacity, $arr_params, $this_date, $hotelroom);
                        }
                        //=====================================================================
                        //
                        //
                        //                        
                        //=====================================================================
                        //============= NOW GET DAILY RATES ===================================
                        $arr = _rates_calculator_lookup_rates($arr_capacity, $arr_params, $this_date, $con, $arr_eci);

                        $arr = _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $arr_params);

                        $arr = _rates_calculator_calc_rollover_percentage($arr_params, $arr, $roll_over_flg);
                        $arr = _rates_calculator_calc_rollover_flat_pni($arr_params, $arr, $roll_over_flg, "ROOM");

                        $arr = _rates_calculator_calc_discount_PPPN($arr_params, $arr, "ROOM");
                        $arr = _rates_calculator_calc_discount_PNI($arr_params, $arr);

                        $arr = _rates_calculator_lookup_rates_calc_PPPN($arr, $arr_params, $arr_taxcomm, $con, "ROOM");

                        $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr);

                        //$arr[n][MSG]
                        //       [COSTINGS]
                        //=====================================================================
                        //
                        //
                        //
                        //=====================================================================
                        //================== LATE CHECKOUT ====================================
                        $arr_lco = array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
                        if ($idx == $num_nights - 1) {
                            //CALCULATE LATE CHECK OUT ON THE DATE OF CHECKOUT

                            $lco_applied = false; //flag to determine of LCO has been applied or not
                            $checkout_date = new DateTime($this_date);
                            $checkout_date = $checkout_date->modify('+1 day');
                            $checkout_date = $checkout_date->format("Y-m-d");

                            $arr_lco = _rates_calculator_eci_lco("LCO", $arr_capacity, $arr_params, $checkout_date, $hotelroom);

                            //get the rates for the date of checkout
                            $arr_rates = _rates_calculator_lookup_rates($arr_capacity, $arr_params, $checkout_date, $con, $arr_eci);
                            $arr_rates = _rates_calculator_apply_rates_lco_percentage($arr_rates, $arr_lco, $lco_applied, $currency_buy);
                            $arr_rates = _rates_calculator_apply_rates_lco_flat_pni($arr_rates, $arr_lco, $arr_params, $lco_applied);
                            $arr_rates = _rates_calculator_calc_rollover_percentage($arr_params, $arr_rates, $roll_over_flg);

                            if ($lco_applied) {
                                //create another index for LCO
                                $arr_daily[$idx + 1] = $arr_daily[$idx]; //copy previous night values to new checkout date
                                $arr_daily[$idx + 1]["DATE"] = $checkout_date;
                                $arr_rates = _rates_calculator_lookup_rates_calc_PPPN($arr_rates, $arr_params, $arr_taxcomm, $con, "ROOM");
                                $arr_daily[$idx + 1]["COSTINGS_WORKINGS"] = $arr_rates;
                                $arr_daily[$idx + 1]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx + 1]["COSTINGS_WORKINGS"], $arr_columns, "ROOM", $arr_params, $arr_taxcomm, $con);
                                $arr_daily[$idx + 1]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx + 1]["COSTINGS_WORKINGS"], $arr_columns, "NON_ROOM", $arr_params, $arr_taxcomm, $con);
                            }
                        }
                        //=====================================================================
                        //=====================================================================
                        //ROOM TOTAL
                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, "ROOM", $arr_params, $arr_taxcomm, $con);


                        //
                        //=====================================================================
                        //================================================================
                        //MEAL SUPPLEMENTS
                        if ($supp_mealplan != "") {
                            //there is a meal supplement
                            $arr_meal_supp = _rates_calculator_meal_supp($arr_capacity, $arr_params, $this_date, $hotelroom, $con);

                            $arr_meal_supp = _rates_calculator_calc_rollover_percentage($arr_params, $arr_meal_supp, $roll_over_flg);
                            $arr_meal_supp = _rates_calculator_meal_supp_PPPN($arr_meal_supp, $arr_params, $arr_taxcomm, $con);
                            $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_meal_supp);
                        }


                        //=====================================================================
                        //
                        //
                        //
                        //=====================================================================
                        //EXTRA MEAL SUPPLEMENTS
                        //CHECK FOR COMPULSORY MEALS
                        $arr_extra_meal_supp = _rates_calculator_extra_meal_supp($arr_capacity, $arr_params, $this_date, $hotelroom, $con);
                        $arr_extra_meal_supp = _rates_calculator_calc_rollover_percentage($arr_params, $arr_extra_meal_supp, $roll_over_flg);
                        $arr_extra_meal_supp = _rates_calculator_extra_meal_supp_PPPN($arr_extra_meal_supp, $arr_params, $arr_taxcomm, $con);
                        $arr_daily[$idx]["COSTINGS_WORKINGS"] = array_merge($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_extra_meal_supp);

                        //================================================================
                        //===============================================================
                        //NON ROOM TOTAL
                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = _rates_calculator_sum_daily_total($arr_daily[$idx]["COSTINGS_WORKINGS"], $arr_columns, "NON_ROOM", $arr_params, $arr_taxcomm, $con);
                    }
                }
            }
            //========================================================================
        }



        $time_post = microtime(true);
        $exec_time = round(($time_post - $time_pre), 2);




        return array("OUTCOME" => "OK", "NUM NIGHTS" => $num_nights, "DAILY" => $arr_daily,
            "EXEC_TIME" => $exec_time, "COLUMNS" => $arr_columns);
    } catch (Exception $ex) {
        return array("OUTCOME" => "_RATES_CALCULATOR: " . $ex->getMessage());
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

function _rates_calculator_eci_lco($eci_lco, $arr_capacity, $arr_params, $this_date, $hotelroom) {
    $workings = "";

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
                    $workings = "<b><font color='blue'>EARLY CHECK IN</font></b>: before $limit_time ";
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
                    $workings = "<b><font color='blue'>LATE CHECK OUT</font></b>: after $limit_time";
                    return array("WORKINGS" => $workings, "CHARGE_TYPE" => $charge_type, "CHARGE_VALUE" => $charge_value);
                }
            }
        }
    }

    return array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
}

function _rates_calculator_test_children_ages($arr_params, $con) {
    try {

        $current_contract_id = $arr_params["current_contract_id"];
        $children = $arr_params["children"];
        $arr_age_groups = _rates_calculator_get_children_agegroups($current_contract_id, $con);


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
                return $children[$i]["age"] . "YRS OUTSIDE CONTRACT ALLOWABLE AGE RANGES";
            }
        }


        return "OK";
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_TEST_CHILDREN_AGES: " . $ex->getMessage();
    }
}

function _rates_calculator_min_stay_nights($arr_capacity, $arr_params, $this_date, $num_nights) {
    //return OK if min stay is satisfied, minstay nights otherwise

    try {
        $hotelroom = $arr_params["hotelroom"];

        $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);
        if (!is_null($rules)) {
            $arr_minstay_rules = $rules["date_minstay_rules"];
            for ($i = 0; $i < count($arr_minstay_rules); $i++) {
                $minstay_duration = $arr_minstay_rules[$i]["minstay_duration"];
                if ($num_nights < $minstay_duration) {
                    return $minstay_duration;
                }
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_MIN_STAY_NIGHTS: " . $ex->getMessage();
    }
}

function _rates_calculator_ch_own_capacity($arr_capacity, $arr_params, $this_date) {
    //OWN ROOM  children
    //return OK if capacity is satisfied, error message otherwise
    try {
        $hotelroom = $arr_params["hotelroom"];

        $room_details = _rates_calculator_get_room_details($arr_capacity, $hotelroom);
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

function _rates_calculator_adch_capacity($arr_capacity, $arr_params, $this_date) {

    //test adult + SHARING children
    //return OK if capacity is satisfied, error message otherwise
    try {

        $hotelroom = $arr_params["hotelroom"];

        $room_details = _rates_calculator_get_room_details($arr_capacity, $hotelroom);

        $children = array();
        for ($i = 0; $i < count($arr_params["children"]); $i++) {
            if ($arr_params["children"][$i]["sharing_own"] == "SHARING") {
                //filter only those children that are sharing
                $children[] = $arr_params["children"][$i];
            }
        }
        $adult = count($arr_params["adults"]);

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

function _rates_calculator_get_room_details($arr_capacity, $roomid) {
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

function _rates_calculator_lookup_rates($arr_capacity, $arr_params, $this_date, $con, $arr_eci) {

    $hotelroom = $arr_params["hotelroom"];
    $room_details = _rates_calculator_get_room_details($arr_capacity, $hotelroom);
    $room_type = $room_details["room_variants"]; //"PERSONS", "UNITS"

    if ($room_type == "PERSONS") {
        return _rates_calculator_lookup_rates_persons($arr_capacity, $arr_params, $this_date, $con, $arr_eci);
    } else if ($room_type == "UNITS") {
        return _rates_calculator_lookup_rates_units($arr_capacity, $arr_params, $this_date, $con, $arr_eci);
    }
}

function _rates_calculator_lookup_rates_units($arr_capacity, $arr_params, $this_date, $con, $arr_eci) {
    $arr = array();

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

        $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con);

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
            $arr_extra_adult = _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates, $arr_eci);
            $arr = array_merge($arr, $arr_extra_adult);
            $flg_extra_people = true;
        }
        //===========================================================
        //===========================================================
        //for each extra children, calculate the price                
        $arr_extra_children = _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params, $arr_group_children, $normal_rates, $arr_eci, $flg_extra_people);
        $arr = array_merge($arr, $arr_extra_children);

        //===========================================================
        //finally get the normal standard unit price and split it per person
        //reload the $arr_group_children
        $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con);


        $num_persons = 0;
        if ($flg_extra_people) {
            $num_persons = $arr_std_occup[0]["MAX"];
        } else {
            $num_persons = count($arr_params["adults"]) + count($arr_params["children"]);
        }


        $per_person_buyprice = round($normal_rates / $num_persons);
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
            _rates_calculator_apply_spo_discount_percentage($per_person_buyprice, $_workings, $arr_params, $pax["adch"], $pax["age"], $pax["bride_groom"], "ROOM");


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

    $rates = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "unit_price", 0);
    $workings = "<b>UNIT PRICE</b>: $currency_buy $rates";

    if ($rates == "") {
        $rates = 0;
        $workings = "NO UNIT PRICE";
    }

    return array("RATES_STANDARD" => $rates, "WORKINGS_STANDARD" => $workings);
}

function _rates_calculator_lookup_rates_units_extra_children($rules, $arr_params, $arr_group_children, $normal_rates, $arr_eci, &$flg_extra_people) {
    $arr = array();
    $currency_buy = $arr_params["currency_buy_code"];

    $arr_children_rules = $rules["date_childpolicies_rules"];

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
                $workings_children = "<b>EXTRA PAX</b>: Ch #$idx (<b>$child_age yrs</b>) = <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any                    
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children, $arr_params, "CHILDREN", $child_age, "", "ROOM");


                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            } else if ($basis == "FLAT") {

                $rates_children = $value;
                $workings_children = "<b>EXTRA PAX</b>: Ch #$idx (<b>$child_age yrs</b>) = $currency_buy $value";

                //apply eci for that child if any
                _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);

                //apply spo percent discount for that child if any                    
                _rates_calculator_apply_spo_discount_percentage($rates_children, $workings_children, $arr_params, "CHILDREN", $child_age, "", "ROOM");

                $arr[] = array("MSG" => $workings_children, "COSTINGS" => $rates_children,
                    "ADCH" => "CHILDREN",
                    "AGE" => $child_age,
                    "BRIDEGROOM" => "");
            }
        }
    }

    return $arr;
}

function _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates, $arr_eci) {
    $arr = array();
    $rates = 0;
    $workings = "";
    $currency_buy = $arr_params["currency_buy_code"];
    $arr_adults = $arr_params["adults"];

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
            $workings = "<b>EXTRA PAX</b>: Ad #$adult_index: <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";
        } else if ($basis == "FLAT") {
            $rates = $value;
            $workings = "<b>EXTRA PAX</b>: Ad #$adult_index: $currency_buy $value";
        }

        //apply eci for that extra adult if any
        _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);

        //apply spo percent discount for that extra adult if any                    
        _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $adult_pax["age"], $adult_pax["bride_groom"], "ROOM");


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

function _rates_calculator_lookup_rates_persons($arr_capacity, $arr_params, $this_date, $con, $arr_eci) {

    $adult = $arr_params["adults"];
    $count_adult = count($adult);
    $children = $arr_params["children"];
    $flg_got_rates = false; //to be passed by reference below
    $arr = array();

    if ($count_adult == 1 && count($children) > 0) {
        //single parent policy exists?
        $arr = _rates_calculator_lookup_rates_single_parent($arr_capacity, $arr_params, $this_date, $con, $flg_got_rates, $arr_eci);
    }
    if (!$flg_got_rates) {
        //no single parent rates defined?! then lookup in normal rates
        $arr = _rates_calculator_lookup_rates_normal($arr_capacity, $arr_params, $this_date, $con, $arr_eci);
    }

    return $arr;
}

function _rates_calculator_calc_rollover_flat_pni($arr_params, $arr, $roll_over_flg) {

    if (!$roll_over_flg) {
        return $arr;
    }

    $currency_buy = $arr_params["currency_buy_code"];
    $roll_over_basis = $arr_params["roll_over_basis"];
    $roll_over_value = $arr_params["roll_over_value"];


    if ($roll_over_basis != "add_per_night") {
        return $arr;
    }


    //split rollover equally between non FOC pax

    $num_non_foc_pax = 0;

    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i]["COSTINGS"] > 0) {
            $num_non_foc_pax ++;
        }
    }

    //split the roll over now
    $pax_rollover = round($roll_over_value / $num_non_foc_pax, 2);

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


            $msg .= "<br> + (<font color='blue'><b>ROLLOVER</b>: </font> FLAT PNI : $currency_buy $roll_over_value &#247; $num_non_foc_pax = $currency_buy $pax_rollover per non FOC pax)";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_rollover, $msg, $arr_params, $adch, $age, $bridegroom, "ROOM");

            $costings += $pax_rollover;

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_calc_rollover_percentage($arr_params, $arr, $roll_over_flg) {

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

function _rates_calculator_lookup_rates_single_parent($arr_capacity, $arr_params, $this_date, $con, &$flg_got_rates, $arr_eci) {

    $arr = array();

    $children = $arr_params["children"];
    $hotelroom = $arr_params["hotelroom"];

    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con);
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
            $arr_children_rates = _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules_age_range, $arr_params, $arr_adultpolicies_rules, $arr_eci);
            $arr = array_merge($arr, $arr_children_rates);

            //calculate adult rates
            $arr_adult_rates = _rates_calculator_lookup_single_parent_parent_rates($rules_age_range, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci);
            $arr = array_merge($arr, $arr_adult_rates);
        }
    } else {
        $flg_got_rates = false;
        $arr[] = array("MSG" => "NO SINGLE PARENT RATES", "COSTINGS" => array());
    }

    return $arr;
}

function _rates_calculator_lookup_rates_normal($arr_capacity, $arr_params, $this_date, $con, $arr_eci) {

    $arr = array();
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

        $adult_buyprice = round($rates / $adult); //split the adult rates per person
        $cumul_buyprice = 0;

        for ($adinx = 1; $adinx <= $adult; $adinx++) {
            if ($adinx == $adult) {
                $adult_buyprice = $rates - $cumul_buyprice;
            } else {
                $cumul_buyprice += $adult_buyprice;
            }

            $_workings = "$workings => Ad #$adinx = $currency_buy $adult_buyprice";

            //apply eci for that adult if any
            _rates_calculator_apply_rates_eci_percentage($adult_buyprice, $arr_eci, $_workings, $currency_buy);

            //apply spo percent discount for that adult if any
            $pax = $arr_params["adults"][($adinx - 1)];
            _rates_calculator_apply_spo_discount_percentage($adult_buyprice, $_workings, $arr_params, "ADULT", $pax["age"], $pax["bride_groom"], "ROOM");

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
        $arr_rates_children = _rates_calculator_calc_children($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $con, $arr_eci);
        $arr = array_merge($arr, $arr_rates_children);

        //======================================================================
        //======================================================================
    } else {

        $arr[] = array("MSG" => "NO RATES FOUND FOR THIS DATE", "COSTINGS" => array());
    }


    return $arr;
}

function _rates_calculator_calc_children($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $con, $arr_eci) {

    //regroup each child in $children by age groups defined in the contract
    $arr = array();

    $current_contract_id = $arr_params["current_contract_id"];
    $arr_age_groups = _rates_calculator_get_children_agegroups($current_contract_id, $con);

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
            $_arr = _rates_calculator_calc_children_by_agegroup($arr_temp_children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci);
            $arr = array_merge($arr, $_arr);
        }
    }

    return $arr;
}

function _rates_calculator_get_children_agegroups($contractid, $con) {
    $arr_age_groups = array();

    //return an array of age groups for that contract
    $sql = "SELECT ca.agefrom, ca.ageto
            FROM tblservice_contract_childages scc
            INNER JOIN tblchildrenagerange ca ON scc.child_age_fk = ca.id
            WHERE service_contract_fk = :contractid
            GROUP BY ca.agefrom, ca.ageto
            ORDER BY ca.agefrom, ca.ageto";

    $query = $con->prepare($sql);
    $query->execute(array(":contractid" => $contractid));

    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr_age_groups[] = array("AGEFROM" => $rw["agefrom"], "AGETO" => $rw["ageto"]);
    }

    return $arr_age_groups;
}

function _rates_calculator_calc_children_by_agegroup($children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci) {
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
    $_arr = _rates_calculator_calculate_children_rates("SHARING", $arr_sharing, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci);
    $arr_final = array_merge($arr_final, $_arr);

    //===============================================================
    //get single children rates    
    $_arr = _rates_calculator_calculate_children_rates("SINGLE", $arr_single, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci);
    $arr_final = array_merge($arr_final, $_arr);
    //===========================================================================

    return $arr_final;
}

function _rates_calculator_calculate_children_rates($sharing_single, $arr_children, $arr_childrenpolicies_rules, $arr_adultpolicies_rules, $arr_params, $arr_eci) {
    $currency_buy = $arr_params["currency_buy_code"];

    $arr = array();
    $workings = "";
    $child_index = count($arr_children);
    $rates_children = 0;

    while ($child_index > 0) {
        $child_age = $arr_children[$child_index - 1]["age"];
        //get the basis and value for that childindex + age combination

        $arr_lkup = _rates_calculator_lookup_child_basis_value($sharing_single, $child_index, $child_age, $arr_childrenpolicies_rules);
        $basis = $arr_lkup["BASIS"];
        $value = $arr_lkup["VALUE"];
        $status = $arr_lkup["STATUS"];

        if ($status == "NO_RATES") {
            $workings = " (CH #$child_index {$child_age}yr NO RATES) => ";

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
                $workings = " (CH #$child_index {$child_age}yr SNGL $currency_buy $value) ";
                $rates_children = $value;

                $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                //stop here
                break;
            } else if ($basis == "FLAT") {
                //here just take the flat rate
                $workings = " (CH #$child_index {$child_age}yr FLAT $currency_buy $value) ";
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
                $workings = "(CH #$child_index {$child_age}yr $value% of AD $currency_buy $rates_adult = $currency_buy $child_rate_value)";

                $arr[] = array("WORKINGS" => $workings, "RATES" => $child_rate_value, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 0);

                //stop here
                break;
            } else if ($basis == "DOUBLE") {
                //implies take the adult double rate
                //here we are just taking the value attached to the child (good?)

                $workings = "(CH #$child_index $basis {$child_age}yr DBL $currency_buy $value) ";
                $rates_children = $value;

                $arr[] = array("WORKINGS" => $workings, "RATES" => $rates_children, "CHILDINDEX" => $child_index, "TO_SPLIT_BETWEEN" => 2);

                //stop here
                break;
            } else if ($basis == "TRIPLE") {
                //implies take the adult double rate
                //here we are just taking the value attached to the child (good?)

                $workings = " (CH #$child_index $basis {$child_age}yr TRPL $currency_buy $value) ";
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
        $child_age = $arr_children[$child_index - 1]["age"];

        if ($split_between == 0) {

            //apply eci for that child if any
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $work, $currency_buy);

            //apply spo percent discount for that child if any
            _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params, "CHILDREN", $child_age, "", "ROOM");


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
                _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params, "CHILDREN", $child_age, "", "ROOM");

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

function _rates_calculator_extra_meal_supp($arr_capacity, $arr_params, $this_date, $hotelroom, $con) {
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
                            "EXTRA_MEAL_SUPPLEMENT");

                    $arr[] = array("MSG" => $msg, "COSTINGS" => $extra_adult,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }



                //============= and now children ==========
                $children_rules = $rules["extra_children"];
                $arr_children_result = _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name);
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

function _rates_calculator_meal_supp($arr_capacity, $arr_params, $this_date, $hotelroom, $con) {


    $workings = "";

    $arr = array();

    $supp_mealplan = $arr_params["supp_mealplan"];
    $children = $arr_params["children"];

    $currency_buy = $arr_params["currency_buy_code"];

    //======================

    $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);
    if (!is_null($rules)) {
        $arr_mealsupplement_rules = $rules["date_mealsupplement_rules"];

        for ($i = 0; $i < count($arr_mealsupplement_rules); $i++) {
            $rules = $arr_mealsupplement_rules[$i];

            if ($rules["meal_ismain"] == 0 && $rules["meal_mealplanfk"] == $supp_mealplan) {

                //get the meal supplement caption
                $meal_supplement_caption = _rates_calculator_get_meal_name($supp_mealplan, $con);
                $adult_supp_price = $rules["meal_adult_count"];

                //=========================================================================
                //=========================================================================
                //for each adult:

                for ($a = 1; $a <= count($arr_params["adults"]); $a++) {

                    $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font> Ad #$a => $currency_buy $adult_supp_price";

                    //apply spo percent discount if any for that pax
                    $ad_pax = $arr_params["adults"][$a - 1];
                    _rates_calculator_apply_spo_discount_percentage($adult_supp_price, $workings, $arr_params,
                            "ADULT", $ad_pax["age"], $ad_pax["bride_groom"], "MEAL_SUPPLEMENT");


                    $arr[] = array("MSG" => $workings,
                        "COSTINGS" => $adult_supp_price,
                        "ADCH" => "ADULT",
                        "AGE" => $ad_pax["age"],
                        "BRIDEGROOM" => $ad_pax["bride_groom"]);
                }

                //============= and now for each children ==========
                $children_rules = $rules["meal_children"];
                $arr_children_result = _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption);
                $arr = array_merge($arr, $arr_children_result);


                //=========================================================================
                //=========================================================================
            }
        }
    }

    return $arr;
}

function _rates_calculator_extra_meal_supplement_children($children_rules, $children, $con, $arr_params, $extra_extra_name) {
    $workings = "";
    $arr = array();

    $workings = "<font color='blue'><b>EXTRA MANDATORY MEAL</b>: $extra_extra_name : </font>";

    $currency_buy = $arr_params["currency_buy_code"];

    $current_contract_id = $arr_params["current_contract_id"];
    $arr_age_groups = _rates_calculator_get_children_agegroups($current_contract_id, $con);

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
                                "EXTRA_MEAL_SUPPLEMENT");

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

function _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption) {
    $workings = "";
    $arr = array();

    $currency_buy = $arr_params["currency_buy_code"];

    $current_contract_id = $arr_params["current_contract_id"];
    $arr_age_groups = _rates_calculator_get_children_agegroups($current_contract_id, $con);

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

                        $workings = "<font color='blue'><b>MEAL SUPPLEMENT</b>: $meal_supplement_caption </font>" .
                                " Ch #$ec {$age_from}-{$age_to}yrs => $currency_buy $child_meal_rate";

                        //apply spo percent discount if any for that pax
                        _rates_calculator_apply_spo_discount_percentage($child_meal_rate, $workings, $arr_params,
                                "CHILDREN", $age, "", "MEAL_SUPPLEMENT");


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

function _rates_calculator_regroup_children_by_age($arr_params, $children, $con) {
    //for each age group, get children that fall within that range
    $arr_group_children = array();

    //group children by age groups in the contract
    $current_contract_id = $arr_params["current_contract_id"];
    $arr_age_groups = _rates_calculator_get_children_agegroups($current_contract_id, $con);


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

function _rates_calculator_lookup_single_parent_parent_rates($rules, $arr_params, $arr_adultpolicies_rules, $children, $arr_eci) {

    $arr = array();
    $rates = 0;
    $workings = "SINGLE PRNT: ";

    $currency_buy = $arr_params["currency_buy_code"];

    $num_children = count($children);
    $basis = "";

    while ($basis == "" && $num_children > 1) {
        $basis = _rates_calculator_lookup_single_parent_rules_cells($rules, "basis", $num_children);
        $category = _rates_calculator_lookup_single_parent_rules_cells($rules, "category", $num_children);
        $value = _rates_calculator_lookup_single_parent_rules_cells($rules, "value", $num_children);
        $num_children --;
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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM");

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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM");


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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM");


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
            _rates_calculator_apply_spo_discount_percentage($rates, $workings, $arr_params, "ADULT", $single_pax["age"], $single_pax["bride_groom"], "ROOM");

            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates,
                "ADCH" => "ADULT",
                "AGE" => $single_pax["age"],
                "BRIDEGROOM" => $single_pax["bride_groom"]);
        }
    }

    return $arr;
}

function _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules, $arr_params, $arr_adultpolicies_rules, $arr_eci) {

    $arr_final = array();
    $rates = 0;

    $currency_buy = $arr_params["currency_buy_code"];

    //for each child in $arr_group_children, get the agerange and the index
    //for that age range and index, lookup the category, basis and value

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
                $workings = "SINGLE PARENT: (CH #$index {$child_age}yr $category $currency_buy $value)";

                //take price as it is
                $rates = $value;

                if ($category == "SINGLE") {

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                    //NO BREAK, MAY NEED TO CONTINUE IF MORE CHILDREN LEFT...
                } else if ($category == "DOUBLE" || $category == "1/2 DBL") {
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

                if ($category == "SINGLE") {

                    //get price from adult for that index and category
                    $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
                    $adult_index = 1;
                    $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

                    $workings = "SINGLE PARENT: (CH #$index $category {$child_age}yr : {$value}% of ";
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
                } else if ($category == "DOUBLE" || $category == "1/2 DBL") {
                    //get price from adult for that index and category
                    $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
                    $adult_index = 2;
                    $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];


                    $workings = "SINGLE PARENT: (CH $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;
                    $workings .= " = $currency_buy $fees)";

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 2);

                    //BREAK HERE, NO NEED TO CONTINUE FURTHER
                    break;
                } else if ($category == "TRPL") {

                    //get price from adult for that index and category
                    $arr_adult_workings = array("RATES_ADULT" => 0, "WORKINGS_ADULT" => "");
                    $adult_index = 3;
                    $arr_adult_workings = _rates_calculator_calc_adult_recur($adult_index, $arr_adultpolicies_rules, $arr_adult_workings, $arr_params);
                    $rates_adult = $arr_adult_workings["RATES_ADULT"];
                    $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

                    $workings = "SINGLE PARENT: (CH $category {$child_age}yr : {$value}% of ";
                    if (trim($workings_adult) != "") {
                        $workings .= "$workings_adult";
                    }

                    $fees = 0;
                    if ($value > 0) {
                        $fees = round(($value / 100) * $rates_adult, 2);
                    }
                    $rates = $fees;
                    $workings .= " = $currency_buy $fees)";

                    $_arr[] = array("WORKINGS" => $workings, "RATES" => $rates, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 3);

                    //BREAK HERE, NO NEED TO CONTINUE FURTHER
                    break;
                } else if ($category == "SHARING") {
                    //this is not possible
                    $workings = "SINGLE PARENT: (CH #$index {$child_age}yr : SHARING % IS NOT POSSIBLE)";
                    $_arr[] = array("WORKINGS" => $workings, "RATES" => 0, "CHILDINDEX" => $index, "TO_SPLIT_BETWEEN" => 0);
                }
            }

            $index --;
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
                _rates_calculator_apply_spo_discount_percentage($rates, $work, $arr_params, "CHILDREN", $child_age, "", "ROOM");


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
                    _rates_calculator_apply_spo_discount_percentage($ch_buyprice, $msg, $arr_params, "CHILDREN", $child_age, "", "ROOM");


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

function _rates_calculator_apply_rates_lco_flat_pni($arr, $arr_lco, $arr_params, &$lco_applied) {
    //$lco_applied = false;
    //this will only apply if late check out FLAT PNI, then:
    //share the lco costs between non FOC pax

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
    $pax_lco = round($charge_value / $num_non_foc_pax, 2);

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

            $msg .= " <br> ($workings = FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_lco per non FOC pax) ";
            $costings = $pax_lco;

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($costings, $msg, $arr_params, $pax_adch, $pax_age, $pax_bridegroom, "ROOM");

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $arr_params) {
    //this will only apply if early check in FLAT PNI, then:
    //share the eci costs between non FOC pax
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
    $pax_eci = round($charge_value / $num_non_foc_pax, 2);

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

            $msg .= "<br> + ($workings =  FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_eci per non FOC pax) ";

            //apply spo percent discount if any for that pax
            _rates_calculator_apply_spo_discount_percentage($pax_eci, $msg, $arr_params, $pax_adch, $pax_age, $pax_bridegroom, "ROOM");

            $costings += $pax_eci;

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_calc_discount_PNI($arr_params, $arr) {
    //this discount is split equally and is applicable for all pax irrespective of age, bridegroom

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
    $currency_buy = $arr_params["currency_buy_code"];
    $num_pax = count($arr);

    for ($i = 0; $i < count($arr_spo_discounts); $i++) {
        $discount_item = $arr_spo_discounts[$i];

        $spo_id = $discount_item["SPO_ID"];
        $spo_name = $discount_item["SPO_NAME"];
        $spo_type = $discount_item["SPO_TYPE"];
        $disc_type = $discount_item["ROOM_ALL_FLAT"]; //is discount percentage_room, percentage_all or FLAT
        $disc_value = $discount_item["VALUE"]; //value of discount
        
        //finally apply the discount when it flat PPPN
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
                        
                        if($costings < 0)
                        {
                            $costings = 0;
                        }

                        $arr[$p]["MSG"] = $msg;
                        $arr[$p]["COSTINGS"] = $costings;
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


        if (($adult_children == "ADULT" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "ADULT")) ||
                $adult_children == "CHILDREN" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "CHILDREN")) {
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

    return;
}

function _rates_calculator_apply_spo_discount_percentage(&$rates, &$msg, $arr_params, $adult_children, $pax_age, $pax_bridegroom, $room_nonroom) {
    // $adult_child = {ADULT, CHILDREN}
    // $pax_age is the age of the pax in question
    // $pax_bridegroom is the marital status of the pax in question
    // $room_nonroom = {ROOM, NONROOM}

    $arr_spo_discounts = $arr_params["spo_discounts_array"];
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


        if (($adult_children == "ADULT" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "ADULT")) ||
                $adult_children == "CHILDREN" && ($disc_ad_ch == "BOTH" || $disc_ad_ch == "CHILDREN")) {
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

        $fees = 0;

        if ($charge_value > 0) {
            $fees = round(($charge_value / 100) * $rates, 2);
        }
        $msg .= "<br> - ($workings Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
        $rates -= $fees;
    } else if ($charge_type == "%C") {
        $fees = 0;
        if ($charge_value > 0) {
            $fees = round(($charge_value / 100) * $rates, 2);
        }

        $msg .= "<br> + ($workings Charge <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
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

            $fees = 0;

            if ($charge_value > 0) {
                $fees = round(($charge_value / 100) * $rates, 2);
            }
            $msg .= "<br> - ($workings Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
            $rates -= $fees;
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
?>

