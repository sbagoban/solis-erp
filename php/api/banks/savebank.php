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
    $bankname = trim($_POST["bankname"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $countryfk = trim($_POST["countryfk"]);    
    $swift = trim($_POST["swift"]);
    


    $con = pdo_con();

    //check duplicates for area name
    $sql = "SELECT * FROM tblbanks WHERE bankname = :bankname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":bankname" => $bankname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE BANK NAME!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblbanks (bankname,address,countryfk,city,swift) 
                VALUES (:bankname,:address,:countryfk,:city,:swift) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":bankname" => $bankname, 
            ":countryfk" => $countryfk,             
            ":address" => $address,
            ":city" => $city,
            ":swift" => $swift));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblbanks SET bankname=:bankname, 
                address=:address, countryfk=:countryfk,                
                city=:city,swift=:swift
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":bankname" => $bankname, 
            ":address" => $address, 
            ":countryfk" => $countryfk,             
            ":city" => $city,
            ":swift" => $swift));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
