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

    if (!isset($_POST["t"])) {
        die("INVALID TOKEN");
    }
    if (!isset($_POST["params"])) {
        die("INVALID PARAMETERS");
    }

    if (!isset($_POST["spo_params"])) {
        die("INVALID SPO PARAMETERS");
    }

    if ($_POST["t"] != $_SESSION["token"]) {
        //die("INVALID TOKEN");
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
    $arr_main_params = json_decode($_POST["params"], true);
    $arr_spo_params = json_decode($_POST["spo_params"], true);
    $arr_params = array_merge($arr_main_params, $arr_spo_params);

    $outcome = _rates_calculator($con, $arr_params);


    /*
      $arr_params_test["checkin_date"] = "2019-04-01";
      $arr_params_test["checkin_time"] = "";
      $arr_params_test["checkout_date"] = "2019-04-05";
      $arr_params_test["checkout_time"] = "";
      $arr_params_test["mealplan"] = 6;
      $arr_params_test["suppmealplan"] = 4;
      $arr_params_test["country"] = 845;
      $arr_params_test["touroperator"] = 10;
      $arr_params_test["hotel"] = 19;
      $arr_params_test["hotelroom"] = 3;
      $arr_params_test["max_pax"] = 4;
      $arr_params_test["booking_date"] = "2019-04-01";
      $arr_params_test["travel_date"] = "2019-04-01";
      $arr_params_test["wedding_interested"] = 1;

      $arr_params_test["arr_pax"] = array();
      $arr_params_test["arr_pax"][] = array("count"=>1,"age"=>30,"bride_groom"=>"BRIDE");
      $arr_params_test["arr_pax"][] = array("count"=>2,"age"=>35,"bride_groom"=>"GROOM");
      $arr_params_test["arr_pax"][] = array("count"=>3,"age"=>5,"bride_groom"=>"");

      $test = _rates_calculator_reservation_get_cost_claim($con, 5, $arr_params_test);

     */
    
    
      $arr_params_test["checkin_date"] = "2019-04-01";
      $arr_params_test["checkin_time"] = "";
      $arr_params_test["checkout_date"] = "2019-04-05";
      $arr_params_test["checkout_time"] = "";
      $arr_params_test["touroperator"] = 10;
      $arr_params_test["max_pax"] = 4;
      $arr_params_test["booking_date"] = "2019-04-01";
      $arr_params_test["travel_date"] = "2019-04-01";
      $arr_params_test["wedding_interested"] = 0;

      $arr_params_test["arr_pax"] = array();
      $arr_params_test["arr_pax"][] = array("count"=>1,"age"=>"","bride_groom"=>"");
      $arr_params_test["arr_pax"][] = array("count"=>2,"age"=>"","bride_groom"=>"");
      $arr_params_test["arr_pax"][] = array("count"=>3,"age"=>5,"bride_groom"=>"");

      $test = _rates_calculator_get_applicable_contracts($con, $arr_params_test);


    $test = array();
    echo json_encode(array("OUTCOME" => "OK", "RESULT" => $outcome, "TEST" => $test));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

