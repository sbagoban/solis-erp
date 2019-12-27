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
            throw new Exception("INVALID TOKEN1");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            throw new Exception("INVALID TOKEN2");
        }
        
        if (!isset($_GET["id_product"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product = $_GET["id_product"];
        $id_service_type = trim($_POST["id_service_type"]);
        $id_product_type = trim($_POST["id_product_type"]);
        $product_name = trim($_POST["product_name"]);

        $id_user = $_SESSION["solis_userid"];
        $uname = $_SESSION["solis_username"];
        $log_status = "UPDATE";

        $con = pdo_con();
         //check duplicates for area name
        $sql_name = "SELECT * FROM product 
        WHERE product_name = :product_name 
        AND id_product <> :id_product
        AND active = 1";
        $stmt_name = $con->prepare($sql_name);
        $stmt_name->bindParam(':product_name', $product_name);
        $stmt_name->bindParam(':id_product', $id_product);
        $stmt_name->execute(); 

        if ($rw = $stmt_name->fetch(PDO::FETCH_ASSOC)) {
            die(json_encode(array("OUTCOME" => "ERROR_NAME")));
        }
        $sql = "UPDATE product SET 
                        id_service_type=:id_service_type,
                        id_product_type=:id_product_type,
                        product_name=:product_name
                        WHERE id_product=:id_product";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product" => $id_product,
                ":product_name" => $product_name,
                ":id_product_type" => $id_product_type,
                ":id_service_type" => $id_service_type));

        // Start Product Log
        $sqlLog = "INSERT INTO product_log ( 
            id_product,
            id_product_type, 
            id_service_type, 
            product_name,
            id_user,
            uname,
            log_status
            ) 
                VALUES (
                    :id_product,
                    :id_product_type, 
                    :id_service_type, 
                    :product_name,
                    :id_user,
                    :uname,
                    :log_status
                    )";
    
        $stmt = $con->prepare($sqlLog);
                    $stmt->execute(array(
                    ":id_product" => $id_product,
                    ":id_product_type" => $id_product_type, 
                    ":id_service_type" => $id_service_type,
                    ":product_name" => $product_name,
                    ":id_user" => $id_user,
                    ":uname" => $uname,
                    ":log_status" => $log_status
                ));
    
        // End Of Log
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
