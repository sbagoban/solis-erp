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

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $id = $_POST["id"];
    $checkin = utils_DMY_YMD(trim($_POST["checkin"]));
    $checkout = utils_DMY_YMD(trim($_POST["checkout"]));
    $groupfk = trim($_POST["groupfk"]);
    $seasonfk = trim($_POST["seasonfk"]);
    $hotelfk = trim($_POST["hotelfk"]);
    $chkCreateforGroup = trim($_POST["chkCreateforGroup"]);
    $active = $_POST["active"];


    $con = pdo_con();
    $con->beginTransaction();

    
    $sql = "SELECT * FROM tbldateperiods
                WHERE hotelfk=:hotelfk                 
                AND active=1
                AND 
                (:checkin <= checkout AND :checkout >= checkin)
                AND id<>:id";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":checkin" => $checkin, ":checkout" => $checkout, ":id" => $id,
        ":hotelfk" => $hotelfk));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("OVERLAPPING DATES!");
    }


    if ($id == "-1") {

        $sql = "INSERT INTO tbldateperiods (checkin,checkout,hotelfk,seasonfk,active) 
                VALUES (:checkin,:checkout,:hotelfk,:seasonfk,1) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":checkin" => $checkin,
            ":checkout" => $checkout,
            ":hotelfk" => $hotelfk,
            ":seasonfk" => $seasonfk));
        $id = $con->lastInsertId();
        
        if ($groupfk != "" && $groupfk != "-1" && $chkCreateforGroup == "1") {
            
            //insert a record for the other hotels of the same group

            //grouped hotels
            //get all hotels for that group
            $sql = "SELECT * FROM tblhotels WHERE (groupfk=:groupfk AND active=1 AND id<>:id)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":groupfk" => $groupfk, ":id" => $hotelfk));
            while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    
                $hotelid = $rw["id"];
                $hotel_name = $rw["hotelname"];
                
                //first check that there is no overlap for that hotel
                $sql = "SELECT * FROM tbldateperiods
                        WHERE hotelfk=:hotelfk  
                        AND active=1
                        AND 
                        (:checkin <= checkout AND :checkout >= checkin)";
                $stmtchk = $con->prepare($sql);
                $stmtchk->execute(array(":checkin" => $checkin, ":checkout" => $checkout,
                    ":hotelfk" => $hotelid));
                if ($rwchk = $stmtchk->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception("OVERLAPPING DATES FOR HOTEL $hotel_name! SAVE DENIED.");
                }
                
                $sql = "INSERT INTO tbldateperiods (checkin,checkout,hotelfk,seasonfk,active) 
                        VALUES (:checkin,:checkout,:hotelfk,:seasonfk,1) ";

                $query = $con->prepare($sql);
                $query->execute(array(":checkin" => $checkin,
                    ":checkout" => $checkout,
                    ":hotelfk" => $hotelid,                    
                    ":seasonfk" => $seasonfk));
            }
        }

        $id = $con->lastInsertId();
    } else {

        $sql = "UPDATE tbldateperiods SET checkin=:checkin,checkout=:checkout,
                seasonfk=:seasonfk,active=:active WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":checkin" => $checkin,
            ":checkout" => $checkout,
            ":id" => $id,
            ":seasonfk" => $seasonfk,
            ":active" => $active,));
    }


    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
