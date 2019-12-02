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

if (!isset($_GET["id_product_service_claim"])) {
    throw new Exception("INVALID ID". $_GET["id_product_service_claim"]);
}

$id_product_service_claim = $_GET["id_product_service_claim"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sqlActivityExtra = $con->prepare("SELECT 
                                                                SE.id_service_extra,
                                                                SE.extra_name,
                                                                PSE.extra_description,
                                                                PSE.charge,
                                                                PSE_CLAIM.id_product_service_extra_claim,
                                                                CLAIM_CUR.currency_code claim_cur,
                                                                PSE_CLAIM.ps_adult_claim,
                                                                PSE_CLAIM.ps_teen_claim,
                                                                PSE_CLAIM.ps_child_claim,
                                                                PSE_CLAIM.ps_infant_claim,
                                                                PSE_COST.id_product_service_extra_cost,
                                                                COST_CUR.currency_code cost_cur,
                                                                PSE_COST.ps_adult_cost,
                                                                PSE_COST.ps_teen_cost,
                                                                PSE_COST.ps_child_cost,
                                                                PSE_COST.ps_infant_cost
                                                            FROM
                                                                product_service_extra_claim PSE_CLAIM,
                                                                product_service_extra_cost PSE_COST,
                                                                product_service_extra PSE,
                                                                service_extra SE,
                                                                product_service PS,
                                                                tblcurrency CLAIM_CUR,
                                                                tblcurrency COST_CUR
                                                            WHERE PSE_CLAIM.id_product_service_extra_cost = PSE_COST.id_product_service_extra_cost
                                                            AND PSE_COST.id_product_service_extra = PSE.id_product_service_extra
                                                            AND PSE.id_service_extra = SE.id_service_extra
                                                            AND PSE.id_product_service = PS.id_product_service
                                                            AND PSE_CLAIM.id_currency = CLAIM_CUR.id
                                                            AND PSE_COST.id_currency = COST_CUR.id
                                                            AND PSE_CLAIM.id_product_service_claim = :id_product_service_claim
                                                            AND PSE_CLAIM.active = 1
                                                            AND PSE_COST.active = 1
                                                            AND PSE.active = 1
                                                            AND PS.active = 1");
$sqlActivityExtra->execute(array(":id_product_service_claim"=>$id_product_service_claim));
$row_count_c = $sqlActivityExtra->rowCount();

if ($row_count_c > 0) {
    while ($row = $sqlActivityExtra->fetch(PDO::FETCH_ASSOC)) {
        $activityExtraDetails[] = array(
            'id_service_extra'  => $row['id_service_extra'],
            'extra_name'    => $row['extra_name'],
            'extra_description' => $row['extra_description'],
            'charge'    => $row['charge'],
            'id_product_service_extra_claim'    => $row['id_product_service_extra_claim'],
            'claim_cur' => $row['claim_cur'],
            'ps_adult_claim'    => $row['ps_adult_claim'],
            'ps_teen_claim' => $row['ps_teen_claim'],
            'ps_child_claim'    => $row['ps_child_claim'],
            'ps_infant_claim'   => $row['ps_infant_claim'],
            'id_product_service_extra_cost' => $row['id_product_service_extra_cost'],
            'cost_cur'  => $row['cost_cur'],
            'ps_adult_cost' => $row['ps_adult_cost'],
            'ps_teen_cost'  => $row['ps_teen_cost'],
            'ps_child_cost' => $row['ps_child_cost'],
            'ps_infant_cost'    => $row['ps_infant_cost']
        );
    }    $myData = $activityExtraDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $activityExtraDetails[] = array(
            'id_service_extra'  => '-',
            'extra_name'    => '-',
            'extra_description' => '-',
            'charge'    => '-',
            'id_product_service_extra_claim'    => '-',
            'claim_cur' => '-',
            'ps_adult_claim'    => '-',
            'ps_teen_claim' => '-',
            'ps_child_claim'    => '-',
            'ps_infant_claim'   => '-',
            'id_product_service_extra_cost' => '-',
            'cost_cur'  => '-',
            'ps_adult_cost' => '-',
            'ps_teen_cost'  => '-',
            'ps_child_cost' => '-',
            'ps_infant_cost'    => '-'
    );
    $myData = $activityExtraDetails;
    echo json_encode($myData);
}
