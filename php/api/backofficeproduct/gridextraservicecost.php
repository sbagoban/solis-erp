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

if (!isset($_GET["id_product_services_cost"])) {
    throw new Exception("INVALID ID". $_GET["id_product_services_cost"]);
}

$id_product_services_cost = $_GET["id_product_services_cost"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT * FROM product_services_extra_cost WHERE id_product_services_cost = :id_product_services_cost AND active=1");
$query_c->execute(array(":id_product_services_cost"=>$id_product_services_cost));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $extraservicesExtraCost[] = array(
            'id_product_services_extra_cost' => $row['id_product_services_extra_cost'],
            'id_product_services_cost' => $row['id_product_services_cost'],
            'id_product_services'         => $row['id_product_services'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'allDate' => $row['valid_from'] . ' / ' . $row['valid_to'],
            'charges'    => $row['charges'],
            'ps_adult_cost'    => $row['ps_adult_cost'],
            'ps_teen_cost'    => $row['ps_teen_cost'],
            'ps_child_cost' => $row['ps_child_cost'],
            'ps_infant_cost'    => $row['ps_infant_cost'],
            'id_currency'    => $row['id_currency'],
            'currency_code' => $row['currency'],            
            'id_product_services_extra' => $row['id_product_services_extra'],
            'extra_name' => $row['extra_name']
        );
    }    $myData = $extraservicesExtraCost;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $extraservicesExtraCost[] = array(
        'id_product_services_extra_cost' => '-',
        'id_product_services_cost' => '-',
        'id_product_services' => '-',
        'valid_from' => '-',
        'valid_to'     => '-',
        'allDate' => '-',
        'charges' => '-',
        'ps_adult_cost' => '-',
        'ps_teen_cost'     => '-',
        'ps_child_cost' => '-',
        'ps_infant_cost' => '-',
        'id_currency'     => '-',
        'currency_code' => '-',
        'id_product_services_extra' => '',
        'extra_name' => ''
    );
    $myData = $extraservicesExtraCost;
    echo json_encode($myData);
}
