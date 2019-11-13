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
SELECT PRS.id_product_service, PRS.id_product, PRS.service_name, PRS.charge, PRS.valid_from, PRS.valid_to, PRS.transfer_included,
PRS.id_dept, PRS.id_country, PRS.id_coast, PR.product_name, PRS.id_dept, DP.deptname, PRS.active, PRS.id_tax, PRS.charge, PRS.duration,
PRS.comments, PRS.cancellation, PRS.description, PRS.age_child_to, PRS.age_inf_to, PRS.age_teen_to, PRS.min_pax, PRS.max_pax, 
PRS.on_monday, PRS.on_tuesday, PRS.on_wednesday,  PRS.on_thursday, PRS.on_friday, PRS.on_saturday, PRS.on_sunday, PRS.id_creditor,
PRS.for_infant, PRS.for_child, PRS.for_teen, PRS.age_child_from, PRS.age_inf_from, PRS.age_teen_from, PRS.min_age, PRS.max_age, PRS.for_adult,  PRS.is_pakage
FROM product_service PRS
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
            'charge'    => $row['charge'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'valid_range' => $row['valid_from']. '--' .$row['valid_to'],
            'product_name'    => $row['product_name'],
            'deptname'    => $row['deptname'],
            'id_product_service' => $row['id_product_service'],
            'id_dept' => $row['id_dept'], 
            'id_country' => $row['id_country'],
            'id_coast' => $row['id_coast'],
            'id_tax' => $row['id_tax'], 
            'charge' => $row['charge'], 
            'transfer_included' => $row['transfer_included'],
            'charge' => $row['charge'], 
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
            'id_creditor' => $row['id_creditor'],
            'for_infant' => $row['for_infant'],
            'for_child' => $row['for_child'], 
            'for_teen' => $row['for_teen'],
            'age_child_from' => $row['age_child_from'],            
            'age_inf_from' => $row['age_inf_from'],            
            'age_teen_from' => $row['age_teen_from'],
            'min_age' => $row['min_age'], 
            'max_age' => $row['max_age'], 
            'for_adult' => $row['for_adult'], 
            'is_pakage' => $row['is_pakage']          
        );
    }
    $myData = $services;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $services[] = array(
        'id_product_service' => '-',
        'id_product' => '-',
        'service_name' => '-',
        'charge' => '-',
        'valid_from' => '-',
        'valid_to' => '-',
        'product_name' => '-',
        'deptname' => '-',
        'valid_range' => '-',
        'allName'    => '-', 
        'id_dept' => '-', 
        'id_country' => '-',
        'id_coast' => '-', 
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
        'id_creditor' => '-',
        'for_infant' => '-',
        'for_child' => '-',
        'for_teen' => '-',
        'age_child_from' => '-', 
        'age_inf_from' => '-',
        'age_teen_from' => '-',
        'min_age' => '-', 
        'max_age' => '-', 
        'for_adult' => '-', 
        'is_pakage' => '-'
    );
    $myData = $services;
    echo json_encode($myData);
}
