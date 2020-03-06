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

    if (!isset($_POST["hid"])) {
        throw new Exception("INVALID HOTEL ID");
    }

    require_once("../../connector/pdo_connect_main.php");
    require_once("../bckoffhotels/_hotels_details.php");

    $hotelid = $_POST["hid"];

    $con = pdo_con();

    $arr_hotels = _hotel_details($con, $hotelid);

    echo json_encode(array("OUTCOME" => "OK", "HOTELS" => $arr_hotels));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
