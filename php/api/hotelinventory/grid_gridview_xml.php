<?php

try {

//=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
    set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
// error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        throw new Exception($errstr . " " . $errno);
    });

    // mb_internal_encoding("iso-8859-1");
    // mb_http_output( "iso-8859-1" );
    // ob_start("mb_output_handler");

    if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
        header("Content-type: application/xhtml+xml");
    } else {
        header("Content-type: text/xml");
    }
    echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");


    session_start();
    
    require_once("../../utils/utilities.php");

    if (!isset($_SESSION["solis_userid"])) {
        die("NO LOG IN!");
    }

    if (!isset($_GET["t"])) {
        die("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        die("INVALID TOKEN");
    }

  
    $dates = json_decode($_GET["dates"],true);
    
    function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }

    usort($dates, "date_sort");
    
    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FDEBD0;";


    print '<rows>';
    print '<head>';
    print '<column width="0" id="month" type="ro" '
            . 'align="center" sort="date"></column>  ';
    print '<column width="200" id="mydate" type="ro" '
            . 'align="center" sort="date">Date</column>  ';
    print '<settings><colwidth>px</colwidth></settings> ';

    print '</head>';

    for ($i = 0; $i < count($dates); $i++) {

        print "<row id='$dates[$i]'>";
        print "<cell style='$cellstyle'>" . date("M",strtotime($dates[$i])) . "</cell>";
        print "<cell style='$cellstyle'>" . date("d M Y",strtotime($dates[$i])) . "</cell>";
        print "</row>";
    }

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}


?>


