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
        echo '<item text="Markets" id="markets"  />';
        echo '<item text="Currencies" id="currencies"  />';
        echo '<item text="Price Codes" id="pricecodes"  />';
        echo '<item text="Allocations" id="allocations"  />';
        echo '<item text="Internet" id="internet"  />';
        echo '<item text="Companies" id="companies"  />';
    echo '</item>';

    echo '<item text="Balances" id="balances"  />';
    echo '<item text="Transactions" id="transactions"  />';
    echo '<item text="Contacts" id="contacts"  />';
    echo '<item text="Notes" id="notes"  />';
    echo '<item text="API" id="api"  />';
    echo '<item text="Message" id="message"  />';
}

print '</tree>';
?>