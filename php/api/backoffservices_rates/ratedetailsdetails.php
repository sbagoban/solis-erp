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

if (!isset($_GET["idrates_fk"])) {
    throw new Exception("INVALID ID". $_GET["idrates_fk"]);
}

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$idrates_fk = $_GET["idrates_fk"];
$con = pdo_con();

$query_c = $con->prepare("SSELECT sri.id, sri.servicedatefrom, sri.servicedateto, src.serviceclosedstartdate, src.serviceclosedenddate 
FROM tblexcursion_services_rates_insertrates sri 
join tblexcursion_services_rates_insertclosedate src on sri.id = src.idrates_fk




WHERE idrates_fk = :idrates_fk");
$query_c->execute(array(":idservicesfk"=>$idservicesfk));

$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $rateServiceDateDetails[] = array(
            'id'                => $row['id'],
            'idservicesfk'      => $row['idservicesfk'],
            'servicedatefrom' => $row['servicedatefrom'],
            'servicedateto'  => $row['servicedateto']
        );
    }
    $myData = $rateServiceDateDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $rateServiceDateDetails[] = array(
        'id' => '-',
        'idservicesfk' => '-',
        'servicedatefrom' => '-',
        'servicedateto' => '-'
    );
    $myData = $rateServiceDateDetails;
    echo json_encode($myData);
}
