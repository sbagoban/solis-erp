<?php

function _rates_get_contract_id($con, $arr_params) {
    try {

        $checkin_date = $arr_params["checkin_date"]; //yyyy-mm-dd
        $checkout_date = $arr_params["checkout_date"]; //yyyy-mm-dd

        $country = $arr_params["country"];
        $hotel = $arr_params["hotel"];
        $hotelroom = $arr_params["hotelroom"];
        $mealplan = $arr_params["mealplan"];
        $rate = $arr_params["rate"];
        $touroperator = $arr_params["touroperator"];
        $filter_contract_ids = $arr_params["contractids"];

        //return array of days
        //For each day in from check in to check out
        //  get all contracts that are not deleted 
        //  that are active internal or external
        //  for that hotel
        //  having the selected hotelroom
        //  having the selected country
        //  having the selected mealplan
        //  having the selected rate
        //  having the selected TO
        //  with date between contract.activefrom AND contract.activeto
        //Next


        $checkin = new DateTime($checkin_date);
        $checkout = new DateTime($checkout_date);
        //$checkout = $checkout->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($checkin, $interval, $checkout);
        $num_nights = iterator_count($period);

        $arr_daily = array();
        $idx = 0;
        foreach ($period as $dt) {

            $this_date = $dt->format("Y-m-d");
            $arr_daily[$idx]["DATE"] = $this_date;

            $arr_contracts = _rates_get_contract_for_the_date($arr_params, $this_date, $con);
            $arr_daily[$idx]["CONTRACT_ID"] = $arr_contracts;

            $idx ++;
        }
        //==========================================================
        return array("OUTCOME" => "OK", "NUM NIGHTS" => $num_nights, "DAILY" => $arr_daily);
    } catch (Exception $ex) {
        return array("OUTCOME" => "_RATES_GET_CONTRACT: " . $ex->getMessage());
    }
}

function _rates_get_contract_for_the_date($arr_params, $thedate, $con) {

    //return list of contract ID(s) that fall within the parameters
    //$thedate in yyyy-mm-dd

    $country = $arr_params["country"];
    $hotel = $arr_params["hotel"];
    $hotelroom = $arr_params["hotelroom"];
    $mealplan = $arr_params["mealplan"];
    $rate = $arr_params["rate"];
    $touroperator = $arr_params["touroperator"];
    $filter_contract_ids = $arr_params["contractids"];
    
    $filter_cond = "";
    if($filter_contract_ids != "")
    {
         $filter_cond = " AND id IN ($filter_contract_ids) ";
    }

    $sql = "SELECT * FROM tblservice_contract WHERE deleted = 0 AND active_internal = 1 $filter_cond ";

   

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

function _rates_validate_daily_contract_id($arr_days, $rollover) {
    //validate day by day
    //must not allow overlapping contracts for any day
    //must make sure that: either there is a similar contract id for each day
    //or there no contract id for any days

    $arr_msg = _rates_validate_daily_contract_id_overlapping($arr_days, $rollover);
    if (count($arr_msg) > 0) {
        return array("OUTCOME" => "FAIL_OVERLAPPING_TEST", "DETAILS" => $arr_msg);
    }

    $arr_msg = _rates_validate_daily_contract_id_different($arr_days, $rollover);
    if (count($arr_msg) > 0) {
        //here error can be either MULTIPLE_CONTRACT OR NO_CONTRACT
        for ($i = 0; $i < count($arr_msg); $i++) {
            if ($arr_msg[$i]["STATUS"] == "MULTIPLE_CONTRACT") {
                //surely MULTIPLE_CONTRACT
                return array("OUTCOME" => "FAIL_MULTILE_PERIODS_TEST", "DETAILS" => $arr_msg);
            }
        }

        //otherwise must be NO_CONTRACT
        return array("OUTCOME" => "FAIL_NO_CONTRACT", "DETAILS" => $arr_msg);
    }

    return array("OUTCOME" => "OK", "DETAILS" => array());
}

function _rates_validate_daily_contract_id_overlapping($arr_days, $rollover) {
    //check if there are possibilities of more than one contracts for any day
    $arr_error = array();

    $rollover_msg = "";
    if ($rollover) {
        $rollover_msg = "<font color='red'>NO CONTRACT FOUND FOR THIS DATE</font><BR>ROLLING OVER LAST YEAR...";
    }

    for ($i = 0; $i < count($arr_days); $i++) {
        $arr_contracts = $arr_days[$i]["CONTRACT_ID"];
        $date = $arr_days[$i]["DATE"];
        if (count($arr_contracts) > 1) {
            $arr_error[] = array("DATE" => $date,
                "STATUS" => "OVERLAP_CONTRACTS",
                "ROLLOVER" => $rollover,
                "ROLLOVER_BASIS" => "",
                "ROLLOVER_VALUE" => "",
                "CONTRACT_ID" => implode(",", $arr_days[$i]["CONTRACT_ID"]),
                "CURRENCY_SELL_CODE" => "",
                "CURRENCY_BUY_CODE" => "",
                "COSTINGS_WORKINGS" => array(array("MSG"=>"$rollover_msg <font color='red'>OVERLAPPING CONTRACTS " . implode(",", $arr_days[$i]["CONTRACT_ID"]) . "</font>",
                                             "COSTINGS"=>array())));
        }
    }

    return $arr_error;
}

function _rates_validate_daily_contract_id_different($arr_days, $rollover) {
    //check if there are possibilites of more than one contract for a period

    $valid_contract_id = -1;

    $rollover_msg = "";
    if ($rollover) {
        $rollover_msg = "<font color='red'>NO CONTRACT FOUND FOR THIS DATE</font><BR>ROLLING OVER LAST YEAR...";
    }

    $arr_error = array();

    for ($i = 0; $i < count($arr_days); $i++) {
        $arr_contracts = $arr_days[$i]["CONTRACT_ID"];
        $date = $arr_days[$i]["DATE"];

        if (count($arr_contracts) == 1) {
            if ($valid_contract_id == -1) {
                $valid_contract_id = $arr_contracts[0];
            } else {
                if ($valid_contract_id != $arr_contracts[0]) {
                    $arr_error[] = array("DATE" => $date,
                        "STATUS" => "MULTIPLE_CONTRACT",
                        "ROLLOVER" => $rollover,
                        "ROLLOVER_BASIS" => "",
                        "ROLLOVER_VALUE" => "",
                        "CONTRACT_ID" => $valid_contract_id . " --> " . $arr_contracts[0],
                        "CURRENCY_SELL_CODE" => "",
                        "CURRENCY_BUY_CODE" => "",
                        "COSTINGS_WORKINGS" => array(array("MSG"=>"$rollover_msg<BR><font color='red'>MULTIPLE CONTRACT PERIODS: CONTRACT ID</font>",
                                                     "COSTINGS"=>array())));
                    $valid_contract_id = $arr_contracts[0];
                }
            }
        } else if (count($arr_contracts) == 0) {
            //this day is without a contract
            $arr_error[] = array("DATE" => $date,
                "STATUS" => "NO_CONTRACT",
                "ROLLOVER" => $rollover,
                "ROLLOVER_BASIS" => "",
                "ROLLOVER_VALUE" => "",
                "CONTRACT_ID" => "",
                "CURRENCY_SELL_CODE" => "",
                "CURRENCY_BUY_CODE" => "",
                "COSTINGS_WORKINGS" => array(array("MSG"=>"$rollover_msg<BR><font color='red'>NO CONTRACT FOUND FOR THIS DATE</font>",
                                             "COSTINGS"=>array())));
        }
    }

    return $arr_error;
}
?>

