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
if (!isset($_GET["id_product_service_claim"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service_claim"]);
}
$id_product_service_cost = $_GET["id_product_service_cost"];
$id_product_service_claim = $_GET["id_product_service_claim"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("
SELECT PRSC.id_product_service_claim, PRSC.id_product_service_cost, PRSC.id_product_service, PRSC.valid_from, PRSC.valid_to,
PRSC.id_dept, PRSC.specific_to, PRSC.specific_to_name, PRSC.charge, PRSC.ps_adult_claim, PRSC.ps_teen_claim, PRSC.ps_child_claim, PRSC.ps_infant_claim, 
PRSC.id_currency, PRSC.currency, PRSC.ex_monday, PRSC.ex_tuesday, PRSC.ex_wednesday, PRSC.ex_thursday, PRSC.ex_friday, PRSC.ex_saturday, PRSC.ex_sunday, PRSC.rollover_value, PRSC.rollover_type, PRSC.on_approved, PRSC.on_api, TD.deptname, 
PS.service_name, PR.product_name
FROM product_service_claim PRSC
JOIN tbldepartments TD on PRSC.id_dept = TD.id
JOIN product_service PS on PRSC.id_product_service = PS.id_product_service
JOIN product PR on PS.id_product = PR.id_product
WHERE PRSC.id_product_service_cost = :id_product_service_cost
AND PRSC.id_product_service_claim <> :id_product_service_claim
AND PRSC.active = 1
ORDER BY id_product_service_claim DESC;");
$query_c->execute(array(
    ":id_product_service_cost"=>$id_product_service_cost,    
    ":id_product_service_claim"=>$id_product_service_claim
));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $productServicesClaim[] = array(
            'id_product_service_claim' => $row['id_product_service_claim'],
            'id_product_service_cost' => $row['id_product_service_cost'], 
            'id_product_service' => $row['id_product_service'], 
            'valid_from' => $row['valid_from'], 
            'valid_to' => $row['valid_to'],
            'id_dept' => $row['id_dept'],
            'specific_to' => $row['specific_to'],            
            'specific_to_name' => $row['specific_to_name'],
            'charge' => $row['charge'],
            'ps_adult_claim' => $row['ps_adult_claim'],
            'ps_teen_claim' => $row['ps_teen_claim'],
            'ps_child_claim' => $row['ps_child_claim'],
            'ps_infant_claim' => $row['ps_infant_claim'],
            'id_currency' => $row['id_currency'],
            'currency' => $row['currency'],
            'ex_monday' => $row['ex_monday'],
            'ex_tuesday' => $row['ex_tuesday'],
            'ex_wednesday' => $row['ex_wednesday'],
            'ex_thursday' => $row['ex_thursday'],
            'ex_friday' => $row['ex_friday'],
            'ex_saturday' => $row['ex_saturday'],
            'ex_sunday' => $row['ex_sunday'],
            'allDate'  => $row['valid_from'].' / '.$row['valid_to'],
            'deptname' => $row['deptname'],
            'service_name' => $row['service_name'],
            'product_name' => $row['product_name'],
            'allName' => $row['service_name']. ' / ' .$row['product_name'],            
            'rollover_value' => $row['rollover_value'],
            'rollover_type' => $row['rollover_type'],            
            'on_approved' => $row['on_approved'],
            'on_api' => $row['on_api']
        );
    }
    $myData = $productServicesClaim;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $productServicesClaim[] = array(
        'id_product_service_claim' => '-',
        'id_product_service_cost' => '-',
        'id_product_service' => '-',
        'valid_from' => '-',
        'valid_to' => '-',
        'id_dept' => '-',
        'specific_to' => '-',        
        'specific_to_name' => '-',
        'charge' => '-',
        'ps_adult_claim' => '-',
        'ps_teen_claim' => '-',
        'ps_child_claim' => '-',
        'ps_infant_claim' => '-',
        'id_currency' => '-',
        'currency' => '-',
        'ex_monday' => '-',
        'ex_tuesday' => '-',
        'ex_wednesday' => '-',
        'ex_thursday' => '-',
        'ex_friday' => '-',
        'ex_saturday' => '-',
        'ex_sunday' => '-',
        'allDate' => '-',
        'deptname' => '-',
        'service_name' => '-',
        'product_name' => '-',
        'allName' => '-',
        'rollover_value' => '-',
        'rollover_type' => '-',
        'on_approved' => '-',
        'on_api' => '-'
    );
    $myData = $productServicesClaim;
    echo json_encode($myData);
}
