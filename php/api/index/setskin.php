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
    
    if (!isset($_POST["skin"])) {
        throw new Exception("INVALID SKIN");
    }
    
    $skin = $_POST["skin"];
    

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $stmt = $con->prepare("UPDATE tbluser SET skin=:skin WHERE id = :id");
    $stmt->execute(array(":id"=>$_SESSION["solis_userid"], ":skin"=>$skin));
    
    $_SESSION["solis_skin"] = $skin;
            
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>
