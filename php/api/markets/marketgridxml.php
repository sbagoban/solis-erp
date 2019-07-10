<?php

error_reporting(E_ALL ^ E_NOTICE);

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


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();


$xml = "<rows>";
$xml .= getmarkets($con,"WHERE market_parent_fk IS NULL");
$xml .= "</rows>";

print $xml;

function getmarkets($con,$condition) {
    
    $xml = "";
    
    $sql = "SELECT * FROM tblmarkets $condition ORDER BY market_name ASC";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":market_parent_fk"=>$parentid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {

        $id = $rw["id"];
        $name = $rw["market_name"];
        $description = $rw["market_description"];
        $active = $rw["active"];
        $market_parent_fk = $rw["market_parent_fk"];

        $xml .= "<row id=\"" . $id . "\">";
        $xml .= "<cell style=\"font-weight:bold; color:#3380FF;\"><![CDATA[" . str_replace(array("\"", "/", ">", "<", "'"), "", $name) . "]]></cell>";
        $xml .= "<cell>$active</cell>";
        $xml .= "<cell><![CDATA[" . str_replace(array("\"", "/", ">", "<", "'"), "", $description) . "]]></cell>";
        
        $xml .= getmarkets($con,"WHERE market_parent_fk = $id");

        $xml .= "</row>";
    }
    
    return $xml;
}
?>