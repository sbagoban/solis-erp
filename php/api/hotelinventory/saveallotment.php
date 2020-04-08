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

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $con->beginTransaction();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        //throw new Exception("INVALID TOKEN");
    }

    //DETAILS 

    $details = json_decode($_POST["details"], true); //+

    $id = $details["id"];
    $hotelfk = $details["hotelfk"];
    $rooms_ids = $details["rooms_ids"];
    $date_from = date("Y-m-d", strtotime($details["date_from"]));
    $date_from = date("Y-m-d", strtotime($details["date_to"]));
    $release_type = $details["release_type"];
    $priority = $details["priority"];
    $specific_no_days = utils_stringBlank($details["specific_no_days"], null);
    $specific_date = utils_stringBlank($details["specific_date"], null);
    $to_ids = $details["to_ids"];
    $market_countries_ids = $details["market_countries_ids"];

    $comment = trim($details["comment"]);
    $units = trim($details["units"]);

    if (!is_null($specific_date)) {
        $specific_date = date("Y-m-d", strtotime($specific_date));
    }




    //is it an insert or an update
    if ($id == "-1") {
        $sql = "INSERT INTO tblinventory_allotment
                (hotel_fk,created_on,created_by,deleted) 
                VALUES (:hotel_fk, NOW(), :created_by, 0)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotel_fk" => $hotelfk,
            ":created_by" => $_SESSION["solis_userid"]));
        $id = $con->lastInsertId();
    }

    //=============================================

    $sql = "UPDATE tblinventory_allotment SET 
            release_type=:release_type,
            specific_no_days=:specific_no_days, 
            specific_date=:specific_date,
            comment=:comment, 
            units=:units,
            priority=:priority
            WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":release_type" => $release_type,
        ":specific_no_days" => $specific_no_days,
        ":specific_date" => $specific_date,
        ":comment" => $comment,
        ":priority" => $priority,
        ":units" => $units));


    $outcome = saverooms($rooms_ids, $id);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    
    $outcome = savetouroperators($to_ids, $id);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    $outcome = savecountries($market_countries_ids, $id);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    
    
    //DONE

    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function savetouroperators($touroperator_ids, $id) {
    try {

        global $con;

        $sql = "DELETE FROM tblinventory_allotment_to 
                WHERE allotmentfk=:id AND 
                tofk NOT IN ($touroperator_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_to_ids = explode(",", $touroperator_ids);
        for ($i = 0; $i < count($arr_to_ids); $i++) {
            $toid = trim($arr_to_ids[$i]);
            $sql = "SELECT * FROM tblinventory_allotment_to WHERE 
                allotmentfk=:id AND tofk=:tofk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":tofk" => $toid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblinventory_allotment_to 
                    (allotmentfk,tofk) 
                    VALUES (:id,:tofk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":tofk" => $toid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TOUR OPERATORS: " . $ex->getMessage();
    }
}

function savecountries($market_countries_ids, $id) {
    try {

        global $con;

        $sql = "DELETE FROM tblinventory_allotment_countries 
                WHERE allotmentfk=:id AND 
                countryfk NOT IN ($market_countries_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_country_ids = explode(",", $market_countries_ids);
        for ($i = 0; $i < count($arr_country_ids); $i++) {
            $cid = trim($arr_country_ids[$i]);
            $sql = "SELECT * FROM tblinventory_allotment_countries WHERE 
                allotmentfk=:id AND countryfk=:countryfk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":countryfk" => $cid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblinventory_allotment_countries 
                    (allotmentfk,countryfk) 
                    VALUES (:id,:countryfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":countryfk" => $cid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE COUNTRIES: " . $ex->getMessage();
    }
}

function saverooms($rooms_ids, $id) {
    try {

        global $con;

        $sql = "DELETE FROM tblinventory_allotment_rooms 
                WHERE allotmentfk=:id AND 
                roomfk NOT IN ($rooms_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_room_ids = explode(",", $rooms_ids);
        for ($i = 0; $i < count($arr_room_ids); $i++) {
            $rid = trim($arr_room_ids[$i]);
            $sql = "SELECT * FROM tblinventory_allotment_rooms 
                    WHERE 
                    allotmentfk=:id AND roomfk=:roomfk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":roomfk" => $rid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblinventory_allotment_rooms 
                        (allotmentfk,roomfk) 
                        VALUES (:id,:roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":roomfk" => $rid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOMS: " . $ex->getMessage();
    }
}

?>
