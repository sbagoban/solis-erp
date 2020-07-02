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

    $arr_pax = array();
    $arr_params_test["arr_pax"] = $_POST["arr_pax"];
    

    // CLIENT ACTIVITY
    $data = $arr_params_test["arr_pax"];
    $count = 0;
    
    // var_dump($data); 
    foreach($data as $d) {
        $count++;
        //$arr_params_test->execute(array(':id_booking_room_claim' => $id_booking_room_claim, ':id_booking' => $id_booking, ':booking_client' => $d));
        $arr_params_test["arr_pax"][] = array(
                                            "count"=>$count,
                                            "age"=>$d['age'],
                                            "bride_groom"=>$d['bride_groom']);
    }
    
    // $arr_params_test["arr_pax"] = array();
    // $arr_params_test["arr_pax"][] = array("count"=>1,"age"=>30,"bride_groom"=>"BRIDE");
    // $arr_params_test["arr_pax"][] = array("count"=>2,"age"=>35,"bride_groom"=>"GROOM");

    // $arr_params_test["arr_pax"][] = array("count"=>4,"age"=>35,"bride_groom"=>"");
    // $arr_params_test["arr_pax"][] = array("count"=>4,"age"=>5,"bride_groom"=>"");
    // $arr_params_test["arr_pax"][] = array("count"=>6,"age"=>17,"bride_groom"=>"");

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

    $the_contract_id = _rates_reservation_get_contract_id($con, $arr_params_test);
    // // $arr_combinations = _rates_calculator_reservation_gen_room_combination($con, $the_contract_id, $arr_params_test);

    $test = array();
    $test = _rates_calculator_reservation_get_cost_claim($con, $the_contract_id, $arr_params_test);

    echo json_encode($test);

} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
