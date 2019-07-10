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

    if (!isset($_POST["linkid"])) {
        throw new Exception("INVALID LINK ID");
    }

    require_once("../../connector/pdo_connect_main.php");

    $linkid = $_POST["linkid"];

    $con = pdo_con();

    $sql = "UPDATE tblspecial_offer_link SET deleted=1 WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $linkid));


    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
