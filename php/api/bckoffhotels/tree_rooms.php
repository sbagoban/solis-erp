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

if (!isset($_GET["action"])) {
    die("MISSING ACTION");
}

$action = $_GET["action"];

header('Content-type: text/xml');

print "<?xml version='1.0' encoding='iso-8859-1'?>";

print '<tree id="0">';


if ($action == "NEW") {
    echo '<item text="Details" id="details" select="yes"  />';
} else {
    echo '<item text="Details" id="details" select="yes"  />';
    echo '<item text="Images" id="images"  />';
}

print '</tree>';
?>