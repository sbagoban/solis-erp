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


$hotelfk = "-1";
$condition_sql = "";
$condition_sql_rooms = "";
$condition_sql_countries = "";
$condition_sql_to = "";

if(isset($_GET["params"]))
{
    $params = json_decode($_GET["params"], true);
    
    $hotelfk = $params["hotelfk"];
    
    $date_from = date("Y-m-d", strtotime($params["dtfrom"]));
    $date_to = date("Y-m-d", strtotime($params["dtto"]));
    
    $priority = $params["priority"];
    
    $rooms_ids = trim($params["rooms_ids"]);
    $market_countries_ids = trim($params["market_countries_ids"]);
    $to_ids = trim($params["to_ids"]);
    
    
    
    $condition_sql = " AND deleted=0 
                       AND date_from < '$date_to'
                       AND date_to > '$date_from' ";
    
    if($priority != "all")
    {
        $condition_sql .= " AND priority='$priority' ";
    }
    
    
    $condition_sql_rooms = " AND ar.roomfk IN ($rooms_ids)";
    $condition_sql_countries = " AND so.countryfk IN ($market_countries_ids)";
    
    if($to_ids != "")
    {
        $condition_sql_to = " AND ato.tofk IN ($to_ids)";
    }       
}
else
{
    $hotelfk = $_GET["hotelfk"];
    $condition_sql = " AND a.id = " . $_GET["allotmentid"];
}




require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


$data = new JSONDataConnector($con, "PDO");

$sql = "
    
    SELECT 
    a.*, 
    r.roomnames, r.rooms_display, r.rooms_ids,
    co.countries, co.market_countries_display, co.market_countries_ids, 
    tp.tour_operator_names, tp.to_display, tp.to_ids

    FROM
    (
        select
        a.id, a.hotel_fk,
        a.release_type, a.specific_no_days,
        a.priority,
        DATE_FORMAT(a.specific_date,'%d-%m-%Y') AS specific_date,
        a.comment,
        a.units,
        DATE_FORMAT(a.created_on,'%d-%m-%Y') AS created_on,
        DATE_FORMAT(a.date_from,'%d-%m-%Y') AS date_from,
        DATE_FORMAT(a.date_to,'%d-%m-%Y') AS date_to
        FROM tblinventory_allotment a
        WHERE 
        a.hotel_fk = $hotelfk 
        $condition_sql
        order by a.date_from DESC, a.date_to DESC
    ) a
    -- rooms
    left join
    (
        select a.id, 
        group_concat(hr.roomname ORDER BY hr.roomname ASC SEPARATOR '<br>') as roomnames,
        group_concat(hr.roomname ORDER BY hr.roomname ASC SEPARATOR ' , ') as rooms_display,
        group_concat(hr.id ORDER BY hr.roomname ASC SEPARATOR ',') as rooms_ids
        from tblinventory_allotment a
        inner join tblinventory_allotment_rooms ar on a.id = ar.allotmentfk
        inner join tblhotel_rooms hr on ar.roomfk = hr.id
        where a.hotel_fk = $hotelfk
        $condition_sql_rooms
        group by a.id
    ) r

    on a.id = r.id

    -- tour operators
    left join

    (
        select a.id, 
        group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR '<br>') as tour_operator_names,
        group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR ' , ') as to_display,
        group_concat(tp.id ORDER BY tp.toname ASC SEPARATOR ',') as to_ids
        from tblinventory_allotment a
        inner join tblinventory_allotment_to ato on a.id = ato.allotmentfk
        inner join tbltouroperator tp on ato.tofk = tp.id
        where a.hotel_fk = $hotelfk
        $condition_sql_to
        group by a.id
    ) tp

    on a.id = tp.id

    -- countries
    left join

    (
        select a.id, 
        group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR '<br>') AS countries,
        group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR ' , ') AS market_countries_display,
        group_concat(c.id ORDER BY c.country_name ASC SEPARATOR ',') AS market_countries_ids
        from tblinventory_allotment a
        inner join tblinventory_allotment_countries so on a.id = so.allotmentfk
        inner join tblcountries c on so.countryfk = c.id
        where a.hotel_fk = $hotelfk
        $condition_sql_countries
        group by a.id
    ) co

    on a.id = co.id

    order by a.date_from DESC, a.date_to DESC;";


$data->render_complex_sql($sql, "id", "hotel_fk,release_type,specific_no_days,specific_date,
    comment,deleted,units,priority,
    created_by,created_on,date_from,date_to,
    market_countries_display,market_countries_ids,countries,
    rooms_display,rooms_ids,roomnames,
    tour_operator_names,to_display,to_ids");
?>


    

