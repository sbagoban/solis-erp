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
    $pvalue = trim($_POST["pvalue"]);

    $con = pdo_con();
    $sql = "UPDATE tblsysparams SET pvalue=:pvalue WHERE id=:id ";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(
        ":id" => $id,
        ":pvalue" => $pvalue));

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
