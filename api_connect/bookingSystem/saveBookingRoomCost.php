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

    require_once("../../connector/pdo_connect_main.php");
    
    $con = pdo_con();

    $id_booking_room_cost = $_POST["id_booking_room_cost"];
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
    $room_cost_calcultation = $_POST["room_cost_calcultation"];
    $room_adult_amt = $_POST["room_adult_amt"];
    $room_teen_amt = $_POST["room_teen_amt"];
    $room_child_amt = $_POST["room_child_amt"];
    $room_infant_amt = $_POST["room_infant_amt"];
    $room_total_pax = $_POST["room_total_pax"];
    $id_dept = $_POST["id_dept"];
    $room_charge = $_POST["room_charge"];
    $id_service_tax = $_POST["id_service_tax"];
    $tax_value = $_POST["tax_value"];
    $id_cost_cur = $_POST["id_cost_cur"];
    $room_adult_cost = $_POST["room_adult_cost"];
    $room_teen_cost = $_POST["room_teen_cost"];
    $room_child_cost = $_POST["room_child_cost"];
    $room_infant_cost = $_POST["room_infant_cost"];
    $room_total_cost = $_POST["room_total_cost"];
    $room_rebate_cost_type = $_POST["room_rebate_cost_type"];
    $room_rebate_cost_approve_by = $_POST["room_rebate_cost_approve_by"];
    $room_rebate_cost_percentage = $_POST["room_rebate_cost_percentage"];
    $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
    $room_adult_cost_after_rebate = $_POST["room_adult_cost_after_rebate"];
    $room_teen_cost_rebate = $_POST["room_teen_cost_rebate"];
    $room_teen_cost_after_rebate = $_POST["room_teen_cost_after_rebate"];
    $room_child_cost_rebate = $_POST["room_child_cost_rebate"];
    $room_child_cost_after_rebate = $_POST["room_child_cost_after_rebate"];
    $room_infant_cost_rebate = $_POST["room_infant_cost_rebate"];
    $room_infant_cost_after_rebate = $_POST["room_infant_cost_after_rebate"];
    $room_total_cost_after_rebate = $_POST["room_total_cost_after_rebate"];
    // $room_status = $_POST["room_status"];
    
    // Session User details
    $created_by = $_SESSION["solis_userid"];
    $created_name = $_SESSION["solis_username"];
    $log_status = "CREATE";

    //check duplicates for save Booking
    $sqlBookingRoomCost = "SELECT * FROM booking_room_cost WHERE id_booking_room_cost = :id_booking_room_cost";
    $stmt = $con->prepare($sqlBookingRoomCost);
    $stmt->execute(array(":id_booking_room_cost" => $id_booking_room_cost));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE BOOKING ROOM COST!");
    }

    if ($id_booking_room_cost == "-1") {
        $sqlSaveRoomCost= "INSERT INTO booking_room_cost
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
                room_cost_calcultation,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_total_pax,
                id_dept,
                room_charge,
                id_service_tax,
                tax_value,
                id_cost_cur,
                room_adult_cost,
                room_teen_cost,
                room_child_cost,
                room_infant_cost,
                room_total_cost,
                room_rebate_cost_type,
                room_rebate_cost_approve_by,
                room_rebate_cost_percentage,
                room_adult_cost_rebate,
                room_adult_cost_after_rebate,
                room_teen_cost_rebate,
                room_teen_cost_after_rebate,
                room_child_cost_rebate,
                room_child_cost_after_rebate,
                room_infant_cost_rebate,
                room_infant_cost_after_rebate,
                room_total_cost_after_rebate,
                -- room_status,
                created_by,
                created_name
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
                :room_cost_calcultation,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_total_pax,
                :id_dept,
                :room_charge,
                :id_service_tax,
                :tax_value,
                :id_cost_cur,
                :room_adult_cost,
                :room_teen_cost,
                :room_child_cost,
                :room_infant_cost,
                :room_total_cost,
                :room_rebate_cost_type,
                :room_rebate_cost_approve_by,
                :room_rebate_cost_percentage,
                :room_adult_cost_rebate,
                :room_adult_cost_after_rebate,
                :room_teen_cost_rebate,
                :room_teen_cost_after_rebate,
                :room_child_cost_rebate,
                :room_child_cost_after_rebate,
                :room_infant_cost_rebate,
                :room_infant_cost_after_rebate,
                :room_total_cost_after_rebate,
                -- :room_status,
                :created_by,
                :created_name
            )";
    
    $stmt = $con->prepare($sqlSaveRoomCost);
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
        ":room_cost_calcultation" => $room_cost_calcultation,
        ":room_adult_amt" => $room_adult_amt,
        ":room_teen_amt" => $room_teen_amt,
        ":room_child_amt" => $room_child_amt,
        ":room_infant_amt" => $room_infant_amt,
        ":room_total_pax" => $room_total_pax,
        ":id_dept" => $id_dept,
        ":room_charge" => $room_charge,
        ":id_service_tax" => $id_service_tax,
        ":tax_value" => $tax_value,
        ":id_cost_cur" => $id_cost_cur,
        ":room_adult_cost" => $room_adult_cost,
        ":room_teen_cost" => $room_teen_cost,
        ":room_child_cost" => $room_child_cost,
        ":room_infant_cost" => $room_infant_cost,
        ":room_total_cost" => $room_total_cost,
        ":room_rebate_cost_type" => $room_rebate_cost_type,
        ":room_rebate_cost_approve_by" => $room_rebate_cost_approve_by,
        ":room_rebate_cost_percentage" => $room_rebate_cost_percentage,
        ":room_adult_cost_rebate" => $room_adult_cost_rebate,
        ":room_adult_cost_after_rebate" => $room_adult_cost_after_rebate,
        ":room_teen_cost_rebate" => $room_teen_cost_rebate,
        ":room_teen_cost_after_rebate" => $room_teen_cost_after_rebate,
        ":room_child_cost_rebate" => $room_child_cost_rebate,
        ":room_child_cost_after_rebate" => $room_child_cost_after_rebate,
        ":room_infant_cost_rebate" => $room_infant_cost_rebate,
        ":room_infant_cost_after_rebate" => $room_infant_cost_after_rebate,
        ":room_total_cost_after_rebate" => $room_total_cost, // TO be update
        // ":room_status" => $room_status,
        ":created_by" => $created_by,
        ":created_name" => $created_name
    ));
    
    $id_booking_room_cost= $con->lastInsertId();

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
        "room_cost_calcultation" => $room_cost_calcultation,
        "room_adult_amt" => $room_adult_amt,
        "room_teen_amt" => $room_teen_amt,
        "room_child_amt" => $room_child_amt,
        "room_infant_amt" => $room_infant_amt,
        "room_total_pax" => $room_total_pax,
        "id_dept" => $id_dept,
        "room_charge" => $room_charge,
        "id_service_tax" => $id_service_tax,
        "tax_value" => $tax_value,
        "id_cost_cur" => $id_cost_cur,
        "room_adult_cost" => $room_adult_cost,
        "room_teen_cost" => $room_teen_cost,
        "room_child_cost" => $room_child_cost,
        "room_infant_cost" => $room_infant_cost,
        "room_total_cost" => $room_total_cost,
        "room_rebate_cost_type" => $room_rebate_cost_type,
        "room_rebate_cost_approve_by" => $room_rebate_cost_approve_by,
        "room_rebate_cost_percentage" => $room_rebate_cost_percentage,
        "room_adult_cost_rebate" => $room_adult_cost_rebate,
        "room_adult_cost_after_rebate" => $room_adult_cost_after_rebate,
        "room_teen_cost_rebate" => $room_teen_cost_rebate,
        "room_teen_cost_after_rebate" => $room_teen_cost_after_rebate,
        "room_child_cost_rebate" => $room_child_cost_rebate,
        "room_child_cost_after_rebate" => $room_child_cost_after_rebate,
        "room_infant_cost_rebate" => $room_infant_cost_rebate,
        "room_infant_cost_after_rebate" => $room_infant_cost_after_rebate,
        "room_total_cost_after_rebate" => $room_total_cost_after_rebate,
        "created_by" => $created_by,
        "created_name" => $created_name
    ));
    } catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }
