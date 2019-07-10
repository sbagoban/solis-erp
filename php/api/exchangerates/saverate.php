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
    require_once("../../utils/utilities.php");

    $id = $_POST["id"];
    $ratefk = trim($_POST["ratefk"]);
    $dtfrom = utils_DMY_YMD(trim($_POST["dtfrom"]));
    $dtto = utils_DMY_YMD(trim($_POST["dtto"]));
    $exchange_rate = trim($_POST["exchange_rate"]);
    
    $con = pdo_con();

    //check overlaps?
    $sql = "SELECT * FROM tblexchangerates_periods
                WHERE ratefk=:ratefk                 
                AND (:dtfrom <= dtto AND :dtto >= dtfrom)
                AND id<>:id";
    
    
    
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":dtfrom" =>$dtfrom, ":dtto"=>$dtto, ":id" => $id, ":ratefk"=>$ratefk));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("OVERLAPPING DATES!");
    }



    if ($id == "-1") {
        $sql = "INSERT INTO tblexchangerates_periods (ratefk,dtfrom,dtto,exchange_rate) 
                VALUES (:ratefk,:dtfrom,:dtto,:exchange_rate) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":ratefk" => $ratefk,
            ":dtfrom" => $dtfrom,
            ":dtto" => $dtto,
            ":exchange_rate" => $exchange_rate));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblexchangerates_periods SET dtfrom=:dtfrom, dtto=:dtto,
                exchange_rate=:exchange_rate
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":dtfrom" => $dtfrom,
            ":dtto" => $dtto,
            ":exchange_rate" => $exchange_rate,
            ":id" => $id));
    }


    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
