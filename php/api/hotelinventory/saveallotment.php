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
    $date_from = $details["date_from"];
    $date_to = $details["date_to"];
    $release_type = $details["release_type"];
    $specific_no_days = utils_stringBlank($details["specific_no_days"], null);
    $specific_date = utils_stringBlank($details["specific_date"], null);
    $to_ids = $details["to_ids"];
    $market_countries_ids = $details["market_countries_ids"];

    $comment = trim($details["comment"]);
    $units = trim($details["units"]);

    if (!is_null($specific_date)) {
        $specific_date = date("Y-m-d", strtotime($specific_date));
    }
   
    $date_from = date("Y-m-d", strtotime($date_from));
    $date_to = date("Y-m-d", strtotime($date_to));
    
    $date_from = new DateTime($date_from);
    $date_to = new DateTime($date_to);    
    $date_to->modify('+1 day');
    
    
    
    $interval = DateInterval::createFromDateString('1 day');
      
    
    

    //split room wise
    $arr_room_ids = explode(",", $rooms_ids);

    //split TO ids
    $arr_to_ids = explode(",", $to_ids);

    for ($i = 0; $i < count($arr_room_ids); $i++) {
        $roomid = $arr_room_ids[$i]; //<------------------- room id

        for ($j = 0; $j < count($arr_to_ids); $j++) {
            $tofk = $arr_to_ids[$j]; //<------------------- tour operator id
            
           $period = new DatePeriod($date_from, $interval, $date_to);
            
            
            
            foreach ($period as $dt) {
                $this_date = $dt->format("Y-m-d");

                //first check if there is a record for this date, room, hotel, touroperator
                $id = -1;
                
                $sql = "SELECT * FROM tblinventory_allotment WHERE 
                        allotment_date=:allotment_date AND
                        hotel_fk=:hotelfk AND 
                        room_fk=:roomfk AND 
                        to_fk=:to_fk";

                $query = $con->prepare($sql);
                $query->execute(array(":allotment_date" => $this_date, ":hotelfk" => $hotelfk,
                    ":roomfk" => $roomid, ":to_fk" => $tofk));

                if (!$rw = $query->fetch(PDO::FETCH_ASSOC)) {
                    $sql = "INSERT INTO tblinventory_allotment
                            (allotment_date,created_on,created_by,deleted) 
                            VALUES (:allotment_date, NOW(), :created_by, 0)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":allotment_date" => $this_date,
                                          ":created_by"=>$_SESSION["solis_userid"] ));
                    $id = $con->lastInsertId();
                }
                else
                {
                    $id = $rw["id"];
                }
                
                //=============================================

                $sql = "UPDATE tblinventory_allotment SET 
                        hotel_fk=:hotelfk,
                        room_fk=:roomfk,
                        to_fk=:to_fk,
                        release_type=:release_type,
                        specific_no_days=:specific_no_days, 
                        specific_date=:specific_date,
                        comment=:comment, 
                        units=:units
                        WHERE id=:id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id,
                    ":hotelfk" => $hotelfk,
                    ":roomfk" => $roomid,
                    ":to_fk" => $tofk,
                    ":release_type" => $release_type,
                    ":specific_no_days" => $specific_no_days,
                    ":specific_date" => $specific_date,
                    ":comment" => $comment,
                    ":units" => $units));
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
?>
