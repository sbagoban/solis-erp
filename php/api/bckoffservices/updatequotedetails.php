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
        
        if (!isset($_POST["id"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id = $_POST["id"];
        $extraname = trim($_POST["extraname"]);
        $extradescription = trim($_POST["extradescription"]);
        $chargeper = trim($_POST["chargeper"]);

        $con = pdo_con();
        $sql = "UPDATE tblexcursion_services_quotedetails SET 
                        extraname=:extraname,
                        extradescription=:extradescription,
                        chargeper=:chargeper
                        WHERE id=:id";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id" => $id,
                ":extraname" => $extraname,
                ":extradescription" => $extradescription,
                ":chargeper" => $chargeper));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
