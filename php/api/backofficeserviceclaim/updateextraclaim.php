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
        
        if (!isset($_GET["id_product_service_extra_claim"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product_service_extra_claim = $_GET["id_product_service_extra_claim"];

        $ps_adult_claim = trim($_POST["ps_adult_claim"]);
        $ps_teen_claim = trim($_POST["ps_teen_claim"]);
        $ps_child_claim = trim($_POST["ps_child_claim"]);
        $ps_infant_claim = trim($_POST["ps_infant_claim"]);

        $con = pdo_con();
        $sql = "UPDATE product_service_extra_claim SET 
                        ps_adult_claim=:ps_adult_claim,
                        ps_teen_claim=:ps_teen_claim,
                        ps_child_claim=:ps_child_claim,
                        ps_infant_claim=:ps_infant_claim
                        WHERE id_product_service_extra_claim=:id_product_service_extra_claim";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product_service_extra_claim" => $id_product_service_extra_claim,
                ":ps_adult_claim" => $ps_adult_claim,
                ":ps_teen_claim" => $ps_teen_claim,
                ":ps_child_claim" => $ps_child_claim,
                ":ps_infant_claim" => $ps_infant_claim));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
