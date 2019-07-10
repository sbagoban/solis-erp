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
    $bankfk = trim($_POST["bankfk"]);
    $accountno = trim($_POST["accountno"]);
    $iban = trim($_POST["iban"]);
    $currencyfk = trim($_POST["currencyfk"]);
    $companyfk = trim($_POST["companyfk"]);


    $con = pdo_con();

    //check account duplicate for that company and bank

    $sql = "SELECT * FROM tblcompanyaccounts WHERE accountno = :accountno 
            AND bankfk=:bankfk AND companyfk=:companyfk AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":accountno" => $accountno, ":bankfk" => $bankfk, 
                         ":id" => $id, ":companyfk" => $companyfk));
    
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE ACCOUNT NAME FOR THAT BANK AND COMPANY COMBINATION!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblcompanyaccounts (bankfk,accountno,iban,
                currencyfk,companyfk) 
                VALUES (:bankfk,:accountno,:iban,
                :currencyfk,:companyfk) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":bankfk" => $bankfk,
            ":accountno" => $accountno,
            ":iban" => $iban,
            ":currencyfk" => $currencyfk,
            ":companyfk" => $companyfk));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblcompanyaccounts SET 
                bankfk=:bankfk, accountno=:accountno,
                iban=:iban,
                currencyfk=:currencyfk,
                companyfk=:companyfk
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":bankfk" => $bankfk,
            ":accountno" => $accountno,
            ":iban" => $iban,
            ":currencyfk" => $currencyfk,
            ":companyfk" => $companyfk));
    }



    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
