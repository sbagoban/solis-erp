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

    //to prevent mysql from truncating group_concat values
    $sql = "SET SESSION group_concat_max_len=10000;";
    $stmt = $con->prepare($sql);
    $stmt->execute();


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
    $date_to = date("Y-m-d", strtotime($details["date_to"]));
    $release_type = $details["release_type"];
    $priority = $details["priority"];
    $specific_no_days = utils_stringBlank($details["specific_no_days"], null);
    $specific_date = utils_stringBlank($details["specific_date"], null);
    $to_ids = trim($details["to_ids"]);
    $market_countries_ids = trim($details["market_countries_ids"]);

    $comment = trim($details["comment"]);
    $units = trim($details["units"]);

    if (!is_null($specific_date)) {
        $specific_date = date("Y-m-d", strtotime($specific_date));
    }


    //======================================================
    //test for overlapping dates for same TO and Room
    $test_outcome = allotment_test_overlapping();
    if ($test_outcome != "OK") {
        throw new Exception($test_outcome);
    }

    //======================================================
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
            priority=:priority,
            date_from=:date_from,
            date_to=:date_to
            WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":release_type" => $release_type,
        ":specific_no_days" => $specific_no_days,
        ":specific_date" => $specific_date,
        ":comment" => $comment,
        ":priority" => $priority,
        ":date_from" => $date_from,
        ":date_to" => $date_to,
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
                WHERE allotmentfk=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));


        if ($touroperator_ids != "") {
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
                WHERE allotmentfk=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        if ($market_countries_ids != "") {
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
                WHERE allotmentfk=:id";

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

function allotment_test_overlapping() {
    global $con, $id, $date_from, $date_to, $to_ids, $rooms_ids, $priority, $market_countries_ids;

    $sql = "SELECT a.id, a.date_from, a.date_to, 
            group_concat(ar.roomfk SEPARATOR ',') as roomids,
            group_concat(ato.tofk SEPARATOR ',') as toids,
            group_concat(ac.countryfk SEPARATOR ',') as countryids
            FROM tblinventory_allotment a
            INNER JOIN tblinventory_allotment_rooms ar on a.id = ar.allotmentfk
            LEFT JOIN tblinventory_allotment_to ato on a.id = ato.allotmentfk
            LEFT JOIN tblinventory_allotment_countries ac on a.id = ac.allotmentfk
            WHERE
            a.deleted = 0 AND 
            a.id <> :id AND
            a.date_from <= :date_to AND
            a.date_to >= :date_from AND
            a.priority = :priority
            AND ar.roomfk in ($rooms_ids) ";

    if ($to_ids != "") {
        $sql .= " AND ato.tofk in ($to_ids) ";
    } else if ($market_countries_ids != "") {
        $sql .= " AND ac.countryfk in ($market_countries_ids) ";
    }

    $sql .= " group by a.id, a.date_from, a.date_to";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id, ":date_to" => $date_to,
        ":date_from" => $date_from,
        ":priority" => $priority));

    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

        //ouch! overlapping dates detected! inform overlapping for what priority, room and to

        $_roomids = $rw["roomids"];
        $_toids = $rw["toids"];
        $_countryids = $rw["countryids"];

        $_date_from = $rw["date_from"];
        $_date_to = $rw["date_to"];

        $overlapping_to_names = "";
        $overlapping_room_names = "";
        $overlapping_country_names = "";

        $arr_save_toids = explode(",", $to_ids);
        $arr_save_roomids = explode(",", $rooms_ids);
        $arr_save_countryids = explode(",", $market_countries_ids);

        $_arr_roomids = explode(",", $_roomids);
        $_arr_toids = explode(",", $_toids);
        $_arr_countryids = explode(",", $_countryids);

        //check for overlapping rooms and tos
        $arr_common_rooms = array_intersect($_arr_roomids, $arr_save_roomids);
        $arr_common_tos = array_intersect($_arr_toids, $arr_save_toids);
        $arr_common_countries = array_intersect($_arr_countryids, $arr_save_countryids);

        //============================================================================
        //now get the to names and room names and country names
        $overlapping_to_ids = implode(",", $arr_common_tos);

        if ($overlapping_to_ids != "") {
            $sql = "select group_concat(toname order by toname asc separator ',' ) as tonames
                    from tbltouroperator where id in ($overlapping_to_ids)";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $overlapping_to_names = $rw["tonames"];
            }
        }


        //============================================================================
        $overlapping_room_ids = implode(",", $arr_common_rooms);

        if ($overlapping_room_ids != "") {
            $sql = "select group_concat(roomname order by roomname asc separator ',' ) as roomnames
                from tblhotel_rooms where id in ($overlapping_room_ids)";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $overlapping_room_names = $rw["roomnames"];
            }
        }

        //============================================================================
        $overlapping_country_ids = implode(",", $arr_common_countries);

        if ($overlapping_country_ids != "") {
            $sql = "select group_concat(countrycode_3 order by countrycode_3 asc separator ',' ) as country_names
                from tblcountries where id in ($overlapping_country_ids)";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $overlapping_country_names = $rw["country_names"];
            }
        }



        //return the error message
        $msg = "<b><font color='red'>OVERLAPPING DETECTED</font></b>:<BR>";
        $msg .= "<B>DATE FROM:</B> " . date("d M Y", strtotime($_date_from)) . " <BR>";
        $msg .= "<B>DATE TO:</B> " . date("d M Y", strtotime($_date_to)) . " <BR>";
        $msg .= "<B>PRIORITY:</B> $priority <BR>";
        $msg .= "<B>ROOMS:</B> $overlapping_room_names <BR>";
        $msg .= "<B>COUNTRIES:</B> $overlapping_country_names <BR>";
        $msg .= "<B>TOUR OPERATORS:</B> $overlapping_to_names <BR>";


        return $msg;
    }

    return "OK";
}

?>
