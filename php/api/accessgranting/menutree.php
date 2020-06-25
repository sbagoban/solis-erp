<?php

header('Content-type: text/xml');
// mb_internal_encoding("iso-8859-1");
// mb_http_output( "iso-8859-1" );
// ob_start("mb_output_handler");

print "<?xml version='1.0' encoding='iso-8859-1'?>";

require_once("../../connector/pdo_connect_main.php");

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if(!isset($_GET["t"]))
{
    die("INVALID TOKEN");
}

if($_GET["t"] != $_SESSION["token"])
{
    die("INVALID TOKEN");
}


$con = pdo_con();

print '<tree id="0">';

print MenuTree_buildRecurTree(0, $con);

print '</tree>';

function menuTree_buildrecurTree($parentFK, $con) {
    $sql = "SELECT *
            FROM tblmenu 
            WHERE
            parentfk = :parentfk
            ORDER BY ordering;";

    $query = $con->prepare($sql);
    $query->execute(array(":parentfk" => $parentFK));

    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        
        $color = "blue";
        
        if($rw["inout"] == "I")
        {
            $color = "green";
        }
        
        echo '<item text="' . $rw["menuname"] . '" id="' . $rw["menuid"] . '" style="color:' . $color . '">';

        //if ($rw["leaf"] == "N") {
            menuTree_buildrecurTree($rw["menuid"], $con);
        //}

        echo '</item>';
    }
}

?>