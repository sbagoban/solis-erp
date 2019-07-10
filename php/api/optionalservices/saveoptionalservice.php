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
    $optname = trim($_POST["optname"]);
    $optnotes = trim($_POST["optnotes"]);
    $usedwithactivities = trim($_POST["usedwithactivities"]);
    $usedwithtransfer = trim($_POST["usedwithtransfer"]);
    $useascheckbox = trim($_POST["useascheckbox"]);
    $appliedfor = trim($_POST["appliedfor"]);

    $con = pdo_con();

    //check duplicates for optname
    $sql = "SELECT * FROM tbloptionalservices WHERE optname = :optname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":optname" => $optname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE OPTION NAME!");
    }



    if ($id == "-1") {
        $sql = "INSERT INTO tbloptionalservices (optname,optnotes,usedwithactivities,
                usedwithtransfer,
                useascheckbox,appliedfor) 
                VALUES (:optname,:optnotes,:usedwithactivities,:usedwithtransfer,
                :useascheckbox,:appliedfor) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":optname" => $optname,
            ":optnotes" => $optnotes,
            ":usedwithactivities" => $usedwithactivities,
            ":usedwithtransfer" => $usedwithtransfer,
            ":useascheckbox" => $useascheckbox,
            ":appliedfor" => $appliedfor));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tbloptionalservices SET optname=:optname, optnotes=:optnotes,
                usedwithactivities=:usedwithactivities,usedwithtransfer=:usedwithtransfer,
                useascheckbox=:useascheckbox,
                appliedfor=:appliedfor
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":optname" => $optname,
            ":optnotes" => $optnotes,
            ":usedwithactivities" => $usedwithactivities,
            ":id" => $id,
            ":usedwithtransfer" => $usedwithtransfer,
            ":useascheckbox" => $useascheckbox,
            ":appliedfor" => $appliedfor));
    }


    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
