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

if (!isset($_GET["hoid"])) {
    die("INVALID HOTEL ID");
}

$hotelfk = $_GET["hoid"];


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();


$sql = "select 
        DATE_FORMAT(dp.checkin,'%d-%m-%Y') AS checkin,
        DATE_FORMAT(dp.checkout,'%d-%m-%Y') AS checkout,
        DATE_FORMAT(dp.checkin,'%d %b %Y') AS checkin_disp,
        DATE_FORMAT(dp.checkout,'%d %b %Y') AS checkout_disp,
        DATE_FORMAT(checkin,'%Y') as theyear,
        dp.hotelfk, dp.active,
        dp.id, h.hotelname, h.groupfk,dp.seasonfk,
        gh.grpname, s.season
        from tbldateperiods dp
        inner join tblhotels h on dp.hotelfk = h.id
        left join tblgrouphotels gh on h.groupfk = gh.id
        left join tblseasons s on dp.seasonfk = s.id
        where dp.hotelfk=$hotelfk AND h.active = 1 and dp.active=1
        order by h.hotelname ASC, dp.checkin ASC, dp.checkout ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "checkin,checkout,hotelname,hotelfk,grpname,groupfk,season,active,theyear,checkin_disp,checkout_disp,seasonfk");
?>