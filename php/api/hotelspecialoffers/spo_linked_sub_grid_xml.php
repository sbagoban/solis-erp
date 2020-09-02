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

$cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4;  border-right:1px solid #A4A4A4;";

$hotelfk = $_GET["hoid"];
$spoid = $_GET["spoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


$sql = "SELECT A.*, B.spo_count
FROM
(
	select sol.active, sol.deleted, sol.description, sol.id as sol_id,
	sols.id as sols_id, sols.cumulative, 
	so.id as spoid, so.active_external, so.active_internal,
	so.spocode, so.sponame, so.template, so.spo_type,
        group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR ' , ') as tour_operator_names
	from tblspecial_offer_link sol
	left join tblspecial_offer_link_spos sols on sol.id = sols.linkfk AND sols.spofk in (select id from tblspecial_offer where hotel_fk = :hotel_fk and deleted=0)
	left join tblspecial_offer so on sols.spofk = so.id and so.hotel_fk = :hotel_fk and so.deleted = 0
        left join tblspecial_offer_touroperator sot on so.id = sot.spofk
        left join tbltouroperator tp on sot.tofk = tp.id
	WHERE sol.hotel_fk = :hotel_fk AND sol.deleted = 0
        AND sol.id IN (SELECT linkfk FROM tblspecial_offer_link_spos WHERE spofk=:spofk)
        GROUP BY sol.active, sol.deleted, sol.description, sol.id,
	sols.id, sols.cumulative, 
	so.id, so.active_external, so.active_internal,
	so.spocode, so.sponame, so.template, so.spo_type
	ORDER BY so.date_created asc
) A,
(
	select count(sols.id) as spo_count, sol.id as sol_id
	from tblspecial_offer_link sol
	left join tblspecial_offer_link_spos sols on sol.id = sols.linkfk AND sols.spofk in (select id from tblspecial_offer where hotel_fk = :hotel_fk and deleted=0)
	left join tblspecial_offer so on sols.spofk = so.id AND so.hotel_fk = :hotel_fk and so.deleted = 0 
	where sol.deleted = 0
    group by sol.id
) B
WHERE A.sol_id = B.sol_id ORDER BY A.sol_id ASC";

$query_parent = $con->prepare($sql);
$query_parent->execute(array(":hotel_fk" => $hotelfk, ":spofk"=>$spoid));


$rwid = 1;

$xml = "<rows>";

$xml .= "<head>";
$xml .= "    <column width=\"200\" type=\"ro\" align=\"left\"  sort=\"str\">Link Description</column>";
$xml .= "    <column width=\"70\" type=\"ro\" align=\"center\"  sort=\"str\">Link Active</column>";
$xml .= "    <column width=\"100\" type=\"ro\" align=\"left\" sort=\"str\">SPO Template</column>";
$xml .= "    <column width=\"80\" type=\"ro\" align=\"left\" sort=\"str\">SPO ID</column>";
$xml .= "    <column width=\"200\" type=\"ro\" align=\"left\" sort=\"str\">SPO Code</column>";
$xml .= "    <column width=\"200\" type=\"ro\" align=\"left\" sort=\"str\">SPO Name</column>";
$xml .= "    <column width=\"1000\" type=\"ro\" align=\"left\" sort=\"str\">Tour Operator</column>";
$xml .= "    <column width=\"70\" type=\"ro\" align=\"center\" sort=\"str\">SPO Cumulative</column>";
$xml .= "    <column width=\"70\" type=\"ro\" align=\"center\" sort=\"str\">SPO Active Internal</column>";
$xml .= "    <column width=\"70\" type=\"ro\" align=\"center\" sort=\"str\">SPO Active External</column>";
$xml .= "    <column width=\"70\" type=\"ro\" align=\"right\" sort=\"str\">SPO Type</column>";

$xml .= "<beforeInit>";
$xml .= "<call command='enableMultiline'>";
$xml .= "<param>";
$xml .= "true";
$xml .= "</param>";
$xml .= "</call>";

$xml .= "<call command='enableColSpan'>";
$xml .= "<param>";
$xml .= "true";
$xml .= "</param>";
$xml .= "</call>";

$xml .= "<call command='enableRowspan'>";
$xml .= "<param>";
$xml .= "true";
$xml .= "</param>";
$xml .= "</call>";

$xml .= "<call command='setEditable'>";
$xml .= "<param>";
$xml .= "false";
$xml .= "</param>";
$xml .= "</call>";

$xml .= "<call command='enableAlterCss'>";
$xml .= "<param>";
$xml .= "\"\"";
$xml .= "</param>";
$xml .= "<param>";
$xml .= "\"\"";
$xml .= "</param>";
$xml .= "</call>";

$xml .= "</beforeInit>";

$xml .= "</head>";

$last_link_id = -1;
while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {

    $link_id = $rw["sol_id"];
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
    $spo_touroperators = $rw["tour_operator_names"];
    
    $link_active = "NO";
    if ($rw["active"] == 1) {
        $link_active = "YES";
    }
    
            
    $activeinternal = "NO";
    if ($rw["active_internal"] == 1) {
        $activeinternal = "YES";
    }

    $activeexternal = "NO";
    if ($rw["active_external"] == 1) {
        $activeexternal = "YES";
    }
    
    $cumulative = "NO";
    if ($rw["cumulative"] == 1) {
        $cumulative = "YES";
    }
    
    $rowspan = "";
    $spo_count = $rw["spo_count"];

    $bgcolor = "background-color:#DCFAE7;";
    $forecolor = "color:black;";
    
    if($spoid == $spo_id)
    {
        $forecolor = "color:blue; font-weight:bold;";
    }
    
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
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_template]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_id]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_spocode]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_sponame]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_touroperators]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$cumulative]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$activeinternal]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$activeexternal]]></cell>";
    $xml .= "<cell style='$cellstyle $topborder $forecolor $bgcolor'><![CDATA[$spo_spo_type]]></cell>";

    $xml .= "</row>";

    $rwid++;
}

$xml .= "</rows>";

print $xml;
?>




