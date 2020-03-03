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


    require_once("../../connector/pdo_connect_main.php");

    $id = $_POST["id"];
    $colid = $_POST["colid"];
    $val = $_POST["val"];


    $con = pdo_con();
    $sql = "UPDATE tblservice_contract_settings_profile SET $colid = :val WHERE id=:id";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id, ":val" => $val));

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
