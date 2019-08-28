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

if (!isset($_GET["idservicesfk"])) {
    throw new Exception("INVALID ID". $_GET["idservicesfk"]);
}

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$idservicesfk = $_GET["idservicesfk"];
$con = pdo_con();

$query_c = $con->prepare("SELECT * FROM tblexcursion_services_rates_insertclosedate WHERE idservicesfk = :idservicesfk");
$query_c->execute(array(":idservicesfk"=>$idservicesfk));

$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $rateCloseDateDetails[] = array(
            'id'                => $row['id'],
            'idservicesfk'      => $row['idservicesfk'],
            'serviceclosedstartdate' => $row['serviceclosedstartdate'],
            'serviceclosedenddate'  => $row['serviceclosedenddate']
        );
    }
    $myData = $rateCloseDateDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $rateCloseDateDetails[] = array(
        'id' => '-',
        'idservicesfk' => '-',
        'serviceclosedstartdate' => '-',
        'serviceclosedenddate' => '-'
    );
    $myData = $rateCloseDateDetails;
    echo json_encode($myData);
}
