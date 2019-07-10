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

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $con->beginTransaction();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }


    if (!isset($_POST["linkid"])) {
        throw new Exception("INVALID LINK ID");
    }

    if (!isset($_POST["spoids"])) {
        throw new Exception("INVALID SPO IDS");
    }


    $linkid = $_POST["linkid"];
    $spoids = $_POST["spoids"];

    $arrspoids = explode(",", $spoids);

    for ($i = 0; $i < count($arrspoids); $i++) {
        $spoid = $arrspoids[$i];
        $sql = "SELECT * FROM tblspecial_offer_link_spos
                        WHERE spofk=:spo_fk AND 
                        linkfk=:linkfk";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $spoid, ":linkfk" => $linkid));

        if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $sql = "INSERT INTO tblspecial_offer_link_spos 
                            (spofk, linkfk) 
                            VALUES 
                            (:spo_fk, :linkfk)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $spoid, ":linkfk" => $linkid));
        }
    }


    $con->commit();

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
