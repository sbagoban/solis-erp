<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if (!isset($_GET["t"])) {
    die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) {
    die("INVALID TOKEN");
}

if (!isset($_GET["marketfk"])) {
    throw new Exception("INVALID ID". $_GET["marketfk"]);
}

$marketfk = $_GET["marketfk"];

require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT tmc.id, tmc.marketfk, tmc.countryfk, tc.country_name 
FROM tblmarket_countries tmc 
join tblcountries tc on tmc.countryfk = tc.id where tmc.marketfk = :marketfk  
ORDER BY `tmc`.`marketfk` ASC");
$query_c->execute(array(":marketfk"=>$marketfk));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $countries[] = array(
            'id' => $row['id'],
            'country_name' => $row['country_name']
        );
    }
    $myData = $countries;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>