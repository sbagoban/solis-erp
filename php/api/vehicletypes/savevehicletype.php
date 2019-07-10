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


    require_once("../../connector/pdo_connect_main.php");


    session_start();

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
    $vehname = trim($_POST["vehname"]);
    $description = trim($_POST["description"]);
    $vehtype = trim($_POST["vehtype"]);
    $perway = trim($_POST["perway"]);
    $perseat = trim($_POST["perseat"]);
    $activities = trim($_POST["activities"]);
    $allowed_surcharge = trim($_POST["allowed_surcharge"]);
    $default_accomodation_transfer = trim($_POST["default_accomodation_transfer"]);
    $max_capacity = trim($_POST["max_capacity"]);
    $adult_count = trim($_POST["adult_count"]);
    $children_count = trim($_POST["children_count"]);

    $arr_luggages = array();
    $luggages = trim($_POST["luggages"]);
    if ($luggages != "") {
        $arr_luggages = json_decode($luggages, true);
    }




    //check duplicates for vehname
    $sql = "SELECT * FROM tblvehicletype WHERE vehname = :vehname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":vehname" => $vehname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE VEHICLE NAME!");
    }



    if ($id == "-1") {
        $sql = "INSERT INTO tblvehicletype (vehname,description,vehtype,perway,perseat,activities,
                allowed_surcharge,default_accomodation_transfer,max_capacity,adult_count,
                children_count) 
                VALUES (:vehname,:description,:vehtype,:perway,:perseat,:activities,
                :allowed_surcharge,:default_accomodation_transfer,:max_capacity,:adult_count,
                :children_count) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":vehname" => $vehname,
            ":description" => $description,
            ":vehtype" => $vehtype,
            ":perway" => $perway,
            ":perseat" => $perseat,
            ":activities" => $activities,
            ":allowed_surcharge" => $allowed_surcharge,
            ":default_accomodation_transfer" => $default_accomodation_transfer,
            ":max_capacity" => $max_capacity,
            ":adult_count" => $adult_count,
            ":children_count" => $children_count));

        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblvehicletype SET 
                vehname = :vehname,
                description = :description,
                vehtype = :vehtype,
                perway = :perway,
                perseat = :perseat,
                activities = :activities,
                allowed_surcharge = :allowed_surcharge,
                default_accomodation_transfer = :default_accomodation_transfer,
                max_capacity = :max_capacity,
                adult_count = :adult_count,
                children_count = :children_count
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":vehname" => $vehname,
            ":description" => $description,
            ":vehtype" => $vehtype,
            ":perway" => $perway,
            ":perseat" => $perseat,
            ":activities" => $activities,
            ":allowed_surcharge" => $allowed_surcharge,
            ":default_accomodation_transfer" => $default_accomodation_transfer,
            ":max_capacity" => $max_capacity,
            ":adult_count" => $adult_count,
            ":children_count" => $children_count));
    }

    //save luggages

    for ($i = 0; $i < count($arr_luggages); $i++) {
        $luggageid = $arr_luggages[$i]["rwid"];
        $suitcase = $arr_luggages[$i]["cells"]["suitcase"];
        $handbag = $arr_luggages[$i]["cells"]["handbag"];
        $golfbag = $arr_luggages[$i]["cells"]["golfbag"];
        $action = $arr_luggages[$i]["cells"]["action"];

        if ($action == "DELETE") {
            $sql = "DELETE FROM tblvehicletype_luggage WHERE id=:id";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $luggageid));
        } else if ($action == "ADD") {
            $sql = "INSERT INTO tblvehicletype_luggage (vehtypefk,suitcase,handbag,golfbag)
                        VALUES (:vehtypefk,:suitcase,:handbag,:golfbag)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":vehtypefk" => $id,
                ":suitcase" => $suitcase,
                ":handbag" => $handbag,
                ":golfbag" => $golfbag));
        } else {
            $sql = "UPDATE tblvehicletype_luggage
                        SET                                                
                        suitcase = :suitcase,
                        handbag = :handbag,
                        golfbag = :golfbag
                        WHERE id = :id";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ":suitcase" => $suitcase,
                ":handbag" => $handbag,
                ":golfbag" => $golfbag,
                ":id" => $luggageid));
        }
    }


    $con->commit();
    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
