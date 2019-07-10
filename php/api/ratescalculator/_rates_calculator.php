<?php

function _rates_calculator($con, $arr_params) {
    try {

        $time_pre = microtime(true);


        $adult = $arr_params["adults"];
        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkin_time = $arr_params["checkin_time"];
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd
        $checkout_time = $arr_params["checkout_time"];
        $children = $arr_params["children"];
        $country = $arr_params["country"];
        $hotel = $arr_params["hotel"];
        $hotelroom = $arr_params["hotelroom"];
        $mealplan = $arr_params["mealplan"];
        $supp_mealplan = $arr_params["supp_mealplan"];
        $rate = $arr_params["rate"];
        $touroperator = $arr_params["touroperator"];

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
                $msg .= "ROLLING OVER: <b>$checkin_rollover_dMY - $checkout_rollover_dMY</b>";

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
                $children_age_test = _rates_calculator_test_children_ages($arr_capacity, $arr_params, $con);
                if ($children_age_test != "OK") {
                    $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 3</font>: CHILDREN AGES: $children_age_test",
                        "COSTINGS" => array());
                    $arr_daily[$idx]["STATUS"] = "CHILDREN_AGE_FAIL";
                } else {

                    $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 3</font>: CHILDREN AGES",
                        "COSTINGS" => array());

                    //TEST 4: ADULT AND CHILDREN CAPACITY
                    $capacity_test_adch = _rates_calculator_adch_capacity($arr_capacity, $arr_params, $this_date, $con);
                    if ($capacity_test_adch != "OK") {
                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 4</font>: CAPACITY TEST ADULT + CHILDREN SHARING: $capacity_test_adch",
                            "COSTINGS" => array());

                        $arr_daily[$idx]["STATUS"] = "CAPACITY_FAIL";
                    } else {

                        $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 4</font>: CAPACITY ADULT + CHILDREN SHARING",
                            "COSTINGS" => array());

                        //TEST 5: CHILDREN IN OWN ROOM
                        $capacity_test_ch_ownroom = _rates_calculator_ch_own_capacity($arr_capacity, $arr_params, $this_date);
                        if ($capacity_test_ch_ownroom != "OK") {
                            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='red'>FAILED TEST 5</font>: CAPACITY TEST CHILDREN OWN ROOM: $capacity_test_ch_ownroom",
                                "COSTINGS" => array());

                            $arr_daily[$idx]["STATUS"] = "CHILDREN_OWN_ROOM_FAIL";
                        } else {
                            $arr_daily[$idx]["COSTINGS_WORKINGS"][] = array("MSG" => "<font color='green'>PASSED TEST 5</font>: CAPACITY CHILDREN OWN ROOM",
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

                        $arr = _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $currency_buy);

                        $arr = _rates_calculator_calc_rollover_percentage($arr_params, $arr, $roll_over_flg);
                        $arr = _rates_calculator_calc_rollover_flat_pni($arr_params, $arr, $roll_over_flg, "ROOM");

                        $arr = _rates_calculator_lookup_rates_calc_PPPN($arr, $arr_params, $arr_taxcomm, $con, "ROOM");

                        $arr_daily[$idx]["COSTINGS_WORKINGS"] = $arr;

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
                            $arr_rates = _rates_calculator_apply_rates_lco($arr_rates, $arr_lco, $lco_applied, $currency_buy);
                            $arr_rates = _rates_calculator_apply_rates_lco_flat_pni($arr_rates, $arr_lco, $currency_buy, $lco_applied);
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
                    $workings = "<font color='blue'>EARLY CHECK IN</font>: before $limit_time ";
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
                    $workings = "<BR><font color='blue'>LATE CHECK OUT</font>: after $limit_time";
                    return array("WORKINGS" => $workings, "CHARGE_TYPE" => $charge_type, "CHARGE_VALUE" => $charge_value);
                }
            }
        }
    }

    return array("CHARGE_TYPE" => "", "WORKINGS" => "", "CHARGE_VALUE" => "");
}

function _rates_calculator_test_children_ages($arr_capacity, $arr_params, $con) {
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
            return "OK";
        }


        $children = array();
        for ($i = 0; $i < count($arr_params["children"]); $i++) {
            if ($arr_params["children"][$i]["sharing_own"] == "OWN") {
                //filter only those children that are in own room
                $children[] = $arr_params["children"][$i];
            }
        }


        if (count($children) == 0) {
            return true; //there is children in own room to test here
        }

        $rules = _rates_calculator_get_arrcapacity_daterange($arr_capacity, $hotelroom, $this_date);


        if (!is_null($rules)) {
            $arr_capacity_rules = $rules["date_capacity_rules"];


            //============ for each capacity rule =================
            for ($i = 0; $i < count($arr_capacity_rules); $i++) {
                $rule_capacity = $arr_capacity_rules[$i]["rule_capacity"];

                $adult = 0; //test children own room with adult = 0
                if (_rates_calculator_test_capacity_rule_persons($rule_capacity, $adult, $children)) {
                    return "OK";
                }
            }
            //======================================================
            //if we are here means that no rules satisfy
            return "NO CHILDREN OWN ROOM CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT";
        } else {
            return "NO CHILDREN OWN ROOM CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT";
        }
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_ADCH_CAPACITY: " . $ex->getMessage();
    }
}

function _rates_calculator_adch_capacity($arr_capacity, $arr_params, $this_date, $con) {

    //test adult + SHARING children
    //return OK if capacity is satisfied, error message otherwise
    try {


        $hotelroom = $arr_params["hotelroom"];

        $room_details = _rates_calculator_get_room_details($arr_capacity, $hotelroom);
        $room_type = $room_details["room_variants"]; //"PERSONS", "UNITS"

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
            $arr_capacity_rules = $rules["date_capacity_rules"];


            //============ for each capacity rule =================
            for ($i = 0; $i < count($arr_capacity_rules); $i++) {
                $rule_capacity = $arr_capacity_rules[$i]["rule_capacity"];

                if ($room_type == "PERSONS") {
                    if (_rates_calculator_test_capacity_rule_persons($rule_capacity, $adult, $children)) {
                        return "OK";
                    }
                } else if ($room_type == "UNITS") {
                    if (_rates_calculator_test_capacity_rule_units($rule_capacity, $arr_params, $con)) {
                        return "OK";
                    }
                }
            }
            //======================================================
            //if we are here means that no rules satisfy
            return "ADULT AND CHILDREN CAPACITY <B>NOT</B> SATISFIED";
        } else {
            return "NO ADULT AND CHILDREN CAPACITY DEFINED FOR THAT DATE, ROOM AND CONTRACT";
        }
    } catch (Exception $ex) {
        return "_RATES_CALCULATOR_ADCH_CAPACITY: " . $ex->getMessage();
    }
}

function _rates_calculator_test_capacity_rule_units_getoccupation($rule_capacity, $category) {
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

function _rates_calculator_test_capacity_rule_units($rule_capacity, $arr_params, $con) {
    //test the specific capacity rule to know if adult and children are allowable
    //return true if fine, false other wise

    $adult = count($arr_params["adults"]);
    $children = $arr_params["children"];

    $number_pax = $adult + count($children);

    $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con);


    //get the rules occupation rules
    $arr_std_occup = _rates_calculator_test_capacity_rule_units_getoccupation($rule_capacity, "STANDARDOCCUPATION");
    $arr_add_adult_occup = _rates_calculator_test_capacity_rule_units_getoccupation($rule_capacity, "ADDITIONALPERSONS");
    $arr_children_rules = _rates_calculator_test_capacity_rule_units_getoccupation($rule_capacity, "CH");

    //$arr_std_occup and $arr_add_adult_occup should have only 1 entry
    //$arr_children_rules can have multiple entries for each age group
    //CHECK 1: STANDARD OCCUPATION 
    //first check if adults fit in standard occupation rules

    $std_min = $arr_std_occup[0]["MIN"];
    $std_max = $arr_std_occup[0]["MAX"];

    //====================================================
    //is there enough pax?
    if ($number_pax < $std_min) {
        return false;
    }

    //====================================================

    _rates_calculator_test_capacity_rule_units_extra_adult($std_max, $adult);
    _rates_calculator_test_capacity_rule_units_extra_child($std_max, $arr_group_children);

    //=====================================================
    //check for adult
    if ($adult > 0) {
        //there is extra adult
        $extra_adult_max = $arr_add_adult_occup[0]["MAX"];
        if ($adult > $extra_adult_max) {
            return false;
        }
    }

    //=====================================================
    //check for children
    for ($i = 0; $i < count($arr_group_children); $i++) {
        $age_from = $arr_group_children[$i]["AGFROM"];
        $age_to = $arr_group_children[$i]["AGTO"];
        $arr_children = $arr_group_children[$i]["CHILDREN"];
        $child_count = count($arr_children);
        if ($child_count > 0) {
            //got extra children here
            //check if it is within the range of that age group
            if (!_rates_calculator_test_capacity_rule_units_children_age($child_count, $age_from, $age_to, $arr_children_rules)) {
                return false;
            }
        }
    }

    //==========================================================


    return true;
}

function _rates_calculator_test_capacity_rule_units_children_age($child_count, $age_from, $age_to, $arr_children_rules) {
    for ($i = 0; $i < count($arr_children_rules); $i++) {

        if ($arr_children_rules[$i]["AGE_FROM"] == $age_from &&
                $arr_children_rules[$i]["AGE_TO"] == $age_to) {
            $max = $arr_children_rules[$i]["MAX"];
            if ($child_count > $max) {
                return false;
            } else {
                return true;
            }
        }
    }

    return true;
}

function _rates_calculator_test_capacity_rule_units_extra_child(&$std_max, &$arr_group_children) {
    //decrement children from the standard allocation to see what children are left as extra

    $arr_idx_to_remove = array();

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

function _rates_calculator_test_capacity_rule_persons($rule_capacity, $adult, $children) {
    //test the specific capacity rule to know if adult and children are allowable
    //return true if fine, false other wise
    //if ($adult > 0) {
    if (!_rates_calculator_adch_capacity_test("ADULT", $adult, 0, $rule_capacity)) {
        return false;
    }
    //}
    //adult check passed, now check for each accompanying chid
    for ($c = 0; $c < count($children); $c++) {
        $age = $children[$c]["age"];
        if (!_rates_calculator_adch_capacity_test("CHILD", 1, $age, $rule_capacity)) {
            return false;
        }
    }

    return true;
}

function _rates_calculator_adch_capacity_test($adch, $pax_count, $age, &$rule_capacity) {

    for ($j = 0; $j < count($rule_capacity); $j++) {
        $capacity_category = $rule_capacity[$j]["capacity_category"];
        $capacity_minpax = $rule_capacity[$j]["capacity_minpax"];
        $capacity_maxpax = $rule_capacity[$j]["capacity_maxpax"];
        $capacity_child_agefrom = $rule_capacity[$j]["capacity_child_agefrom"];
        $capacity_child_ageto = $rule_capacity[$j]["capacity_child_ageto"];

        if ($capacity_minpax == "") {
            $capacity_minpax = 0;
        }
        if ($capacity_maxpax == "") {
            $capacity_maxpax = 0;
        }


        if ($adch == $capacity_category) {
            if ($capacity_minpax <= $pax_count && $pax_count <= $capacity_maxpax) {
                if ($adch == "ADULT") {
                    return true;
                } else if ($adch == "CHILD") {
                    //age check for children
                    if ($capacity_child_agefrom <= $age && $age <= $capacity_child_ageto) {
                        $rule_capacity[$j]["capacity_maxpax"] --; //decrement for next child if needed
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

function _rates_calculator_get_arrcapacity_room($arr_capacity, $roomid) {
    for ($i = 0; $i < count($arr_capacity); $i++) {
        //get the room
        if ($roomid == $arr_capacity[$i]["room_id"]) {
            return $arr_capacity[$i];
        }
    }

    return null;
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
        $arr_std_occup = _rates_calculator_test_capacity_rule_units_getoccupation($rule_capacity, "STANDARDOCCUPATION");

        $arr_group_children = _rates_calculator_regroup_children_by_age($arr_params, $children, $con);

        $std_max = $arr_std_occup[0]["MAX"];

        //get extra adults and children
        _rates_calculator_test_capacity_rule_units_extra_adult($std_max, $adult);
        _rates_calculator_test_capacity_rule_units_extra_child($std_max, $arr_group_children);

        
        $flg_extra_people = false;
        
        //for each extra adult and extra children, calculate the price
        if ($adult > 0) {
            //there is extra adult
            $arr_extra_adult = _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates);
            $extra_adult_rates = $arr_extra_adult["RATES_EXTRA_ADULT"];
            $extra_adult_workings = $arr_extra_adult["WORKINGS_EXTRA_ADULT"];
            $flg_extra_people = true;
            $arr[] = array("MSG"=>$extra_adult_workings,"COSTINGS"=>$extra_adult_rates);
        }

        //for each extra children, calculate the price
        $arr_children_rules = $rules["date_childpolicies_rules"];
        
        for ($i = 0; $i < count($arr_group_children); $i++) {
            $arr_children = $arr_group_children[$i]["CHILDREN"];
            $age_from = $arr_group_children[$i]["AGFROM"];
            $age_to = $arr_group_children[$i]["AGTO"];

            for ($idx = 1; $idx <= count($arr_children); $idx++) {
                $flg_extra_people = true;
                
                $basis = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "basis", $idx, $age_from, $age_to);
                $value = _rates_calculator_lookup_rates_units_lookup_children_rates($arr_children_rules, "value", $idx, $age_from, $age_to);

                
                if ($basis == "%") {
                    $percentage = 0;
                    if ($value > 0) {
                        $percentage = round(($value / 100) * $normal_rates, 2);
                    }
                    
                    $rates_children = $percentage;
                    $workings_children = "<b>EXTRA</b>: Ch #$idx (<b>$age_from-$age_to yrs</b>) = <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";
                    
                    _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);
                    
                    $arr[] = array("MSG"=>$workings_children,"COSTINGS"=>$rates_children);
                    
                } else if ($basis == "FLAT") {
                    $rates_children = $value;
                    $workings_children = "<b>EXTRA</b>: Ch #$idx (<b>$age_from-$age_to yrs</b>) = $currency_buy $value";
                    
                    _rates_calculator_apply_rates_eci_percentage($rates_children, $arr_eci, $workings_children, $currency_buy);
                    
                    $arr[] = array("MSG"=>$workings_children,"COSTINGS"=>$rates_children);
                }
            }
        }
        
        //finally get the normal standard unit price and split it per person
        $num_persons = 0;
        if($flg_extra_people)
        {
            $num_persons = $arr_std_occup[0]["MAX"];
        }
        else
        {
            $num_persons = count($arr_params["adults"]) + count($arr_params["children"]);
        }
        
        $arr_normal = _rates_calculator_lookup_rates_units_standard($rules, $arr_params);
        $normal_rates = $arr_normal["RATES_STANDARD"];
        $normal_workings = $arr_normal["WORKINGS_STANDARD"];
        
        $per_person_buyprice = round($normal_rates / $num_persons); 
        $cumul_buyprice = 0;

        for ($adinx = 1; $adinx <= $num_persons; $adinx++) {
            if ($adinx == $adult) {
                $per_person_buyprice = $normal_rates - $cumul_buyprice;
            } else {
                $cumul_buyprice += $per_person_buyprice;
            }

            $_workings = "$normal_workings => Pax #$adinx = $currency_buy $per_person_buyprice";

            //record adult rates for each adult
            _rates_calculator_apply_rates_eci_percentage($per_person_buyprice, $arr_eci, $_workings, $currency_buy);
            $arr[] = array("MSG" => $_workings, "COSTINGS" => $per_person_buyprice);
        }

    }
    
    


    return $arr;
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

function _rates_calculator_lookup_rates_units_extra_adult($rules, $arr_params, $adult, $normal_rates) {
    $arr = array();
    $rates = 0;
    $workings = "";
    $currency_buy = $arr_params["currency_buy_code"];

    $arr_adult_rules = $rules["date_adultpolicies_rules"];

    //get the basis and value for each extra adult
    for ($i = 1; $i <= $adult; $i++) {
        $basis = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_basis", $i);
        $value = _rates_calculator_lookup_rates_units_lookup_adult_rates($arr_adult_rules, "additional_adult_value", $i);


        if ($basis == "%") {
            $percentage = 0;
            if ($value > 0) {
                $percentage = round(($value / 100) * $normal_rates, 2);
            }
            $rates = $percentage;
            $workings = "<b>EXTRA</b>: Ad #$i: <b>$value%</b> of $currency_buy $normal_rates = $currency_buy $percentage";
        } else if ($basis == "FLAT") {
            $rates = $value;
            $workings = "<b>EXTRA</b>: Ad #$i: $currency_buy $value";
        }
        
        $arr[] = array("MSG"=>$workings,"COSTINGS"=>$rates);
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
        $arr = _rates_calculator_lookup_rates_single_parent($arr_capacity, $arr_params, $this_date, $con, $flg_got_rates, $con, $arr_eci);
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
        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_rollover;
            } else {
                $pax_rollover = $roll_over_value - $cumul_applied;
            }
            $applied_to ++;

            $costings += $pax_rollover;
            $msg .= "<br> + (<font color='blue'>ROLLOVER: </font> FLAT PNI : $currency_buy $roll_over_value &#247; $num_non_foc_pax = $currency_buy $pax_rollover per non FOC pax)";


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

            $msg .= "<br> + (<font color='blue'>ROLLOVER: </font> $roll_over_value % of $currency_buy $costings = $currency_buy $addon)";
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
        $the_age_range = _rates_calculator_single_parent_exact_match_children($arr_age_ranges, $children, $arr_params, $arr_group_children);
        if ($the_age_range == "") {
            //if no exact match found, look for next best match
            $the_age_range = _rates_calculator_single_parent_nextbest_match_children($arr_age_ranges, $children, $arr_params, $arr_group_children);
        }


        if ($the_age_range == "") {
            //really no rates defined for single parent!
            $flg_got_rates = false;
            $arr[] = array("MSG" => "NO SINGLE PARENT RATES", "COSTINGS" => array());
        } else {

            $flg_got_rates = true;
            $rules_age_range = _rates_calculator_single_parent_get_rules_by_agerange($arr_singleparent_rules, $the_age_range);

            //calculate children rates
            $arr_children_rates = _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules_age_range, $arr_params, $arr_adultpolicies_rules, $con, $arr_eci);
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

            //record adult rates for each adult
            _rates_calculator_apply_rates_eci_percentage($adult_buyprice, $arr_eci, $_workings, $currency_buy);
            $arr[] = array("MSG" => $_workings, "COSTINGS" => $adult_buyprice);
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
    $rates_children = 0;
    $workings = "";
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
                $workings_adult = $arr_adult_workings["WORKINGS_ADULT"];

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

        if ($split_between == 0) {
            $temp_arr[] = array("MSG" => $work, "COSTINGS" => $rates);
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

                
                _rates_calculator_apply_rates_eci_percentage($ch_buyprice, $arr_eci, $msg, $currency_buy);
                $temp_arr[] = array("MSG" => $msg, "COSTINGS" => $ch_buyprice);

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
                        $arr["STATUS"] = "OK";
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
    $adult_fees = 0;
    $children_fees = 0;
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

                $workings = "<font color='blue'>EXTRA MANDATORY MEAL: $extra_extra_name : </font>";

                //============= now adult ==========
                $extra_adult = $rules["extra_adult_count"];
                for ($a = 1; $a <= $adult; $a++) {
                    $msg = "$workings Ad #{$a} = $currency_buy $extra_adult";
                    $arr[] = array("MSG" => $msg, "COSTINGS" => $extra_adult);
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

    $adult_fees = 0;
    $children_fees = 0;
    $workings = "";

    $arr = array();

    $supp_mealplan = $arr_params["supp_mealplan"];
    $adult = count($arr_params["adults"]);
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

                //=========================================================================
                //=========================================================================
                //for each adult:

                for ($a = 1; $a <= $adult; $a++) {
                    $workings = "<font color='blue'>MEAL SUPPLEMENT: $meal_supplement_caption </font> Ad #$a => $currency_buy " . $rules["meal_adult_count"];
                    $arr[] = array("MSG" => $workings, "COSTINGS" => $rules["meal_adult_count"]);
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
    $children_fees = 0;
    $arr = array();

    $workings = "<font color='blue'>EXTRA MANDATORY MEAL: $extra_extra_name : </font>";

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
                        $msg = "$workings Ch #{$ch} ({$age_from}-{$age_to}yrs) = $currency_buy " . $children_rules[$r]["child_count"];
                        $arr[] = array("MSG" => $msg, "COSTINGS" => $children_rules[$r]["child_count"]);
                    }
                }
            }
        }
    }

    return $arr;
}

function _rates_calculator_meal_supplement_children($children_rules, $children, $con, $arr_params, $meal_supplement_caption) {
    $workings = "";
    $children_fees = 0;
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
                        $workings = "<font color='blue'>MEAL SUPPLEMENT: $meal_supplement_caption </font>" .
                                " Ch #$ec {$age_from}-{$age_to}yrs => $currency_buy " . $children_rules[$r]["child_count"];

                        $arr[] = array("MSG" => $workings, "COSTINGS" => $children_rules[$r]["child_count"]);
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

function _rates_calculator_single_parent_exact_match_children($arr_age_ranges, $children, $arr_params, $arr_group_children) {

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

function _rates_calculator_single_parent_nextbest_match_children($arr_age_ranges, $children, $arr_params, $arr_group_children) {
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

    $arr_items = array("ROOM");

    $currency_buy = $arr_params["currency_buy_code"];

    $num_children = count($children);
    $basis = "";

    while ($basis == "" && $num_children > 1) {
        $basis = _rates_calculator_lookup_single_parent_rules_cells($rules, "basis", $num_children);
        $category = _rates_calculator_lookup_single_parent_rules_cells($rules, "category", $num_children);
        $value = _rates_calculator_lookup_single_parent_rules_cells($rules, "value", $num_children);
        $num_children --;
    }


    if ($category == "SINGLE") {
        if ($basis == "FLAT") {
            //take the rate as it is
            $workings .= "$category $currency_buy $value";

            //take price as it is
            $rates = $value;
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);
            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates);
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

            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);
            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates);
        }
    } else if ($category == "1/2 DBL") {
        if ($basis == "FLAT") {
            //take the rate as it is
            $workings .= "$category $currency_buy $value";

            //take price as it is
            $rates = $value;
            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);
            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates);
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

            _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $workings, $currency_buy);
            $arr[] = array("MSG" => $workings, "COSTINGS" => $rates);
        }
    }

    return $arr;
}

function _rates_calculator_lookup_single_parent_children_rates($arr_group_children, $rules, $arr_params, $arr_adultpolicies_rules, $con, $arr_eci) {

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

            if ($split_between == 0) {
                _rates_calculator_apply_rates_eci_percentage($rates, $arr_eci, $work, $currency_buy);
                $temp_arr[] = array("MSG" => $work, "COSTINGS" => $rates);
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

                    _rates_calculator_apply_rates_eci_percentage($ch_buyprice, $arr_eci, $msg, $currency_buy);
                    $temp_arr[] = array("MSG" => $msg, "COSTINGS" => $ch_buyprice);

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

function _rates_calculator_apply_rates_lco_flat_pni($arr, $arr_lco, $currency_buy, &$lco_applied) {
    //$lco_applied = false;

    //this will only apply if late check out FLAT PNI, then:
    //share the lco costs between non FOC pax

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

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
}

function _rates_calculator_apply_rates_eci_flat_pni($arr, $arr_eci, $currency_buy) {
    //this will only apply if early check in FLAT PNI, then:
    //share the eci costs between non FOC pax
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
        if ($costings > 0) {
            $msg = $arr[$i]["MSG"];
            if ($applied_to < $num_non_foc_pax) {
                $cumul_applied += $pax_eci;
            } else {
                $pax_eci = $charge_value - $cumul_applied;
            }

            $applied_to ++;

            $costings += $pax_eci;
            $msg .= "<br> + ($workings =  FLAT PNI : $currency_buy $charge_value &#247; $num_non_foc_pax = $currency_buy $pax_eci per non FOC pax) ";

            $arr[$i]["MSG"] = $msg;
            $arr[$i]["COSTINGS"] = $costings;
        }
    }


    return $arr;
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
        $msg .= "- ($workings Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
        $rates -= $fees;
    } else if ($charge_type == "%C") {
        $fees = 0;
        if ($charge_value > 0) {
            $fees = round(($charge_value / 100) * $rates, 2);
        }

        $msg .= " + ($workings Charge <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
        $rates += $fees;
    }
}

function _rates_calculator_apply_rates_lco($arr_rates, $arr_lco, &$lco_applied, $currency_buy) {
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
            $msg .= "- ($workings Discount <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
            $rates -= $fees;
        } else if ($charge_type == "%C") {
            $fees = 0;
            if ($charge_value > 0) {
                $fees = round(($charge_value / 100) * $rates, 2);
            }

            $msg .= " => ($workings Charge <b>$charge_value%</b> of $currency_buy $rates = $currency_buy $fees) ";
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
        if ($arr[$i]["CATEGORY"] == $category) {
            $arr_lines = $arr[$i]["COSTINGS"];

            for ($c = 0; $c < count($arr_lines); $c++) {

                $arr_total[$c]["VALUE"] += $arr_lines[$c]["VALUE"];
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

