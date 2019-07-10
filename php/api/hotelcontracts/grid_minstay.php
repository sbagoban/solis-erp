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

if (!isset($_GET["cid"])) {
    die("INVALID CONTRACT ID");
}

$cid = $_GET["cid"];


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "select 
        scm.id, scm.override_dtfrom, scm.override_dtto, 
        scm.duration, scm.description, 
        group_concat(scmr.roomfk SEPARATOR ',') as selected_rooms_ids,
        group_concat(hr.roomname SEPARATOR ',') as rooms,
        '' AS action
        from tblservice_contract_minstay scm
        inner join tblservice_contract_minstay_rooms scmr on scm.id = scmr.minstayfk
        inner join tblhotel_rooms hr on scmr.roomfk = hr.id
        where scm.servicecontractfk = $cid
        group by scm.id, scm.override_dtfrom, scm.override_dtto, 
        scm.duration, scm.description
        order by scm.override_dtfrom ASC";


        
$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "duration,description,rooms,override_dtfrom,override_dtto,selected_rooms_ids,action");
?>