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
    $child_ages_ids = $_GET["child_ages_ids"];
    $selected_currency_buy_ids = $_GET["selected_currency_buy_ids"];
    $selected_currency_sell_ids = $_GET["selected_currency_sell_ids"];
    $costprice_currencyid = $_GET["costprice_currencyid"];

    $arr_currency_buy = getCurrencyArray($selected_currency_buy_ids, $con);
    $arr_currency_sell = getCurrencyArray($selected_currency_sell_ids, $con);
    $arr_currency_cost = getCurrencyArray($costprice_currencyid, $con);
    $arr_child_agecodes = getChildAgesArray($child_ages_ids, $con);


    error_reporting(E_ALL ^ E_NOTICE);

    $basis_options = "<option value='%'>%</option><option value='FLAT'>FLAT</option>";

    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FDEBD0;";


    $con = pdo_con();

    print '<rows>';
    print '<head>';


    //========== NORMAL ADULT ========================
    $caption_adult = "Normal Adult";
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print '<column width="55" id="normal_adult_' . $buycurrencyid . '" type="edn" '
                . 'align="center"  sort="na">' . $caption_adult . '</column>  ';
        $caption_adult = "#cspan";
    }

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print '<column width="55" id="normal_adult_' . $sellcurrencyid . '" type="edn" '
                . 'align="center"  sort="na">' . $caption_adult . '</column>  ';
    }


    //========== ADDITIONAL ADULT ========================

    print '<column width="60" id="additional_adult_basis" type="combo" '
            . 'align="center"  sort="na">Addtional Adult</column>  ';

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print '<column width="55" id="additional_adult_value_' . $buycurrencyid . '" type="edn" '
                . 'align="center" sort="na">#cspan</column>  ';
    }

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print '<column width="55" id="additional_adult_value_' . $sellcurrencyid . '" type="edn" '
                . 'align="center" sort="na">#cspan</column>  ';
    }



    //========== ADDITIONAL CHILD ========================

    for ($i = 0; $i < count($arr_child_agecodes); $i++) {
        $agefrom = $arr_child_agecodes[$i]["AGEFROM"];
        $ageto = $arr_child_agecodes[$i]["AGETO"];

        print '<column width="60" id="additional_child_basis_' . $agefrom . '_' . $ageto . '" type="combo" '
                . 'align="center"  sort="na">Addtional Child (' . $agefrom . '-' . $ageto . ')</column>  ';

        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print '<column width="55" id="additional_child_value_' . $buycurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print '<column width="55" id="additional_child_value_' . $sellcurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }
    }


    print '<beforeInit>';
    
    //===============================================
    
    print '<call command="attachHeader">';
    print '<param>';


    //========== NORMAL ADULT ===================
    $adult_caption = "Buying";
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print $adult_caption . ',';
        $adult_caption = "#cspan";
    }
    
    $adult_caption = "Selling";
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print $adult_caption . ',';
        $adult_caption = "#cspan";
    }

    //========== ADDITIONAL ADULT ===================


    print 'Basis';
    
    $add_adult_caption = "Buying";
    
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print ',' . $add_adult_caption;
        $add_adult_caption = "#cspan";
    }
    
    $add_adult_caption = "Selling";

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print ',' . $add_adult_caption;
        $add_adult_caption = "#cspan";
    }

    //========== ADDITIONAL CHILD ===================
    
    for ($i = 0; $i < count($arr_child_agecodes); $i++) {
        print ',Basis';
        $child_caption = "Buying";
        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print ',' . $child_caption;
            $child_caption = "#cspan";
        }
        
        $child_caption = "Selling";
        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print ',' . $child_caption;
            $child_caption = "#cspan";
        }
    }
    print '</param>';
    print '</call>';
    
    
    //==============================================
    
    print '<call command="attachHeader">';
    print '<param>';


    //========== NORMAL ADULT ===================
    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print $buycurrencycode . ',';
    }

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print $sellcurrencycode . ',';
    }

    //========== ADDITIONAL ADULT ===================


    print '#rspan';

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print ',' . $buycurrencycode;
    }

    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print ',' . $sellcurrencycode;
    }

    //========== ADDITIONAL CHILD ===================

    for ($i = 0; $i < count($arr_child_agecodes); $i++) {
        print ',#rspan';
        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print ',' . $buycurrencycode;
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print ',' . $sellcurrencycode;
        }
    }
    print '</param>';
    print '</call>';
    
    print '</beforeInit>';


    print '<settings><colwidth>px</colwidth></settings> ';

    print '</head>';

    print "<row id='units'>";
    
    //=========== NORMAL ADULT =========================

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print "<cell type='edn' align='right' context='normal_adult_value' variant='UNITS' "
                . "sort='na' category='normal_adult_value' "
                . " buy_sell='buy' "
                . " currencyid='$buycurrencyid' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle'></cell>";
    }
    
    
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print "<cell type='ron' align='right' context='normal_adult_value' variant='UNITS' "
                . "sort='na' category='normal_adult_value' "
                . " buy_sell='sell' "
                . " currencyid='$sellcurrencyid' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle $cellstylelocked'></cell>";
    }
    
    //=========== ADDITIONAL ADULT =========================
    

    print "<cell type='combo' "
            . "align='center' context='additional_adult_basis' variant='UNITS' "
            . "sort='na' category='additional_adult_basis' "
            . " currencyid='' buy_sell='' "
            . " xmlcontent=\"true\" editable=\"0\" agefrom='0' ageto='0' "
            . "style='$cellstyle'>$basis_options</cell>";

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print "<cell type='edn' align='right' "
                . "context='additional_adult_value' variant='UNITS' "
                . "sort='na' category='additional_adult_value' "
                . "currencyid='$buycurrencyid' "
                . " buy_sell='buy' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle'></cell>";
    }
    
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];

        print "<cell type='ron' align='right' "
                . "context='additional_adult_value' variant='UNITS' "
                . "sort='na' category='additional_adult_value' "
                . "currencyid='$sellcurrencyid' "
                . " buy_sell='sell' "
                . " agefrom='0' ageto='0' "
                . "style='$cellstyle $cellstylelocked'></cell>";
    }

    
    //=========== ADDITIONAL CHILDREN =========================
    
    for ($i = 0; $i < count($arr_child_agecodes); $i++) {
        $agefrom = $arr_child_agecodes[$i]["AGEFROM"];
        $ageto = $arr_child_agecodes[$i]["AGETO"];

        print "<cell type='combo' "
                . "align='center' context='additional_child_basis' variant='UNITS' "
                . "sort='na' category='additional_child_basis' "
                . " xmlcontent=\"true\" editable=\"0\" agefrom='$agefrom' ageto='$ageto' "
                . " currencyid='' buy_sell='' "
                . "style='$cellstyle'>$basis_options</cell>";

        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print "<cell type='edn' align='right' "
                    . "context='additional_child_value' variant='UNITS' "
                    . "sort='na' category='additional_child_value' "
                    . "currencyid='$buycurrencyid' "
                    . "agefrom='$agefrom' ageto='$ageto' "
                    . " buy_sell='buy' "
                    . "style='$cellstyle'></cell>";
        }
        
        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print "<cell type='ron' align='right' "
                    . "context='additional_child_value' variant='UNITS' "
                    . "sort='na' category='additional_child_value' "
                    . "currencyid='$sellcurrencyid' "
                    . "agefrom='$agefrom' ageto='$ageto' "
                    . " buy_sell='sell' "
                    . "style='$cellstyle $cellstylelocked'></cell>";
        }
        
    }
    
    print "</row>";

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}

function getChildAgesArray($child_ages_ids, $con) {
    $arrages = array();

    $sql = "select * from tblchildrenagerange where "
            . "id in ($child_ages_ids) order by agefrom, ageto";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arrages[] = array("AGEFROM" => $rw["agefrom"], "AGETO" => $rw["ageto"], "ID" => $rw["id"]);
    }

    return $arrages;
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


