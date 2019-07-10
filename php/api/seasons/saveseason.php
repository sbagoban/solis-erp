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


    require_once("../../connector/pdo_connect_main.php");


    session_start();

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


    $id = $_POST["id"];
    $scode = trim($_POST["scode"]);
    $season = trim($_POST["season"]);
    
    //check duplicates for season code
    $sql = "SELECT * FROM tblseasons WHERE scode = :scode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":scode" => $scode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SEASON CODE!");
    }
    
    //check duplicates for season name
    $sql = "SELECT * FROM tblseasons WHERE season = :season AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":season" => $season, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SEASON NAME!");
    }


    if ($id == "-1") {
        $sql = "INSERT INTO tblseasons (season,scode) 
                VALUES (:season,:scode) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":season" => $season,":scode"=>$scode));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblseasons SET 
                season = :season,
                scode = :scode
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":season" => $season, ":scode" => $scode,
            ":id" => $id));
    }



    $con->commit();
    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
