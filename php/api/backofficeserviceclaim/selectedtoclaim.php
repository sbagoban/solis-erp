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

$query_c = $con->prepare("SELECT PSCC.id_tour_operator, TRO.toname
FROM product_service_claim_to PSCC
JOIN tbltouroperator TRO on PSCC.id_tour_operator = TRO.id
WHERE id_product_service_claim = :id_product_service_claim
AND PSCC.active = 1");
$query_c->execute(array(":id_product_service_claim"=>$id_product_service_claim));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $toSelected[] = array(
            'id_tour_operator' => $row['id_tour_operator'],
            'toname' => $row['toname']
        );
    }    $myData = $toSelected;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
