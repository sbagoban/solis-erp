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
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["t"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    require_once("../../connector/pdo_connect_main.php");
    
    $id = $_POST["id"];
    $locationservice = trim($_POST["locationservice"]);
    $servicetype = trim($_POST["servicetype"]);
    $supplier = trim($_POST["supplier"]);
    $optioncode = trim($_POST["optioncode"]);
    $descriptionservice = trim($_POST["descriptionservice"]);
    $comments = trim($_POST["comments"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM tblnewservices WHERE optioncode = :optioncode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":optioncode" => $optioncode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblnewservices (locationservice, servicetype, supplier, optioncode, descriptionservice, comments) 
                VALUES (:locationservice, :servicetype, :supplier, :optioncode, : descriptionservice, :comments)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":locationservice" => $locationservice, 
            ":servicetype" => $servicetype,
            ":supplier" => $supplier,
            ":optioncode" => $optioncode,
            ":descriptionservice" => $descriptionservice,
            ":comments" => $comments));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblnewservices SET 
                locationservice=:locationservice, 
                servicetype=:servicetype, 
                supplier=:supplier, 
                optioncode=:optioncode, 
                descriptionservice=:descriptionservice, 
                comments=:comments,
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":locationservice" => $locationservice, 
            ":servicetype" => $servicetype,
            ":supplier" => $supplier,
            ":optioncode" => $optioncode,
            ":descriptionservice" => $descriptionservice,
            ":comments" => $comments));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
