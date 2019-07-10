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
    $market_name = trim($_POST["market_name"]);
    $market_description = trim($_POST["market_description"]);    
    $active = trim($_POST["active"]);
    $market_parent_fk = trim($_POST["market_parent_fk"]);
    if($market_parent_fk == "")
    {
        $market_parent_fk = null;
    }


    $con = pdo_con();

    //check duplicates for airport name for that country
    $sql = "SELECT * FROM tblmarkets WHERE market_name = :market_name AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":market_name" => $market_name, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE MARKET NAME!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblmarkets (market_name,market_description,active,market_parent_fk) 
                VALUES (:market_name,:market_description,:active, :market_parent_fk) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":market_name" => $market_name, 
            ":market_description" => $market_description,    
            ":market_parent_fk"=>$market_parent_fk,
            ":active" => $active));
        
        $id = $con->lastInsertId();
        
        
    } else {
        $sql = "UPDATE tblmarkets SET market_name=:market_name, 
                market_description=:market_description,                
                active=:active
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":market_name" => $market_name, 
            ":market_description" => $market_description,             
            ":active" => $active));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
