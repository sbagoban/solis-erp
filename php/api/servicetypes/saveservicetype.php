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
    $servicecode = trim($_POST["servicecode"]);
    $servicetype = trim($_POST["servicetype"]);
    $isaccomodation = trim($_POST["isaccomodation"]);
    $isexcursion = trim($_POST["isexcursion"]);
    $istransfer = trim($_POST["istransfer"]);

    //check duplicates for service code
    $sql = "SELECT * FROM tblservicetype WHERE servicecode = :servicecode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":servicecode" => $servicecode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICE CODE!");
    }

    //check duplicates for service name
    $sql = "SELECT * FROM tblservicetype WHERE servicetype = :servicetype AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":servicetype" => $servicetype, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICE TYPE!");
    }



    if ($id == "-1") {
        $sql = "INSERT INTO tblservicetype (servicecode,servicetype,
                isaccomodation,isexcursion,istransfer) 
                VALUES (:servicecode,:servicetype,
                :isaccomodation,:isexcursion,:istransfer) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":servicecode" => $servicecode,
            ":servicetype" => $servicetype,
            ":isaccomodation" => $isaccomodation,
            ":isexcursion" => $isexcursion,
            ":istransfer" => $istransfer));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblservicetype SET 
                servicecode = :servicecode,
                servicetype = :servicetype,
                isaccomodation = :isaccomodation,
                isexcursion = :isexcursion,
                istransfer = :istransfer
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":servicecode" => $servicecode,
            ":servicetype" => $servicetype,
            ":isaccomodation" => $isaccomodation,
            ":isexcursion" => $isexcursion,
            ":id" => $id,
            ":istransfer" => $istransfer));
    }



    $con->commit();
    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
