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

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "SELECT id, 0 AS X, CONCAT(agefrom,'-',ageto) AS value,agefrom,ageto
        FROM tblchildrenagerange        
        ORDER BY agefrom ASC,ageto; ";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "X,value,agefrom,ageto");
?>