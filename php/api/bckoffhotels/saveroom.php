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
    $hotelfk = trim($_POST["hotelfk"]);
    $roomname = trim($_POST["roomname"]);
    $description = trim($_POST["description"]);
    $numbedrooms = trim($_POST["numbedrooms"]);
    
    if(!is_numeric($numbedrooms))
    {
        $numbedrooms = null;
    }

    $con = pdo_con();

    //check duplicates for roomname
    $sql = "SELECT * FROM tblhotel_rooms WHERE roomname = :roomname AND hotelfk=:hotelfk AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":roomname" => $roomname, ":hotelfk" => $hotelfk, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE ROOM NAME!");
    }
    if ($id == "-1") {
        $sql = "INSERT INTO tblhotel_rooms 
                (hotelfk,roomname,description,numbedrooms) 
                VALUES (:hotelfk,:roomname,:description,:numbedrooms) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotelfk" => $hotelfk,
            ":roomname" => $roomname,
            ":description" => $description,
            ":numbedrooms" => $numbedrooms));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblhotel_rooms SET hotelfk=:hotelfk, 
                roomname=:roomname,
                description=:description,numbedrooms=:numbedrooms
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":hotelfk" => $hotelfk,
            ":roomname" => $roomname,
            ":description" => $description,
            ":numbedrooms" => $numbedrooms));
    }


    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
