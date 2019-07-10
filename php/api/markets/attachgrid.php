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

if (!isset($_GET["marketid"])) {
    die("INVALID MARKET ID");
}

$marketid = $_GET["marketid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "select c.id, c.country_name, o.continent, 0 AS X
        from tblcountries c
        left join tblcontinents o on c.continent = o.continent_code
        WHERE c.id NOT IN (SELECT countryfk FROM tblmarket_countries)
        order by c.country_name ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "X,country_name,continent");
?>