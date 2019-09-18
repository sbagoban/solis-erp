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
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    require_once("../../connector/pdo_connect_main.php");
    
    $id = $_POST["id"];
    $countryfk = trim($_POST["countryfk"]);
    $servicetypefk = trim($_POST["servicetypefk"]);
    $supplierfk = trim($_POST["supplierfk"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product WHERE optioncode = :optioncode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblexcursion_services (countryfk, servicetypefk, supplierfk) 
                VALUES (:countryfk, :servicetypefk, :supplierfk)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":countryfk" => $countryfk, 
            ":servicetypefk" => $servicetypefk,
            ":supplierfk" => $supplierfk));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblexcursion_services SET 
                countryfk=:countryfk, 
                servicetypefk=:servicetypefk, 
                supplierfk=:supplierfk,
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":countryfk" => $countryfk, 
            ":servicetypefk" => $servicetypefk,
            ":supplierfk" => $supplierfk));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
