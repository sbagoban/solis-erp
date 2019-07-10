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
    $grpname = trim($_POST["grpname"]);
    $address = trim($_POST["address"]);    
    $email = trim($_POST["email"]);
    $fax = trim($_POST["fax"]);
    $phone = trim($_POST["phone"]);

    $con = pdo_con();

    //check duplicates for area name
    $sql = "SELECT * FROM tblgrouphotels WHERE grpname = :grpname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":grpname" => $grpname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE HOTEL GROUP NAME!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblgrouphotels (grpname,address,email,fax,phone) 
                VALUES (:grpname,:address,:email,:fax,:phone) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":grpname" => $grpname, 
            ":address" => $address,             
            ":email" => $email,
            ":fax" => $fax,
            ":phone" => $phone));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblgrouphotels SET grpname=:grpname, address=:address,                
                email=:email,fax=:fax,phone=:phone
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":grpname" => $grpname, 
            ":address" => $address,             
            ":email" => $email,
            ":fax" => $fax,
            ":id"=>$id,
            ":phone" => $phone));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
