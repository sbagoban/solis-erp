<?php

error_reporting(E_ALL ^ E_NOTICE);
// mb_internal_encoding("iso-8859-1");
// mb_http_output( "iso-8859-1" );
// ob_start("mb_output_handler");

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n");
?>

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

if (!isset($_GET["linkid"])) {
    die("INVALID LINK ID");
}

$hotelfk = $_GET["hoid"];
$linkid = $_GET["linkid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


//get all spos for that hotel that are not already in a link

$sql = "select A.id, A.sponame, A.spocode, A.template, A.active_internal,
        A.active_external, A.spo_type,A.ratecodes,
        B.validities, C.tour_operator_names FROM
        (
        select spo.*, rc.ratecodes
        from tblspecial_offer spo
        left join tblratecodes rc on spo.rate_fk = rc.id
        WHERE spo.hotel_fk = :hotel_fk AND spo.deleted = 0 
        
        -- AND spo.id NOT IN 
        -- (
            -- get lists of spos that have not already been attached to links of this hotel
        --    SELECT a.spofk from tblspecial_offer_link_spos a
        --    inner join tblspecial_offer_link  b on a.linkfk = b.id
        --    WHERE b.hotel_fk = 19 and b.deleted = 0 )
        ) A,
        (
        select spo_fk, 
        group_concat(concat(DATE_FORMAT(valid_from,'%d %b %Y'), ' - ', DATE_FORMAT(valid_to,'%d %b %Y')) 
        order by valid_from desc, valid_to desc SEPARATOR '<br>') AS validities
        from tblspecial_offer_validityperiods  spv
        inner join tblspecial_offer spo on spv.spo_fk = spo.id
        WHERE spo.hotel_fk = :hotel_fk and spo.deleted = 0 
        group by spo_fk
        ) B,
        (
                select spo.id,
                group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR '<br>') as tour_operator_names
                from tblspecial_offer spo
                left join tblspecial_offer_touroperator sots on spo.id = sots.spofk
                inner join tbltouroperator tp on sots.tofk = tp.id
                where spo.hotel_fk = :hotel_fk and spo.deleted = 0 
                group by spo.id
        ) C
        WHERE A.id = B.spo_fk AND A.id = C.id ORDER BY B.validities";



$xml = "<rows>";

    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":hotel_fk"=>$hotelfk, ":linkfk"=>$linkid));
    
    $activeinternal = "NO";
    if($rw["active_internal"] == 1)
    {
        $activeinternal = "YES";
    }
    
    $activeexternal = "NO";
    if($rw["active_external"] == 1)
    {
        $activeexternal = "YES";
    }
    
    
    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $xml .= "<row id=\"" . $rw["id"] . "\"  >";            
            $xml .= "<cell>0</cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["sponame"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $activeinternal) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $activeexternal) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["spo_type"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["spocode"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["template"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["tour_operator_names"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["ratecodes"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", "'"), "", $rw["validities"]) . "]]></cell>";            
        $xml .= "</row>";
    }
    
$xml .= "</rows>";

print $xml;


?>


    

