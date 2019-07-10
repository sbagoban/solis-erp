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

    if (!isset($_POST["spoid"])) {
        throw new Exception("INVALID SPECIAL OFFER ID");
    }

    if (!isset($_POST["hid"])) {
        throw new Exception("INVALID HOTEL ID");
    }

    require_once("../../connector/pdo_connect_main.php");
    require_once("./_spo.php");
    require_once("./_spo_taxcommi.php");

    $spoid = $_POST["spoid"];
    $hotel_fk = $_POST["hid"];

    $con = pdo_con();

    $arr_spo = _spo_loadspo($con, $spoid,$hotel_fk);

    echo json_encode(array("OUTCOME" => "OK", "SPO" => $arr_spo));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
