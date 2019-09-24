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
        
        if (!isset($_GET["id_product_services_cost"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product_services_cost = $_GET["id_product_services_cost"]; 
        $id_dept = trim($_POST["id_dept"]);
        $valid_to = trim($_POST["valid_to"]);
        $valid_from = trim($_POST["valid_from"]);

        $ps_adult_cost = trim($_POST["ps_adult_cost"]);
        $ps_teen_cost = trim($_POST["ps_teen_cost"]);
        $ps_child_cost = trim($_POST["ps_child_cost"]);
        $ps_infant_cost = trim($_POST["ps_infant_cost"]);
        $id_currency = trim($_POST["id_currency"]);

        $con = pdo_con();
        $sql = "UPDATE product_services_cost SET 
                        valid_to=:valid_to,
                        valid_from=:valid_from,                        
                        ps_adult_cost=:ps_adult_cost, 
                        ps_teen_cost=:ps_teen_cost,
                        ps_child_cost=:ps_child_cost,
                        ps_infant_cost=:ps_infant_cost,
                        id_currency=:id_currency,
                        id_dept=:id_dept
                        WHERE id_product_services_cost=:id_product_services_cost";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product_services_cost" => $id_product_services_cost,
                ":valid_to" => $valid_to,
                ":valid_from" => $valid_from,
                ":ps_adult_cost" => $ps_adult_cost,
                ":ps_teen_cost" => $ps_teen_cost,
                ":ps_child_cost" => $ps_child_cost,
                ":ps_infant_cost" => $ps_infant_cost,
                ":id_currency" => $id_currency,
                ":id_dept" => $id_dept));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
