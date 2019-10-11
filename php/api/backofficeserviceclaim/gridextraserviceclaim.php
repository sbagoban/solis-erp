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

if (!isset($_GET["id_product_service_claim"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service_claim"]);
}

$id_product_service_claim = $_GET["id_product_service_claim"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT PSEC.id_product_service_extra_claim, PSEC.id_product_service_cost, PSEC.charge, PSEC.ps_adult_claim, PSEC.ps_teen_claim,
PSEC.ps_child_claim, PSEC.ps_infant_claim, PS.extra_name, PSEC.id_product_service_claim
FROM product_service_extra_claim PSEC
JOIN product_service_extra_cost PS on PSEC.id_product_service_extra_cost = PS.id_product_service_extra_cost
WHERE PSEC.id_product_service_claim = :id_product_service_claim
AND PSEC.active = 1");
$query_c->execute(array(":id_product_service_claim"=>$id_product_service_claim));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $extraserviceExtraClaim[] = array(
            'id_product_service_extra_claim' => $row['id_product_service_extra_claim'],
            'id_product_service_cost' => $row['id_product_service_cost'],
            'charge'         => $row['charge'],
            'ps_adult_claim'         => $row['ps_adult_claim'],
            'ps_teen_claim'         => $row['ps_teen_claim'],
            'ps_child_claim'         => $row['ps_child_claim'],
            'ps_infant_claim'         => $row['ps_infant_claim'],
            'extra_name'         => $row['extra_name'],
            'id_product_service_claim'         => $row['id_product_service_claim']
        );
    }    $myData = $extraserviceExtraClaim;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $extraserviceExtraClaim[] = array(
        'id_product_service_extra_claim' => '-',
        'id_product_service_cost' => '-',
        'charge' => '-',
        'ps_adult_claim' => '-',
        'ps_teen_claim'     => '-',
        'ps_child_claim' => '-',
        'ps_infant_claim' => '-',
        'extra_name' => '-',
        'id_product_service_claim' => '-'
    );
    $myData = $extraserviceExtraClaim;
    echo json_encode($myData);
}
