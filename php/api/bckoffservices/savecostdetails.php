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
        // set service new values for cost details
        $locality_costdetails = trim($_POST["locality_costdetails"]);
        $descriptionservice = trim($_POST["descriptionservice"]);
        $comments = trim($_POST["comments"]);
        $invoice_desciption_costdetails = trim($_POST["invoice_desciption_costdetails"]);
        $duration_costdetails = trim($_POST["duration_costdetails"]);
        // $taxbasis_costdetails = trim($_POST["taxbasis_costdetails"]);
        $charged_unit_children_costdetails = trim($_POST["charged_unit_children_costdetails"]);
        $min_children_costdetails = trim($_POST["min_children_costdetails"]);
        $max_children_costdetails = trim($_POST["max_children_costdetails"]);
        $charged_unit_adults_costdetails = trim($_POST["charged_unit_adults_costdetails"]);        
        $min_adults_costdetails = trim($_POST["min_adults_costdetails"]);
        $max_adults_costdetails = trim($_POST["max_adults_costdetails"]);

        $con = pdo_con();
        $sql = "UPDATE tblexcursion_services SET 
                        locality_costdetails=:locality_costdetails, 
                        descriptionservice=:descriptionservice,
                        comments=:comments,
                        invoice_desciption_costdetails=:invoice_desciption_costdetails, 
                        duration_costdetails=:duration_costdetails,
                        -- taxbasis_costdetails=:taxbasis_costdetails,
                        charged_unit_children_costdetails=:charged_unit_children_costdetails,
                        min_children_costdetails=:min_children_costdetails,
                        max_children_costdetails=:max_children_costdetails,
                        charged_unit_adults_costdetails=:charged_unit_adults_costdetails,
                        min_adults_costdetails=:min_adults_costdetails,
                        max_adults_costdetails=:max_adults_costdetails
                        WHERE id=:id";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id" => $id,
                ":locality_costdetails" => $locality_costdetails, 
                ":descriptionservice" => $descriptionservice,
                ":comments" => $comments,
                ":invoice_desciption_costdetails" => $invoice_desciption_costdetails,
                "duration_costdetails" => $duration_costdetails,
                // "taxbasis_costdetails" => $taxbasis_costdetails,
                "charged_unit_children_costdetails" => $charged_unit_children_costdetails,
                "min_children_costdetails" => $min_children_costdetails,
                "max_children_costdetails" => $max_children_costdetails,
                "charged_unit_adults_costdetails" => $charged_unit_adults_costdetails,
                "min_adults_costdetails" => $min_adults_costdetails,
                "max_adults_costdetails" => $max_adults_costdetails));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
