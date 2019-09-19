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

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("
SELECT PR.id_product, ST.servicetype, PR.product_name, PR.id_product_type, PR.id_service_type
FROM product PR
JOIN tblservicetype ST on PR.id_service_type = ST.id
WHERE PR.active = 1");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $excursionservices[] = array(
            'id_product'         => $row['id_product'],
            'id_service_type'    => $row['id_service_type'],
            'id_product_type'    => $row['id_product_type'],
            'servicetype'    => $row['servicetype'],
            'product_name'    => $row['product_name'],
        );
    }
    $myData = $excursionservices;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    
    $excursionservices[] = array(
        'id_product'          => '-',
        'servicetype'     => '-',
        'product_name'     => '-',
    );
    $myData = $excursionservices;
    echo json_encode($myData);
}
