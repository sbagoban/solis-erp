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

    if (!isset($_SESSION["solis_userid"])) {
        die("NO LOG IN!");
    }

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


    $con = pdo_con();

    $arr_params_test["checkin_date"] = "2020-10-10";
    $arr_params_test["checkin_time"] = "";
    $arr_params_test["checkout_date"] = "2020-10-20";
    $arr_params_test["checkout_time"] = "";
    $arr_params_test["touroperator"] = 1;
    $arr_params_test["max_pax"] = 2;
    $arr_params_test["booking_date"] = "2020-10-04";
    $arr_params_test["travel_date"] = "2020-10-10";
    $arr_params_test["wedding_interested"] = 0;

    $arr_params_test["arr_pax"] = array();
    $arr_params_test["arr_pax"][] = array("count" => 1, "age" => "", "bride_groom" => "");
    $arr_params_test["arr_pax"][] = array("count" => 2, "age" => 10, "bride_groom" => "");
    
    $time_pre = microtime(true);
    
    $test = _rates_calculator_get_applicable_contracts($con, $arr_params_test);
    
    $time_post = microtime(true);
    $exec_time = round(($time_post - $time_pre), 2);

    echo json_encode(array("OUTCOME" => "OK", "TEST" => $test, "TIME"=>$exec_time));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

