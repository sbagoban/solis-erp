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

$cond = "where h.deleted = 0";

if(isset($_GET["hoid"]))
{
    $cond = " WHERE h.id= " . $_GET["hoid"];
}

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "select h.id, h.hotelname, h.hoteltypefk,
        IFNULL(h.groupfk,'-') AS groupfk,
        h.description, h.phy_address, h.phy_address2, h.phy_city, h.phy_postcode,
        h.phy_countryfk, h.areafk, h.coastfk, h.website, h.ratecode, h.specialratecode,
        h.lat, h.lon, h.active, h.mail_address, h.mail_address2, h.mail_city,
        h.mail_postcode,
        h.mail_countryfk,h.deleted,
        g.grpname, ht.hoteltype,
        h.rating, h.property_name, h.company_name, r.num_stars, r.description as rating_description
        from tblhotels h 
        left join tblhoteltype ht on h.hoteltypefk = ht.id
        left join tblgrouphotels g on h.groupfk = g.id
        left join tblrating r on h.rating = r.id
        $cond
        order by h.hotelname ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "grpname,hoteltype,hotelname,hoteltypefk,
        groupfk,description,phy_address,phy_address2,phy_city,phy_postcode,
        phy_countryfk,areafk,coastfk,website,ratecode,specialratecode,
        lat,lon,active,mail_address,mail_address2,mail_city,mail_postcode,
        mail_countryfk,deleted,rating,property_name,company_name,
        num_stars,rating_description");
?>



