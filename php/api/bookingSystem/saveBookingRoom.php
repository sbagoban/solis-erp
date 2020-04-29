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

    $id_booking_room = $_POST["id_booking_room"];
    // $room_remarks = $_POST["room_remarks"];
    // $room_internal_remarks = $_POST["room_internal_remarks"];
    $room_status = trim($_POST["room_status"]);
    $stay_from = $_POST["stay_from"];
    $stay_to = $_POST["stay_to"];
    $room_adult_amt = $_POST["room_adult_amt"];
    $room_teen_amt = $_POST["room_teen_amt"];                                                                                                
    $room_child_amt = $_POST["room_child_amt"];
    $room_infant_amt = $_POST["room_infant_amt"];
    $room_status = $_POST["room_status"];
    $id_booking = $_POST["id_booking"];

    // Session User details
    $created_by = $_SESSION["solis_userid"];
    $created_name = $_SESSION["solis_username"];
    $log_status = "CREATE";

    //check duplicates for save Booking
    $sqlBookingRoom = "SELECT * FROM booking_room WHERE id_booking_room = :id_booking_room ";
    $stmt = $con->prepare($sqlBookingRoom);
    $stmt->execute(array(":id_booking_room" => $id_booking_room));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE BOOKING ROOM!");
    }

    if ($id_booking_room == "-1") {
        $sqlSaveRoom= "INSERT INTO booking_room
            (
                id_booking,
                stay_from,
                stay_to,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_status,
                created_by,
                created_name
            )
        VALUES
            (
                :id_booking,
                :stay_from,
                :stay_to,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_status,
                :created_by,
                :created_name
            )
    ";
    
    $stmt = $con->prepare($sqlSaveRoom);
    $stmt->execute(array(
        ":id_booking"=>$id_booking,
        ":stay_from"=>$stay_from,
        ":stay_to"=>$stay_to,
        ":room_adult_amt"=>$room_adult_amt,
        ":room_teen_amt"=>$room_teen_amt,
        ":room_child_amt"=>$room_child_amt,
        ":room_infant_amt"=>$room_infant_amt,
        ":room_status"=>$room_status,
        ":created_by"=>$created_by,
        ":created_name"=>$created_name
    ));
    
    $id_booking_room = $con->lastInsertId();
    
    $sqlSaveRoomLog= "INSERT INTO booking_room_log
            (
                id_booking_room,
                id_booking,
                stay_from,
                stay_to,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_status,
                id_user,
                uname,
                log_status
            )
        VALUES
            (
                :id_booking_room,
                :id_booking,
                :stay_from,
                :stay_to,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_status,
                :id_user,
                :uname, 
                :log_status
            )";
    
        $stmt = $con->prepare($sqlSaveRoomLog);
        $stmt->execute(array(
            ":id_booking_room"=>$id_booking_room,
            ":id_booking"=>$id_booking,
            ":stay_from"=>$stay_from,
            ":stay_to"=>$stay_to,
            ":room_adult_amt"=>$room_adult_amt,
            ":room_teen_amt"=>$room_teen_amt,
            ":room_child_amt"=>$room_child_amt,
            ":room_infant_amt"=>$room_infant_amt,
            ":room_status"=>$room_status,
            ":id_user"=>$created_by,
            ":uname"=>$created_name,
            ":log_status"=>$log_status
        ));

    }

    echo json_encode(array(
        "OUTCOME" => "OK", 
        "id_booking_room"=>$id_booking_room,
        "id_booking"=>$id_booking,
        "stay_from"=>$stay_from,
        "stay_to"=>$stay_to,
        "room_adult_amt"=>$room_adult_amt,
        "room_teen_amt"=>$room_teen_amt,
        "room_child_amt"=>$room_child_amt,
        "room_infant_amt"=>$room_infant_amt,
        "room_status"=>$room_status,
        "created_by"=>$created_by,
        "created_name"=>$created_name
    ));
    } catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }
