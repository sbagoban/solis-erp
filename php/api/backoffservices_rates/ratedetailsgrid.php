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

$query_c = $con->prepare("
SELECT DISTINCT SRI.id, SRI.servicedatefrom, SRI.servicedateto, 
SRIC.serviceclosedstartdate, SRIC.serviceclosedenddate, 
SRC.country_id, SRC.ID, C.country_name, 
SRT.to_id, TR.toname, 
SRR.ratestype_id, 
TRC.ratecodes 
FROM tblexcursion_services_rates_insertrates SRI, 
tblexcursion_services_rates_insertclosedate SRIC, 
tblexcursion_services_rates_countries SRC, tblcountries C, 
tblexcursion_services_rates_to SRT, 
tbltouroperator TR,
tblexcursion_services_rates_ratestype SRR, 
tblratecodes TRC 
WHERE 
SRI.id = SRIC.idrates_fk 
AND SRI.id = SRC.idrates_fk 
AND SRC.country_id = C.id 
AND SRT.to_id = TR.id 
AND SRR.ratestype_id = TRC.id 
AND SRI.id = :idrates_fk");
$query_c->execute(array(":idrates_fk"=>$idrates_fk));

$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $rateServiceAllDetails[] = array(
            'id' => $row['id'],
            'servicedatefrom' => $row['servicedatefrom'],
            'servicedateto' => $row['servicedateto'],
            'serviceclosedstartdate'  => $row['serviceclosedstartdate'],
            'serviceclosedenddate'  => $row['serviceclosedenddate'],
            'country_id'  => $row['country_id'],
            'country_name'  => $row['country_name'],
            'to_id'  => $row['to_id'],
            'toname'  => $row['toname'],
            'ratestype_id'  => $row['ratestype_id'],
            'ratecodes'  => $row['ratecodes']
        );
    }
    $myData = $rateServiceAllDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $rateServiceAllDetails[] = array(
        'id' => '-',
        'servicedatefrom' => '-',
        'servicedateto' => '-',
        'serviceclosedstartdate' => '-',
        'serviceclosedenddate' => '-',
        'country_id' => '-',
        'country_name' => '-',
        'to_id'  => '-',
        'toname'  => '-',
        'ratestype_id' => '-',
        'ratecodes' => '-'
    );
    $myData = $rateServiceAllDetails;
    echo json_encode($myData);
}
