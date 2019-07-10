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
    
    /*
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    *
     * 
     */
    if (!isset($_POST["json_capacity"])) {
        throw new Exception("INVALID CAPACITY JSON VALUE");
    }
    
    if (!isset($_POST["dateid"])) {
        throw new Exception("INVALID DATE ID VALUE");
    }
    
    if (!isset($_POST["roomid"])) {
        throw new Exception("INVALID ROOM ID");
    }
    
    
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../hotelcontracts/_contract_combinations_rooms.php");
    
    $roomid = $_POST["roomid"];
    $dateid = $_POST["dateid"];
    $arr_room = json_decode($_POST["json_capacity"],true);
    $arr_combinations = _contract_combinations_rooms($arr_room,$roomid, $dateid);

    echo json_encode(array("OUTCOME" => "OK", "COMBINATIONS" => $arr_combinations));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
