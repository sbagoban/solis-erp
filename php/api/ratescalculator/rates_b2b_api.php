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

    session_start();

    
    //test if there is a session logged in
    if (!isset($_SESSION["solis_userid"])) {
        die("NO LOG IN!");
    }

    //below includes are needed to call the API
    require_once("../../connector/pdo_connect_main.php");
    require_once("./_rates_calculator.php");
    require_once("./_rates_get_contract.php");
    require_once("../hotelspecialoffers/_spo.php");
    require_once("../hotelspecialoffers/_spo_taxcommi.php");
    require_once("../hotelcontracts/_contract_capacityarr.php");
    require_once("../hotelcontracts/_contract_exchangerates.php");
    require_once("../hotelcontracts/_contract_calculatesp.php");
    require_once("../hotelcontracts/_contract_taxcommi.php");
    require_once("../hotelcontracts/_contract_combinations_rooms.php");
    require_once("../../globalvars/globalvars.php");
    require_once("../../utils/utilities.php");    
    require_once("../bckoffhotels/_hotels_details.php");
    
    
    
    //API Parameters needed
    /*
         *     @type Integer    $touroperator               tour operator id
         *     @type Date       $checkin_date               checkin date in yyyy-mm-dd
         *     @type Time       $checkin_time               checkin time in HH:mm
         *     @type Date       $checkout_date              checkout date in yyyy-mm-dd
         *     @type Time       $checkout_time              checkout time in HH:mm
         *     @type Date       $booking_date               booking date in yyyy-mm-dd
         *     @type Date       $travel_date                travel date in yyyy-mm-dd
         *     @type String     $room_type                  type of room: PERSONS/UNITS/BOTH
         *     @type Integer    $max_pax                    maximum number of passengers in the reservation
         *     @type Array      $hotel_char_starts_with     array of hotel names starting with given characters
         *     @type Boolean    $wedding_interested         if interested in wedding SPOS (1/0)
         *     @type Array      $arr_pax {
         *          Array of adults/children details mixed together
         *          @type Integer   $count          index of child/adult
         *          @type Integer   $age            age of the child/adult. If no age, then is adult
         *          @type String    $bride_groom    if adult if bride or groom or none. values = {"BRIDE","GROOM",""}
         *     }
     * 
     *      */
    
    
    //extract the values from POST    
    $checkin_date = $_POST["checkin_date"];
    $checkin_time = $_POST["checkin_time"];
    $checkout_date = $_POST["checkout_date"];
    $checkin_dt = $_POST["checkout_time"];
    $touroperator = $_POST["touroperator"];
    $max_pax = $_POST["max_pax"];
    $booking_date = $_POST["booking_date"];
    $travel_date = $_POST["travel_date"];
    $wedding_interested = $_POST["wedding_interested"];
    $hotel_char_starts_with = json_decode($_POST["hotel_char_starts_with"], true);
    $room_type = $_POST["room_type"];
    $arr_pax = json_decode($_POST["arr_pax"], true);
    
    
    //push the values into a parameter array
    $arr_params_test["checkin_date"] = $checkin_date;
    $arr_params_test["checkin_time"] = $checkin_time;
    $arr_params_test["checkout_date"] = $checkout_date;
    $arr_params_test["touroperator"] = $touroperator;
    $arr_params_test["max_pax"] = $max_pax;
    $arr_params_test["booking_date"] = $booking_date;
    $arr_params_test["travel_date"] = $travel_date;
    $arr_params_test["wedding_interested"] = $wedding_interested;
    $arr_params_test["hotel_char_starts_with"] = $hotel_char_starts_with;
    $arr_params_test["room_type"] = $room_type; 
    $arr_params_test["arr_pax"] = $arr_pax;
    
    /*
    //hard coding values for testing ==================
    $arr_params_test["checkin_date"] = "2020-10-10"; //can be passed from POST
    $arr_params_test["checkin_time"] = "";
    $arr_params_test["checkout_date"] = "2020-10-15";
    $arr_params_test["checkout_time"] = "";
    $arr_params_test["touroperator"] = 1;
    $arr_params_test["max_pax"] = 2;
    $arr_params_test["booking_date"] = "2020-10-04";
    $arr_params_test["travel_date"] = "2020-10-10";
    $arr_params_test["wedding_interested"] = 0;
    $arr_params_test["hotel_char_starts_with"] = array("MARITIM CRYSTALS");
    $arr_params_test["room_type"] = "PERSONS"; //   PERSONS,UNITS,BOTH    
    $arr_params_test["arr_pax"][] = array("count" => 1, "age" => "", "bride_groom" => "");
    $arr_params_test["arr_pax"][] = array("count" => 2, "age" => 10, "bride_groom" => "");
    
    //=======================================================
    */
    
    
    $time_pre = microtime(true); //start timer
    
    $con = pdo_con();
    
    //call the rates function 
    $answer = _rates_calculator_get_applicable_contracts($con, $arr_params_test);
    
    $time_post = microtime(true); //end timer
    $exec_time = round(($time_post - $time_pre), 2); //calculate time taken for search

    echo json_encode(array("OUTCOME" => "OK", "ANSWER" => $answer, "TIME"=>$exec_time));
    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

