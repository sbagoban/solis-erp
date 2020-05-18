<?php

error_reporting(E_ALL ^ E_NOTICE);
mb_internal_encoding("iso-8859-1");
mb_http_output( "iso-8859-1" );
ob_start("mb_output_handler");

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
    //die("INVALID TOKEN");
}

if (!isset($_GET["hoid"])) {
    die("INVALID HOTEL ID");
}

$cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4;  border-right:1px solid #A4A4A4;";
$hotelfk = $_GET["hoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "SELECT A.*, B.spo_count
FROM
(
	select sol.active, sol.deleted, sol.description, sol.id as sol_id,
	sols.id as sols_id, sols.cumulative, 
	so.id as spoid, so.active_external, so.active_internal,
	so.spocode, so.sponame, so.template, so.spo_type
	from tblspecial_offer_link sol
	left join tblspecial_offer_link_spos sols on sol.id = sols.linkfk AND sols.spofk in (select id from tblspecial_offer where hotel_fk = :hotel_fk and deleted=0)
	left join tblspecial_offer so on sols.spofk = so.id and so.hotel_fk = :hotel_fk and so.deleted = 0
	WHERE sol.hotel_fk = :hotel_fk AND sol.deleted = 0
	ORDER BY so.date_created asc
) A
,
(
	select count(sols.id) as spo_count, sol.id as sol_id
	from tblspecial_offer_link sol
	left join tblspecial_offer_link_spos sols on sol.id = sols.linkfk AND sols.spofk in (select id from tblspecial_offer where hotel_fk = :hotel_fk and deleted=0)
	left join tblspecial_offer so on sols.spofk = so.id AND so.hotel_fk = :hotel_fk and so.deleted = 0 
	where sol.deleted = 0
    group by sol.id
) B
WHERE A.sol_id = B.sol_id";

$query_parent = $con->prepare($sql);
$query_parent->execute(array(":hotel_fk" => $hotelfk));


$rwid = 1;

$xml = "<rows>";

$last_link_id = -1;
while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {

    $link_id = $rw["sol_id"];
    $link_active = $rw["active"];
    $link_deleted = $rw["deleted"];
    $link_description = $rw["description"];

    $link_line_id = $rw["sols_id"];
    $link_line_cumulative = $rw["cumulative"];

    $spo_id = $rw["spoid"];
    $spo_active_external = $rw["active_external"];
    $spo_active_internal = $rw["active_internal"];
    $spo_spocode = $rw["spocode"];
    $spo_sponame = $rw["sponame"];
    $spo_template = $rw["template"];
    $spo_spo_type = $rw["spo_type"];

    $activeinternal = "NO";
    if ($rw["active_internal"] == 1) {
        $activeinternal = "YES";
    }

    $activeexternal = "NO";
    if ($rw["active_external"] == 1) {
        $activeexternal = "YES";
    }


    $rowspan = "";
    $spo_count = $rw["spo_count"];

    $bgcolor = "";
    $topborder = "";

    if ($spo_count == 0) {
        $last_link_id = $link_id;
        $bgcolor = " background-color:#E7E7E7; ";
        $topborder = " border-top:3px solid #A4A4A4; ";
        $rowspan = "";
    } else {
        if ($last_link_id != $link_id) {
            $last_link_id = $link_id;
            $topborder = " border-top:3px solid #A4A4A4; ";
            $rowspan = " rowspan=\"$spo_count\" ";
        } else {
            $rowspan = "";
            $topborder = " border-top:1px solid #A4A4A4; ";
        }
    }




    $xml .= "<row id=\"$rwid\"  >";

    $xml .= "<cell style='$cellstyle $topborder' $rowspan ><![CDATA[$link_description]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder' $rowspan ><![CDATA[$link_active]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$spo_template]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$spo_spocode]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$spo_sponame]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$link_line_cumulative]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$activeinternal]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$activeexternal]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$spo_spo_type]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$link_id]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$link_line_id]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $bgcolor'><![CDATA[$spo_id]]></cell>";

    $xml .= "</row>";

    $rwid++;
}

$xml .= "</rows>";

print $xml;
?>




