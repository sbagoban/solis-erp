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
    $id_booking = $_GET["id_booking"];
	$id_user = $_SESSION["solis_userid"];
	$uname = $_SESSION["solis_username"];
	$log_status = "DELETE";
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
	
    // BOOKING TRANSFER DETAILS
	$qryBookingTransfer = $con->prepare("SELECT *
                                        FROM booking_transfer_claim BT_CLAIM
                                        WHERE BT_CLAIM.id_booking_transfer_claim = :id_booking_transfer_claim
                                        AND BT_CLAIM.active = 1");
	$qryBookingTransfer->execute(array(":id_booking_transfer_claim"=>$id_booking_transfer_claim));
    $row_count_c = $qryBookingTransfer->rowCount();

    if ($row_count_c > 0) 
    {
        while ($row = $qryBookingTransfer->fetch(PDO::FETCH_ASSOC)) 
        {
            $id_booking_transfer_claim                            = $row['id_booking_transfer_claim'];  
            $id_booking                                                    = $row['id_booking'];  
            $transfer_service_paid_by                              = $row['transfer_service_paid_by'];  
            $id_tour_operator                                           = $row['id_tour_operator'];  
            $id_client                                                        = $row['id_client'];  
            $transfer_date                                                = $row['transfer_date'];  
            $transfer_flight_no                                         = $row['transfer_flight_no'];  
            $transfer_time                                                = $row['transfer_time'];  
            $id_transfer_from                                           = $row['id_transfer_from'];  
            $transfer_from_name                                     = $row['transfer_from_name'];  
            $id_transfer_to                                               = $row['id_transfer_to'];  
            $transfer_to_name                                         = $row['transfer_to_name'];  
            $transfer_booking_date                                 = $row['transfer_booking_date'];  
            $transfer_type                                               = $row['transfer_type'];  
            $id_product                                                   = $row['id_product']; 
            $id_product_service                                      = $row['id_product_service']; 
            $transfer_special_name                                = $row['transfer_special_name'];
            $transfer_name                                             = $row['transfer_name'];
            $transfer_adult_amt                                      = $row['transfer_adult_amt'];
            $transfer_child_amt                                      = $row['transfer_child_amt'];
            $transfer_infant_amt                                    = $row['transfer_infant_amt'];
            $transfer_total_pax                                       = $row['transfer_total_pax'];
            $id_product_service_claim                            = $row['id_product_service_claim'];
            $id_product_service_claim_cur                     = $row['id_product_service_claim_cur'];
            $id_dept                                                       = $row['id_dept'];
            $transfer_claim_dept                                    = $row['transfer_claim_dept'];
            $transfer_charge                                           = $row['transfer_charge'];
            $id_service_tax                                             = $row['id_service_tax'];
            $tax_value                                                    = $row['tax_value'];
            $transfer_adult_claim_exTAX                       = $row['transfer_adult_claim_exTAX'];
            $transfer_adult_claim                                   = $row['transfer_adult_claim'];
            $transfer_child_claim_exTAX                        = $row['transfer_child_claim_exTAX'];
            $transfer_child_claim                                    = $row['transfer_child_claim'];
            $transfer_infant_claim_exTAX                       = $row['transfer_infant_claim_exTAX'];
            $transfer_infant_claim                                   = $row['transfer_infant_claim'];
            $transfer_total_claim_exTAX                         = $row['transfer_total_claim_exTAX'];
            $transfer_total_claim                                     = $row['transfer_total_claim'];
            $transfer_rebate_claim_type                          = $row['transfer_rebate_claim_type'];
            $transfer_rebate_claim_approve_by               = $row['transfer_rebate_claim_approve_by']; 
            $transfer_rebate_claim_percentage                = $row['transfer_rebate_claim_percentage']; 
            $transfer_adult_claim_rebate                          = $row['transfer_adult_claim_rebate']; 
            $transfer_adult_claim_after_rebate_exTAX      = $row['transfer_adult_claim_after_rebate_exTAX'];  
            $transfer_adult_claim_after_rebate                  = $row['transfer_adult_claim_after_rebate'];  
            $transfer_child_claim_rebate                            = $row['transfer_child_claim_rebate'];  
            $transfer_child_claim_after_rebate                   = $row['transfer_child_claim_after_rebate'];  
            $transfer_child_claim_after_rebate_exTAX        = $row['transfer_child_claim_after_rebate_exTAX'];   
            $transfer_infant_claim_rebate                           = $row['transfer_infant_claim_rebate'];   
            $transfer_infant_claim_after_rebate                  = $row['transfer_infant_claim_after_rebate'];   
            $transfer_infant_claim_after_rebate_exTAX      = $row['transfer_infant_claim_after_rebate_exTAX'];  
            $transfer_total_claim_after_rebate_exTAX        = $row['transfer_total_claim_after_rebate_exTAX'];  
            $transfer_total_claim_after_rebate                    = $row['transfer_total_claim_after_rebate'];  
            $transfer_client_room_no                                 = $row['transfer_client_room_no'];  
            $id_language                                                     = $row['id_language'];  
            $id_rep                                                              = $row['id_rep'];  
            $transfer_voucher_no                                       = $row['transfer_voucher_no'];  
            $transfer_remarks                                             = $row['transfer_remarks'];  
            $transfer_internal_remarks                               = $row['transfer_internal_remarks'];  
            $transfer_invoice_no                                        = $row['transfer_invoice_no'];  
            $transfer_invoiced_date                                   = $row['transfer_invoiced_date'];  
            $transfer_close                                                 = $row['transfer_close'];  
            $transfer_close_on                                          = $row['transfer_close_on'];  
            $transfer_status                                               = $row['transfer_status'];  
            $planning_status                                              = $row['planning_status'];  
            $id_planning                                                     = $row['id_planning'];  
            $created_by                                                      = $row['created_by'];  
            $created_name                                                = $row['created_name'];
        }    
	
        $qryBookingTransferDelete = $con->prepare("UPDATE booking_transfer_claim SET active=0 WHERE id_booking_transfer_claim = :id_booking_transfer_claim");
        $qryBookingTransferDelete->execute(array(":id_booking_transfer_claim"=>$id_booking_transfer_claim));
        
        // BOOKING TRANSFER CLIENT
        $qryBookingTransferClient = $con->prepare("SELECT * 
                                    FROM booking_transfer_client
                                    WHERE id_booking_transfer_claim = :id_booking_transfer_claim
                                    AND active = 1");
        $qryBookingTransferClient->execute(array(":id_booking_transfer_claim"=>$id_booking_transfer_claim));
        $row_count_c = $qryBookingTransferClient->rowCount();
        $transfer_clients= '';
        if ($row_count_c > 0) 
        {
            while ($row = $qryBookingTransferClient->fetch(PDO::FETCH_ASSOC)) 
            {
            $transfer_clients .= $row['id_client'].',';
            }    
        }
        else
        {
             $transfer_clients = '0';
        }

        $qryBookingTransferClientDelete = $con->prepare("UPDATE booking_transfer_client SET active=0 WHERE id_booking_transfer_claim = :id_booking_transfer_claim");
        $qryBookingTransferClientDelete->execute(array(":id_booking_transfer_claim"=>$id_booking_transfer_claim));

        // BOOKING TRANSFER LOG
       $qryBookingTransferClaimDeleteLog = $con->prepare("
            INSERT INTO booking_transfer_claim_log
            (
                id_booking_transfer_claim,
                id_booking,
                transfer_service_paid_by,
                id_tour_operator,
                id_client,
                transfer_date,
                transfer_flight_no,
                transfer_time,
                id_transfer_from,
                transfer_from_name,
                id_transfer_to,
                transfer_to_name,
                transfer_booking_date,
                transfer_type,
                id_product,
                id_product_service,
                transfer_special_name,
                transfer_name,
                transfer_clients,
                transfer_adult_amt,
                transfer_child_amt,
                transfer_infant_amt,
                transfer_total_pax,
                id_product_service_claim,
                id_product_service_claim_cur,
                id_dept,
                transfer_claim_dept,
                transfer_charge,
                id_service_tax,
                tax_value,
                transfer_adult_claim_exTAX,
                transfer_adult_claim,
                transfer_child_claim_exTAX,
                transfer_child_claim,
                transfer_infant_claim_exTAX,
                transfer_infant_claim,
                transfer_total_claim_exTAX,
                transfer_total_claim,
                transfer_rebate_claim_type,
                transfer_rebate_claim_approve_by,
                transfer_rebate_claim_percentage,
                transfer_adult_claim_rebate,
                transfer_adult_claim_after_rebate_exTAX,
                transfer_adult_claim_after_rebate,
                transfer_child_claim_rebate,
                transfer_child_claim_after_rebate,
                transfer_child_claim_after_rebate_exTAX,
                transfer_infant_claim_rebate,
                transfer_infant_claim_after_rebate,
                transfer_infant_claim_after_rebate_exTAX,
                transfer_total_claim_after_rebate_exTAX,
                transfer_total_claim_after_rebate,
                transfer_client_room_no,
                id_language,
                id_rep,
                transfer_voucher_no,
                transfer_remarks,
                transfer_internal_remarks,
                transfer_invoice_no,
                transfer_invoiced_date,
                transfer_close,
                transfer_close_on,
                transfer_status,
                planning_status,
                id_planning,
                id_user,
                uname,
                log_status
            )
            VALUES
            (
                :id_booking_transfer_claim,
                :id_booking,
                :transfer_service_paid_by,
                :id_tour_operator,
                :id_client,
                :transfer_date,
                :transfer_flight_no,
                :transfer_time,
                :id_transfer_from,
                :transfer_from_name,
                :id_transfer_to,
                :transfer_to_name,
                :transfer_booking_date,
                :transfer_type,
                :id_product,
                :id_product_service,
                :transfer_special_name,
                :transfer_name,
                :transfer_clients,
                :transfer_adult_amt,
                :transfer_child_amt,
                :transfer_infant_amt,
                :transfer_total_pax,
                :id_product_service_claim,
                :id_product_service_claim_cur,
                :id_dept,
                :transfer_claim_dept,
                :transfer_charge,
                :id_service_tax,
                :tax_value,
                :transfer_adult_claim_exTAX,
                :transfer_adult_claim,
                :transfer_child_claim_exTAX,
                :transfer_child_claim,
                :transfer_infant_claim_exTAX,
                :transfer_infant_claim,
                :transfer_total_claim_exTAX,
                :transfer_total_claim,
                :transfer_rebate_claim_type,
                :transfer_rebate_claim_approve_by,
                :transfer_rebate_claim_percentage,
                :transfer_adult_claim_rebate,
                :transfer_adult_claim_after_rebate_exTAX,
                :transfer_adult_claim_after_rebate,
                :transfer_child_claim_rebate,
                :transfer_child_claim_after_rebate,
                :transfer_child_claim_after_rebate_exTAX,
                :transfer_infant_claim_rebate,
                :transfer_infant_claim_after_rebate,
                :transfer_infant_claim_after_rebate_exTAX,
                :transfer_total_claim_after_rebate_exTAX,
                :transfer_total_claim_after_rebate,
                :transfer_client_room_no,
                :id_language,
                :id_rep,
                :transfer_voucher_no,
                :transfer_remarks,
                :transfer_internal_remarks,
                :transfer_invoice_no,
                :transfer_invoiced_date,
                :transfer_close,
                :transfer_close_on,
                :transfer_status,
                :planning_status,
                :id_planning,
                :id_user,
                :uname,
                :log_status
            )
        ");

        $qryBookingTransferClaimDeleteLog->execute(array(
            'id_booking_transfer_claim'                            => $id_booking_transfer_claim,  
            'id_booking'                                                    => $id_booking,  
            'transfer_service_paid_by'                              => $transfer_service_paid_by,  
            'id_tour_operator'                                           => $id_tour_operator,  
            'id_client'                                                        => $id_client,  
            'transfer_date'                                                => $transfer_date,  
            'transfer_flight_no'                                         => $transfer_flight_no,  
            'transfer_time'                                                => $transfer_time,  
            'id_transfer_from'                                           => $id_transfer_from,  
            'transfer_from_name'                                     => $transfer_from_name,  
            'id_transfer_to'                                               => $id_transfer_to,  
            'transfer_to_name'                                         => $transfer_to_name,  
            'transfer_booking_date'                                 => $transfer_booking_date,  
            'transfer_type'                                               => $transfer_type,  
            'id_product'                                                   => $id_product, 
            'id_product_service'                                      => $id_product_service, 
            'transfer_special_name'                                => $transfer_special_name,
            'transfer_name'                                             => $transfer_name,
            'transfer_clients'                                            => $transfer_clients,
            'transfer_adult_amt'                                      => $transfer_adult_amt,
            'transfer_child_amt'                                      => $transfer_child_amt,
            'transfer_infant_amt'                                    => $transfer_infant_amt,
            'transfer_total_pax'                                       => $transfer_total_pax,
            'id_product_service_claim'                            => $id_product_service_claim,
            'id_product_service_claim_cur'                     => $id_product_service_claim_cur,
            'id_dept'                                                       => $id_dept,
            'transfer_claim_dept'                                    => $transfer_claim_dept,
            'transfer_charge'                                           => $transfer_charge,
            'id_service_tax'                                             => $id_service_tax,
            'tax_value'                                                    => $tax_value,
            'transfer_adult_claim_exTAX'                       => $transfer_adult_claim_exTAX,
            'transfer_adult_claim'                                   => $transfer_adult_claim,
            'transfer_child_claim_exTAX'                        => $transfer_child_claim_exTAX,
            'transfer_child_claim'                                    => $transfer_child_claim,
            'transfer_infant_claim_exTAX'                       => $transfer_infant_claim_exTAX,
            'transfer_infant_claim'                                   => $transfer_infant_claim,
            'transfer_total_claim_exTAX'                         => $transfer_total_claim_exTAX,
            'transfer_total_claim'                                     => $transfer_total_claim,
            'transfer_rebate_claim_type'                          => $transfer_rebate_claim_type,
            'transfer_rebate_claim_approve_by'               => $transfer_rebate_claim_approve_by, 
            'transfer_rebate_claim_percentage'                => $transfer_rebate_claim_percentage, 
            'transfer_adult_claim_rebate'                          => $transfer_adult_claim_rebate, 
            'transfer_adult_claim_after_rebate_exTAX'      => $transfer_adult_claim_after_rebate_exTAX,  
            'transfer_adult_claim_after_rebate'                  => $transfer_adult_claim_after_rebate,  
            'transfer_child_claim_rebate'                            => $transfer_child_claim_rebate,  
            'transfer_child_claim_after_rebate'                   => $transfer_child_claim_after_rebate,  
            'transfer_child_claim_after_rebate_exTAX'        => $transfer_child_claim_after_rebate_exTAX,   
            'transfer_infant_claim_rebate'                           => $transfer_infant_claim_rebate,   
            'transfer_infant_claim_after_rebate'                  => $transfer_infant_claim_after_rebate,   
            'transfer_infant_claim_after_rebate_exTAX'      => $transfer_infant_claim_after_rebate_exTAX,  
            'transfer_total_claim_after_rebate_exTAX'        => $transfer_total_claim_after_rebate_exTAX,  
            'transfer_total_claim_after_rebate'                    => $transfer_total_claim_after_rebate,  
            'transfer_client_room_no'                                 => $transfer_client_room_no,  
            'id_language'                                                     => $id_language,  
            'id_rep'                                                              => $id_rep,  
            'transfer_voucher_no'                                       => $transfer_voucher_no,  
            'transfer_remarks'                                             => $transfer_remarks,  
            'transfer_internal_remarks'                               => $transfer_internal_remarks,  
            'transfer_invoice_no'                                        => $transfer_invoice_no,  
            'transfer_invoiced_date'                                   => $transfer_invoiced_date,  
            'transfer_close'                                                 => $transfer_close,  
            'transfer_close_on'                                           => $transfer_close_on,  
            'transfer_status'                                               => $transfer_status,  
            'planning_status'                                              => $planning_status,  
            'id_planning'                                                     => $id_planning, 
            'id_user'                                                            => $id_user,
            'uname'                                                             => $uname,
            'log_status'                                                        => $log_status
        ));	

        $bookingTransfer_result= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim);
        echo json_encode($bookingTransfer_result);     

        }
    else
    {
        $bookingTransfer_result= array("OUTCOME" => "FAIL", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim);
        echo json_encode($bookingTransfer_result);  
    }
    
}
catch (Exception $ex) 
{
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>

