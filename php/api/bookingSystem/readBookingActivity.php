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
    
    if (!isset($_GET["id_booking_activity"])) {
        throw new Exception("INVALID ID". $_GET["id_booking_activity"]);
    }
    
    $id_booking_activity = $_GET["id_booking_activity"];
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $qryBookingActivity = $con->prepare("SELECT *
                                        FROM booking_activity BA
                                        WHERE BA.id_booking_activity = :id_booking_activity
                                        AND BA.active = 1");
    $qryBookingActivity->execute(array(":id_booking_activity"=>$id_booking_activity));
    $row_count_c = $qryBookingActivity->rowCount();

    if ($row_count_c > 0) {
        while ($row = $qryBookingActivity->fetch(PDO::FETCH_ASSOC)) {
            $bookingActivityDetails[] = array(
				"id_booking_activity" => $row['id_booking_activity'],
				"id_booking" => $row['id_booking'],
				"activity_service_paid_by" => $row['activity_service_paid_by'],
				"id_tour_operator" => $row['id_tour_operator'],
				"id_client" => $row['id_client'],
				"activity_date" => $row['activity_date'],
				"activity_time" => $row['activity_time'],
				"activity_booking_date" => $row['activity_booking_date'],
				"id_product" => $row['id_product'],
				"id_product_service" => $row['id_product_service'],
				"activity_name" => $row['activity_name'],
				"id_hotel" => $row['id_hotel'],
				"activity_pickup_time" => $row['activity_pickup_time'],
				"activity_adult_amt" => $row['activity_adult_amt'],
				"activity_teen_amt" => $row['activity_teen_amt'],
				"activity_child_amt" => $row['activity_child_amt'],
				"activity_infant_amt" => $row['activity_infant_amt'],
				"activity_infant_amt" => $row['activity_infant_amt'],
				"id_product_service_claim" => $row['id_product_service_claim'],
				"activity_rebate_type" => $row['activity_rebate_type'],
				"activity_rebate_approve_by" => $row['activity_rebate_approve_by'],
				"activity_discount_percentage" => $row['activity_discount_percentage'],
				"activity_adult_claim_after_disc" => $row['activity_adult_claim_after_disc'],
				"activity_teen_claim_after_disc" => $row['activity_teen_claim_after_disc'],
				"activity_child_claim_after_disc" => $row['activity_child_claim_after_disc'],
				"activity_infant_claim_after_disc" => $row['activity_infant_claim_after_disc'],
				"activity_client_room_no" => $row['activity_client_room_no'],
				"id_language" => $row['id_language'],
				"id_rep" => $row['id_rep'],
				"activity_voucher_no" => $row['activity_voucher_no'],
				"activity_remarks" => $row['activity_remarks'],
				"activity_internal_remarks" => $row['activity_internal_remarks'],
				"activity_status" => $row['activity_status'],
				"activity_close" => $row['activity_close'],
				"activity_close_on" => $row['activity_close_on'],
                "OUTCOME" => 'OK'
            );
        }    
        $myData = $bookingActivityDetails;
        echo json_encode($myData);
    } else {
        //echo "NO DATA";    
        $bookingActivityDetails[] = array(
				"id_booking_activity" => '-',
				"id_booking" => '-',
				"activity_service_paid_by" => '-',
				"id_tour_operator" => '-',
				"id_client" => '-',
				"activity_date" => '-',
				"activity_time" => '-',
				"activity_booking_date" => '-',
				"id_product" => '-',
				"id_product_service" => '-',
				"activity_name" => '-',
				"id_hotel" => '-',
				"activity_pickup_time" => '-',
				"activity_adult_amt" => '-',
				"activity_teen_amt" => '-',
				"activity_child_amt" => '-',
				"activity_infant_amt" => '-',
				"activity_infant_amt" => '-',
				"id_product_service_claim" => '-',
				"activity_rebate_type" => '-',
				"activity_rebate_approve_by" => '-',
				"activity_discount_percentage" => '-',
				"activity_adult_claim_after_disc" => '-',
				"activity_teen_claim_after_disc" => '-',
				"activity_child_claim_after_disc" => '-',
				"activity_infant_claim_after_disc" => '-',
				"activity_client_room_no" => '-',
				"id_language" => '-',
				"id_rep" => '-',
				"activity_voucher_no" => '-',
				"activity_remarks" => '-',
				"activity_internal_remarks" => '-',
				"activity_status" => '-',
				"activity_close" => '-',
				"activity_close_on" => '-',
                "OUTCOME" => 'ERROR'
        );
    }
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

