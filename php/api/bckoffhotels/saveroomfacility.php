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

    $roomfk = trim($_POST["roomid"]);
    $data = trim($_POST["data"]);

    $arrdata_details = json_decode($data, true);

    $id = $arrdata_details["id"];
    $facility = $arrdata_details["facility"];
    $description = $arrdata_details["description"];

    $con = pdo_con();

    //check duplicates for roomname
    $sql = "SELECT * FROM tblfacilities WHERE facility = :facility AND category='ROOM' AND
            deleted=0 AND id<>:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":facility" => $facility, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE FACILITY NAME!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblfacilities 
                (facility,description,deleted,ordering,category) 
                VALUES (:facility,:description,0,0,'ROOM') ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":facility" => $facility,
            ":description" => $description));
        
        $id = $con->lastInsertId();
        
    } else {
        $sql = "UPDATE tblfacilities SET facility=:facility,
                description=:description WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":facility" => $facility,
            ":description" => $description,
            ":id" => $id));
        
        
    }



    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    
}
?>
