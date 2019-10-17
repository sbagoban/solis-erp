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

if (!isset($_GET["id_product_service1"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service1"]);
}

$id_product_service1 = $_GET["id_product_service1"];
$id_product_service = trim($_POST["id_product_service"]);

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT id_product_service, 
            valid_from, valid_to, id_dept, charge, ps_adult_cost, ps_teen_cost, ps_child_cost, 
            ps_infant_cost, id_currency, currency 
            from product_service_cost 
            WHERE id_product_service = :id_product_service1");
$query_c->execute(array(":id_product_service1"=>$id_product_service1));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $serviceCost[] = array(
            'id_product_service' => $row['id_product_service'],
            'valid_from'    => $row['valid_from'],
            'valid_to'    => $row['valid_to'],
            'id_dept'    => $row['id_dept'],
            'charge'    => $row['charge'],
            'ps_adult_cost'    => $row['ps_adult_cost'],
            'ps_teen_cost'    => $row['ps_teen_cost'],
            'ps_child_cost'    => $row['ps_child_cost'],
            'ps_infant_cost'    => $row['ps_infant_cost'],
            'id_currency'    => $row['id_currency'],
            'currency'    => $row['currency'],
        );
    }
    $myData = $serviceCost;

    if ($id_product_service > 0) {
        for( $i = 0; $i < sizeof($myData); $i++ ) {
            $valid_from =$myData[$i]['valid_from'];
            $valid_to =$myData[$i]['valid_to'];
            $id_dept =$myData[$i]['id_dept'];
            $charge =$myData[$i]['charge'];
            $ps_adult_cost =$myData[$i]['ps_adult_cost'];
            $ps_teen_cost =$myData[$i]['ps_teen_cost'];
            $ps_child_cost =$myData[$i]['ps_child_cost'];
            $ps_infant_cost =$myData[$i]['ps_infant_cost'];
            $id_currency =$myData[$i]['id_currency'];
            $currency =$myData[$i]['currency'];

            $sql2 = "INSERT INTO product_service_cost 
                (
                    id_product_service, 
                    valid_from, 
                    valid_to, 
                    id_dept,
                    charge, 
                    ps_adult_cost, 
                    ps_teen_cost, 
                    ps_child_cost, 
                    ps_infant_cost, 
                    id_currency, 
                    currency
                )  
                VALUES (
                    :id_product_service, 
                    :valid_from, 
                    :valid_to, 
                    :id_dept,
                    :charge, 
                    :ps_adult_cost, 
                    :ps_teen_cost, 
                    :ps_child_cost, 
                    :ps_infant_cost, 
                    :id_currency, 
                    :currency
                )";
                $stmt = $con->prepare($sql2);
                $stmt->bindParam(':id_product_service', $id_product_service);
                $stmt->bindParam(':valid_from', $valid_from);
                $stmt->bindParam(':valid_to', $valid_to);
                $stmt->bindParam(':id_dept', $id_dept);
                $stmt->bindParam(':charge', $charge);
                $stmt->bindParam(':ps_adult_cost', $ps_adult_cost);
                $stmt->bindParam(':ps_teen_cost', $ps_teen_cost);
                $stmt->bindParam(':ps_child_cost', $ps_child_cost);
                $stmt->bindParam(':ps_infant_cost', $ps_infant_cost);
                $stmt->bindParam(':id_currency', $id_currency);
                $stmt->bindParam(':currency', $currency);
                
                $stmt->execute(); 
        }
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service"=>$id_product_service));

} else {
    echo "NO DATA";
}
