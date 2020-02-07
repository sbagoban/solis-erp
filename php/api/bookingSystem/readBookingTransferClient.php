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
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    if (!isset($_GET["id_booking_transfer_claim"])) {
        throw new Exception("INVALID ID". $_GET["id_booking_transfer_claim"]);
    }
    
    $id_booking_transfer_claim = $_GET["id_booking_transfer_claim"];
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $qryBookingTransferClient = $con->prepare("SELECT * 
                                        FROM booking_transfer_client
                                        WHERE id_booking_transfer_claim = :id_booking_transfer_claim
                                        AND active = 1");
    $qryBookingTransferClient->execute(array(":id_booking_transfer_claim"=>$id_booking_transfer_claim));
    $row_count_c = $qryBookingTransferClient->rowCount();

    if ($row_count_c > 0) {
        while ($row = $qryBookingTransferClient->fetch(PDO::FETCH_ASSOC)) {
            $bookingTransferClientDetails[] = array(
				"id_booking_transfer_client" => $row['id_booking_transfer_client'],
				"id_client" => $row['id_client'],
				"id_booking_transfer_claim" => $row['id_booking_transfer_claim'],
				"id_booking" => $row['id_booking'],
                "OUTCOME" => 'OK'
                
            );
        }    
        $myData = $bookingTransferClientDetails;
        echo json_encode($myData);
    } else {
        //echo "NO DATA";    
        $bookingTransferClientDetails[] = array(
				"id_booking_transfer_client" => '-',
				"id_client" => '-',
				"id_booking_transfer_claim" => '-',
				"id_booking" => '-',
                "OUTCOME" => 'ERROR'
        );
    }
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

