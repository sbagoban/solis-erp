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
    $inventory_type = $details["inventory_type"];
    $title = $details["title"];
    $release_days_value = utils_stringBlank($details["release_days_value"],null);
    $release_date_value = utils_stringBlank($details["release_date_value"],null);
    $autho_reserve_days_from = utils_stringBlank($details["autho_reserve_days_from"],null);
    $autho_reserve_days_to = utils_stringBlank($details["autho_reserve_days_to"],null);
    $autho_reserve_date_from = utils_stringBlank($details["autho_reserve_date_from"],null);
    $autho_reserve_date_to = utils_stringBlank($details["autho_reserve_date_to"],null);
    
    $autho_reserve_time_from = utils_stringBlank($details["autho_reserve_time_from"],null);
    $autho_reserve_time_to = utils_stringBlank($details["autho_reserve_time_to"],null);
      
    $note = $details["note"];
    $rooms_ids = $details["rooms_ids"];
    $to_ids = $details["to_ids"];
    $market_countries_ids = $details["market_countries_ids"];

    
    if (!is_null($release_date_value)) {
        $release_date_value = date("Y-m-d", strtotime($release_date_value));
    }
    if (!is_null($autho_reserve_date_from)) {
        $autho_reserve_date_from = date("Y-m-d", strtotime($autho_reserve_date_from));
    }

    if (!is_null($autho_reserve_date_to)) {
        $autho_reserve_date_to = date("Y-m-d", strtotime($autho_reserve_date_to));
    }
    
    //===========================================================================
    //CHECK FOR OVERLAPPING RECORDS:
    //ROOM + MARKET
    
    //get all the countries as comma separated
    //for each room
    //check if there is another record of inventory of same type for that room with a subset of countries
    
    
    
    $arr_room_ids = explode(",", $rooms_ids);
    for($i = 0; $i < count($arr_room_ids); $i++)
    {
        $roomid = trim($arr_room_ids[$i]);
        if($roomid != "")
        {
            $sql = "select inv.id, group_concat(c.country_name SEPARATOR ',') as bad_countries,
            hr.roomname
            from tblinventory inv
            inner join tblinventory_countries ic on inv.id = ic.inventoryfk
            inner join tblcountries c on ic.countryfk = c.id
            inner join tblinventory_rooms ir on ic.inventoryfk = inv.id
            inner join tblhotel_rooms hr on ir.roomfk = hr.id
            WHERE inv.id <> :id AND inv.inventory_type =:inventory_type 
            AND inv.hotelfk = :hotelfk 
            and inv.deleted = 0 and ir.roomfk = :roomid
            and ic.countryfk in ($market_countries_ids)
            group by inv.id, hr.roomname";
            
            $stmt = $con->prepare($sql);
            
            $stmt->execute(array(":hotelfk" => $hotelfk, ":roomid" => $roomid, ":id"=>$id,":inventory_type"=>$inventory_type));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $countries = $rw["bad_countries"];
                $roomname = $rw["roomname"];
                
                throw new Exception("<font color='red'><b>OVERLAPPING MARKETS</b></font> FOR ROOM: <b>$roomname</b><br> IN COUNTRIES: <b>$countries</b>!<br>SAVE DENIED");
            } 
        }
    }
    //===========================================================================

    if ($id == "-1") {

        $sql = "INSERT INTO tblinventory
                (hotelfk,date_created,deleted) 
                VALUES (:hotelfk,NOW(),0)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotelfk" => $hotelfk));
        $id = $con->lastInsertId();
    }


    //=============================================


    $sql = "UPDATE tblinventory SET 
            inventory_type=:inventory_type, release_days_value=:release_days_value,
            release_date_value=:release_date_value, autho_reserve_days_from=:autho_reserve_days_from,
            autho_reserve_days_to=:autho_reserve_days_to, 
            autho_reserve_date_from=:autho_reserve_date_from,
            autho_reserve_date_to=:autho_reserve_date_to,
            autho_reserve_time_from=:autho_reserve_time_from,
            autho_reserve_time_to=:autho_reserve_time_to,
            note=:note,title=:title
            WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":inventory_type" => $inventory_type,
        ":release_days_value" => $release_days_value,
        ":release_date_value" => $release_date_value,
        ":autho_reserve_days_from" => $autho_reserve_days_from,
        ":autho_reserve_days_to" => $autho_reserve_days_to,
        ":autho_reserve_date_from" => $autho_reserve_date_from,
        ":autho_reserve_date_to" => $autho_reserve_date_to,
        ":autho_reserve_time_from" => $autho_reserve_time_from,
        ":autho_reserve_time_to" => $autho_reserve_time_to,
        ":note" => $note,
        ":title" => $title));

    $outcome = saverooms($rooms_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    
    $outcome = savetouroperators($to_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    $outcome = savecountries($market_countries_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }


    $outcome = savedates($arr_dates);
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

function savedates($arr_dates) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_dates); $i++) {

            $date = $arr_dates[$i];

            //check if exists
            $sql = "SELECT * FROM tblinventory_dates WHERE inventoryfk=:inventoryfk AND 
                    inventory_date=:inventory_date";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventoryfk" => $id, ":inventory_date" => $date));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblinventory_dates (inventoryfk, inventory_date) VALUES 
                        (:inventoryfk, :inventory_date)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventoryfk" => $id, ":inventory_date" => $date));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblinventory_dates WHERE 
                    inventoryfk=:inventoryfk 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventoryfk" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE DATES: " . $ex->getMessage();
    }
}

function savetouroperators($to_ids)
{
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_to_ids = explode(",", $to_ids);

        for ($i = 0; $i < count($arr_to_ids); $i++) {

            $tofk = $arr_to_ids[$i];

            //check if exists
            $sql = "SELECT * FROM tblinventory_touroperators WHERE inventoryfk=:inventory_fk AND 
                    tofk=:tofk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventory_fk" => $id, ":tofk" => $tofk));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblinventory_touroperators (inventoryfk, tofk) VALUES 
                        (:inventoryfk, :tofk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventoryfk" => $id, ":tofk" => $tofk));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblinventory_touroperators WHERE 
                    inventoryfk=:inventory_fk 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventory_fk" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TOUR OPERATORS: " . $ex->getMessage();
    }
}
function saverooms($rooms_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_roomids = explode(",", $rooms_ids);

        for ($i = 0; $i < count($arr_roomids); $i++) {

            $roomid = $arr_roomids[$i];

            //check if exists
            $sql = "SELECT * FROM tblinventory_rooms WHERE inventory_fk=:inventory_fk AND 
                    roomfk=:roomfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventory_fk" => $id, ":roomfk" => $roomid));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblinventory_rooms (inventory_fk, roomfk) VALUES 
                        (:inventory_fk, :roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventory_fk" => $id, ":roomfk" => $roomid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblinventory_rooms WHERE 
                    inventory_fk=:inventory_fk 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventory_fk" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOMS: " . $ex->getMessage();
    }
}

function savecountries($market_countries_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_countries = explode(",", $market_countries_ids);

        for ($i = 0; $i < count($arr_countries); $i++) {

            $countryid = $arr_countries[$i];

            //check if exists
            $sql = "SELECT * FROM tblinventory_countries WHERE inventoryfk=:inventoryfk AND 
                    countryfk=:countryfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventoryfk" => $id, ":countryfk" => $countryid));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblinventory_countries (inventoryfk, countryfk) VALUES 
                        (:inventoryfk, :countryfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventoryfk" => $id, ":countryfk" => $countryid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblinventory_countries WHERE 
                    inventoryfk=:inventoryfk 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":inventoryfk" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE COUNTRIES: " . $ex->getMessage();
    }
}

?>
