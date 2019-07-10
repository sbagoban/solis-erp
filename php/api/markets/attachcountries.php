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
    
    if (!isset($_POST["countryids"])) {
        throw new Exception("INVALID COUNTRY ID");
    }
    
    $countryids = $_POST["countryids"];
    $marketid = $_POST["marketid"];
    
    

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $con->beginTransaction();
    
    $arrids = explode(",", $countryids);
    
    for($i = 0; $i < count($arrids); $i++)
    {
        $stmt = $con->prepare("INSERT INTO tblmarket_countries (marketfk,countryfk)"
                            . " VALUES (:marketfk,:countryfk)");
        $stmt->execute(array(":marketfk"=>$marketid,":countryfk"=>$arrids[$i]));
    }
    
    
    $con->commit();
    
    echo json_encode(array("OUTCOME" => "OK"));
    
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}


?>
