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


    require_once("../../connector/pdo_connect_main.php");
    $con = pdo_con();


    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["aid"])) {
        throw new Exception("INVALID ALLOTMENT ID");
    }

    $aid = $_POST["aid"];

    
    $sql = "UPDATE tblinventory_allotment SET deleted=1 WHERE id=:id";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $aid));
    

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
