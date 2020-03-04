<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if (!isset($_GET["t"])) {
    die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) {
    die("INVALID TOKEN");
}

if (!isset($_GET["id_booking"])) {
    throw new Exception("INVALID ID". $_GET["id_booking"]);
}

$id_booking = $_GET["id_booking"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sqlBookingTransfer = $con->prepare("SELECT 
                                                                BT_CLAIM.id_booking_transfer_claim,
                                                                BT_CLAIM.id_booking,
                                                                BT_CLAIM.transfer_service_paid_by,
                                                                BT_CLAIM.id_tour_operator,
                                                                BT_CLAIM.id_client,
                                                                BT_CLAIM.transfer_date,
                                                                BT_CLAIM.transfer_flight_no,
                                                                BT_CLAIM.transfer_time,
                                                                BT_CLAIM.id_transfer_from,
                                                                BT_CLAIM.transfer_from_name,
                                                                BT_CLAIM.id_transfer_to,
                                                                BT_CLAIM.transfer_to_name,
                                                                BT_CLAIM.transfer_booking_date,
                                                                BT_CLAIM.transfer_type,
                                                                BT_CLAIM.id_product,
                                                                BT_CLAIM.id_product_service,
                                                                BT_CLAIM.transfer_special_name,
                                                                BT_CLAIM.transfer_name,
                                                                BT_CLAIM.transfer_adult_amt,
                                                                BT_CLAIM.transfer_child_amt,
                                                                BT_CLAIM.transfer_infant_amt,
                                                                BT_CLAIM.transfer_total_pax,
                                                                BT_CLAIM.id_product_service_claim,
                                                                BT_CLAIM.id_product_service_claim_cur,
                                                                C.currency_code,
                                                                BT_CLAIM.id_dept,
                                                                BT_CLAIM.transfer_charge,
                                                                BT_CLAIM.id_service_tax,
                                                                BT_CLAIM.tax_value,
                                                                BT_CLAIM.transfer_adult_claim_exTAX,
                                                                BT_CLAIM.transfer_adult_claim,
                                                                BT_CLAIM.transfer_child_claim_exTAX,
                                                                BT_CLAIM.transfer_child_claim,
                                                                BT_CLAIM.transfer_infant_claim_exTAX,
                                                                BT_CLAIM.transfer_infant_claim,
                                                                BT_CLAIM.transfer_total_claim_exTAX,
                                                                BT_CLAIM.transfer_total_claim,
                                                                BT_CLAIM.transfer_rebate_claim_type,
                                                                BT_CLAIM.transfer_rebate_claim_approve_by,
                                                                BT_CLAIM.transfer_rebate_claim_percentage,
                                                                BT_CLAIM.transfer_adult_claim_rebate,
                                                                BT_CLAIM.transfer_adult_claim_after_rebate_exTAX,
                                                                BT_CLAIM.transfer_adult_claim_after_rebate,
                                                                BT_CLAIM.transfer_child_claim_rebate,
                                                                BT_CLAIM.transfer_child_claim_after_rebate,
                                                                BT_CLAIM.transfer_child_claim_after_rebate_exTAX,
                                                                BT_CLAIM.transfer_infant_claim_rebate,
                                                                BT_CLAIM.transfer_infant_claim_after_rebate,
                                                                BT_CLAIM.transfer_infant_claim_after_rebate_exTAX,
                                                                BT_CLAIM.transfer_total_claim_after_rebate_exTAX,
                                                                BT_CLAIM.transfer_total_claim_after_rebate,
                                                                BT_CLAIM.transfer_client_room_no,
                                                                BT_CLAIM.id_language,
                                                                BT_CLAIM.id_rep,
                                                                BT_CLAIM.transfer_voucher_no,
                                                                BT_CLAIM.transfer_remarks,
                                                                BT_CLAIM.transfer_internal_remarks,
                                                                BT_CLAIM.transfer_invoice_no,
                                                                BT_CLAIM.transfer_invoiced_date,
                                                                BT_CLAIM.transfer_close,
                                                                BT_CLAIM.transfer_close_on,
                                                                BT_CLAIM.transfer_status,
                                                                BT_CLAIM.planning_status,
                                                                BT_CLAIM.id_planning,
                                                                BT_CLAIM.created_by,
                                                                BT_CLAIM.created_name
                                                            FROM
                                                                 booking_transfer_claim BT_CLAIM,
                                                                 tblcurrency C
                                                            WHERE BT_CLAIM.id_product_service_claim_cur = C.id
                                                            AND BT_CLAIM.id_booking = :id_booking
                                                            AND BT_CLAIM.active = 1");
$sqlBookingTransfer->execute(array(":id_booking"=>$id_booking));
$row_count_c = $sqlBookingTransfer->rowCount();

if ($row_count_c > 0) {
    while ($row = $sqlBookingTransfer->fetch(PDO::FETCH_ASSOC)) {
        $bookingTransferDetails[] = array(
            'id_booking_transfer_claim'                            => $row['id_booking_transfer_claim'],  
            'id_booking'                                                    => $row['id_booking'],  
            'transfer_service_paid_by'                              => $row['transfer_service_paid_by'],  
            'id_tour_operator'                                           => $row['id_tour_operator'],  
            'id_client'                                                        => $row['id_client'],  
            'transfer_date'                                                => $row['transfer_date'],  
            'transfer_flight_no'                                         => $row['transfer_flight_no'],  
            'transfer_time'                                                => $row['transfer_time'],  
            'id_transfer_from'                                           => $row['id_transfer_from'],  
            'transfer_from_name'                                     => $row['transfer_from_name'],  
            'id_transfer_to'                                               => $row['id_transfer_to'],  
            'transfer_to_name'                                         => $row['transfer_to_name'],  
            'transfer_booking_date'                                 => $row['transfer_booking_date'],  
            'transfer_type'                                               => $row['transfer_type'],  
            'id_product'                                                   => $row['id_product'], 
            'id_product_service'                                      => $row['id_product_service'], 
            'transfer_special_name'                                => $row['transfer_special_name'],
            'transfer_name'                                             => $row['transfer_name'],
            'transfer_adult_amt'                                      => $row['transfer_adult_amt'],
            'transfer_child_amt'                                      => $row['transfer_child_amt'],
            'transfer_infant_amt'                                    => $row['transfer_infant_amt'],
            'transfer_total_pax'                                       => $row['transfer_total_pax'],
            'id_product_service_claim'                            => $row['id_product_service_claim'],
            'id_product_service_claim_cur'                     => $row['id_product_service_claim_cur'],
            'currency_code'                                            => $row['currency_code'],
            'id_dept'                                                       => $row['id_dept'],
            'transfer_charge'                                           => $row['transfer_charge'],
            'id_service_tax'                                             => $row['id_service_tax'],
            'tax_value'                                                    => $row['tax_value'],
            'transfer_adult_claim_exTAX'                       => $row['transfer_adult_claim_exTAX'],
            'transfer_adult_claim'                                   => $row['transfer_adult_claim'],
            'transfer_child_claim_exTAX'                        => $row['transfer_child_claim_exTAX'],
            'transfer_child_claim'                                    => $row['transfer_child_claim'],
            'transfer_infant_claim_exTAX'                       => $row['transfer_infant_claim_exTAX'],
            'transfer_infant_claim'                                   => $row['transfer_infant_claim'],
            'transfer_total_claim_exTAX'                         => $row['transfer_total_claim_exTAX'],
            'transfer_total_claim'                                     => $row['transfer_total_claim'],
            'transfer_rebate_claim_type'                          => $row['transfer_rebate_claim_type'],
            'transfer_rebate_claim_approve_by'               => $row['transfer_rebate_claim_approve_by'], 
            'transfer_rebate_claim_percentage'                => $row['transfer_rebate_claim_percentage'], 
            'transfer_adult_claim_rebate'                          => $row['transfer_adult_claim_rebate'], 
            'transfer_adult_claim_after_rebate_exTAX'      => $row['transfer_adult_claim_after_rebate_exTAX'],  
            'transfer_adult_claim_after_rebate'                  => $row['transfer_adult_claim_after_rebate'],  
            'transfer_child_claim_rebate'                            => $row['transfer_child_claim_rebate'],  
            'transfer_child_claim_after_rebate'                   => $row['transfer_child_claim_after_rebate'],  
            'transfer_child_claim_after_rebate_exTAX'        => $row['transfer_child_claim_after_rebate_exTAX'],   
            'transfer_infant_claim_rebate'                           => $row['transfer_infant_claim_rebate'],   
            'transfer_infant_claim_after_rebate'                  => $row['transfer_infant_claim_after_rebate'],   
            'transfer_infant_claim_after_rebate_exTAX'      => $row['transfer_infant_claim_after_rebate_exTAX'],  
            'transfer_total_claim_after_rebate_exTAX'        => $row['transfer_total_claim_after_rebate_exTAX'],  
            'transfer_total_claim_after_rebate'                    => $row['transfer_total_claim_after_rebate'],  
            'transfer_client_room_no'                                 => $row['transfer_client_room_no'],  
            'id_language'                                                     => $row['id_language'],  
            'id_rep'                                                              => $row['id_rep'],  
            'transfer_voucher_no'                                       => $row['transfer_voucher_no'],  
            'transfer_remarks'                                             => $row['transfer_remarks'],  
            'transfer_internal_remarks'                               => $row['transfer_internal_remarks'],  
            'transfer_invoice_no'                                        => $row['transfer_invoice_no'],  
            'transfer_invoiced_date'                                   => $row['transfer_invoiced_date'],  
            'transfer_close'                                                 => $row['transfer_close'],  
            'transfer_close_on'                                           => $row['transfer_close_on'],  
            'transfer_status'                                               => $row['transfer_status'],  
            'planning_status'                                              => $row['planning_status'],  
            'id_planning'                                                     => $row['id_planning'],  
            'created_by'                                                      => $row['created_by'],  
            'created_name'                                                 => $row['created_name'],
            'OUTCOME'                                                     => 'OK'
            
        );
    }    $myData = $bookingTransferDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $bookingTransferDetails[] = array(
            'id_booking_transfer_claim'                            => '-',  
            'id_booking'                                                    => '-',
            'transfer_service_paid_by'                              => '-',
            'id_tour_operator'                                           => '-',
            'id_client'                                                        => '-',
            'transfer_date'                                                => '-',
            'transfer_flight_no'                                         => '-',
            'transfer_time'                                                => '-',
            'id_transfer_from'                                           => '-',
            'transfer_from_name'                                     => '-',
            'id_transfer_to'                                               => '-',
            'transfer_to_name'                                         => '-',
            'transfer_booking_date'                                 => '-',
            'transfer_type'                                               => '-',
            'id_product'                                                   => '-',
            'id_product_service'                                      => '-',
            'transfer_special_name'                                => '-',
            'transfer_name'                                             => '-',
            'transfer_adult_amt'                                      => '-',
            'transfer_child_amt'                                      => '-',
            'transfer_infant_amt'                                    => '-',
            'transfer_total_pax'                                       => '-',
            'id_product_service_claim'                            => '-',
            'id_product_service_claim_cur'                     => '-',
            'currency_code'                                            => '-',
            'id_dept'                                                       => '-',
            'transfer_charge'                                           => '-',
            'id_service_tax'                                             => '-',
            'tax_value'                                                    => '-',
            'transfer_adult_claim_exTAX'                       => '-',
            'transfer_adult_claim'                                   => '-',
            'transfer_child_claim_exTAX'                        => '-',
            'transfer_child_claim'                                    => '-',
            'transfer_infant_claim_exTAX'                       => '-',
            'transfer_infant_claim'                                   => '-',
            'transfer_total_claim_exTAX'                         => '-',
            'transfer_total_claim'                                     => '-',
            'transfer_rebate_claim_type'                          => '-',
            'transfer_rebate_claim_approve_by'               => '-',
            'transfer_rebate_claim_percentage'                => '-', 
            'transfer_adult_claim_rebate'                          => '-',
            'transfer_adult_claim_after_rebate_exTAX'      => '-',  
            'transfer_adult_claim_after_rebate'                  => '-',  
            'transfer_child_claim_rebate'                            => '-', 
            'transfer_child_claim_after_rebate'                   => '-', 
            'transfer_child_claim_after_rebate_exTAX'        => '-', 
            'transfer_infant_claim_rebate'                           => '-',  
            'transfer_infant_claim_after_rebate'                  => '-',  
            'transfer_infant_claim_after_rebate_exTAX'      => '-',
            'transfer_total_claim_after_rebate_exTAX'        => '-',
            'transfer_total_claim_after_rebate'                    => '-', 
            'transfer_client_room_no'                                 => '-',
            'id_language'                                                     => '-',
            'id_rep'                                                              => '-',
            'transfer_voucher_no'                                       => '-',
            'transfer_remarks'                                             => '-', 
            'transfer_internal_remarks'                               => '-',
            'transfer_invoice_no'                                        => '-',
            'transfer_invoiced_date'                                   => '-',
            'transfer_close'                                                 => '-',
            'transfer_close_on'                                           => '-',
            'transfer_status'                                               => '-',  
            'planning_status'                                              => '-',
            'id_planning'                                                     => '-',
            'created_by'                                                      => '-',
            'created_name'                                                 => '-',
            'OUTCOME'                                                     => 'NO DATA'
    );
    $myData = $bookingTransferDetails;
    echo json_encode($myData);
}
