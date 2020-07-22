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
    
    $id_product_service_pax_break_claim  = $_POST["id_product_service_pax_break_claim"];
    $id_product_service_claim = trim($_POST["id_product_service_claim"]);
    $id_product_service_cost  = trim($_POST["id_product_service_cost"]);
    $id_product_service       = trim($_POST["id_product_service"]);
    $pax_from  = trim($_POST["pax_from"]);
    $pax_to       = trim($_POST["pax_to"]);
    $charge = trim($_POST["charge"]);
    $ps_adult_claim_break = trim($_POST["ps_adult_claim_break"]);
    $ps_teen_claim_break = trim($_POST["ps_teen_claim_break"]);
    $ps_child_claim_break = trim($_POST["ps_child_claim_break"]);
    $ps_infant_claim_break = trim($_POST["ps_infant_claim_break"]);
    $ps_infant_claim_rollover = trim($_POST["ps_infant_claim_rollover"]);
    $ps_child_claim_rollover = trim($_POST["ps_child_claim_rollover"]);
    $ps_teen_claim_rollover = trim($_POST["ps_teen_claim_rollover"]);
    $ps_adult_claim_rollover = trim($_POST["ps_adult_claim_rollover"]);
    $rollover_value = trim($_POST["rollover_value"]);
    $rollover_type = trim($_POST["rollover_type"]);
    $active = 1;
	
    $con = pdo_con();

    //check duplicates for service
    $sql = "SELECT * FROM product_service_paxbreak WHERE id_product_service_pax_break_claim = :id_product_service_pax_break_claim";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service_pax_break_claim" => $id_product_service_pax_break_claim));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES PAX BREAKS!");
    }

    if ($id_product_service_pax_break_claim == "-1") {
        $sql = "INSERT INTO product_service_paxbreak 
        (
                id_product_service_claim,
                id_product_service_cost,
                id_product_service,
                pax_from,
                pax_to,
                charge,
                ps_adult_claim_break,
                ps_teen_claim_break,
                ps_child_claim_break,
                ps_infant_claim_break,
                ps_infant_claim_rollover,
                ps_child_claim_rollover,
                ps_teen_claim_rollover,
                ps_adult_claim_rollover,
                rollover_value,
                rollover_type,
                active
        ) 
                VALUES (
                    :id_product_service_claim,
                    :id_product_service_cost,
                    :id_product_service,
                    :pax_from,
                    :pax_to,
                    :charge,
                    :ps_adult_claim_break,
                    :ps_teen_claim_break,
                    :ps_child_claim_break,
                    :ps_infant_claim_break,
                    :ps_infant_claim_rollover,
                    :ps_child_claim_rollover,
                    :ps_teen_claim_rollover,
                    :ps_adult_claim_rollover,
                    :rollover_value,
                    :rollover_type, 
                    :active
                )";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_claim" => $id_product_service_claim,
            ":id_product_service_cost" => $id_product_service_cost,
            ":id_product_service" => $id_product_service,
            ":pax_to" => $pax_to,
            ":pax_from" => $pax_from,
            ":charge" => $charge,
            ":ps_adult_claim_break" => $ps_adult_claim_break,
            ":ps_teen_claim_break" => $ps_teen_claim_break,
            ":ps_child_claim_break" => $ps_child_claim_break,
            ":ps_infant_claim_break" => $ps_infant_claim_break,
            ":ps_infant_claim_rollover" => $ps_infant_claim_rollover,
            ":ps_child_claim_rollover" => $ps_child_claim_rollover,
            ":ps_teen_claim_rollover" => $ps_teen_claim_rollover,
            ":ps_adult_claim_rollover" => $ps_adult_claim_rollover,
            ":rollover_value" => $rollover_value,
            ":rollover_type" => $rollover_type,
            ":active" => $active
        ));
        
        $id_product_service_extra_claim = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_service_paxbreak SET 
                    id_product_service_claim = :id_product_service_claim,
                    id_product_service_cost = :id_product_service_cost,
                    id_product_service = :id_product_service,
                    pax_from = :pax_from,
                    pax_to = :pax_to,
                    charge = :charge,
                    ps_adult_claim_break = :ps_adult_claim_break,
                    ps_teen_claim_break = :ps_teen_claim_break,
                    ps_child_claim_break = :ps_child_claim_break,
                    ps_infant_claim_break = :ps_infant_claim_break,
                    ps_infant_claim_rollover = :ps_infant_claim_rollover,
                    ps_child_claim_rollover = :ps_child_claim_rollover,
                    ps_teen_claim_rollover = :ps_teen_claim_rollover,
                    ps_adult_claim_rollover = :ps_adult_claim_rollover,
                    rollover_value = :rollover_value,
                    rollover_type = :rollover_type,
                    active = :active
                WHERE id_product_service_pax_break_claim=:id_product_service_pax_break_claim";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_pax_break_claim" => $id_product_service_pax_break_claim,
            ":id_product_service_claim" => $id_product_service_claim,
            ":id_product_service_cost" => $id_product_service_cost,
            ":pax_to" => $pax_to,
            ":pax_from" => $pax_from,
            ":id_product_service" => $id_product_service,
            ":charge" => $charge,
            ":ps_adult_claim_break" => $ps_adult_claim_break,
            ":ps_teen_claim_break" => $ps_teen_claim_break,
            ":ps_child_claim_break" => $ps_child_claim_break,
            ":ps_infant_claim_break" => $ps_infant_claim_break,
            ":ps_infant_claim_rollover" => $ps_infant_claim_rollover,
            ":ps_child_claim_rollover" => $ps_child_claim_rollover,
            ":ps_teen_claim_rollover" => $ps_teen_claim_rollover,
            ":ps_adult_claim_rollover" => $ps_adult_claim_rollover,
            ":rollover_value" => $rollover_value,
            ":rollover_type" => $rollover_type,
            ":active" => $active));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service_claim"=>$id_product_service_claim));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>