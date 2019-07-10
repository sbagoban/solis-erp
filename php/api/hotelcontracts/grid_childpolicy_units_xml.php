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

    $roomid = $_GET["roomid"];
    $arr_childages_count = json_decode($_GET["arr_childages_count"], true);
    $max_child_count = $_GET["max_child_count"];
    $selected_currency_buy_ids = $_GET["selected_currency_buy_ids"];
    $selected_currency_sell_ids = $_GET["selected_currency_sell_ids"];
    $costprice_currencyid = $_GET["costprice_currencyid"];
    $child_mode = $_GET["child_mode"];

    $con = pdo_con();

    $arr_currency_buy = getCurrencyArray($selected_currency_buy_ids, $con);
    $arr_currency_sell = getCurrencyArray($selected_currency_sell_ids, $con);
    $arr_currency_cost = getCurrencyArray($costprice_currencyid, $con);


    error_reporting(E_ALL ^ E_NOTICE);

    $basis_options = "<option value='%'>%</option><option value='FLAT'>FLAT</option>";

    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FDEBD0;";




    print '<rows>';
    print '<head>';



    print '<column width="100" id="number" type="ro" align="center" sort="na">Number</column>';


    for ($i = 0; $i < count($arr_childages_count); $i++) {
        $agefrom = $arr_childages_count[$i]["age_from"];
        $ageto = $arr_childages_count[$i]["age_to"];

        print '<column width="65" id="basis_' . $agefrom . '_' . $ageto . '" type="combo" '
                . 'align="center"  sort="na">Child (' . $agefrom . '-' . $ageto . ')</column>  ';

        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print '<column width="55" id="value_' . $buycurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print '<column width="55" id="value_' . $sellcurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }
    }

    print '<beforeInit>';

    print '<call command="attachHeader">';
    print '<param>';

    

    print '#rspan';
    
    for ($i = 0; $i < count($arr_childages_count); $i++) {
        
        print ',Basis';
        
        $caption = "Buying";
        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];
            print ',' . $caption;
            $caption = "#cspan";
        }


        $caption = "Selling";
        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print ',' . $caption;
            $caption = "#cspan";
        }
    }

    print '</param>';
    print '</call>';

    //===================

    print '<call command="attachHeader">';
    print '<param>';

    print '#rspan';


    for ($i = 0; $i < count($arr_childages_count); $i++) {
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

    for ($chindex = 1; $chindex <= $max_child_count; $chindex++) {
        print "<row id='number_$chindex'>";

        print "<cell type='ro' align='center' context='number'  "
                . "sort='na' number='$chindex' agefrom='0' ageto='0' "
                . "currencyid='' buy_sell='' "
                . "max_count='' min_count='' "
                . "style='$cellstyle $cellstylelocked'>Extra " . decideNumberCaption($chindex) . " Child</cell>";


        for ($i = 0; $i < count($arr_childages_count); $i++) {
            $agefrom = $arr_childages_count[$i]["age_from"];
            $ageto = $arr_childages_count[$i]["age_to"];
            $maxcount = $arr_childages_count[$i][$child_mode]["max_child"];
            $mincount = $arr_childages_count[$i][$child_mode]["min_child"];

            $arr_basis = decideBaisCell($chindex, $maxcount);

            print "<cell type='" . $arr_basis["CELLTYPE_COMBO"] . "' "
                    . "align='center' context='basis'  "
                    . "sort='na' number='$chindex' agefrom='$agefrom' ageto='$ageto' "
                    . "currencyid='' buy_sell=''  "
                    . $arr_basis["XML"]
                    . "max_count='$maxcount' min_count='$mincount' "
                    . " style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . "'>" . $arr_basis["OPTIONS"] . "</cell>";

            for ($j = 0; $j < count($arr_currency_buy); $j++) {
                $buycurrencyid = $arr_currency_buy[$j]["ID"];
                $buycurrencycode = $arr_currency_buy[$j]["CODE"];

                print "<cell type='" . $arr_basis["CELLTYPE_EDN"] . "' "
                        . "align='center' context='value'  "
                        . " buy_sell='buy' "
                        . "sort='na' number='$chindex' agefrom='$agefrom' ageto='$ageto' "
                        . "currencyid='" . $buycurrencyid . "'  "
                        . "max_count='$maxcount' min_count='$mincount' "
                        . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . "'></cell>";
            }

            for ($j = 0; $j < count($arr_currency_sell); $j++) {
                $sellcurrencyid = $arr_currency_sell[$j]["ID"];
                $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

                print "<cell type='ron' "
                        . "align='center' context='value' buy_sell='sell' "
                        . "sort='na' number='$chindex' "
                        . " agefrom='$agefrom' ageto='$ageto' "
                        . "currencyid='" . $sellcurrencyid . "'  "
                        . "max_count='$maxcount' min_count='$mincount' "
                        . "style='$cellstyle $cellstylelocked'></cell>";
            }
        }




        print "</row>";
    }

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}

function decideNumberCaption($i) {
    if ($i == 1) {
        return "1st";
    } else if ($i == 2) {
        return "2nd";
    } else if ($i == 3) {
        return "3rd";
    } else if ($i == 4) {
        return "4th";
    } else if ($i >= 5) {
        return "{$i}th";
    }
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

function decideBaisCell($chindex, $count) {
    global $cellstylelocked;
    global $basis_options;

    $celltype_combo = "ro";
    $celltype_edn = "ro";
    $options = "";
    $xml = "";
    $cellstyleadd = $cellstylelocked;

    if ($chindex <= $count) {
        $celltype_combo = "combo";
        $celltype_edn = "edn";
        $options = $basis_options;
        $xml = " xmlcontent=\"true\" editable=\"0\" ";
        $cellstyleadd = "";
    }

    return array("CELLTYPE_COMBO" => $celltype_combo,
        "CELLTYPE_EDN" => $celltype_edn,
        "OPTIONS" => $options, "XML" => $xml,
        "CELLSTYLELOCKED" => $cellstyleadd);
}
?>


