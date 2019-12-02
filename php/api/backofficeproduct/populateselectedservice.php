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

$query_c = $con->prepare("SELECT *
FROM product_service_package 
WHERE id_product_service = :id_product_service
AND active = 1");
$query_c->execute(array(":id_product_service"=>$id_product_service));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $serviceSelected[] = array(
            'id_product_service' => $row['id_product_service'],
            'id_product' => $row['id_product'],
            'id_service_type' => $row['id_service_type'],
            'id_product_type' => $row['id_product_type'],
            'id_product_service_induded' => $row['id_product_service_induded']
        );
    }    $myData = $serviceSelected;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
