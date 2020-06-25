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

    $con = pdo_con();


    $roomid = $_GET["roomid"];
    $additional_adult_max = $_GET["additional_adult_max"];
    $selected_currency_buy_ids = $_GET["selected_currency_buy_ids"];
    $selected_currency_sell_ids = $_GET["selected_currency_sell_ids"];
    $costprice_currencyid = $_GET["costprice_currencyid"];

    $arr_currency_buy = getCurrencyArray($selected_currency_buy_ids, $con);
    $arr_currency_sell = getCurrencyArray($selected_currency_sell_ids, $con);
    $arr_currency_cost = getCurrencyArray($costprice_currencyid, $con);


    error_reporting(E_ALL ^ E_NOTICE);

    $basis_options = "<option value='%'>%</option><option value='FLAT'>FLAT</option>";

    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FDEBD0;";

    print '<rows>';
    print '<head>';

    print '<column width="100" id="basis" type="ro" '
            . 'align="center" sort="na">Unit/Extra Adult</column>  ';
    
    print '<column width="70" id="basis" type="combo" '
            . 'align="center"  sort="na">Basis</column>  ';

    $buycaption = "Buying";
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print '<column width="65" id="buy_value_' . $buycurrencyid . '" type="edn" '
                . 'align="center" sort="na">' . $buycaption . '</column>  ';
        $buycaption = "#cspan";
    }

    $sellcaption = "Selling";
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print '<column width="65" id="sell_value_' . $sellcurrencyid . '" type="edn" '
                . 'align="center" sort="na">' . $sellcaption . '</column>  ';
        $sellcaption = "#cspan";
    }

    print '<beforeInit>';


    //===============================================

    print '<call command="attachHeader">';
    print '<param>';


    print '#rspan,#rspan';


    //========== THE UNIT ===================
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print ',' . $buycurrencycode;
    }

    $adult_caption = "Selling";
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print ',' . $sellcurrencycode;
    }


    print '</param>';
    print '</call>';

    print '</beforeInit>';


    print '<settings><colwidth>px</colwidth></settings> ';

    print '</head>';


    //===============
    //print unit price row
    print "<row id='unit_price'>";
    
    print "<cell type='ro' "
            . "align='center' context='' "
            . "variant='UNITS' "
            . "sort='na' category='' "
            . " currencyid='' buy_sell='' "
            . " agefrom='0' ageto='0' "
            . "style='$cellstyle $cellstylelocked'>UNIT PRICE</cell>";
    
    print "<cell type='ro' "
            . "align='center' context='' "
            . "variant='UNITS' "
            . "sort='na' category='' "
            . " currencyid='' buy_sell='' "
            . " agefrom='0' ageto='0' "
            . "style='$cellstyle $cellstylelocked'></cell>";

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print "<cell type='edn' align='right' "
                . "context='unit_price' "
                . "variant='UNITS' "
                . "sort='na' category='0' "
                . "currencyid='$buycurrencyid' "
                . " buy_sell='buy' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle'></cell>";
    }

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print "<cell type='ron' align='right' "
                . "context='unit_price' "
                . "variant='UNITS' "
                . "sort='na' category='0' "
                . "currencyid='$sellcurrencyid' "
                . " buy_sell='sell' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle $cellstylelocked'></cell>";
    }

    print "</row>";
    
    //===========================================
    //now print each additional row for each additional adult
    
    for ($rw = 1; $rw <= $additional_adult_max; $rw++) {
        print "<row id='additional_{$rw}'>";
        
        print "<cell type='ro' align='center' "
                    . "context='' "
                    . "variant='UNITS' "
                    . "sort='na' category='' "
                    . "currencyid='' "
                    . " buy_sell='' "
                    . " agefrom='0' ageto='0' "
                    . "style='$cellstyle $cellstylelocked'>Extra Adult $rw</cell>";
        
        print "<cell type='combo' "
                . "align='center' context='additional_adult_basis' "
                . "variant='UNITS' "
                . "sort='na' category='$rw' "
                . " currencyid='' buy_sell='' "
                . " xmlcontent=\"true\" editable=\"0\" agefrom='0' ageto='0' "
                . "style='$cellstyle'>$basis_options</cell>";

        for ($i = 0; $i < count($arr_currency_buy); $i++) {
            $buycurrencyid = $arr_currency_buy[$i]["ID"];
            $buycurrencycode = $arr_currency_buy[$i]["CODE"];

            print "<cell type='edn' align='right' "
                    . "context='additional_adult_value' "
                    . "variant='UNITS' "
                    . "sort='na' category='$rw' "
                    . "currencyid='$buycurrencyid' "
                    . " buy_sell='buy' "
                    . " agefrom='0' ageto='0' "
                    . "style='$cellstyle'></cell>";
        }

        for ($i = 0; $i < count($arr_currency_sell); $i++) {
            $sellcurrencyid = $arr_currency_sell[$i]["ID"];
            $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

            print "<cell type='ron' align='right' "
                    . "context='additional_adult_value' "
                    . "variant='UNITS' "
                    . "sort='na' category='$rw' "
                    . "currencyid='$sellcurrencyid' "
                    . " buy_sell='sell' "
                    . " agefrom='0' ageto='0' "
                    . "style='$cellstyle $cellstylelocked'></cell>";
        }

        print "</row>";
    }

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}

function getCurrencyArray($selected_currency_ids, $con) {
    $arrcodes = array();

    $sql = "select * from tblcurrency where id IN ($selected_currency_ids) ORDER BY currency_code";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arrcodes[] = array("CODE" => $rw["currency_code"], "ID" => $rw["id"]);
    }

    return $arrcodes;
}
?>


