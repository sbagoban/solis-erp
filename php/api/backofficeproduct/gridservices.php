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

if (!isset($_GET["id_product"])) {
    throw new Exception("INVALID ID". $_GET["id_product"]);
}

$id_product = $_GET["id_product"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("
SELECT PRS.id_product_services, PRS.id_product, PRS.service_name, PRS.charges, PRS.valid_from, PRS.valid_to, PRS.transfer_included,
PRS.id_dept, PRS.id_countries, PRS.id_coasts, PR.product_name, PRS.id_dept, DP.deptname, PRS.active, PRS.id_tax, PRS.charges, PRS.duration,
PRS.comments, PRS.cancellation, PRS.description, PRS.age_child_to, PRS.age_inf_to, PRS.age_teen_to, PRS.min_pax, PRS.max_pax, 
PRS.on_monday, PRS.on_tuesday, PRS.on_wednesday,  PRS.on_thursday, PRS.on_friday, PRS.on_saturday, PRS.on_sunday, PRS.id_creditor
FROM product_services PRS
JOIN product PR on PRS.id_product = PR.id_product
JOIN tbldepartments DP on PRS.id_dept = DP.id
WHERE PRS.active = 1
AND  PRS.id_product = :id_product");

$query_c->execute(array(":id_product"=>$id_product));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $services[] = array(
            'id_product'         => $row['id_product'],
            'service_name'    => $row['service_name'],
            'allName'    => $row['service_name']. '--' .$row['product_name'],
            'charges'    => $row['charges'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'valid_range' => $row['valid_from']. '--' .$row['valid_to'],
            'product_name'    => $row['product_name'],
            'deptname'    => $row['deptname'],
            'id_product_services' => $row['id_product_services'],
            'id_dept' => $row['id_dept'], 
            'id_countries' => $row['id_countries'],
            'id_coasts' => $row['id_coasts'],
            'id_tax' => $row['id_tax'], 
            'charges' => $row['charges'], 
            'transfer_included' => $row['transfer_included'],
            'charges' => $row['charges'], 
            'comments' => $row['comments'],
            'description' => $row['description'], 
            'cancellation' => $row['cancellation'],
            'age_child_to' => $row['age_child_to'],
            'age_inf_to' => $row['age_inf_to'], 
            'age_teen_to' => $row['age_teen_to'],
            'min_pax' => $row['min_pax'], 
            'max_pax' => $row['max_pax'],
            'duration' => $row['duration'],
            'on_monday' => $row['on_monday'],
            'on_tuesday' => $row['on_tuesday'], 
            'on_wednesday' => $row['on_wednesday'],
            'on_thursday' => $row['on_thursday'], 
            'on_friday' => $row['on_friday'],
            'on_saturday' => $row['on_saturday'],            
            'on_sunday' => $row['on_sunday'],            
            'id_creditor' => $row['id_creditor']
        );
    }
    $myData = $services;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $services[] = array(
        'id_product' => '-',
        'service_name' => '-',
        'charges' => '-',
        'valid_from' => '-',
        'valid_to' => '-',
        'product_name' => '-',
        'deptname' => '-',
        'valid_range' => '-',
        'allName'    => '-', 
        'id_dept' => '-', 
        'id_countries' => '-',
        'id_coasts' => '-', 
        'id_tax' => '-', 
        'transfer_included' => '-', 
        'comments' => '-',
        'description' => '-',
        'cancellation' => '-',
        'age_child_to' => '-',
        'age_inf_to' => '-',
        'age_teen_to' => '-',
        'min_pax' => '-',
        'max_pax' => '-', 
        'duration' => '-',
        'on_monday' => '-',
        'on_tuesday' => '-',
        'on_wednesday' => '-',
        'on_thursday' => '-',
        'on_friday' => '-',
        'on_saturday' => '-', 
        'on_sunday' => '-',
        'id_creditor' => '-'
    );
    $myData = $services;
    echo json_encode($myData);
}
