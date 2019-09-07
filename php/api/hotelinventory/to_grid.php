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

if (!isset($_GET["countries_ids"])) {
    die("INVALID COUNTRIES IDS");
}

$countries_ids = $_GET["countries_ids"];


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "SELECT id, toname AS value, 0 AS X FROM tbltouroperator 
        WHERE deleted = 0 
        AND id IN 
        (SELECT tofk FROM tblto_countries WHERE countryfk IN ($countries_ids))
        ORDER BY toname ASC;";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "X,value");
?>