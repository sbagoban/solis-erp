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
if (!isset($_GET["id_product_service"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service"]);
}

$id_product_service = $_GET["id_product_service"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("
SELECT PRS.id_product_service_cost, PRS.id_product_service, PRS.valid_from, PRS.valid_to, PS.charge, 
PRS.ps_adult_cost, PRS.ps_teen_cost, PRS.ps_child_cost, PRS.ps_infant_cost, PRS.id_currency,
PS.service_name, TC.currency_code, PS.id_dept
FROM product_service_cost PRS
JOIN product_service PS on PRS.id_product_service = PS.id_product_service
JOIN tblcurrency TC on PRS.id_currency = TC.id
WHERE PRS.active = 1
AND PRS.id_product_service = :id_product_service");
$query_c->execute(array(":id_product_service"=>$id_product_service));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $productServicesCost[] = array(
            'id_product_service_cost' => $row['id_product_service_cost'],
            'id_product_service'         => $row['id_product_service'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'allDate' => $row['valid_from'] . ' / ' . $row['valid_to'],
            'charge'    => $row['charge'],
            'service_name'    => $row['service_name'],
            'ps_adult_cost'    => $row['ps_adult_cost'],
            'ps_teen_cost'    => $row['ps_teen_cost'],
            'ps_child_cost' => $row['ps_child_cost'],
            'ps_infant_cost'    => $row['ps_infant_cost'],
            'id_currency'    => $row['id_currency'],
            'currency_code' => $row['currency_code'],
            'id_dept' => $row['id_dept']
        );
    }
    $myData = $productServicesCost;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $productServicesCost[] = array(
        'id_product_service_cost' => '-',
        'id_product_service' => '-',
        'valid_from' => '-',
        'valid_to'     => '-',
        'allDate' => '-',
        'charge' => '-',
        'service_name'     => '-',
        'ps_adult_cost' => '-',
        'ps_teen_cost'     => '-',
        'ps_child_cost' => '-',
        'ps_infant_cost' => '-',
        'id_currency'     => '-',
        'currency_code' => '-',        
        'id_dept' => '-'
    );
    $myData = $productServicesCost;
    echo json_encode($myData);
}
