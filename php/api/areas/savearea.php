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
    $areaname = trim($_POST["areaname"]);
    $countryfk = trim($_POST["countryfk"]);    
    $lat = trim($_POST["lat"]);
    $lon = trim($_POST["lon"]);
    


    $con = pdo_con();

    //check duplicates for area name
    $sql = "SELECT * FROM tblareas WHERE areaname = :areaname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":areaname" => $areaname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE AREA NAME!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblareas (areaname,countryfk,lat,lon) 
                VALUES (:areaname,:countryfk,:lat,:lon) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":areaname" => $areaname, 
            ":countryfk" => $countryfk,             
            ":lat" => $lat,
            ":lon" => $lon));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblareas SET areaname=:areaname, countryfk=:countryfk,                
                lat=:lat,lon=:lon
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":areaname" => $areaname, 
            ":countryfk" => $countryfk,             
            ":lat" => $lat,
            ":lon" => $lon));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
