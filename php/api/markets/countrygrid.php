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

$sql = "select mc.*, c.country_name, o.continent, 0 AS X
        from tblmarket_countries mc
        inner join tblcountries c on mc.countryfk = c.id
        left join tblcontinents o on c.continent = o.continent_code
        where mc.marketfk = $marketid
        order by c.country_name ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "countryfk", "X,country_name,continent");
?>