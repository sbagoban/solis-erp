<?php

try {

    //=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
    set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        throw new Exception($errstr . " " . $errno);
    });
    //================================================================================


    session_start();
    
    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    if (!isset($_GET["id_product"])) {
        throw new Exception("INVALID ID". $_GET["id_product"]);
    }
    
    $id_product = $_GET["id_product"];
    
    $id_user = $_SESSION["solis_userid"];
    $uname = $_SESSION["solis_username"];
    $log_status = "DELETE";

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $stmt = $con->prepare("SELECT * FROM product WHERE id_product = :id_product AND active = 1");
	$stmt->execute(array(":id_product"=>$id_product));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id_product = $row["id_product"];
		$id_service_type = $row["id_service_type"];
		$id_product_type = $row["id_product_type"];
		$product_name = $row["product_name"];
    }  
    
    $stmt = $con->prepare("UPDATE product SET active=0 WHERE id_product = :id_product");
    $stmt->execute(array(":id_product"=>$id_product));

	$sqlLog = "INSERT INTO product_log ( 
		id_product,
		id_service_type,
		id_product_type,
		product_name,
        id_user,
        uname,
        log_status
		) 
			VALUES (
				:id_product,
                :id_service_type,
                :id_product_type,
                :product_name,
				:id_user,
				:uname,
				:log_status
				)";
	
	$stmt = $con->prepare($sqlLog);
        $stmt->execute(array(
        ":id_product" => $id_product,
        ":id_service_type" => $id_service_type,
        ":id_product_type" => $id_product_type,
        ":product_name" => $product_name,
        ":id_user" => $id_user,
        ":uname" => $uname,
        ":log_status" => $log_status
    ));
} 

catch (Exception $ex) {
    echo json_encode(array("OUTCOME" => "ERROR"));
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>

