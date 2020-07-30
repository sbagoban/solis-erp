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

$query_c = $con->prepare("SELECT * FROM product_service_paxbreak WHERE id_product_service_claim = :id_product_service_claim AND active=1");
$query_c->execute(array(":id_product_service_claim"=>$id_product_service_claim));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $paxbreaks[] = array(
            'id_product_service_pax_break_claim'         => $row['id_product_service_pax_break_claim'],
            'id_product_service_claim'    => $row['id_product_service_claim'],
            'id_product_service_cost'    => $row['id_product_service_cost'],
            'id_product_service'    => $row['id_product_service'],
            'pax_to'    => $row['pax_to'],
            'pax_from'    => $row['pax_from'],
            'charge'    => $row['charge'],
            'ps_adult_claim_break'    => $row['ps_adult_claim_break'],
            'ps_teen_claim_break'    => $row['ps_teen_claim_break'],
            'ps_child_claim_break'    => $row['ps_child_claim_break'],
            'ps_infant_claim_break'    => $row['ps_infant_claim_break'],
            'ps_infant_claim_rollover'    => $row['ps_infant_claim_rollover'],
            'ps_child_claim_rollover'    => $row['ps_child_claim_rollover'],
            'ps_teen_claim_rollover'    => $row['ps_teen_claim_rollover'],
            'ps_adult_claim_rollover'    => $row['ps_adult_claim_rollover'],
            'rollover_value'    => $row['rollover_value'],
            'rollover_type'    => $row['rollover_type']
        );
    }    $myData = $paxbreaks;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $paxbreaks[] = array(
        'id_product_service_pax_break_claim' => '-',
        'id_product_service_claim'    => '-',
        'id_product_service_cost'    => '-',
        'id_product_service'    => '-',
        'pax_to'    => '-',
        'pax_from'    => '-',
        'charge'    => '-',
        'ps_adult_claim_break'    => '-',
        'ps_teen_claim_break'    => '-',
        'ps_child_claim_break'    => '-',
        'ps_infant_claim_break'    => '-',
        'ps_infant_claim_rollover'    => '-',
        'ps_child_claim_rollover'    => '-',
        'ps_teen_claim_rollover'    => '-',
        'ps_adult_claim_rollover'    => '-',
        'rollover_value'    => '-',
        'rollover_type'    => '-'
    );
    $myData = $paxbreaks;
    echo json_encode($myData);
}
