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
$id_prod_cost = trim($_POST["id_prod_cost"]);


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT PSEC.id_product_service,   
            PSEC.valid_from, PSEC.valid_to, PSEC.id_dept,  
            PSEC.charge, PSEC.ps_adult_cost, PSEC.ps_teen_cost,   
            PSEC.ps_child_cost, PSEC.ps_infant_cost, PSEC.id_currency,   
            PSEC.currency, PSEC.active, PSEC.id_product_service_extra,   
            PSEC.extra_name, Z.id_product_service_cost            
            from product_service_extra_cost PSEC
            JOIN product_service_cost Z on PSEC.id_product_service = Z.id_product_service
            WHERE PSEC.id_product_service = :id_product_service1");
$query_c->execute(array(":id_product_service1"=>$id_product_service1));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $serviceExtraCost[] = array(
            'id_product_service' => $row['id_product_service'],
            'valid_from' => $row['valid_from'],
            'valid_to' => $row['valid_to'],
            'id_dept'    => $row['id_dept'], 
            'charge' => $row['charge'],
            'ps_adult_cost' => $row['ps_adult_cost'],
            'ps_teen_cost'    => $row['ps_teen_cost'],
            'ps_child_cost' => $row['ps_child_cost'],
            'ps_infant_cost' => $row['ps_infant_cost'],
            'id_currency'    => $row['id_currency'],
            'currency' => $row['currency'],
            'active' => $row['active'],
            'id_product_service_extra' => $row['id_product_service_extra'],
            'extra_name'    => $row['extra_name'],
            'id_product_service_cost' => $row['id_product_service_cost']
        );
    }
    $myData = $serviceExtraCost;
    echo json_encode($myData);

    if ($id_prod_serv > 0) {
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
            $active =$myData[$i]['active'];
            $id_product_service_extra =$myData[$i]['id_product_service_extra'];
            $extra_name =$myData[$i]['extra_name'];            
            $id_product_service_cost =$myData[$i]['id_product_service_cost'];

            $sql2 = "INSERT INTO product_service_extra_cost 
                (
                    id_product_service_cost,                    
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
                    currency,
                    active,
                    id_product_service_extra,
                    extra_name
                )  
                VALUES (
                    :id_prod_cost,
                    :id_prod_serv,
                    :valid_from,
                    :valid_to,
                    :id_dept,
                    :charge,
                    :ps_adult_cost,
                    :ps_teen_cost,
                    :ps_child_cost,
                    :ps_infant_cost,
                    :id_currency,
                    :currency,
                    :active,
                    :id_product_service_extra,
                    :extra_name
                )";
                $stmt = $con->prepare($sql2);
                $stmt->bindParam(':id_prod_cost', $id_prod_cost);
                $stmt->bindParam(':id_prod_serv', $id_prod_serv);
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
                $stmt->bindParam(':active', $active);
                $stmt->bindParam(':id_product_service_extra', $id_product_service_extra);
                $stmt->bindParam(':extra_name', $extra_name);
                
                $stmt->execute(); 
        }
}
    echo json_encode(array("OUTCOME" => "OK", "id_product_service"=>$id_prod_serv));

} else {
    echo "NO DATA";
}
