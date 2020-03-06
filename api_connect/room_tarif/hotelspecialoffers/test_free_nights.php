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

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        //throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["arr_free_nights"])) {
        throw new Exception("INVALID FREE NIGHTS SETTINGS");
    }
    
    if (!isset($_POST["stays"])) {
        throw new Exception("INVALID NUMBER OF STAY");
    }
    
    if (!isset($_POST["cumulative"])) {
        throw new Exception("INVALID CUMULATIVE SETTING");
    }

    require_once("../../connector/pdo_connect_main.php");
    require_once("../ratescalculator/_rates_calculator.php");

    $arr_free_nights = json_decode($_POST["arr_free_nights"],true);
    $stays = $_POST["stays"];
    $cumulative = $_POST["cumulative"];

    $con = pdo_con();

    $arr_free_nights = _rates_calculator_calc_free_nights($stays, $arr_free_nights, $cumulative);
    
    $stay = $arr_free_nights["STAYS"];
    $pay = $arr_free_nights["PAYS"];
    $free = $arr_free_nights["FREE"];
    
    echo json_encode(array("OUTCOME" => "OK", "STAY"=>$stay,"PAY"=>$pay,"FREE"=>$free));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
