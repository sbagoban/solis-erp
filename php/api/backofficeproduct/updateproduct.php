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
        
        if (!isset($_GET["id_product"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product = $_GET["id_product"];
        $id_service_type = trim($_POST["id_service_type"]);
        $id_product_type = trim($_POST["id_product_type"]);
        $product_name = trim($_POST["product_name"]);

        $con = pdo_con();
        $sql = "UPDATE product SET 
                        id_service_type=:id_service_type,
                        id_product_type=:id_product_type,
                        product_name=:product_name
                        WHERE id_product=:id_product";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product" => $id_product,
                ":product_name" => $product_name,
                ":id_product_type" => $id_product_type,
                ":id_service_type" => $id_service_type));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
