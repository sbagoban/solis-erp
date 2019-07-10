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

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    $gender = "M";
    $userimage = "avatar_male.png";

    $sql = "SELECT * FROM tbluser WHERE id=:id";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $_SESSION["solis_userid"]));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $gender = $rw["gender"];
        if ($gender == "F") {
            $userimage = "avatar_female.png";
        }
    }



    $sql = "UPDATE tbluser SET 
            user_image = :user_image
            WHERE id=:id ";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":user_image" => $userimage,
        ":id" => $_SESSION["solis_userid"]));
    
    $_SESSION["solis_userimage"] = $userimage; 


    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
