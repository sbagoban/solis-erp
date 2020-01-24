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
    
     $arr_params_resa = array(
           'mealplan' => $_POST["mealplan"],
           'suppmealplan' => $_POST["mealplan"],
            'touroperator' => $_POST["touroperator"],
            'hotel' => $_POST["hotel"],
            'hotelroom' => $_POST["hotelroom"],
            'checkin_date' => $_POST["checkin_date"],
            'checkout_date' => $_POST["checkout_date"],
            'booking_date' => $_POST["booking_date"],
            'travel_date' => $_POST["travel_date"],
            'max_pax' => $_POST["max_pax"]
         );
$con = pdo_con();
    
$the_contract_id = _rates_reservation_get_contract_id($con, $arr_params_resa);
$contractid = $the_contract_id;
if(is_numeric($contractid))
{
    $combination = _rates_calculator_reservation_gen_room_combination($con, $contractid, $arr_params_resa);
        echo implode( "".$combination);
    //if($combination[0]OUTCOME == "OK")
//    {
//    }
   // $spo = _rates_calculator_reservation_get_applicable_spos($con, $contractid, $arr_params_resa);
    //echo json_encode(array("OUTCOME" => "OK", "RESULT" => $outcome));

    echo json_encode(array($combination));
}

} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
