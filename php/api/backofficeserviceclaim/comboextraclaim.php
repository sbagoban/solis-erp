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
    "SELECT * from product_service_extra_cost where id_product_service_cost = :id_product_service_cost
    AND active = 1");
$query_c->execute(array(":id_product_service_cost"=>$id_product_service_cost));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'id_product_service_extra_cost' => $row['id_product_service_extra_cost'],
            'extra_name' => $row['extra_name'],
            'id_product_service' => $row['id_product_service'],
            'charge' => $row['charge'],
            'extra_description' => $row['extra_name']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>