<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if (!isset($_GET["t"])) {
    die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) {
    //die("INVALID TOKEN");
}

if (!isset($_GET["hoid"])) {
    die("INVALID HOTEL ID");
}

$hotelfk = $_GET["hoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


$data = new JSONDataConnector($con, "PDO");

$sql = "select A.id, A.inventory_type, D.toname, A.title,B.rooms,C.dates
        FROM
        (
            select inv.*
            from tblinventory inv
            where inv.hotelfk = $hotelfk and inv.deleted = 0 
        ) A,
        (
            select invr.inventory_fk, 
            group_concat(hr.roomname order by roomname ASC SEPARATOR '<br>') AS rooms
            from tblinventory_rooms invr 
            INNER JOIN tblhotel_rooms hr on invr.roomfk = hr.id
            inner join tblinventory inv on invr.inventory_fk = inv.id
            WHERE inv.hotelfk = $hotelfk
            group by inventory_fk
        ) B,
        (
            select invd.inventoryfk,
            group_concat(DATE_FORMAT(invd.inventory_date,'%d %b %Y') ORDER BY invd.inventory_date ASC separator '<BR>') as dates
            from tblinventory_dates invd inner join tblinventory inv on invd.inventoryfk = inv.id
            where inv.hotelfk = $hotelfk
            group by invd.inventoryfk
        ) C,
        (
            select invto.inventoryfk, 
            group_concat(tc.toname order by toname ASC SEPARATOR '<br>') AS toname
            from tblinventory_touroperators invto
            INNER JOIN tbltouroperator tc on invto.tofk = tc.id
            inner join tblinventory inv on invto.inventoryfk = inv.id
            WHERE inv.hotelfk = $hotelfk
            group by inventoryfk
        ) D
        WHERE A.id = B.inventory_fk AND A.id = C.inventoryfk ORDER BY A.id";

$data->render_complex_sql($sql, "id", "inventory_type,toname,title,rooms,dates");
?>


    

