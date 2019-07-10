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

    $id = $_POST["id"];
    $hoteltype = trim($_POST["hoteltype"]);
    $isdefault = trim($_POST["isdefault"]);
    
    

    $con = pdo_con();
    $con->beginTransaction();

    //check duplicates for hoteltype
    $sql = "SELECT * FROM tblhoteltype WHERE hoteltype = :hoteltype AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hoteltype" => $hoteltype, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE HOTEL TYPE!");
    }

    
    if($isdefault == "Y")
    {
        //set other hoteltypes to non-default
        $sql = " UPDATE tblhoteltype SET isdefault='N' WHERE isdefault='Y' ";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblhoteltype (hoteltype,isdefault) 
                VALUES (:hoteltype,:isdefault) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hoteltype" => $hoteltype, 
            ":isdefault" => $isdefault));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblhoteltype SET hoteltype=:hoteltype, isdefault=:isdefault
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":hoteltype" => $hoteltype, 
            ":isdefault" => $isdefault));
    }

    
    $con->commit();
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
