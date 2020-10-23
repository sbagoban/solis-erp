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

if (!isset($_GET["rid"])) {
    die("INVALID ROOM ID");
}

$roomfk = $_GET["rid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$data = new JSONDataConnector($con, "PDO");

$sql = "select hf.*, f.facility,f.ordering,f.description from 
        tblhotel_room_facilities hf
        inner join tblfacilities f on hf.facilityfk = f.id
        where hf.roomfk = $roomfk and f.deleted = 0
        order by f.ordering asc, f.facility asc;";

$data->render_sql($sql, "id", "roomfk,facilityfk,ordering,description,facility");

?>
