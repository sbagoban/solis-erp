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
            from product_service_extra 
            WHERE id_product_service = :id_product_service1");
$query_c->execute(array(":id_product_service1"=>$id_product_service1));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $serviceExtra[] = array(
            'id_service_extra'    => $row['id_service_extra'],
            'extra_name'    => $row['extra_name'],
            'id_product_service' => $row['id_product_service'],
            'extra_description' => $row['extra_description'],
            'charge'    => $row['charge']
        );
    }
    $myData = $serviceExtra;
    echo json_encode($id_prod_serv);

    if ($id_prod_serv > 0) {
        for( $i = 0; $i < sizeof($myData); $i++ ) {
            $id_service_extra =$myData[$i]['id_service_extra'];
            $extra_name =$myData[$i]['extra_name'];
            $extra_description =$myData[$i]['extra_description'];
            $charge =$myData[$i]['charge'];

            $sql2 = "INSERT INTO product_service_extra 
                (
                    id_service_extra, 
                    extra_name, 
                    id_product_service,
                    extra_description, 
                    charge
                )  
                VALUES (
                    :id_service_extra, 
                    :extra_name, 
                    :id_prod_serv,
                    :extra_description, 
                    :charge
                )";
                $stmt = $con->prepare($sql2);
                $stmt->bindParam(':id_service_extra', $id_service_extra);
                $stmt->bindParam(':extra_name', $extra_name);
                $stmt->bindParam(':id_prod_serv', $id_prod_serv);
                $stmt->bindParam(':extra_description', $extra_description);
                $stmt->bindParam(':charge', $charge);
                
                $stmt->execute(); 
        }
}
    echo json_encode(array("OUTCOME" => "OK", "id_product_service"=>$id_prod_serv));

} else {
    echo "NO DATA";
}
