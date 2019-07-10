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

$sql = "SELECT c.*, if(c.default_selected = 'Y','Y' ,'') AS display_default,
        o.continent as continent_name
        FROM tblcountries c 
        INNER JOIN tblcontinents o on c.continent = o.continent_code
        ORDER BY c.countrycode_2; ";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "countrycode_2,countrycode_3,country_numeric,country_name,lat,lon,display_default,continent_name,continent,used_for_hotels,default_selected");
?>