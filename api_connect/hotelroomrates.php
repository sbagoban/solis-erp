<?php

try {

    //=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
    set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        throw new Exception($errstr . " " . $errno);
    });

    $api_name = "api_hotel_room_rates";

    require_once("../php/utils/utilities.php");
    require_once("../php/connector/pdo_connect_main_login_internet.php");

    require_once("../php/api/ratescalculator/_rates_calculator.php");
    require_once("../php/api/ratescalculator/_rates_get_contract.php");
    require_once("../php/api/hotelspecialoffers/_spo.php");
    require_once("../php/api/hotelspecialoffers/_spo_taxcommi.php");
    require_once("../php/api/hotelcontracts/_contract_capacityarr.php");
    require_once("../php/api/hotelcontracts/_contract_exchangerates.php");
    require_once("../php/api/hotelcontracts/_contract_calculatesp.php");
    require_once("../php/api/hotelcontracts/_contract_taxcommi.php");
    require_once("../php/api/hotelcontracts/_contract_combinations_rooms.php");
    require_once("../php/globalvars/globalvars.php");


    date_default_timezone_set('Indian/Mauritius');

    $con = connect_login_pdo();

    //===========================================================================
    //authenticate user
    $authenticate_outcome = utils_authenticate_api_user($con, $_POST, $api_name);
    if (!is_array($authenticate_outcome)) {
        throw new Exception($authenticate_outcome);
    }

    //===========================================================================
    //validate parameters
    $validate_outcome = validate_rates_parameters($_POST, $con);
    if ($validate_outcome != "OK") {
        throw new Exception($authenticate_outcome);
    }

    //===========================================================================
    //all is valid
    $tofk = $authenticate_outcome["TOID"]; //get the toid of the user
    //===========================================================================
    //create the array of parameters for rates calculation
    $arr_params = array();

    $arr_params["adults"] = json_decode($_POST["adults"], true);
    $arr_params["children"] = json_decode($_POST["children"], true);
    $arr_params["checkin_date"] = $_POST["checkin_date"];
    $arr_params["checkout_date"] = $_POST["checkout_date"];
    $arr_params["checkin_time"] = $_POST["checkin_time"];
    $arr_params["checkout_time"] = $_POST["checkout_time"];
    $arr_params["checkout_time"] = $_POST["checkout_time"];
    $arr_params["country"] = getTOCoutry($tofk, $con);
    $arr_params["hotel"] = $_POST["hotel"];
    $arr_params["hotelroom"] = $_POST["hotelroom"];
    $arr_params["mealplan"] = $_POST["mealplan"];
    $arr_params["supp_mealplan"] = $_POST["supp_mealplan"];
    $arr_params["touroperator"] = $tofk;
    $arr_params["spo_chk_is_wedding"] = $_POST["is_wedding"];
    $arr_params["chk_show_invalid_spos"] = 0;
    $arr_params["spo_booking_date"] = $_POST["booking_date"];
    $arr_params["spo_chosen"] = "LOWEST";
    $arr_params["spo_party_pax"] = $_POST["party_pax"];
    $arr_params["spo_travel_date"] = $_POST["travel_date"];
    $arr_params["spo_type"] = "BOTH";
    $arr_params["contractids"] = "";
    
   
    //===========================================================================
    //get the special rate code and standard rate code of that tour operator
    $arr_rate_ids = lookup_TO_rates($tofk, $con);
    $special_rate_rate_id = $arr_rate_ids["SPECIAL"];
    $standard_rate_rate_id = $arr_rate_ids["STANDARD"];


    //================================================================
    //now, launch the rates calculator with special rates first
    //if no contracts found, then lauch the rates calculator with standard rates

    $arr_params["rate"] = $special_rate_rate_id;

    $rates_data = _rates_calculator($con, $arr_params);
    $status = $rates_data["OUTCOME"];

    if ($status == "FAIL_OVERLAPPING_TEST" || $status == "FAIL_MULTILE_PERIODS_TEST") {
        throw new Exception($status);
    } else if ($status == "FAIL_NO_CONTRACT") {
        //recall again this time with standard rates
        $arr_params["rate"] = $standard_rate_rate_id;
        $rates_data = null;
        $rates_data = _rates_calculator($con, $arr_params);
        $status = $rates_data["OUTCOME"];
    }

    if ($status == "FAIL_OVERLAPPING_TEST" || $status == "FAIL_MULTILE_PERIODS_TEST" || $status == "FAIL_NO_CONTRACT") {
        throw new Exception($status);
    }

    //if we are here, means status is OK
    //================================================================
    
    $arr_daily = $rates_data["DAILY"];
    $arr_return = array();
    
    for($i = 0; $i < count($arr_daily); $i++)
    {
        $arr_return[] = array("date"=>$arr_daily[$i]["DATE"],
                              "currency_id"=>$arr_daily[$i]["CURRENCY_SELL_ID"],
                              "amount"=>extractDailyTotal($arr_daily[$i]["COSTINGS_WORKINGS"]),
                              "status"=>$arr_daily[$i]["STATUS"]);
    }
    
    

    echo json_encode(array("OUTCOME" => "OK", "DATA" => $arr_return));
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME" => $ex->getMessage()));
}

function extractDailyTotal($arr_workings)
{
    $sum = 0;
    
    for($i = 0; $i < count($arr_workings); $i++)
    {
        if($arr_workings[$i]["MSG"] == "ROOM TOTAL")
        {
            $amt = extractDailyCategoryTotal($arr_workings[$i]["COSTINGS"]);
            $sum += $amt;
        }
        else if($arr_workings[$i]["MSG"] == "NON_ROOM TOTAL")
        {
            $amt = extractDailyCategoryTotal($arr_workings[$i]["COSTINGS"]);
            $sum += $amt;
        }        
        
    }
    
    return $sum;
}

function extractDailyCategoryTotal($arr_costings)
{
    $amt = 0;
    
    for($i = 0; $i < count($arr_costings); $i++)
    {
        if($arr_costings[$i]["CAPTION"] == "FINAL SELLING PRICE")
        {
            return $arr_costings[$i]["VALUE"];
        }
    }
    
    return $amt;
}

function getTOCoutry($tofk, $con) {
    //get the country of the tour operator

    $countryid = -1;

    $sql = "select * from tblto_countries where tofk = :toid limit 1";
    $query = $con->prepare($sql);
    $query->execute(array(":toid" => $tofk));
    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $countryid = $row["countryfk"];
    }

    return $countryid;
}

function lookup_TO_rates($toid, $con) {
    //lookup the rates of that tour operator
    $special_rate_id = -1;
    $standard_rate_id = -1;

    $sql = "select ratecode,specialratecode, 
            ifnull(rc_std.id,-1) as stdid, ifnull(rc_spec.id,-1) as specid
            from tbltouroperator tourop
            left join tblratecodes rc_std on ratecode = rc_std.ratecodes
            left join tblratecodes rc_spec on specialratecode = rc_spec.ratecodes
            where tourop.id = :toid";

    $query = $con->prepare($sql);
    $query->execute(array(":toid" => $toid));
    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $special_rate_id = $row["specid"];
        $standard_rate_id = $row["stdid"];
    }

    return array("SPECIAL" => $special_rate_id, "STANDARD" => $standard_rate_id);
}

function validate_rates_parameters($arrPOST, $con) {
    //is not blank: checkin_date, checkout_date, hotel, hotelroom, mealplan,
    //spo_booking_date, spo_travel_date
    //checkin_date < checkout_date
    //spo_booking_date <= spo_travel_date
    //adult.count and children.count cannot be both 0
    //cannot have chilren.sharing_own both SHARING and OWN
    //if spo_chk_is_wedding == 1 then must be GROOM and BRIDE in adults array
    //============================================================

    $checkin_date = trim($arrPOST["checkin_date"]);
    if ($checkin_date == "") {
        return "ERR_CHECKIN_DATE";
    }

    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $checkin_date)) {
        return "ERR_CHECKIN_DATE";
    }

    //============================================================

    $checkout_date = trim($arrPOST["checkout_date"]);
    if ($checkout_date == "") {
        return "ERR_CHECKOUT_DATE";
    }

    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $checkout_date)) {
        return "ERR_CHECKOUT_DATE";
    }

    //============================================================
    $booking_date = trim($arrPOST["booking_date"]);
    if ($booking_date == "") {
        return "ERR_BOOKING_DATE";
    }

    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $booking_date)) {
        return "ERR_BOOKING_DATE";
    }

    //============================================================
    $travel_date = trim($arrPOST["travel_date"]);
    if ($travel_date == "") {
        return "ERR_TRAVEL_DATE";
    }

    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $travel_date)) {
        return "ERR_TRAVEL_DATE";
    }
    //============================================================

    $checkin_time = trim($arrPOST["checkin_time"]);
    if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $checkin_time)) {
        return "ERR_CHECKIN_TIME_FORMAT";
    }

    //============================================================

    $checkout_time = trim($arrPOST["checkout_time"]);
    if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $checkout_time)) {
        return "ERR_CHECKOUT_TIME_FORMAT";
    }
    //============================================================

    $hotel = trim($arrPOST["hotel"]);
    $hotelroom = trim($arrPOST["hotelroom"]);

    if ($hotelroom == "") {
        return "ERR_HOTEL_ROOM_ID";
    }

    //check if hotel room id exists for that hotel
    $sql = "SELECT * FROM tblhotel_rooms WHERE hotelfk=:hotelid AND id=:roomid;";
    $query = $con->prepare($sql);
    $query->execute(array(":hotelid" => $hotel, ":roomid" => $hotelroom));
    if (!$row = $query->fetch(PDO::FETCH_ASSOC)) {

        return "ERR_HOTEL_ROOM_ID";
    }

    //=====================================================================
    $checkin_date = strtotime($checkin_date);
    $checkout_date = strtotime($checkout_date);

    if ($checkout_date < $checkin_date) {
        return "ERR_HOTEL_ROOM_ID";
    }

    //=====================================================================
    $adults = json_decode($arrPOST["adults"], true);
    $children = json_decode($arrPOST["children"], true);
    if (count($adults) == 0 && count($children) == 0) {
        return "ERR_NO_PAX";
    }

    //=====================================================================
    $flg_found_sharing = false;
    $flg_found_own = false;

    for ($i = 0; $i < count($children); $i++) {
        if ($children[$i]["sharing_own"] == "SHARING") {
            $flg_found_sharing = true;
        } else if ($children[$i]["sharing_own"] == "OWN") {
            $flg_found_own = true;
        }
    }
    if ($flg_found_sharing && $flg_found_own) {
        return "ERR_CHILDREN_SHARING_OWN";
    }

    return "OK";
}

?>