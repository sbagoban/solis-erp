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

$hotelfk = $_GET["hoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


$sql = "select A.id, A.sponame, A.spocode, A.template, A.active_internal,
        A.active_external, A.spo_type,A.ratecodes,
        B.validities,
        D.tour_operator_names
        FROM
        (
            select spo.*, rc.ratecodes
            from tblspecial_offer spo
            left join tblratecodes rc on spo.rate_fk = rc.id
            where spo.hotel_fk = $hotelfk and spo.deleted = 0 
        ) A,
        (
            select spo_fk, 
            group_concat(concat(DATE_FORMAT(valid_from,'%d %b %Y'), ' - ', DATE_FORMAT(valid_to,'%d %b %Y')) 
            order by valid_from desc, valid_to desc SEPARATOR '<br>') AS validities
            from tblspecial_offer_validityperiods  spv
            inner join tblspecial_offer spo on spv.spo_fk = spo.id
            WHERE spo.hotel_fk = $hotelfk
            group by spo_fk
        ) B,        
        (
                select spo.id,
                group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR '<br>') as tour_operator_names,
                group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR ' , ') as tour_operators_display,
                group_concat(tp.id ORDER BY tp.toname ASC SEPARATOR ',') as tour_operators_ids
                from tblspecial_offer spo
                left join tblspecial_offer_touroperator sots on spo.id = sots.spofk
                inner join tbltouroperator tp on sots.tofk = tp.id
                where spo.hotel_fk = $hotelfk and spo.deleted = 0 
                group by spo.id
        ) D

        WHERE A.id = B.spo_fk AND A.id = D.id ORDER BY B.validities";



$xml = "<rows>";

$query_parent = $con->prepare($sql);
$query_parent->execute();

while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {

    $subgrid_type = "sub_row_grid";
    $url = "php/api/hotelspecialoffers/spo_linked_sub_grid_xml.php?hoid=$hotelfk&spoid=" . $rw["id"];

    //check if the spo is linked?
    if (!_spo_is_linked($con, $rw["id"])) {
        $subgrid_type = "ro";
        $url = "";
    }

    $xml .= "<row id=\"" . $rw["id"] . "\"  >";
    $xml .= "<cell type=\"$subgrid_type\" ><![CDATA[$url]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["id"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["sponame"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["active_internal"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["active_external"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["spo_type"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["spocode"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["template"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", "'"), "", $rw["tour_operator_names"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $rw["ratecodes"]) . "]]></cell>";
    $xml .= "<cell><![CDATA[ " . str_replace(array("\"", "/", "'"), "", $rw["validities"]) . "]]></cell>";
    $xml .= "</row>";
}

$xml .= "</rows>";

print $xml;

function _spo_is_linked($con, $spoid) {
    $sql = "select * 
            from tblspecial_offer_link sol
            inner join tblspecial_offer_link_spos sols on sol.id = sols.linkfk
            where sols.spofk = :spoid and deleted = 0";

    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":spoid"=>$spoid));

    if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        return true;
    }

    return false;
}
?>




