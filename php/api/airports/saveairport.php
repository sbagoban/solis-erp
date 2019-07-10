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
    $airportname = trim($_POST["airportname"]);
    $countryfk = trim($_POST["countryfk"]);    
    $usedfortransferrates = trim($_POST["usedfortransferrates"]);
    


    $con = pdo_con();

    //check duplicates for airport name for that country
    $sql = "SELECT * FROM tblairports WHERE airportname = :airportname AND countryfk=:countryfk AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":airportname" => $airportname, ":countryfk"=>$countryfk, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE AIRPORT NAME FOR THAT COUNTRY!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblairports (airportname,countryfk,usedfortransferrates) 
                VALUES (:airportname,:countryfk,:usedfortransferrates) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":airportname" => $airportname, 
            ":countryfk" => $countryfk,             
            ":usedfortransferrates" => $usedfortransferrates));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblairports SET airportname=:airportname, countryfk=:countryfk,                
                usedfortransferrates=:usedfortransferrates
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":airportname" => $airportname, 
            ":countryfk" => $countryfk,             
            ":usedfortransferrates" => $usedfortransferrates));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
