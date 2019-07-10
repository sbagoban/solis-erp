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
        throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["value_input"])) {
        throw new Exception("INVALID VALUE INPUT");
    }

    if (!isset($_POST["arr_buy"])) {
        throw new Exception("INVALID BUYING SETTINGS");
    }

    if (!isset($_POST["arr_sell"])) {
        throw new Exception("INVALID SELLING SETTINGS");
    }

    if (!isset($_POST["exchgrates"])) {
        throw new Exception("INVALID EXCHANGE RATE SETTINGS");
    }
    
    if (!isset($_POST["items"])) {
        throw new Exception("INVALID ITEMS");
    }

    require_once("../../connector/pdo_connect_main.php");
    require_once("./_contract_calculatesp.php");
    require_once("../../globalvars/globalvars.php");

    $value_input = $_POST["value_input"];
    $currenyid_input = $_POST["currencyid_input"];
    $currenyid_sell = $_POST["currencyid_sell"];
    $arr_buy = json_decode($_POST["arr_buy"], true);
    $arr_sell = json_decode($_POST["arr_sell"], true);
    $exchgrates = json_decode($_POST["exchgrates"], true);
    $items = json_decode($_POST["items"], true);

    $con = pdo_con();

    global $__arr_alphabets;

    $arr_calculations = _contract_calculatesp($con, $value_input, $currenyid_input, $currenyid_sell, $arr_buy, $arr_sell, $exchgrates, $__arr_alphabets, $items);

    echo json_encode(array("OUTCOME" => "OK", "CALCULATIONS" => $arr_calculations));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
