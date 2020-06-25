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
    $adult_max = intval($_GET["adult_max"]);
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
    print '<column width="120" id="category" type="ro" '
            . 'align="center"  sort="na">Category</column>  ';

    print '<column width="80" id="basis" type="ro" '
            . 'align="center"  sort="na">Basis</column>  ';

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];
        $caption = "Buying Price";
        if ($i > 0) {
            $caption = "#cspan";
        }

        print '<column width="55" id="value_' . $buycurrencyid . '" type="ro" '
                . 'align="center"  sort="na">' . $caption . '</column>  ';
    }
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sellcurrencyid = $arr_currency_sell[$i]["ID"];
        $sellcurrencycode = $arr_currency_sell[$i]["CODE"];
        $caption = "Final Sell Price";
        if ($i > 0) {
            $caption = "#cspan";
        }

        print '<column width="55" id="value_' . $sellcurrencyid . '" type="ro" '
                . 'align="center"  sort="na">' . $caption . '</column>  ';
    }

    print '<beforeInit>';
    print '<call command="attachHeader">';
    print '<param>';
    print '#rspan,#rspan';
    

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buy_currency_id = $arr_currency_buy[$i]["ID"];
        $buy_currency_code = $arr_currency_buy[$i]["CODE"];

        print ',' . $buy_currency_code;
    }
    
    for ($i = 0; $i < count($arr_currency_sell); $i++) {
        $sell_currency_id = $arr_currency_sell[$i]["ID"];
        $sell_currency_code = $arr_currency_sell[$i]["CODE"];

        print ',' . $sell_currency_code;
    }

    print '</param>';
    print '</call>';
    print '</beforeInit>';

    print '<settings><colwidth>px</colwidth></settings> ';

    print '</head>';

    for ($i = 1; $i <= $adult_max; $i++) {

        print "<row id='category_$i'>";

        print "<cell type='ro' align='center' context='category' variant='PERSONS' "
                . "sort='na' category='$i' agefrom='0' ageto='0' currencyid='' buy_sell='' "
                . "style='$cellstyle $cellstylelocked'>" . decideCategoryCaption($i) . "</cell>";

        $arr_basis = decideBaisCell($i, $cellstylelocked);
        print "<cell type='" . $arr_basis["CELLTYPE"] . "' "
                . "align='center' context='basis' variant='PERSONS' "
                . "sort='na' category='$i' agefrom='0' ageto='0' currencyid='' buy_sell='' "
                . $arr_basis["XML"]
                . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . "'>" .
                $arr_basis["OPTIONS"] . "</cell>";


            for ($j = 0; $j < count($arr_currency_buy); $j++) {
                $buycurrencyid = $arr_currency_buy[$j]["ID"];
                $buycurrencycode = $arr_currency_buy[$j]["CODE"];

                print "<cell type='edn' align='right' context='value' variant='PERSONS' "
                        . "sort='na' category='$i'  buy_sell='buy' "
                        . "currencyid='$buycurrencyid' agefrom='0' ageto='0' "
                        . "style='$cellstyle'></cell>";
            }
        
            for ($j = 0; $j < count($arr_currency_sell); $j++) {
                $sellcurrencyid = $arr_currency_sell[$j]["ID"];
                $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

                print "<cell type='ron' align='right' context='value' variant='PERSONS' "
                        . "sort='na' category='$i' buy_sell='sell' "
                        . "currencyid='$sellcurrencyid' agefrom='0' ageto='0' "
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

function decideCategoryCaption($index) {
    if ($index == 1) {
        return "SINGLE";
    } else if ($index == 2) {
        return "DOUBLE";
    } else if ($index == 3) {
        return "TRIPLE";
    } else if ($index == 4) {
        return "QUADRUPLE";
    } else if ($index >= 5) {
        return "$index-PAX";
    }
}

function decideBaisCell($index, $cellstylelocked) {
    $celltype = "";
    $options = "";
    $xml = " xmlcontent=\"true\" editable=\"0\" ";

    if ($index == 1) {
        $celltype = "ro";
        $xml = "";
    } else if ($index == 2) {
        $celltype = "combo";
        $options = "<option value='1/n'>1/$index</option><option value='n'>$index</option>";
        $cellstylelocked = "";
    } else if ($index >= 3) {
        $celltype = "combo";
        $options = "<option value='1/n'>1/$index</option><option value='n'>$index</option><option value='ADD'>ADD</option>";
        $cellstylelocked = "";
    }


    return array("CELLTYPE" => $celltype, "OPTIONS" => $options, "XML" => $xml,
        "CELLSTYLELOCKED" => $cellstylelocked);
}
?>


