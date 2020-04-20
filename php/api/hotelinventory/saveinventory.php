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
        throw new Exception("INVALID TOKEN");
    }

    //DETAILS 

    $details = json_decode($_POST["details"], true); //+
    $arr_dates = json_decode($_POST["dates"], true); //+

    $id = $details["id"];
    $hotelfk = $details["hotelfk"];
    $inventory_status = $details["inventory_type"];
    $title = $details["title"];
    $release_days_value = utils_stringBlank($details["release_days_value"], null);
    $release_date_value = utils_stringBlank($details["release_date_value"], null);
    $autho_reserve_days_from = utils_stringBlank($details["autho_reserve_days_from"], null);
    $autho_reserve_days_to = utils_stringBlank($details["autho_reserve_days_to"], null);
    $autho_reserve_date_from = utils_stringBlank($details["autho_reserve_date_from"], null);
    $autho_reserve_date_to = utils_stringBlank($details["autho_reserve_date_to"], null);

    $autho_reserve_time_from = utils_stringBlank($details["autho_reserve_time_from"], null);
    $autho_reserve_time_to = utils_stringBlank($details["autho_reserve_time_to"], null);

    $note = $details["note"];
    $rooms_ids = $details["rooms_ids"];
    $to_ids = trim($details["to_ids"]);
    $market_countries_ids = trim($details["market_countries_ids"]);
    $specific_to = $details["specific_to"];

    if (!is_null($release_date_value)) {
        $release_date_value = date("Y-m-d", strtotime($release_date_value));
    }
    if (!is_null($autho_reserve_date_from)) {
        $autho_reserve_date_from = date("Y-m-d", strtotime($autho_reserve_date_from));
    }

    if (!is_null($autho_reserve_date_to)) {
        $autho_reserve_date_to = date("Y-m-d", strtotime($autho_reserve_date_to));
    }


    //split room wise
    $arr_room_ids = explode(",", $rooms_ids);

    for ($i = 0; $i < count($arr_room_ids); $i++) {
        $roomid = $arr_room_ids[$i]; //<------------------- room id

        for ($j = 0; $j < count($arr_dates); $j++) {

            $date = $arr_dates[$j]; //<------------------- inventory date
            
            
            //=========================================================
            //WORLDWIDE MARKET: NO TOs AND COUNTRIES
            if($to_ids == "" && $market_countries_ids == "")
            {
                $tofk = null; //<------------------- TO ID set to NULL
                $countryfk = null; //<------------------- Country ID set to NULL
                
                $outcome = saveinventory_record($tofk, $countryfk, $date, $roomid);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
            //=========================================================
            //COUNTRIES MARKET: COUNTRIES BUT NO TOs 
            else if($market_countries_ids != "")
            {
                //split TO wise
                $arr_country_ids = explode(",", $market_countries_ids);
                for ($t = 0; $t < count($arr_country_ids); $t++) {
                    
                    $tofk = null; //<------------------- TO ID set to NULL
                    $countryfk = $arr_country_ids[$t]; //<------------------- Country ID
                    
                    $outcome = saveinventory_record($tofk, $countryfk, $date, $roomid);
                    if ($outcome != "OK") {
                        throw new Exception($outcome);
                    }
                }
            }
            //=========================================================
            //TO MARKET
            else if($to_ids != "")
            {
                //split TO wise
                $arr_to_ids = explode(",", $to_ids);
                for ($t = 0; $t < count($arr_to_ids); $t++) {

                    $tofk = $arr_to_ids[$t]; //<------------------- TO ID
                    $countryfk = null; //<------------------- Country ID set to NULL
                    
                    $outcome = saveinventory_record($tofk, $countryfk, $date, $roomid);
                    if ($outcome != "OK") {
                        throw new Exception($outcome);
                    }
                }
            }
            
        }
    }
    
    //DONE

    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function saveinventory_record($tofk, $countryfk, $date, $roomid) {
    try {

        global $con;
        global $hotelfk;
        global $inventory_status;
        global $title;
        global $release_days_value;
        global $release_date_value;
        global $autho_reserve_days_from;
        global $autho_reserve_days_to;
        global $autho_reserve_date_from;
        global $autho_reserve_date_to;
        global $autho_reserve_time_from;
        global $autho_reserve_time_to;
        global $note;
        global $specific_to;


        //first check if there is a record for this date, room, hotel, touroperator
        $id = -1;

        $sql = "SELECT * FROM tblinventory_dates WHERE 
                inventory_date=:inventory_date AND
                hotelfk=:hotelfk AND 
                roomfk=:roomfk AND 
                specific_to=:specific_to AND
                to_fk=:to_fk AND
                country_fk=:country_fk";

        $query = $con->prepare($sql);
        $query->execute(array(":inventory_date" => $date, ":hotelfk" => $hotelfk,
            ":roomfk" => $roomid, ":to_fk" => $tofk,
            ":country_fk" => $countryfk,
            ":specific_to" => $specific_to));

        if (!$rw = $query->fetch(PDO::FETCH_ASSOC)) {
            $sql = "INSERT INTO tblinventory_dates
                    (inventory_date,date_created,deleted) 
                    VALUES (:inventory_date,NOW(),0)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventory_date" => $date));
            $id = $con->lastInsertId();
        } else {
            $id = $rw["id"];
        }

        //=============================================

        $sql = "UPDATE tblinventory_dates SET 
                hotelfk=:hotelfk,
                roomfk=:roomfk,
                inventory_status=:inventory_status,
                to_fk=:to_fk,
                country_fk=:country_fk,
                release_days_value=:release_days_value,
                release_date_value=:release_date_value, 
                autho_reserve_days_from=:autho_reserve_days_from,
                autho_reserve_days_to=:autho_reserve_days_to, 
                autho_reserve_date_from=:autho_reserve_date_from,
                autho_reserve_date_to=:autho_reserve_date_to,
                autho_reserve_time_from=:autho_reserve_time_from,
                autho_reserve_time_to=:autho_reserve_time_to,
                note=:note,
                title=:title,
                specific_to=:specific_to
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":hotelfk" => $hotelfk,
            ":roomfk" => $roomid,
            ":to_fk" => $tofk,
            ":country_fk" => $countryfk,
            ":inventory_status" => $inventory_status,
            ":release_days_value" => $release_days_value,
            ":release_date_value" => $release_date_value,
            ":autho_reserve_days_from" => $autho_reserve_days_from,
            ":autho_reserve_days_to" => $autho_reserve_days_to,
            ":autho_reserve_date_from" => $autho_reserve_date_from,
            ":autho_reserve_date_to" => $autho_reserve_date_to,
            ":autho_reserve_time_from" => $autho_reserve_time_from,
            ":autho_reserve_time_to" => $autho_reserve_time_to,
            ":note" => $note,
            ":specific_to" => $specific_to,
            ":title" => $title));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE INVENTORY RECORD: " . $ex->getMessage();
    }
}

?>
