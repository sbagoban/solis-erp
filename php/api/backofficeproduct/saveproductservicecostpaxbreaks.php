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
    
    $id_product_service_pax_break_cost  = $_POST["id_product_service_pax_break_cost"];
    $id_product_service_cost  = trim($_POST["id_product_service_cost"]);
    $id_product_service       = trim($_POST["id_product_service"]);
    $pax_from  = trim($_POST["pax_from"]);
    $pax_to       = trim($_POST["pax_to"]);
    $charge = trim($_POST["charge"]);
    $ps_adult_cost_break = trim($_POST["ps_adult_cost_break"]);
    $ps_teen_cost_break = trim($_POST["ps_teen_cost_break"]);
    $ps_child_cost_break = trim($_POST["ps_child_cost_break"]);
    $ps_infant_cost_break = trim($_POST["ps_infant_cost_break"]);
    $active = 1;
	
    $con = pdo_con();

    //check duplicates for service
    $sql = "
    SELECT * FROM product_service_paxbreak_cost
    WHERE id_product_service_pax_break_cost = :id_product_service_pax_break_cost";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(
        ":id_product_service_pax_break_cost" => $id_product_service_pax_break_cost
    ));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES PAX BREAKS!");
    } else {

        //check duplicates for service
        $sql_chk = "
        SELECT * FROM product_service_paxbreak_cost 
        WHERE id_product_service_cost = :id_product_service_cost
        AND pax_from = :pax_from
        AND pax_to = :pax_to";
        $stmt_chk = $con->prepare($sql_chk);
        $stmt_chk->execute(array(
            ":id_product_service_cost" => $id_product_service_cost,
            ":pax_from" => $pax_from,
            ":pax_to" => $pax_to
        ));
        if ($rw = $stmt_chk->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("duplicate_entry");

        } else {
                    if ($id_product_service_pax_break_cost == "-1") {
                        $sql = "INSERT INTO product_service_paxbreak_cost
                        (
                                id_product_service_cost,
                                id_product_service,
                                pax_from,
                                pax_to,
                                charge,
                                ps_adult_cost_break,
                                ps_teen_cost_break,
                                ps_child_cost_break,
                                ps_infant_cost_break,
                                active
                        ) 
                                VALUES (
                                    :id_product_service_cost,
                                    :id_product_service,
                                    :pax_from,
                                    :pax_to,
                                    :charge,
                                    :ps_adult_cost_break,
                                    :ps_teen_cost_break,
                                    :ps_child_cost_break,
                                    :ps_infant_cost_break,
                                    :active
                                )";

                        $stmt = $con->prepare($sql);
                        $stmt->execute(array(
                            ":id_product_service_cost" => $id_product_service_cost,
                            ":id_product_service" => $id_product_service,
                            ":pax_to" => $pax_to,
                            ":pax_from" => $pax_from,
                            ":charge" => $charge,
                            ":ps_adult_cost_break" => $ps_adult_cost_break,
                            ":ps_teen_cost_break" => $ps_teen_cost_break,
                            ":ps_child_cost_break" => $ps_child_cost_break,
                            ":ps_infant_cost_break" => $ps_infant_cost_break,
                            ":active" => $active
                        ));
                        
                        $id_product_service_extra_claim = $con->lastInsertId();
                    } 
        }

    }

    echo json_encode(array("OUTCOME" => "OK", "id_product_service_cost"=>$id_product_service_cost));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>