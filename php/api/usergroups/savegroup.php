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
    $ugroup = trim($_POST["ugroup"]);
    $description = trim($_POST["description"]);
    $grpactiveyn = $_POST["grpactiveyn"];
    $grpcode = trim($_POST["grpcode"]);


    $con = pdo_con();

    //check duplicates for username
    $sql = "SELECT * FROM tblugroup WHERE ugroup = :ugroup AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":ugroup" => $ugroup, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE GROUP NAME  !");
    }

    //check duplicates for group code
    $sql = "SELECT * FROM tblugroup WHERE grpcode = :grpcode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":grpcode" => $grpcode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE GROUP CODE!");
    }




    if ($id == "-1") {
        $sql = "INSERT INTO tblugroup (ugroup,description,grpactiveyn,grpcode) 
                VALUES (:ugroup,:description,:grpactiveyn,:grpcode) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":ugroup" => $ugroup, ":description" => $description, ":grpactiveyn" => $grpactiveyn,
            ":grpcode" => $grpcode));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblugroup SET ugroup=:ugroup, description=:description,
                grpactiveyn=:grpactiveyn,grpcode=:grpcode
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":ugroup" => $ugroup, ":description" => $description, ":grpactiveyn" => $grpactiveyn,
            ":grpcode" => $grpcode,":id"=>$id));
    }


    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
