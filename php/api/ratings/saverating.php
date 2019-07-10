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
    $num_stars = trim($_POST["num_stars"]);
    $description = trim($_POST["description"]);    
    


    $con = pdo_con();

    //check duplicates for numstars
    $sql = "SELECT * FROM tblrating WHERE num_stars = :num_stars AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":num_stars" => $num_stars, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE RATING!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblrating (num_stars,description) 
                VALUES (:num_stars,:description) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":num_stars" => $num_stars, 
            ":description" => $description));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblrating SET num_stars=:num_stars, description=:description
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":num_stars" => $num_stars, 
            ":description" => $description));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
