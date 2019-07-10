<?php

header('Content-type: text/xml');

print "<?xml version='1.0' encoding='iso-8859-1'?>";

require_once("../../connector/pdo_connect_main.php");

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

$ugrpid = $_GET["ugrpid"];


$con = pdo_con();

print '<tree id="0">';



print menuTree_buildRecurTree(0, $con, $ugrpid);

print '</tree>';

function menuTree_buildRecurTree($parentFK, $con, $ugrpid) {
    $sql = "SELECT M.*, G.* 
            FROM tblmenu M INNER JOIN tblgrpmenurights G 
            ON M.menuid = G.menufk WHERE G.groupfk = :ugrpid
            AND parentfk = :parentK 
            ORDER BY ordering;";

    $query = $con->prepare($sql);

    $query->execute(array(":ugrpid" => $ugrpid, ":parentK" => $parentFK));

    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        
        $color = "blue";
        
        if($rw["inout"] == "I")
        {
            $color = "green";
        }
        
        echo '<item text="' . $rw["menuname"] . '" id="' . $rw["menuid"] . '" style="color:' . $color . '">';

        //if ($rw["leaf"] == "N") {
            menuTree_buildRecurTree($rw["menuid"], $con, $ugrpid);
        //}

        echo '</item>';
    }
}

?>