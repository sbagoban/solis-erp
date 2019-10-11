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

$con = pdo_con();

$query_c = $con->prepare(
    "SELECT PSEC.id_product_service_extra_cost, PSEC.extra_name, PSEC.id_product_service, PSEC.charge, PSE.extra_description
    FROM product_service_extra_cost PSEC
    JOIN product_service_extra PSE on PSEC.id_product_service = PSE.id_product_service
    where id_product_service_cost = :id_product_service_cost
    AND PSEC.active = 1");
$query_c->execute(array(":id_product_service_cost"=>$id_product_service_cost));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'id_product_service_extra_cost' => $row['id_product_service_extra_cost'],
            'extra_name' => $row['extra_name'],
            'id_product_service' => $row['id_product_service'],
            'charge' => $row['charge'],
            'extra_description' => $row['extra_description']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>