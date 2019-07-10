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
    $companyname = trim($_POST["companyname"]);
    $countryfk = trim($_POST["countryfk"]);
    $defaultcurrencyfk = trim($_POST["defaultcurrencyfk"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $brn = trim($_POST["brn"]);
    $vatno = trim($_POST["vatno"]);
    $website = trim($_POST["website"]);
    $phone = trim($_POST["phone"]);
    $fax = trim($_POST["fax"]);
    $description = trim($_POST["description"]);
    $active = trim($_POST["active"]);


    $con = pdo_con();
   
    //check duplicates for companyname
    $sql = "SELECT * FROM tblcompanies WHERE companyname = :companyname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":companyname" => $companyname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE COMPANY NAME!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblcompanies (companyname,countryfk,defaultcurrencyfk,
                email,address,brn,vatno,website,phone,fax,description,active) 
                VALUES (:companyname,:countryfk,:defaultcurrencyfk,
                :email,:address,:brn,:vatno,:website,:phone,:fax,:description,:active) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":companyname" => $companyname,
            ":countryfk" => $countryfk,
            ":defaultcurrencyfk" => $defaultcurrencyfk,
            ":email" => $email,
            ":address" => $address,
            ":brn" => $brn,
            ":vatno" => $vatno,
            ":website" => $website,
            ":phone" => $phone,
            ":fax" => $fax,
            ":description" => $description,
            ":active" => $active));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblcompanies SET 
                companyname=:companyname, countryfk=:countryfk,
                defaultcurrencyfk=:defaultcurrencyfk,
                email=:email,
                address=:address,
                brn=:brn,vatno=:vatno,
                website=:website,phone=:phone,
                fax=:fax,description=:description,
                active=:active
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":companyname" => $companyname,
            ":countryfk" => $countryfk,
            ":defaultcurrencyfk" => $defaultcurrencyfk,
            ":email" => $email,
            ":address" => $address,
            ":brn" => $brn,
            ":vatno" => $vatno,
            ":website" => $website,
            ":phone" => $phone,
            ":fax" => $fax,
            ":description" => $description,
            ":active" => $active));
    }



    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
