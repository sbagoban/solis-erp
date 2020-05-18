<?php

session_start();
mb_internal_encoding("iso-8859-1");
mb_http_output( "iso-8859-1" );
ob_start("mb_output_handler");
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
    //modify
    echo '<item text="Setup" id="setup" open="true">';
        echo '<item text="Details" id="details" select="yes"  />';
        echo '<item text="Description" id="description" select="yes"  />';
        echo '<item text="Commission" id="commission"  />';
        echo '<item text="Currencies" id="currencies"  />';
    echo '</item>';

    echo '<item text="Contacts" id="contacts"  />';
    echo '<item text="Facilities" id="facilities"  />';
    echo '<item text="Images" id="images"  />';
}

print '</tree>';
?>