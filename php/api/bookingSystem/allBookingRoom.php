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
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $sqlBookingRoom = $con->prepare("SELECT 
                BR_CLAIM.id_booking_room_claim,
                BR_CLAIM.id_booking,
                BR_CLAIM.id_booking_room,
                BR_CLAIM.room_service_paid_by,
                BR_CLAIM.id_tour_operator,
                BR_CLAIM.id_client,
                BR_CLAIM.room_stay_from,
                BR_CLAIM.room_stay_to,
                BR_CLAIM.room_booking_date,
                BR_CLAIM.id_contract,
                BR_CLAIM.id_hotel,
                BR_CLAIM.hotelname,
                BR_CLAIM.id_room,
                BR_CLAIM.room_details,
                BR_CLAIM.room_claim_calcultation,
                BR_CLAIM.room_adult_amt,
                BR_CLAIM.room_teen_amt,
                BR_CLAIM.room_child_amt,
                BR_CLAIM.room_infant_amt,
                BR_CLAIM.room_total_pax,
                BR_CLAIM.id_dept,
                BR_CLAIM.room_charge,
                BR_CLAIM.id_service_tax,
                BR_CLAIM.tax_value,
                BR_CLAIM.id_claim_cur,
                C.currency_code,
                BR_COST.id_cost_cur,
                BR_CLAIM.room_adult_claim_exTAX,
                BR_CLAIM.room_adult_claim,
                BR_COST.room_adult_cost_exTAX,
                BR_COST.room_adult_cost,
                BR_CLAIM.room_teen_claim_exTAX,
                BR_CLAIM.room_teen_claim,
                BR_COST.room_teen_cost_exTAX,
                BR_COST.room_teen_cost,
                BR_CLAIM.room_child_claim_exTAX,
                BR_CLAIM.room_child_claim,
                BR_COST.room_child_cost_exTAX,
                BR_COST.room_child_cost,
                BR_CLAIM.room_infant_claim_exTAX,
                BR_CLAIM.room_infant_claim,
                BR_COST.room_infant_cost_exTAX,
                BR_COST.room_infant_cost,
                BR_CLAIM.room_total_claim_exTAX,
                BR_CLAIM.room_total_claim,
                BR_COST.room_total_cost_exTAX,
                BR_COST.room_total_cost,
                BR_CLAIM.room_rebate_claim_type,
                BR_COST.room_rebate_cost_type,
                BR_CLAIM.room_rebate_claim_approve_by,
                BR_COST.room_rebate_cost_approve_by,
                BR_CLAIM.room_rebate_claim_percentage,
                BR_COST.room_rebate_cost_percentage,
                BR_CLAIM.room_adult_claim_rebate,
                BR_CLAIM.room_adult_claim_after_rebate_exTAX,
                BR_CLAIM.room_adult_claim_after_rebate,
                BR_COST.room_adult_cost_rebate,
                BR_COST.room_adult_cost_after_rebate_exTAX,
                BR_COST.room_adult_cost_after_rebate,
                BR_CLAIM.room_teen_claim_rebate,
                BR_CLAIM.room_teen_claim_after_rebate,
                BR_CLAIM.room_teen_claim_after_rebate_exTAX,
                BR_COST.room_teen_cost_rebate,
                BR_COST.room_teen_cost_after_rebate,
                BR_COST.room_teen_cost_after_rebate_exTAX,
                BR_CLAIM.room_child_claim_rebate,
                BR_CLAIM.room_child_claim_after_rebate,
                BR_CLAIM.room_child_claim_after_rebate_exTAX,
                BR_COST.room_child_cost_rebate,
                BR_COST.room_child_cost_after_rebate,
                BR_COST.room_child_cost_after_rebate_exTAX,
                BR_CLAIM.room_infant_claim_rebate,
                BR_CLAIM.room_infant_claim_after_rebate,
                BR_CLAIM.room_infant_claim_after_rebate_exTAX,
                BR_COST.room_infant_cost_rebate,
                BR_COST.room_infant_cost_after_rebate,
                BR_COST.room_infant_cost_after_rebate_exTAX,
                BR_CLAIM.room_total_claim_after_rebate_exTAX,
                BR_CLAIM.room_total_claim_after_rebate,
                BR_COST.room_total_cost_after_rebate_exTAX,
                BR_COST.room_total_cost_after_rebate,
                BR_COST.service_details,
                BR_CLAIM.room_remarks,
                BR_CLAIM.room_internal_remarks,
                BR_CLAIM.room_invoice_no,
                BR_CLAIM.room_invoiced_date,
                BR_CLAIM.room_close,
                BR_CLAIM.room_close_on,
                BR_CLAIM.room_status,
                BR_CLAIM.created_by,
                BR_CLAIM.created_name
            FROM 
                booking_room_claim BR_CLAIM,
                booking_room_cost BR_COST,
                tblcurrency C
            WHERE BR_CLAIM.id_booking_room_claim = BR_COST.id_booking_room_claim
            AND BR_CLAIM.id_claim_cur = C.id
            AND BR_CLAIM.id_booking = :id_booking
            AND BR_CLAIM.active = 1
            AND BR_COST.active = 1");
    $sqlBookingRoom->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $sqlBookingRoom->rowCount();

    if ($row_count_c > 0) {
        while ($row = $sqlBookingRoom->fetch(PDO::FETCH_ASSOC)) {
            $bookingRoomDetails[] = array(
                'id_booking_room_claim'                                => $row['id_booking_room_claim'],
                'id_booking'                                                    => $row['id_booking'],
                'id_booking_room'                                          => $row['id_booking_room'],
                'room_service_paid_by'                                   => $row['room_service_paid_by'],
                'id_tour_operator'                                           => $row['id_tour_operator'],
                'id_client'                                                         => $row['id_client'],
                'room_stay_from'                                             => $row['room_stay_from'],
                'room_stay_to'                                                 => $row['room_stay_to'],
                'room_booking_date'                                       => $row['room_booking_date'],
                'id_contract'                                                     => $row['id_contract'],
                'id_hotel'                                                          => $row['id_hotel'],
                'hotelname'                                                      => $row['hotelname'],
                'id_room'                                                          => $row['id_room'],
                'room_details'                                                   => $row['room_details'],
                'room_claim_calcultation'                                 => $row['room_claim_calcultation'],
                'room_adult_amt'                                             => $row['room_adult_amt'],
                'room_teen_amt'                                              => $row['room_teen_amt'],
                'room_child_amt'                                              => $row['room_child_amt'],
                'room_infant_amt'                                            => $row['room_infant_amt'],
                'room_total_pax'                                               => $row['room_total_pax'],
                'id_dept'                                                            => $row['id_dept'],
                'room_charge'                                                   => $row['room_charge'],
                'id_service_tax'                                                 => $row['id_service_tax'],
                'tax_value'                                                        => $row['tax_value'],
                'id_claim_cur'                                                    => $row['id_claim_cur'],
                'currency_code'                                                => $row['currency_code'],
                'id_claim_cur'                                                     => $row['id_claim_cur'],
                'room_adult_claim_exTAX'                                => $row['room_adult_claim_exTAX'],
                'room_adult_claim'                                            => $row['room_adult_claim'],
                'room_adult_cost_exTAX'                                  => $row['room_adult_cost_exTAX'],
                'room_adult_cost'                                             => $row['room_adult_cost'],
                'room_teen_claim_exTAX'                                 => $row['room_teen_claim_exTAX'],
                'room_teen_claim'                                             => $row['room_teen_claim'],
                'room_teen_cost_exTAX'                                   => $row['room_teen_cost_exTAX'],
                'room_teen_cost'                                              => $row['room_teen_cost'],
                'room_child_claim_exTAX'                                 => $row['room_child_claim_exTAX'],
                'room_child_claim'                                            => $row['room_child_claim'],
                'room_child_cost_exTAX'                                  => $row['room_child_cost_exTAX'],
                'room_child_cost'                                              => $row['room_child_cost'],
                'room_infant_claim_exTAX'                               => $row['room_infant_claim_exTAX'],
                'room_infant_claim'                                          => $row['room_infant_claim'],
                'room_infant_cost_exTAX'                                => $row['room_infant_cost_exTAX'],
                'room_infant_cost'                                            => $row['room_infant_cost'],
                'room_total_claim_exTAX'                                 => $row['room_total_claim_exTAX'],
                'room_total_claim'                                            => $row['room_total_claim'],
                'room_total_cost_exTAX'                                  => $row['room_total_cost_exTAX'],
                'room_total_cost'                                             => $row['room_total_cost'],
                'room_rebate_claim_type'                                => $row['room_rebate_claim_type'],
                'room_rebate_cost_type'                                  => $row['room_rebate_cost_type'],
                'room_rebate_claim_approve_by'                     => $row['room_rebate_claim_approve_by'],
                'room_rebate_cost_approve_by'                      => $row['room_rebate_cost_approve_by'],
                'room_rebate_claim_percentage'                     => $row['room_rebate_claim_percentage'],
                'room_rebate_cost_percentage'                      => $row['room_rebate_cost_percentage'],
                'room_adult_claim_rebate'                              => $row['room_adult_claim_rebate'],
                'room_adult_claim_after_rebate_exTAX'         => $row['room_adult_claim_after_rebate_exTAX'],
                'room_adult_claim_after_rebate'                     => $row['room_adult_claim_after_rebate'],
                'room_adult_cost_rebate'                               => $row['room_adult_cost_rebate'],
                'room_adult_cost_after_rebate_exTAX'          => $row['room_adult_cost_after_rebate_exTAX'],
                'room_adult_cost_after_rebate'                      => $row['room_adult_cost_after_rebate'],
                'room_teen_claim_rebate'                              => $row['room_teen_claim_rebate'],
                'room_teen_claim_after_rebate'                     => $row['room_teen_claim_after_rebate'],
                'room_teen_claim_after_rebate_exTAX'         => $row['room_teen_claim_after_rebate_exTAX'],
                'room_teen_cost_rebate'                               => $row['room_teen_cost_rebate'],
                'room_teen_cost_after_rebate'                      => $row['room_teen_cost_after_rebate'],
                'room_teen_cost_after_rebate_exTAX'          => $row['room_teen_cost_after_rebate_exTAX'],
                'room_child_claim_rebate'                             => $row['room_child_claim_rebate'],
                'room_child_claim_after_rebate'                    => $row['room_child_claim_after_rebate'],
                'room_child_claim_after_rebate_exTAX'        => $row['room_child_claim_after_rebate_exTAX'],
                'room_child_cost_rebate'                              => $row['room_child_cost_rebate'],
                'room_child_cost_after_rebate'                     => $row['room_child_cost_after_rebate'],
                'room_child_cost_after_rebate_exTAX'         => $row['room_child_cost_after_rebate_exTAX'],
                'room_infant_claim_rebate'                          => $row['room_infant_claim_rebate'],
                'room_infant_claim_after_rebate'                 => $row['room_infant_claim_after_rebate'],
                'room_infant_claim_after_rebate_exTAX'     => $row['room_infant_claim_after_rebate_exTAX'],
                'room_infant_cost_rebate'                           => $row['room_infant_cost_rebate'],
                'room_infant_cost_after_rebate'                  => $row['room_infant_cost_after_rebate'],
                'room_infant_cost_after_rebate_exTAX'       => $row['room_infant_cost_after_rebate_exTAX'],
                'room_total_claim_after_rebate_exTAX'       => $row['room_total_claim_after_rebate_exTAX'],
                'room_total_claim_after_rebate'                   => $row['room_total_claim_after_rebate'],
                'room_total_cost_after_rebate_exTAX'         => $row['room_total_cost_after_rebate_exTAX'],
                'room_total_cost_after_rebate'                    => $row['room_total_cost_after_rebate'],
                'service_details'                    => $row['service_details'],
                'room_remarks'                                            => $row['room_remarks'],
                'room_internal_remarks'                              => $row['room_internal_remarks'],
                'room_invoice_no'                                       => $row['room_invoice_no'],
                'room_invoiced_date'                                  => $row['room_invoiced_date'],
                'room_close'                                                => $row['room_close'],
                'room_close_on'                                          => $row['room_close_on'],
                'room_status'                                              => $row['room_status'],
                'created_by'                                                => $row['created_by'],
                'created_name'                                           => $row['created_name'],
                'OUTCOME'                                               => 'OK'
            );
        }    
        $myData = $bookingRoomDetails;
        echo json_encode($myData);
    } 
    else 
    {
        //echo "NO DATA";    
        $bookingRoomDetails[] = array(
                'id_booking_room_claim'                                => '-',
                'id_booking'                                                    => '-',
                'id_booking_room'                                           => '-',
                'room_service_paid_by'                                   => '-',
                'id_tour_operator'                                           => '-',
                'id_client'                                                         => '-',
                'room_stay_from'                                             => '-',
                'room_stay_to'                                                 => '-',
                'room_booking_date'                                       => '-',
                'id_contract'                                                     => '-',
                'id_hotel'                                                          => '-',
                'hotelname'                                                      => '-',
                'id_room'                                                          => '-',
                'room_details'                                                   => '-',
                'room_claim_calcultation'                                 => '-',
                'room_adult_amt'                                             => '-',
                'room_teen_amt'                                              => '-',
                'room_child_amt'                                              => '-',
                'room_infant_amt'                                            => '-',
                'room_total_pax'                                               => '-',
                'id_dept'                                                            => '-',
                'room_charge'                                                   => '-',
                'id_service_tax'                                                 => '-',
                'tax_value'                                                        => '-',
                'id_claim_cur'                                                    => '-',
                'currency_code'                                                => '-',
                'id_claim_cur'                                                     => '-',
                'room_adult_claim_exTAX'                                => '-',
                'room_adult_claim'                                            => '-',
                'room_adult_cost_exTAX'                                  => '-',
                'room_adult_cost'                                             => '-',
                'room_teen_claim_exTAX'                                 => '-',
                'room_teen_claim'                                             => '-',
                'room_teen_cost_exTAX'                                   => '-',
                'room_teen_cost'                                              => '-',
                'room_child_claim_exTAX'                                 => '-',
                'room_child_claim'                                            => '-',
                'room_child_cost_exTAX'                                  => '-',
                'room_child_cost'                                              => '-',
                'room_infant_claim_exTAX'                               => '-',
                'room_infant_claim'                                          => '-',
                'room_infant_cost_exTAX'                                => '-',
                'room_infant_cost'                                            => '-',
                'room_total_claim_exTAX'                                 => '-',
                'room_total_claim'                                            => '-',
                'room_total_cost_exTAX'                                  => '-',
                'room_total_cost'                                             => '-',
                'room_rebate_claim_type'                                => '-',
                'room_rebate_cost_type'                                  => '-',
                'room_rebate_claim_approve_by'                     => '-',
                'room_rebate_cost_approve_by'                      => '-',
                'room_rebate_claim_percentage'                     => '-',
                'room_rebate_cost_percentage'                      => '-',
                'room_adult_claim_rebate'                              => '-',
                'room_adult_claim_after_rebate_exTAX'         => '-',
                'room_adult_claim_after_rebate'                     => '-',
                'room_adult_cost_rebate'                               => '-',
                'room_adult_cost_after_rebate_exTAX'          => '-',
                'room_adult_cost_after_rebate'                      => '-',
                'room_teen_claim_rebate'                              => '-',
                'room_teen_claim_after_rebate'                     => '-',
                'room_teen_claim_after_rebate_exTAX'         => '-',
                'room_teen_cost_rebate'                               => '-',
                'room_teen_cost_after_rebate'                      => '-',
                'room_teen_cost_after_rebate_exTAX'          => '-',
                'room_child_claim_rebate'                             => '-',
                'room_child_claim_after_rebate'                    => '-',
                'room_child_claim_after_rebate_exTAX'        => '-',
                'room_child_cost_rebate'                              => '-',
                'room_child_cost_after_rebate'                     => '-',
                'room_child_cost_after_rebate_exTAX'         => '-',
                'room_infant_claim_rebate'                          => '-',
                'room_infant_claim_after_rebate'                 => '-',
                'room_infant_claim_after_rebate_exTAX'     => '-',
                'room_infant_cost_rebate'                           => '-',
                'room_infant_cost_after_rebate'                  => '-',
                'room_infant_cost_after_rebate_exTAX'       => '-',
                'room_total_claim_after_rebate_exTAX'       => '-',
                'room_total_claim_after_rebate'                   => '-',
                'service_details'=> '-',
                'room_total_cost_after_rebate_exTAX'         => '-',
                'room_total_cost_after_rebate'                    => '-',
                'room_remarks'                                            => '-',
                'room_internal_remarks'                              => '-',
                'room_invoice_no'                                       => '-',
                'room_invoiced_date'                                  => '-',
                'room_close'                                                => '-',
                'room_close_on'                                          =>  '-',
                'room_status'                                              =>  '-',
                'created_by'                                                =>  '-',
                'created_name'                                           =>  '-',
                'OUTCOME'                                               => 'NO DATA'
        );
        $myData = $bookingRoomDetails;
        echo json_encode($myData);
    }
}catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>