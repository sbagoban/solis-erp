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

    require_once("../../php/connector/pdo_connect_main.php");
    
    $con = pdo_con();


    $id_booking = $_POST["id_booking"];
    $id_booking_room_claim = $_POST["id_booking_room_claim"];
    $id_booking_room = $_POST["id_booking_room"];
    $room_service_paid_by = $_POST["room_service_paid_by"];
    $id_tour_operator = $_POST["id_tour_operator"];
    $id_client = $_POST["id_client"];
    $room_stay_from = $_POST["room_stay_from"];
    $room_stay_to = $_POST["room_stay_to"];
    $room_booking_date = $_POST["room_booking_date"];
    $id_contract = $_POST["id_contract"];
    $id_hotel = $_POST["id_hotel"];
    $hotelname = $_POST["hotelname"];
    $id_room = $_POST["id_room"];
    $room_details = $_POST["room_details"];
    $service_details = $_POST["service_details"];
    $room_claim_calcultation = $_POST["room_claim_calcultation"];
    $room_adult_amt = $_POST["room_adult_amt"];
    $room_teen_amt = $_POST["room_teen_amt"];
    $room_child_amt = $_POST["room_child_amt"];
    $room_infant_amt = $_POST["room_infant_amt"];
    $room_total_pax = $_POST["room_total_pax"];
    $id_dept = $_POST["id_dept"];
    $room_charge = $_POST["room_charge"];
    $id_service_tax = $_POST["id_service_tax"];
    $tax_value = $_POST["tax_value"];
    $id_claim_cur = $_POST["id_claim_cur"];
    $room_adult_claim = $_POST["room_adult_claim"];
    $room_teen_claim = $_POST["room_teen_claim"];
    $room_child_claim = $_POST["room_child_claim"];
    $room_infant_claim = $_POST["room_infant_claim"];
    $room_total_claim = $_POST["room_total_claim"];
    $room_rebate_claim_type = $_POST["room_rebate_claim_type"];
    $room_rebate_claim_approve_by = $_POST["room_rebate_claim_approve_by"];
    $room_rebate_claim_percentage = $_POST["room_rebate_claim_percentage"];
    $room_adult_claim_rebate = $_POST["room_adult_claim_rebate"];
    $room_adult_claim_after_rebate = $_POST["room_adult_claim_after_rebate"];
    $room_teen_claim_rebate = $_POST["room_teen_claim_rebate"];
    $room_teen_claim_after_rebate = $_POST["room_teen_claim_after_rebate"];
    $room_child_claim_rebate = $_POST["room_child_claim_rebate"];
    $room_child_claim_after_rebate = $_POST["room_child_claim_after_rebate"];
    $room_infant_claim_rebate = $_POST["room_infant_claim_rebate"];
    $room_infant_claim_after_rebate = $_POST["room_infant_claim_after_rebate"];
    $room_total_claim_after_rebate = $_POST["room_total_claim_after_rebate"];
    $room_remarks = $_POST["room_remarks"];
    $room_internal_remarks = $_POST["room_internal_remarks"];
    $room_status = $_POST["room_status"];
    $booking_client = $_POST["booking_client"];
    
    // Session User details
    $created_by = $_SESSION["solis_userid"];
    $created_name = $_SESSION["solis_username"];
    $log_status = "CREATE";

    //check duplicates for save Booking
    $sqlBookingRoomClaim = "SELECT * FROM booking_room_claim WHERE id_booking_room_claim = :id_booking_room_claim ";
    $stmt = $con->prepare($sqlBookingRoomClaim);
    $stmt->execute(array(":id_booking_room_claim" => $id_booking_room_claim));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE BOOKING ROOM CLAIMS!");
    }

    if ($id_booking_room_claim == "-1") {
        $sqlSaveRoomClaim= "INSERT INTO booking_room_claim
            (
                id_booking,
                id_booking_room,
                room_service_paid_by,
                id_tour_operator,
                id_client,
                room_stay_from,
                room_stay_to,
                room_booking_date,
                id_contract,
                id_hotel,
                hotelname,
                id_room,
                room_details,
                service_details,
                room_claim_calcultation,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_total_pax,
                id_dept,
                room_charge,
                id_service_tax,
                tax_value,
                id_claim_cur,
                room_adult_claim,
                room_teen_claim,
                room_child_claim,
                room_infant_claim,
                room_total_claim,
                room_rebate_claim_type,
                room_rebate_claim_approve_by,
                room_rebate_claim_percentage,
                room_adult_claim_rebate,
                room_adult_claim_after_rebate,
                room_teen_claim_rebate,
                room_teen_claim_after_rebate,
                room_child_claim_rebate,
                room_child_claim_after_rebate,
                room_infant_claim_rebate,
                room_infant_claim_after_rebate,
                room_total_claim_after_rebate,
                room_remarks,
                room_internal_remarks,
                room_status,
                created_by,
                created_name
            )
        VALUES
            (
            :id_booking,
            :id_booking_room,
            :room_service_paid_by,
            :id_tour_operator,
            :id_client,
            :room_stay_from,
            :room_stay_to,
            :room_booking_date,
            :id_contract,
            :id_hotel,
            :hotelname,
            :id_room,
            :room_details,
            :service_details,
            :room_claim_calcultation,
            :room_adult_amt,
            :room_teen_amt,
            :room_child_amt,
            :room_infant_amt,
            :room_total_pax,
            :id_dept,
            :room_charge,
            :id_service_tax,
            :tax_value,
            :id_claim_cur,
            :room_adult_claim,
            :room_teen_claim,
            :room_child_claim,
            :room_infant_claim,
            :room_total_claim,
            :room_rebate_claim_type,
            :room_rebate_claim_approve_by,
            :room_rebate_claim_percentage,
            :room_adult_claim_rebate,
            :room_adult_claim_after_rebate,
            :room_teen_claim_rebate,
            :room_teen_claim_after_rebate,
            :room_child_claim_rebate,
            :room_child_claim_after_rebate,
            :room_infant_claim_rebate,
            :room_infant_claim_after_rebate,
            :room_total_claim_after_rebate,
            :room_remarks,
            :room_internal_remarks,
            :room_status,
            :created_by,
            :created_name
            )";
    
    $stmt = $con->prepare($sqlSaveRoomClaim);
    $stmt->execute(array(
        ":id_booking" => $id_booking,
        ":id_booking_room" => $id_booking_room,
        ":room_service_paid_by" => $room_service_paid_by,
        ":id_tour_operator" => $id_tour_operator,
        ":id_client" => $id_client,
        ":room_stay_from" => $room_stay_from,
        ":room_stay_to" => $room_stay_to,
        ":room_booking_date" => $room_booking_date,
        ":id_contract" => $id_contract,
        ":id_hotel" => $id_hotel,
        ":hotelname" => $hotelname,
        ":id_room" => $id_room,
        ":room_details" => $room_details,
        ":service_details" => $service_details,
        ":room_claim_calcultation" => $room_claim_calcultation,
        ":room_adult_amt" => $room_adult_amt,
        ":room_teen_amt" => $room_teen_amt,
        ":room_child_amt" => $room_child_amt,
        ":room_infant_amt" => $room_infant_amt,
        ":room_total_pax" => $room_total_pax,
        ":id_dept" => $id_dept,
        ":room_charge" => $room_charge,
        ":id_service_tax" => $id_service_tax,
        ":tax_value" => $tax_value,
        ":id_claim_cur" => $id_claim_cur,
        ":room_adult_claim" => $room_adult_claim,
        ":room_teen_claim" => $room_teen_claim,
        ":room_child_claim" => $room_child_claim,
        ":room_infant_claim" => $room_infant_claim,
        ":room_total_claim" => $room_total_claim,
        ":room_rebate_claim_type" => $room_rebate_claim_type,
        ":room_rebate_claim_approve_by" => $room_rebate_claim_approve_by,
        ":room_rebate_claim_percentage" => $room_rebate_claim_percentage,
        ":room_adult_claim_rebate" => $room_adult_claim_rebate,
        ":room_adult_claim_after_rebate" => $room_adult_claim_after_rebate,
        ":room_teen_claim_rebate" => $room_teen_claim_rebate,
        ":room_teen_claim_after_rebate" => $room_teen_claim_after_rebate,
        ":room_child_claim_rebate" => $room_child_claim_rebate,
        ":room_child_claim_after_rebate" => $room_child_claim_after_rebate,
        ":room_infant_claim_rebate" => $room_infant_claim_rebate,
        ":room_infant_claim_after_rebate" => $room_infant_claim_after_rebate,
        ":room_total_claim_after_rebate" => $room_total_claim, // To be update
        ":room_remarks" => $room_remarks,
        ":room_internal_remarks" => $room_internal_remarks,
        ":room_status" => $room_status,
        ":created_by" => $created_by,
        ":created_name" => $created_name
    ));
    
    $id_booking_room_claim = $con->lastInsertId();

    // CLIENT ACTIVITY
    $sqlClientRoom = "INSERT INTO booking_room_client (id_client, id_booking_room_claim, id_booking) 
    VALUES (:booking_client, :id_booking_room_claim, :id_booking)";

    $stmt = $con->prepare($sqlClientRoom);
    $data = $booking_client;

    foreach($data as $d) {
        $stmt->execute(array(':id_booking_room_claim' => $id_booking_room_claim, ':id_booking' => $id_booking, ':booking_client' => $d));
    }
    
    $sqlSaveRoomClaimLog= "INSERT INTO booking_room_claim_log
            (
                id_booking_room_claim,
                id_booking,
                id_booking_room,
                room_service_paid_by,
                id_tour_operator,
                id_client,
                room_stay_from,
                room_stay_to,
                room_booking_date,
                id_contract,
                id_hotel,
                hotelname,
                id_room,
                room_details,
                service_details,
                room_claim_calcultation,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_total_pax,
                id_dept,
                room_charge,
                id_service_tax,
                tax_value,
                id_claim_cur,
                room_adult_claim,
                room_teen_claim,
                room_child_claim,
                room_infant_claim,
                room_total_claim,
                room_rebate_claim_type,
                room_rebate_claim_approve_by,
                room_rebate_claim_percentage,
                room_adult_claim_rebate,
                room_adult_claim_after_rebate,
                room_teen_claim_rebate,
                room_teen_claim_after_rebate,
                room_child_claim_rebate,
                room_child_claim_after_rebate,
                room_infant_claim_rebate,
                room_infant_claim_after_rebate,
                room_total_claim_after_rebate,
                room_remarks,
                room_internal_remarks,
                room_status,
                id_user,
                uname,
                log_status
            )
        VALUES
            (
                :id_booking_room_claim,
                :id_booking,
                :id_booking_room,
                :room_service_paid_by,
                :id_tour_operator,
                :id_client,
                :room_stay_from,
                :room_stay_to,
                :room_booking_date,
                :id_contract,
                :id_hotel,
                :hotelname,
                :id_room,
                :room_details,
                :service_details,
                :room_claim_calcultation,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_total_pax,
                :id_dept,
                :room_charge,
                :id_service_tax,
                :tax_value,
                :id_claim_cur,
                :room_adult_claim,
                :room_teen_claim,
                :room_child_claim,
                :room_infant_claim,
                :room_total_claim,
                :room_rebate_claim_type,
                :room_rebate_claim_approve_by,
                :room_rebate_claim_percentage,
                :room_adult_claim_rebate,
                :room_adult_claim_after_rebate,
                :room_teen_claim_rebate,
                :room_teen_claim_after_rebate,
                :room_child_claim_rebate,
                :room_child_claim_after_rebate,
                :room_infant_claim_rebate,
                :room_infant_claim_after_rebate,
                :room_total_claim_after_rebate,
                :room_remarks,
                :room_internal_remarks,
                :room_status,
                :id_user,
                :uname, 
                :log_status
            )";
    
        $stmt = $con->prepare($sqlSaveRoomClaimLog);
        $stmt->execute(array(
            ":id_booking_room_claim" => $id_booking_room_claim,
            ":id_booking" => $id_booking,
            ":id_booking_room" => $id_booking_room,
            ":room_service_paid_by" => $room_service_paid_by,
            ":id_tour_operator" => $id_tour_operator,
            ":id_client" => $id_client,
            ":room_stay_from" => $room_stay_from,
            ":room_stay_to" => $room_stay_to,
            ":room_booking_date" => $room_booking_date,
            ":id_contract" => $id_contract,
            ":id_hotel" => $id_hotel,
            ":hotelname" => $hotelname,
            ":id_room" => $id_room,
            ":room_details" => $room_details,
            ":service_details" => $service_details,
            ":room_claim_calcultation" => $room_claim_calcultation,
            ":room_adult_amt" => $room_adult_amt,
            ":room_teen_amt" => $room_teen_amt,
            ":room_child_amt" => $room_child_amt,
            ":room_infant_amt" => $room_infant_amt,
            ":room_total_pax" => $room_total_pax,
            ":id_dept" => $id_dept,
            ":room_charge" => $room_charge,
            ":id_service_tax" => $id_service_tax,
            ":tax_value" => $tax_value,
            ":id_claim_cur" => $id_claim_cur,
            ":room_adult_claim" => $room_adult_claim,
            ":room_teen_claim" => $room_teen_claim,
            ":room_child_claim" => $room_child_claim,
            ":room_infant_claim" => $room_infant_claim,
            ":room_total_claim" => $room_total_claim,
            ":room_rebate_claim_type" => $room_rebate_claim_type,
            ":room_rebate_claim_approve_by" => $room_rebate_claim_approve_by,
            ":room_rebate_claim_percentage" => $room_rebate_claim_percentage,
            ":room_adult_claim_rebate" => $room_adult_claim_rebate,
            ":room_adult_claim_after_rebate" => $room_adult_claim_after_rebate,
            ":room_teen_claim_rebate" => $room_teen_claim_rebate,
            ":room_teen_claim_after_rebate" => $room_teen_claim_after_rebate,
            ":room_child_claim_rebate" => $room_child_claim_rebate,
            ":room_child_claim_after_rebate" => $room_child_claim_after_rebate,
            ":room_infant_claim_rebate" => $room_infant_claim_rebate,
            ":room_infant_claim_after_rebate" => $room_infant_claim_after_rebate,
            ":room_total_claim_after_rebate" => $room_total_claim_after_rebate,
            ":room_remarks" => $room_remarks,
            ":room_internal_remarks" => $room_internal_remarks,
            ":room_status" => $room_status,
            ":id_user"=>$created_by,
            ":uname"=>$created_name,
            ":log_status"=>$log_status
        ));

    }

    echo json_encode(array(
        "OUTCOME" => "OK",
        "id_booking_room_claim" => $id_booking_room_claim,
        "id_booking" => $id_booking,
        "id_booking_room" => $id_booking_room,
        "room_service_paid_by" => $room_service_paid_by,
        "id_tour_operator" => $id_tour_operator,
        "id_client" => $id_client,
        "room_stay_from" => $room_stay_from,
        "room_stay_to" => $room_stay_to,
        "room_booking_date" => $room_booking_date,
        "id_contract" => $id_contract,
        "id_hotel" => $id_hotel,
        "hotelname" => $hotelname,
        "id_room" => $id_room,
        "room_details" => $room_details,
        "service_details" => $service_details,
        "room_claim_calcultation" => $room_claim_calcultation,
        "room_adult_amt" => $room_adult_amt,
        "room_teen_amt" => $room_teen_amt,
        "room_child_amt" => $room_child_amt,
        "room_infant_amt" => $room_infant_amt,
        "room_total_pax" => $room_total_pax,
        "id_dept" => $id_dept,
        "room_charge" => $room_charge,
        "id_service_tax" => $id_service_tax,
        "tax_value" => $tax_value,
        "id_claim_cur" => $id_claim_cur,
        "room_adult_claim" => $room_adult_claim,
        "room_teen_claim" => $room_teen_claim,
        "room_child_claim" => $room_child_claim,
        "room_infant_claim" => $room_infant_claim,
        "room_total_claim" => $room_total_claim,
        "room_rebate_claim_type" => $room_rebate_claim_type,
        "room_rebate_claim_approve_by" => $room_rebate_claim_approve_by,
        "room_rebate_claim_percentage" => $room_rebate_claim_percentage,
        "room_adult_claim_rebate" => $room_adult_claim_rebate,
        "room_adult_claim_after_rebate" => $room_adult_claim_after_rebate,
        "room_teen_claim_rebate" => $room_teen_claim_rebate,
        "room_teen_claim_after_rebate" => $room_teen_claim_after_rebate,
        "room_child_claim_rebate" => $room_child_claim_rebate,
        "room_child_claim_after_rebate" => $room_child_claim_after_rebate,
        "room_infant_claim_rebate" => $room_infant_claim_rebate,
        "room_infant_claim_after_rebate" => $room_infant_claim_after_rebate,
        "room_total_claim_after_rebate" => $room_total_claim_after_rebate,
        "room_remarks" => $room_remarks,
        "room_internal_remarks" => $room_internal_remarks,
        "room_status" => $room_status
    ));
    } catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }
