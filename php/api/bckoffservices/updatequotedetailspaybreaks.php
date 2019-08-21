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
        
        if (!isset($_GET["t"])) {
            throw new Exception("INVALID TOKEN1");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            throw new Exception("INVALID TOKEN2");
        }

        if (!isset($_GET["idservicesfk"])) {
            throw new Exception("INVALID ID". $_GET["idservicesfk"]);
        }
        require_once("../../connector/pdo_connect_main.php");

        $idservicesfk = $_GET["idservicesfk"];
        $includechildren_paybreaks = trim($_POST["includechildren_paybreaks"]);
        $includeinfant_paybreaks = trim($_POST["includeinfant_paybreaks"]);

        $con = pdo_con();
        $sql = "UPDATE tblexcursion_services_quotedetails SET 
                        includechildren_paybreaks=:includechildren_paybreaks,
                        includeinfant_paybreaks=:includeinfant_paybreaks
                        WHERE idservicesfk=:idservicesfk";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":idservicesfk" => $idservicesfk,
                ":includechildren_paybreaks" => $includechildren_paybreaks,
                ":includeinfant_paybreaks" => $includeinfant_paybreaks));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
