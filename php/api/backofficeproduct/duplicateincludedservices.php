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
$id_prod_serv = trim($_POST["id_prod_serv"]);

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT * 
            from product_service_package 
            WHERE id_product_service = :id_product_service1");
$query_c->execute(array(":id_product_service1"=>$id_product_service1));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $serviceIncluded[] = array(
            'id_product'    => $row['id_product'],
            'id_service_type'    => $row['id_service_type'],
            'id_product_type' => $row['id_product_type'],
            'id_product_service_included' => $row['id_product_service_included']
        );
    }
    $myData = $serviceIncluded;
    if ($id_prod_serv > 0) {
        for( $i = 0; $i < sizeof($myData); $i++ ) {
            $id_product =$myData[$i]['id_product'];
            $id_service_type =$myData[$i]['id_service_type'];
            $id_product_type =$myData[$i]['id_product_type'];
            $id_product_service_included =$myData[$i]['id_product_service_included'];

            $sql2 = "INSERT INTO product_service_package 
                (
                    id_product_service, 
                    id_product, 
                    id_service_type, 
                    id_product_type,
                    id_product_service_included
                )  
                VALUES (
                    :id_prod_serv,
                    :id_product, 
                    :id_service_type, 
                    :id_product_type,
                    :id_product_service_included
                )";
                $stmt = $con->prepare($sql2);
                $stmt->bindParam(':id_prod_serv', $id_prod_serv);
                $stmt->bindParam(':id_product', $id_product);
                $stmt->bindParam(':id_service_type', $id_service_type);
                $stmt->bindParam(':id_product_type', $id_product_type);
                $stmt->bindParam(':id_product_service_included', $id_product_service_included);
                
                $stmt->execute(); 
        }
}
    echo json_encode(array("OUTCOME" => "OK", "id_product_service"=>$id_prod_serv));

} else {
    echo "NO DATA";
}
