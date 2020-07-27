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

if (!isset($_GET["id_product_service_cost"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service_cost"]);
}

$id_product_service_cost = $_GET["id_product_service_cost"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT * FROM product_service_paxbreak_cost WHERE id_product_service_cost = :id_product_service_cost AND active=1");
$query_c->execute(array(":id_product_service_cost"=>$id_product_service_cost));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $paxbreaks[] = array(
            'id_product_service_pax_break_cost'         => $row['id_product_service_pax_break_cost'],
            'id_product_service_cost'    => $row['id_product_service_cost'],
            'id_product_service'    => $row['id_product_service'],
            'pax_to'    => $row['pax_to'],
            'pax_from'    => $row['pax_from'],
            'charge'    => $row['charge'],
            'ps_adult_cost_break'    => $row['ps_adult_cost_break'],
            'ps_teen_cost_break'    => $row['ps_teen_cost_break'],
            'ps_child_cost_break'    => $row['ps_child_cost_break'],
            'ps_infant_cost_break'    => $row['ps_infant_cost_break'],
        );
    }    $myData = $paxbreaks;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $paxbreaks[] = array(
        'id_product_service_pax_break_cost' => '-',
        'id_product_service_cost'    => '-',
        'id_product_service'    => '-',
        'pax_to'    => '-',
        'pax_from'    => '-',
        'charge'    => '-',
        'ps_adult_cost_break'    => '-',
        'ps_teen_cost_break'    => '-',
        'ps_child_cost_break'    => '-',
        'ps_infant_cost_break'    => '-'
    );
    $myData = $paxbreaks;
    echo json_encode($myData);
}
