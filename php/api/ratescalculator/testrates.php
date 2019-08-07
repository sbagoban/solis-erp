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
    
    
    $con = pdo_con();
    $arr_main_params = json_decode($_POST["params"],true);
    $arr_spo_params = json_decode($_POST["spo_params"],true);
    $arr_params = array_merge($arr_main_params,$arr_spo_params);
    
    
    $outcome = _rates_calculator($con, $arr_params);


    echo json_encode(array("OUTCOME" => "OK", "RESULT" => $outcome));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>