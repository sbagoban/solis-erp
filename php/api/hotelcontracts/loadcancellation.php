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
        die("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        die("INVALID TOKEN");
    }

    if ($_POST["t"] != $_SESSION["token"]) {
        die("INVALID TOKEN");
    }

    if (!isset($_POST["cid"])) {
        die("INVALID CONTRACT ID");
    }

    $cid = $_POST["cid"];

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../connector/db_pdo.php");
    require_once("../../connector/data_connector.php");

    $con = pdo_con();

    $arr_checkinout = array();

    $sql = "SELECT * FROM tblservice_contract_cancellation 
        WHERE service_contract_fk=:cid ORDER BY override_dtfrom ASC, override_dtto ASC";

    $query = $con->prepare($sql);
    $query->execute(array(":cid" => $cid));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $cancellation_id = $rw["id"];
        $cancellation_canceltype = $rw["canceltype"];
        $cancellation_override_dtfrom = $rw["override_dtfrom"];
        $cancellation_override_dtto = $rw["override_dtto"];
        $cancellation_charge_method = $rw["charge_method"];
        $cancellation_charge_value = $rw["charge_value"];
        $cancellation_days_before_arrival_from = $rw["days_before_arrival_from"];
        $cancellation_days_before_arrival_to = $rw["days_before_arrival_to"];
        $cancellation_dates_before_arrival_from = $rw["dates_before_arrival_from"];
        $cancellation_dates_before_arrival_to = $rw["dates_before_arrival_to"];


        $arr_checkinout[] = array("cancellation_id" => $cancellation_id,
            "cancellation_canceltype" => $cancellation_canceltype,
            "cancellation_override_dtfrom" => $cancellation_override_dtfrom,
            "cancellation_override_dtto" => $cancellation_override_dtto,
            "cancellation_charge_method" => $cancellation_charge_method,
            "cancellation_charge_value" => $cancellation_charge_value,
            "cancellation_days_before_arrival_from" => $cancellation_days_before_arrival_from,
            "cancellation_days_before_arrival_to" => $cancellation_days_before_arrival_to,
            "cancellation_dates_before_arrival_from" => $cancellation_dates_before_arrival_from,
            "cancellation_dates_before_arrival_to" => $cancellation_dates_before_arrival_to,
            "cancellation_action" => "",
            "cancellation_rooms" => getRooms($cancellation_id, $con));
    }

    echo json_encode(array("OUTCOME" => "OK", "CANCELLATIONS" => $arr_checkinout));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function getRooms($cancellation_id,$con) {

    $arr_rooms = array();
    
    //get children count by age range
    $sql = "SELECT sccr.*, hr.roomname
            FROM tblservice_contract_cancellation_rooms sccr
            INNER JOIN tblhotel_rooms hr on sccr.roomfk = hr.id
            WHERE service_contract_cancellation_fk=:cancellationid 
            ORDER BY hr.roomname ASC";

    $query_room = $con->prepare($sql);
    $query_room->execute(array(":cancellationid" => $cancellation_id));
    while ($rw_room = $query_room->fetch(PDO::FETCH_ASSOC)) {

        $room_rwid = $rw_room["id"];
        $room_roomfk = $rw_room["roomfk"];
        $room_roomname = $rw_room["roomname"];
        
        
        $arr_rooms[] = array("room_rwid"=>$room_rwid,
                                "room_roomfk"=>$room_roomfk,
                                "room_roomname"=>$room_roomname,
                                "room_action"=>"");
    }

    return $arr_rooms;
}

?>