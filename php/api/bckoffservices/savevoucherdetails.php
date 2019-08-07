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
        $address_voucherdetails = trim($_POST["address_voucherdetails"]);
        $country_voucherdetails = trim($_POST["country_voucherdetails"]);
        $state_voucherdetails = trim($_POST["state_voucherdetails"]);
        $postcode_voucherdetails = trim($_POST["postcode_voucherdetails"]);
        $vouchercreation_voucherdetails = trim($_POST["vouchercreation_voucherdetails"]);
        $printvoucher_voucherdetails = trim($_POST["printvoucher_voucherdetails"]);
        $vouchertext1_voucherdetails = trim($_POST["vouchertext1_voucherdetails"]);
        $vouchertext2_voucherdetails = trim($_POST["vouchertext2_voucherdetails"]);
        $vouchertext3_voucherdetails = trim($_POST["vouchertext3_voucherdetails"]);
        $vouchertext4_voucherdetails = trim($_POST["vouchertext4_voucherdetails"]);

        $con = pdo_con();
        $sql = "UPDATE tblexcursion_services SET 
                        address_voucherdetails=:address_voucherdetails, 
                        country_voucherdetails=:country_voucherdetails,
                        state_voucherdetails=:state_voucherdetails,
                        postcode_voucherdetails=:postcode_voucherdetails, 
                        vouchercreation_voucherdetails=:vouchercreation_voucherdetails,
                        printvoucher_voucherdetails=:printvoucher_voucherdetails,
                        vouchertext1_voucherdetails=:vouchertext1_voucherdetails,
                        vouchertext2_voucherdetails=:vouchertext2_voucherdetails,
                        vouchertext3_voucherdetails=:vouchertext3_voucherdetails,
                        vouchertext4_voucherdetails=:vouchertext4_voucherdetails
                        WHERE id=:id";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id" => $id,
                ":address_voucherdetails" => $address_voucherdetails, 
                ":country_voucherdetails" => $country_voucherdetails,
                ":state_voucherdetails" => $state_voucherdetails,
                ":postcode_voucherdetails" => $postcode_voucherdetails,
                "vouchercreation_voucherdetails" => $vouchercreation_voucherdetails,
                "printvoucher_voucherdetails" => $printvoucher_voucherdetails,
                "vouchertext1_voucherdetails" => $vouchertext1_voucherdetails,
                "vouchertext2_voucherdetails" => $vouchertext2_voucherdetails,
                "vouchertext3_voucherdetails" => $vouchertext3_voucherdetails,
                "vouchertext4_voucherdetails" => $vouchertext4_voucherdetails));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
