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
        throw new Exception("NO LOG IN!");
    }
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    $arr_params_test["checkin_date"] = $_POST["checkin_date"];
    $arr_params_test["checkin_time"] = $_POST["checkin_time"];

    // $arr_params_test["checkin_date"] = "2019-11-01";
    // $arr_params_test["checkout_date"] = "2019-11-03";

    $arr_params_test["checkout_date"] = $_POST["checkout_date"];
    $arr_params_test["checkout_time"] = $_POST["checkout_time"];

    $arr_params_test["mealplan"] = $_POST["mealplan"];
    $arr_params_test["suppmealplan"] = $_POST["suppmealplan"];
    $arr_params_test["touroperator"] = $_POST["touroperator"];

    $arr_params_test["hotel"] = $_POST["hotel"];
    $arr_params_test["hotelroom"] = $_POST["hotelroom"];

    $arr_params_test["max_pax"] = $_POST["max_pax"];
    $arr_params_test["booking_date"] = $_POST["booking_date"];

    $arr_params_test["travel_date"] = $_POST["travel_date"];
    $arr_params_test["wedding_interested"] = $_POST["wedding_interested"];

    $arr_params_test["arr_pax"] = array();
    $arr_params_test["arr_pax"][] = array("count"=>1,"age"=>30,"bride_groom"=>"BRIDE");
    $arr_params_test["arr_pax"][] = array("count"=>2,"age"=>35,"bride_groom"=>"GROOM");
    $arr_params_test["arr_pax"][] = array("count"=>3,"age"=>5,"bride_groom"=>"");

    require_once("../../connector/pdo_connect_main.php");
    require_once("../ratescalculator/_rates_get_contract.php");
    require_once("../ratescalculator/_rates_calculator.php");
    require_once("../hotelspecialoffers/_spo.php");
    require_once("../hotelspecialoffers/_spo_taxcommi.php");
    require_once("../hotelcontracts/_contract_capacityarr.php");
    require_once("../hotelcontracts/_contract_exchangerates.php");
    require_once("../hotelcontracts/_contract_calculatesp.php");
    require_once("../hotelcontracts/_contract_taxcommi.php");
    require_once("../hotelcontracts/_contract_combinations_rooms.php");
    require_once("../../globalvars/globalvars.php");

    require_once("../../utils/utilities.php");

    $con = pdo_con();

    // $arr_params_test["checkin_date"] = "2019-11-01";
    // $arr_params_test["checkin_time"] = "";
    // $arr_params_test["checkout_date"] = "2019-11-03";
    // $arr_params_test["checkout_time"] = "";

    // $arr_params_test["mealplan"] = 2;
    // $arr_params_test["suppmealplan"] = 6;

    // $arr_params_test["country"] = 1009;
    // $arr_params_test["touroperator"] = 3;

    // $arr_params_test["hotel"] = 26;
    // $arr_params_test["hotelroom"] = 25;

    // $arr_params_test["max_pax"] = 10;
    // $arr_params_test["booking_date"] = "2019-11-01";

    // $arr_params_test["travel_date"] = "2019-11-05";
    // $arr_params_test["wedding_interested"] = 1;

    // $arr_params_test["arr_pax"] = array();
    // $arr_params_test["arr_pax"][] = array("count"=>1,"age"=>30,"bride_groom"=>"BRIDE");
    // $arr_params_test["arr_pax"][] = array("count"=>2,"age"=>35,"bride_groom"=>"GROOM");
    // $arr_params_test["arr_pax"][] = array("count"=>3,"age"=>5,"bride_groom"=>"");

    $the_contract_id = _rates_reservation_get_contract_id($con, $arr_params_test);
    $test = array();
    $test = _rates_calculator_reservation_get_cost_claim($con, $the_contract_id, $arr_params_test);

    echo json_encode($test);

// $the_contract_id = _rates_reservation_get_contract_id($con, $arr_params_resa);
// $contractid = $the_contract_id;
// $ratesCalculator = _rates_calculator_reservation_get_cost_claim($con, $contractid, $arr_params_resa);
//     echo $ratesCalculator;
    //if($combination[0]OUTCOME == "OK")
//    {
//    }
   // $spo = _rates_calculator_reservation_get_applicable_spos($con, $contractid, $arr_params_resa);
    //echo json_encode(array("OUTCOME" => "OK", "RESULT" => $outcome));

    //echo json_encode(array($ratesCalculator));
// if(is_numeric($contractid))
// {
    
// }

} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
