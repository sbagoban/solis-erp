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
SELECT PRS.id_product_services_cost, PRS.id_product_services, PRS.valid_from, PRS.valid_to, PS.charges, 
PRS.ps_adult_cost, PRS.ps_teen_cost, PRS.ps_child_cost, PRS.ps_infant_cost, PRS.id_currency,
PS.service_name, TD.deptname, TC.currency_code, PS.id_creditor, PS.id_coasts, TCO.coast, PRS.id_dept, 
PS.on_monday, PS.on_tuesday, PS.on_wednesday, PS.on_thursday, PS.on_friday, PS.on_saturday, PS.on_sunday, PR.product_name, PSEC.id_product_services_extra_cost
FROM product_services_cost PRS
JOIN product_services PS on PRS.id_product_services = PS.id_product_services
JOIN tblcoasts TCO on PS.id_coasts = TCO.id
JOIN tbldepartments TD on PRS.id_dept = TD.id
JOIN tblcurrency TC on PRS.id_currency = TC.id
JOIN product_services_extra_cost PSEC on PRS.id_product_services_cost = PSEC.id_product_services_cost
JOIN product PR on PS.id_product = PR.id_product");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $productServicesCost[] = array(
            'id_product_services_cost' => $row['id_product_services_cost'],
            'id_product_services'         => $row['id_product_services'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'allDate' => $row['valid_from'] . ' / ' . $row['valid_to'],
            'charges'    => $row['charges'],
            'service_name'    => $row['service_name'],
            'ps_adult_cost'    => $row['ps_adult_cost'],
            'ps_teen_cost'    => $row['ps_teen_cost'],
            'ps_child_cost' => $row['ps_child_cost'],
            'ps_infant_cost'    => $row['ps_infant_cost'],
            'id_currency'    => $row['id_currency'],
            'currency_code' => $row['currency_code'],
            'id_dept' => $row['id_dept'],
            'deptname' => $row['deptname'],
            'id_creditor' => $row['id_creditor'],
            'id_coasts' => $row['id_coasts'],
            'coast' => $row['coast'],
            'on_monday' => $row['on_monday'],
            'on_tuesday' => $row['on_tuesday'],
            'on_wednesday' => $row['on_wednesday'],
            'on_thursday' => $row['on_thursday'],
            'on_friday' => $row['on_friday'],
            'on_saturday' => $row['on_saturday'],
            'on_sunday' => $row['on_sunday'],
            'product_name' => $row['product_name'],
            'id_product_services_extra_cost' => $row['id_product_services_extra_cost']
        );
    }
    $myData = $productServicesCost;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    $productServicesCost[] = array(
        'id_product_services_cost' => '-',
        'id_product_services' => '-',
        'valid_from' => '-',
        'valid_to'     => '-',
        'allDate' => '-',
        'charges' => '-',
        'service_name'     => '-',
        'ps_adult_cost' => '-',
        'ps_teen_cost'     => '-',
        'ps_child_cost' => '-',
        'ps_infant_cost' => '-',
        'id_currency'     => '-',
        'currency_code' => '-',
        'deptname' => '-',
        'id_dept' => '-',
        'id_creditor' => '-',
        'id_coasts' => '-',
        'coast' => '-',
        'on_monday' => '-',
        'on_tuesday' => '-',
        'on_wednesday' => '-',
        'on_thursday' => '-',
        'on_friday' => '-',
        'on_saturday' => '-',
        'on_sunday' => '-',
        'product_name' => '-',
        'id_product_services_extra_cost' => '-'
    );
    $myData = $productServicesCost;
    echo json_encode($myData);
}
