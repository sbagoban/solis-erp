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
    $countrycode_2 = trim($_POST["countrycode_2"]);
    $countrycode_3 = trim($_POST["countrycode_3"]);
    $country_name = trim($_POST["country_name"]);
    $country_numeric = trim($_POST["country_numeric"]);
    $lat = trim($_POST["lat"]);
    $lon = trim($_POST["lon"]);
    $default_selected = trim($_POST["default_selected"]);
    $continent = trim($_POST["continent"]);
    $used_for_hotels = trim($_POST["used_for_hotels"]);


    $con = pdo_con();
    $con->beginTransaction();

    //check duplicates for country code 2,3, numeric
    $sql = "SELECT * FROM tblcountries WHERE countrycode_2 = :countrycode_2 AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":countrycode_2" => $countrycode_2, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE CODE ALPHA 2!");
    }

    $sql = "SELECT * FROM tblcountries WHERE countrycode_3 = :countrycode_3 AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":countrycode_3" => $countrycode_3, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE CODE ALPHA 3!");
    }
    
    $sql = "SELECT * FROM tblcountries WHERE country_numeric = :country_numeric AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":country_numeric" => $country_numeric, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE CODE NUMERIC!");
    }
    
    if($default_selected == "Y")
    {
        //set other countries to non-default
        $sql = " UPDATE tblcountries SET default_selected='N' WHERE default_selected='Y' ";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblcountries 
                (countrycode_2,countrycode_3,country_name,country_numeric,
                 lat,lon,default_selected,continent,used_for_hotels) 
                VALUES (:countrycode_2,:countrycode_3,:country_name,:country_numeric,
                :lat,:lon,:default_selected,:continent,:used_for_hotels) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":countrycode_2" => $countrycode_2, 
            ":countrycode_3" => $countrycode_3, 
            ":country_name" => $country_name,
            ":country_numeric" => $country_numeric,
            ":lat" => $lat,
            ":lon" => $lon,
            ":continent"=>$continent,
            ":default_selected" => $default_selected,
            ":used_for_hotels"=>$used_for_hotels));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblcountries SET countrycode_2=:countrycode_2, countrycode_3=:countrycode_3,
                country_name=:country_name,country_numeric=:country_numeric,
                lat=:lat,lon=:lon,default_selected=:default_selected,
                continent=:continent,used_for_hotels=:used_for_hotels
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":countrycode_2" => $countrycode_2, 
            ":countrycode_3" => $countrycode_3, 
            ":country_name" => $country_name,
            ":country_numeric" => $country_numeric,
            ":lat" => $lat,
            ":lon" => $lon,
            ":continent"=>$continent,
            ":used_for_hotels"=>$used_for_hotels,
            ":default_selected" => $default_selected));
    }

    
    $con->commit();
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
