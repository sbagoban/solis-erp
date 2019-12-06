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

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sqlLanguage = $con->prepare("SELECT *
											FROM lang
											WHERE active = 1");
$sqlLanguage->execute();
$row_count_language = $sqlLanguage->rowCount();

if ($row_count_language > 0) {
    while ($row = $sqlLanguage->fetch(PDO::FETCH_ASSOC)) {
        $languageDetails[] = array(
            'id_language'	=> $row['id_language'],
            'language_name'	=> $row['language_name']
        );
    }    $myData = $languageDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $languageDetails[] = array(
            'id_language'	=> '-',
            'language_name'	=> '-'
        );
    $myData = $languageDetails;
    echo json_encode($myData);
}
