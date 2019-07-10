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
    
    $con = pdo_con();
    $con->beginTransaction();
    
    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    
    $id = $_POST["id"];
    $currency_code = trim($_POST["currency_code"]);
    $currency_name = trim($_POST["currency_name"]);
    $use_for_costprice = trim($_POST["use_for_costprice"]);
    $use_for_base_conversions = trim($_POST["use_for_base_conversions"]);
    
    

    //check duplicates for mealname
    $sql = "SELECT * FROM tblcurrency WHERE currency_code = :currency_code AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":currency_code" => $currency_code, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE CURRENCY CODE!");
    }
    
    if($use_for_costprice == "1")
    {
        $sql = "UPDATE tblcurrency SET use_for_costprice = 0 WHERE id <> :id";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));
    }
    
    if($use_for_base_conversions == "1")
    {
        $sql = "UPDATE tblcurrency SET use_for_base_conversions = 0 WHERE id <> :id";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));
    }
    


    if ($id == "-1") {
        $sql = "INSERT INTO tblcurrency (currency_code,currency_name,
                use_for_costprice,use_for_base_conversions) 
                VALUES (:currency_code,:currency_name,
                :use_for_costprice,:use_for_base_conversions) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":currency_code" => $currency_code,
            ":currency_name" => $currency_name,
            ":use_for_costprice"=>$use_for_costprice,
            ":use_for_base_conversions"=>$use_for_base_conversions));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblcurrency SET currency_code=:currency_code, 
                currency_name=:currency_name,
                use_for_costprice=:use_for_costprice,
                use_for_base_conversions=:use_for_base_conversions
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":currency_code" => $currency_code,
            ":currency_name" => $currency_name,
            ":use_for_costprice"=>$use_for_costprice,
            ":use_for_base_conversions"=>$use_for_base_conversions,
            ":id" => $id));
    }
    
    $con->commit();
    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
    
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
