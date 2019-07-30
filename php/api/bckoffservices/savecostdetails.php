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
    
    if (!isset($_POST["gid"])) {
        throw new Exception("INVALID ID");
    }
    // get id service to edit
    $id = $_POST["id"];

    // set service new values for cost details
    $locality_costdetails = trim($_POST["locality_costdetails"]);
    $invoice_desciption_costdetails = trim($_POST["invoice_desciption_costdetails"]);
    $duration_costdetails = trim($_POST["duration_costdetails"]);
    $taxbasis_costdetails = trim($_POST["taxbasis_costdetails"]);

    require_once("../../connector/pdo_connect_main.php");

    $con = pdo_con();
    $stmt = $con->prepare("UPDATE tblexcursion_services 
                            SET locality_costdetails = :locality_costdetails, invoice_desciption_costdetails = :invoice_desciption_costdetails, duration_costdetails = :duration_costdetails, taxbasis_costdetails = :taxbasis_costdetails
                            WHERE id = :id");
    $stmt->execute(array(":id"=>$id));
    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>
