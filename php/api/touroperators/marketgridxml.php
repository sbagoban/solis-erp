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

$cellstyle = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();


$xml = "<rows>";
$xml .= getmarkets($con, "AND market_parent_fk IS NULL");
$xml .= "</rows>";

print $xml;

function getmarkets($con, $condition) {

    global $cellstyle;
    
    $xml = "";

    $sql = "SELECT * FROM tblmarkets WHERE active=1 $condition ORDER BY market_name ASC";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":market_parent_fk" => $parentid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {

        $id = $rw["id"];
        $name = $rw["market_name"];
        
        $xml .= "<row id=\"market_" . $id . "\" style=\"background-color:#FCF3CF;\" open=\"true\" >";
        
        $xml .= "<cell image=\"folderOpen.gif\" style=\"$cellstyle font-weight:bold;\"><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $name) . "]]></cell>";
        $xml .= "<cell style=\"$cellstyle\">0</cell>";
        

        $xml .= getmarkets($con, "AND market_parent_fk = $id");

        $xml .= getmarketcountry($con, $id);

        $xml .= "</row>";
    }

    return $xml;
}

function getmarketcountry($con, $id) {
    $sql = "select  c.country_name, c.id
            from tblmarket_countries mc 
            inner join tblcountries c on mc.countryfk = c.id
            where mc.marketfk = $id
            order by c.country_name asc";

    $xml = "";

    $query_country = $con->prepare($sql);
    $query_country->execute();

    while ($rw = $query_country->fetch(PDO::FETCH_ASSOC)) {

        global $cellstyle;
        
        $id = $rw["id"];
        $country_name = $rw["country_name"];
        
        
        $xml .= "<row id=\"" . $id . "\" style=\"background-color:white;\">";
        $xml .= "<cell image=\"leaf.gif\" style=\"$cellstyle\"><![CDATA[ " . str_replace(array("\"", "/", ">", "<", "'"), "", $country_name) . "]]></cell>";
        $xml .= "<cell style=\"$cellstyle\">0</cell>";
        $xml .= "</row>";
    }

    return $xml;
}
?>