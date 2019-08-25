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

    require_once("../../connector/pdo_connect_main.php");

    $con = pdo_con();
    $con->beginTransaction();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }


    $pid = $_POST["id"];
    $buy_settings = json_decode($_POST["buying_settings"], true);
    $sell_settings = json_decode($_POST["selling_settings"], true);

    $arr = array();
    $arr[] = array("ITEMS" => $buy_settings, "BUY_SELL" => "BUY");
    $arr[] = array("ITEMS" => $sell_settings, "BUY_SELL" => "SELL");

    if ($pid == "-1") {
        $sql = "INSERT INTO tblservice_contract_settings_profile
                (profile_name,profile_description) 
                VALUES ('[UPDATE PROFILE NAME]','[UPDATE PROFILE DESCRIPTION]')";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $pid = $con->lastInsertId();
    } else {
        //update profile: delete children
        $sql = "DELETE FROM tblservice_contract_settings_profile_details WHERE profile_fk=:profile_fk";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":profile_fk" => $pid));
    }

    //===========================
    //insert children

    for ($x = 0; $x < count($arr); $x++) {
        $buy_sell = $arr[$x]["BUY_SELL"];
        $arr_items = $arr[$x]["ITEMS"];

        for ($i = 0; $i < count($arr_items); $i++) {
            $item_fk = $arr_items[$i]["setting_item_fk"];
            $rounding = $arr_items[$i]["setting_rounding"];
            $basis = $arr_items[$i]["setting_basis"];
            $formula = $arr_items[$i]["setting_applyon_formula"];
            $row_index = $arr_items[$i]["setting_row_index"];

            $sql = "INSERT INTO tblservice_contract_settings_profile_details
                    (profile_fk,buy_sell,item_fk,rounding,basis,formula,row_index) 
                    VALUES 
                    (:profile_fk,:buy_sell,:item_fk,:rounding,:basis,:formula,:row_index)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":profile_fk" => $pid, ":buy_sell" => $buy_sell,
                ":item_fk" => $item_fk, ":rounding" => $rounding,":basis"=>$basis,
                ":formula" => $formula, ":row_index" => $row_index));
        }
    }



    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $pid));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
