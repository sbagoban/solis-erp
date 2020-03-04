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
    
    if (!isset($_GET["id_booking"])) {
        throw new Exception("INVALID ID". $_GET["id_booking"]);
    }
    
    $id_booking = $_GET["id_booking"];
    
    require_once("../../php/connector/pdo_connect_main.php");

    $con = pdo_con();

    $sqlBookingActivity = $con->prepare("SELECT 
                                                                BA_CLAIM.id_booking_activity_claim,
                                                                BA_CLAIM.id_booking,
                                                                BA_CLAIM.activity_service_paid_by,
                                                                BA_CLAIM.id_tour_operator,
                                                                BA_CLAIM.id_client,
                                                                BA_CLAIM.activity_date,
                                                                BA_CLAIM.activity_time,
                                                                BA_CLAIM.activity_booking_date,
                                                                BA_CLAIM.id_product,
                                                                P.product_name,
                                                                BA_CLAIM.id_product_service,
                                                                PS.service_name,
                                                                BA_CLAIM.activity_name,
                                                                BA_CLAIM.activity_duration,
                                                                BA_CLAIM.id_hotel,
                                                                BA_CLAIM.hotelname,
                                                                BA_CLAIM.activity_pickup_time,
                                                                BA_CLAIM.activity_adult_amt,
                                                                BA_CLAIM.activity_teen_amt,
                                                                BA_CLAIM.activity_child_amt,
                                                                BA_CLAIM.activity_infant_amt,
                                                                BA_CLAIM.activity_total_pax,
                                                                BA_CLAIM.id_product_service_claim,
                                                                BA_CLAIM.id_product_service_claim_cur,
                                                                C.currency_code,
                                                                BA_CLAIM.id_dept,
                                                                BA_CLAIM.activity_charge,
                                                                BA_CLAIM.id_service_tax,
                                                                BA_CLAIM.tax_value,
                                                                BA_CLAIM.activity_adult_claim_exTAX,
                                                                BA_CLAIM.activity_adult_claim,
                                                                BA_CLAIM.activity_teen_claim_exTAX,
                                                                BA_CLAIM.activity_teen_claim,
                                                                BA_CLAIM.activity_child_claim_exTAX,
                                                                BA_CLAIM.activity_child_claim,
                                                                BA_CLAIM.activity_infant_claim_exTAX,
                                                                BA_CLAIM.activity_infant_claim,
                                                                BA_CLAIM.activity_total_claim_exTAX,
                                                                BA_CLAIM.activity_total_claim,
                                                                BA_CLAIM.activity_rebate_claim_type,
                                                                BA_CLAIM.activity_rebate_claim_approve_by,
                                                                BA_CLAIM.activity_rebate_claim_percentage,
                                                                BA_CLAIM.activity_adult_claim_rebate,
                                                                BA_CLAIM.activity_adult_claim_after_rebate_exTAX,
                                                                BA_CLAIM.activity_adult_claim_after_rebate,
                                                                BA_CLAIM.activity_teen_claim_rebate,
                                                                BA_CLAIM.activity_teen_claim_after_rebate,
                                                                BA_CLAIM.activity_teen_claim_after_rebate_exTAX,
                                                                BA_CLAIM.activity_child_claim_rebate,
                                                                BA_CLAIM.activity_child_claim_after_rebate,
                                                                BA_CLAIM.activity_child_claim_after_rebate_exTAX,
                                                                BA_CLAIM.activity_infant_claim_rebate,
                                                                BA_CLAIM.activity_infant_claim_after_rebate,
                                                                BA_CLAIM.activity_infant_claim_after_rebate_exTAX,
                                                                BA_CLAIM.activity_total_claim_after_rebate_exTAX,
                                                                BA_CLAIM.activity_total_claim_after_rebate,
                                                                BA_CLAIM.activity_client_room_no,
                                                                BA_CLAIM.id_language,
                                                                BA_CLAIM.id_rep,
                                                                BA_CLAIM.activity_voucher_no,
                                                                BA_CLAIM.activity_remarks,
                                                                BA_CLAIM.activity_internal_remarks,
                                                                BA_CLAIM.activity_invoice_no,
                                                                BA_CLAIM.activity_invoiced_date,
                                                                BA_CLAIM.activity_close,
                                                                BA_CLAIM.activity_close_on,
                                                                BA_CLAIM.activity_status,
                                                                BA_CLAIM.created_by,
                                                                BA_CLAIM.created_name
                                                            FROM 
                                                                booking_activity_claim BA_CLAIM,
                                                                product P,
                                                                product_service PS,
                                                                tblcurrency C
                                                            WHERE BA_CLAIM.id_product = P.id_product
                                                            AND BA_CLAIM.id_product_service = PS.id_product_service
                                                            AND BA_CLAIM.id_product_service_claim_cur = C.id
                                                            AND BA_CLAIM.id_booking = :id_booking
                                                            AND BA_CLAIM.active = 1");
    $sqlBookingActivity->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $sqlBookingActivity->rowCount();

    if ($row_count_c > 0) {
        while ($row = $sqlBookingActivity->fetch(PDO::FETCH_ASSOC)) {
            $bookingActivityDetails[] = array(
                'id_booking_activity_claim'   => $row['id_booking_activity_claim'],
                'id_booking'   => $row['id_booking'],
                'activity_service_paid_by'   => $row['activity_service_paid_by'],
                'id_tour_operator'   => $row['id_tour_operator'],
                'id_client'   => $row['id_client'],
                'activity_date'   => $row['activity_date'],
                'activity_time'   => $row['activity_time'],
                'activity_booking_date'   => $row['activity_booking_date'],
                'id_product'   => $row['id_product'],
                'product_name'   => $row['product_name'],
                'id_product_service'   => $row['id_product_service'],
                'service_name'   => $row['service_name'],
                'activity_name'   => $row['activity_name'],
                'activity_duration'   => $row['activity_duration'],
                'id_hotel'   => $row['id_hotel'],
                'hotelname'   => $row['hotelname'],
                'activity_pickup_time'   => $row['activity_pickup_time'],
                'activity_adult_amt'   => $row['activity_adult_amt'],
                'activity_teen_amt'   => $row['activity_teen_amt'],
                'activity_child_amt'   => $row['activity_child_amt'],
                'activity_infant_amt'   => $row['activity_infant_amt'],
                'activity_total_pax'   => $row['activity_total_pax'],
                'id_product_service_claim'   => $row['id_product_service_claim'],
                'id_product_service_claim_cur'   => $row['id_product_service_claim_cur'],
                'currency_code'   => $row['currency_code'],
                'id_dept'   => $row['id_dept'],
                'activity_charge'   => $row['activity_charge'],
                'id_service_tax'   => $row['id_service_tax'],
                'tax_value'   => $row['tax_value'],
                'activity_adult_claim_exTAX'   => $row['activity_adult_claim_exTAX'],
                'activity_adult_claim'   => $row['activity_adult_claim'],
                'activity_teen_claim_exTAX'   => $row['activity_teen_claim_exTAX'],
                'activity_teen_claim'   => $row['activity_teen_claim'],
                'activity_child_claim_exTAX'   => $row['activity_child_claim_exTAX'],
                'activity_child_claim'   => $row['activity_child_claim'],
                'activity_infant_claim_exTAX'   => $row['activity_infant_claim_exTAX'],
                'activity_infant_claim'   => $row['activity_infant_claim'],
                'activity_total_claim_exTAX'   => $row['activity_total_claim_exTAX'],
                'activity_total_claim'   => $row['activity_total_claim'],
                'activity_rebate_claim_type'   => $row['activity_rebate_claim_type'],
                'activity_rebate_claim_approve_by'   => $row['activity_rebate_claim_approve_by'],
                'activity_rebate_claim_percentage'   => $row['activity_rebate_claim_percentage'],
                'activity_adult_claim_rebate'   => $row['activity_adult_claim_rebate'],
                'activity_adult_claim_after_rebate_exTAX'   => $row['activity_adult_claim_after_rebate_exTAX'],
                'activity_adult_claim_after_rebate'   => $row['activity_adult_claim_after_rebate'],
                'activity_teen_claim_rebate'   => $row['activity_teen_claim_rebate'],
                'activity_teen_claim_after_rebate'   => $row['activity_teen_claim_after_rebate'],
                'activity_teen_claim_after_rebate_exTAX'   => $row['activity_teen_claim_after_rebate_exTAX'],
                'activity_child_claim_rebate'   => $row['activity_child_claim_rebate'],
                'activity_child_claim_after_rebate'   => $row['activity_child_claim_after_rebate'],
                'activity_child_claim_after_rebate_exTAX'   => $row['activity_child_claim_after_rebate_exTAX'],
                'activity_infant_claim_rebate'   => $row['activity_infant_claim_rebate'],
                'activity_infant_claim_after_rebate'   => $row['activity_infant_claim_after_rebate'],
                'activity_infant_claim_after_rebate_exTAX'   => $row['activity_infant_claim_after_rebate_exTAX'],
                'activity_total_claim_after_rebate_exTAX'   => $row['activity_total_claim_after_rebate_exTAX'],
                'activity_total_claim_after_rebate'   => $row['activity_total_claim_after_rebate'],
                'activity_client_room_no'   => $row['activity_client_room_no'],
                'id_language'   => $row['id_language'],
                'id_rep'   => $row['id_rep'],
                'activity_voucher_no'   => $row['activity_voucher_no'],
                'activity_remarks'   => $row['activity_remarks'],
                'activity_internal_remarks'   => $row['activity_internal_remarks'],
                'activity_invoice_no'   => $row['activity_invoice_no'],
                'activity_invoiced_date'   => $row['activity_invoiced_date'],
                'activity_close'   => $row['activity_close'],
                'activity_close_on'   => $row['activity_close_on'],
                'activity_status'   => $row['activity_status'],
                'created_by'   => $row['created_by'],
                'created_name' => $row['created_name'],
                'OUTCOME' => 'OK'
            );
        }    
        $myData = $bookingActivityDetails;
        echo json_encode($myData);
    } 
    else 
    {
        //echo "NO DATA";    
        $bookingActivityDetails[] = array(
                'id_booking_activity_claim'   => '-',
                'id_booking'   => '-',
                'activity_service_paid_by'   => '-',
                'id_tour_operator'   => '-',
                'id_client'   => '-',
                'activity_date'   => '-',
                'activity_time'   => '-',
                'activity_booking_date'   => '-',
                'id_product'   => '-',
                'product_name'   => '-',
                'id_product_service'   => '-',
                'service_name'   => '-',
                'activity_name'   => '-',
                'activity_duration'   => '-',
                'id_hotel'   => '-',
                'hotelname'   => '-',
                'activity_pickup_time'   => '-',
                'activity_adult_amt'   => '-',
                'activity_teen_amt'   => '-',
                'activity_child_amt'   => '-',
                'activity_infant_amt'   => '-',
                'activity_total_pax'   => '-',
                'id_product_service_claim'   => '-',
                'id_product_service_claim_cur'   => '-',
                'currency_code'   => '-',
                'id_dept'   => '-',
                'activity_charge'   => '-',
                'id_service_tax'   => '-',
                'tax_value'   => '-',
                'activity_adult_claim_exTAX'   => '-',
                'activity_adult_claim'   => '-',
                'activity_teen_claim_exTAX'   => '-',
                'activity_teen_claim'   => '-',
                'activity_child_claim_exTAX'   => '-',
                'activity_child_claim'   => '-',
                'activity_infant_claim_exTAX'   => '-',
                'activity_infant_claim'   => '-',
                'activity_total_claim_exTAX'   => '-',
                'activity_total_claim'   => '-',
                'activity_rebate_claim_type'  => '-',
                'activity_rebate_claim_approve_by'   => '-',
                'activity_rebate_claim_percentage'   => '-',
                'activity_adult_claim_rebate'   => '-',
                'activity_adult_claim_after_rebate_exTAX'   => '-',
                'activity_adult_claim_after_rebate'   => '-',
                'activity_teen_claim_rebate'   => '-',
                'activity_teen_claim_after_rebate'   => '-',
                'activity_teen_claim_after_rebate_exTAX'   => '-',
                'activity_child_claim_rebate'   => '-',
                'activity_child_claim_after_rebate'   => '-',
                'activity_child_claim_after_rebate_exTAX'   => '-',
                'activity_infant_claim_rebate'   => '-',
                'activity_infant_claim_after_rebate'   => '-',
                'activity_infant_claim_after_rebate_exTAX'   => '-',
                'activity_total_claim_after_rebate_exTAX'   => '-',
                'activity_total_claim_after_rebate'   => '-',
                'activity_client_room_no'   => '-',
                'id_language'   => '-',
                'id_rep'   => '-',
                'activity_voucher_no'   => '-',
                'activity_remarks'   => '-',
                'activity_internal_remarks'   => '-',
                'activity_invoice_no'   => '-',
                'activity_invoiced_date'   => '-',
                'activity_close'   => '-',
                'activity_close_on'   => '-',
                'activity_status'   => '-',
                'created_by'   => '-',
                'created_name' => '-',
                'id_booking_activity'   => '-',
                'id_booking'   => '-',
                'id_product'    => '-',
                'product_name'  => '-',
                'id_product_service'    => '-',
                'service_name'  => '-',
                'activity_date' => '-',
                'activity_rebate_claim_type'  => '-',
                'activity_total_claim_after_disc'   => '-',
                'currency_code' => '-',
                'OUTCOME' => 'NO DATA'
        );
        $myData = $bookingActivityDetails;
        echo json_encode($myData);
    }
}catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>