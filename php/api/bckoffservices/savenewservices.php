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
    
    if (!isset($_GET["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    require_once("../../connector/pdo_connect_main.php");
    
    $id = $_POST["id"];
    $countryfk = trim($_POST["countryfk"]);
    $servicetypefk = trim($_POST["servicetypefk"]);
    $supplierfk = trim($_POST["supplierfk"]);
    $optioncode = trim($_POST["optioncode"]);
    $descriptionservice = trim($_POST["descriptionservice"]);
    $comments = trim($_POST["comments"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM tblexcursion_services WHERE optioncode = :optioncode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":optioncode" => $optioncode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblexcursion_services (countryfk, servicetypefk, supplierfk, optioncode, descriptionservice, comments) 
                VALUES (:countryfk, :servicetypefk, :supplierfk, :optioncode, :descriptionservice, :comments)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":countryfk" => $countryfk, 
            ":servicetypefk" => $servicetypefk,
            ":supplierfk" => $supplierfk,
            ":optioncode" => $optioncode,
            ":descriptionservice" => $descriptionservice,
            ":comments" => $comments));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblexcursion_services SET 
                countryfk=:countryfk, 
                servicetypefk=:servicetypefk, 
                supplierfk=:supplierfk, 
                optioncode=:optioncode, 
                descriptionservice=:descriptionservice, 
                comments=:comments,
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":countryfk" => $countryfk, 
            ":servicetypefk" => $servicetypefk,
            ":supplierfk" => $supplierfk,
            ":optioncode" => $optioncode,
            ":descriptionservice" => $descriptionservice,
            ":comments" => $comments));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
