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
    
    if (!isset($_GET["id_booking_activity_claim"])) {
        throw new Exception("INVALID ID". $_GET["id_booking_activity_claim"]);
    }
    
    $id_booking_activity_claim = $_GET["id_booking_activity_claim"];
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $qryBookingActivityClient = $con->prepare("SELECT * 
                                        FROM booking_activity_client
                                        WHERE id_booking_activity_claim = :id_booking_activity_claim
                                        AND active = 1");
    $qryBookingActivityClient->execute(array(":id_booking_activity_claim"=>$id_booking_activity_claim));
    $row_count_c = $qryBookingActivityClient->rowCount();

    if ($row_count_c > 0) {
        while ($row = $qryBookingActivityClient->fetch(PDO::FETCH_ASSOC)) {
            $bookingActivityClientDetails[] = array(
				"id_booking_activity_client" => $row['id_booking_activity_client'],
				"id_client" => $row['id_client'],
				"id_booking_activity_claim" => $row['id_booking_activity_claim'],
				"id_booking" => $row['id_booking'],
                "OUTCOME" => 'OK'
                
            );
        }    
        $myData = $bookingActivityClientDetails;
        echo json_encode($myData);
    } else {
        //echo "NO DATA";    
        $bookingActivityClientDetails[] = array(
				"id_booking_activity_client" => '-',
				"id_client" => '-',
				"id_booking_activity_claim" => '-',
				"id_booking" => '-',
                "OUTCOME" => 'ERROR'
        );
    }
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

