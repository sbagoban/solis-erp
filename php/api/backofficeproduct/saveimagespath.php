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

        require_once("../../connector/pdo_connect_main.php");
        
        $id_product_service_images = $_POST["id_product_service_images"];
        $id_product_service = $_POST["id_product_service"];
        $product_service_images_path = trim($_POST["product_service_images_path"]);
        $active = trim($_POST["active"]);

        $id_user = $_SESSION["solis_userid"];
        $uname = $_SESSION["solis_username"];
        $log_status = "CREATE";

        $con = pdo_con();

        //check duplicates for area name
        $sql_image = "SELECT * FROM product_service_images WHERE active = 1";
        $sql_image = $con->prepare($sql_image);
        $sql_image->execute(); 


        if ($id_product_service_images == "-1") {
                
            $sql = "INSERT INTO product_service_images (product_service_images_path, id_product_service, active) 
            VALUES (:product_service_images_path, :id_product_service, :active)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
            ":product_service_images_path" => $product_service_images_path,
            ":id_product_service" => $id_product_service,
            ":active" => 1));

            $id_product_service_images_path = $con->lastInsertId();
            
        }
        echo json_encode(array("OUTCOME" => "OK", "id_product_service_images"=>$id_product_service_images));
    } catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

?>
