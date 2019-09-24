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

if (!isset($_GET["id_product_services"])) {
    throw new Exception("INVALID ID". $_GET["id_product_services"]);
}

$id_product_services = $_GET["id_product_services"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT * FROM product_services_extra WHERE id_product_services = :id_product_services");
$query_c->execute(array(":id_product_services"=>$id_product_services));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $extraservices[] = array(
            'id_product_services_extra'         => $row['id_product_services_extra'],
            'id_services_extra'    => $row['id_services_extra'],
            'extra_name'    => $row['extra_name'],
            'id_product_services'    => $row['id_product_services'],
            'extra_description'    => $row['extra_description'],
            'charges'    => $row['charges'],
        );
    }    $myData = $extraservices;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $extraservices[] = array(
        'id_product_services_extra'          => '-',
        'id_services_extra'     => '-',
        'extra_name'     => '-',
        'id_product_services'          => '-',
        'extra_description'     => '-',
        'charges'     => '-',
    );
    $myData = $extraservices;
    echo json_encode($myData);
}
