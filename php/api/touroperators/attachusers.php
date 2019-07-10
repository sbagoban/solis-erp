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
    //================================================================================

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    
    session_start();
    $con = pdo_con();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["uids"])) {
        throw new Exception("INVALID USER IDS");
    }

    if (!isset($_POST["toid"])) {
        throw new Exception("INVALID TO ID");
    }

    $uids = $_POST["uids"];
    $toid = $_POST["toid"];


    
    $stmt = $con->prepare("UPDATE tbluser SET tofk=:toid WHERE id IN ($uids)");
    $stmt->execute(array(":toid"=>$toid));
    
    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
