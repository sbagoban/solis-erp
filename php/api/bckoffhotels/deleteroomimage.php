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
    //================================================================================


    session_start();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }


    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["id"])) {
        throw new Exception("INVALID ID");
    }

    $id = $_POST["id"];
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");
    
    $con = pdo_con();
    
    $absolute_server_path = utils_getsysparams($con, "HOTEL_ROOM", "PHOTO", "ABSOLUTE_PATH");
    if (is_null($absolute_server_path)) {
        throw new Exception("NO HOTEL PHOTO ABSOLUTE PATH DEFINED!");
    }

    $image_name = "";

    //unlink file
    $sql = "SELECT * FROM tblhotel_room_images WHERE id=:id";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $image_name = $rw["image_name"];
    }

    $path = $absolute_server_path . $image_name;
    if (file_exists($path)) {
        if (!unlink($path)) {
            throw new Exception("Error Deleting Photo!");
        }
    }

    
    $stmt = $con->prepare("DELETE FROM tblhotel_room_images WHERE id=:id");
    $stmt->execute(array(":id" => $id));

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
