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
    $meal = trim($_POST["meal"]);
    $mealfullname = trim($_POST["mealfullname"]);
    $compulsory = trim($_POST["compulsory"]);
    $usedinsearchengine = trim($_POST["usedinsearchengine"]);
    $usedasboardbasis = trim($_POST["usedasboardbasis"]);
    $description = trim($_POST["description"]);

    $con = pdo_con();

    //check duplicates for mealname
    $sql = "SELECT * FROM tblmealplans WHERE meal = :meal AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":meal" => $meal, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE MEAL NAME!");
    }



    if ($id == "-1") {
        $sql = "INSERT INTO tblmealplans (meal,mealfullname,compulsory,usedinsearchengine,
                usedasboardbasis,description) 
                VALUES (:meal,:mealfullname,:compulsory,:usedinsearchengine,
                :usedasboardbasis,:description) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":meal" => $meal,
            ":mealfullname" => $mealfullname,
            ":compulsory" => $compulsory,
            ":usedinsearchengine" => $usedinsearchengine,
            ":usedasboardbasis" => $usedasboardbasis,
            ":description" => $description));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblmealplans SET meal=:meal, mealfullname=:mealfullname,
                compulsory=:compulsory,usedinsearchengine=:usedinsearchengine,
                usedasboardbasis=:usedasboardbasis,
                description=:description
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":meal" => $meal,
            ":mealfullname" => $mealfullname,
            ":compulsory" => $compulsory,
            ":id" => $id,
            ":usedinsearchengine" => $usedinsearchengine,
            ":usedasboardbasis" => $usedasboardbasis,
            ":description" => $description));
    }


    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
