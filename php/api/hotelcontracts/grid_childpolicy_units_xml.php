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

    $roomid = $_GET["roomid"];
    $arr_main_childages = json_decode($_GET["arr_main_childages"], true);
    $arr_result = json_decode($_GET["arr_result"], true);
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

    
    for ($i = 0; $i < count($arr_main_childages); $i++) {
        $agefrom = $arr_main_childages[$i]["age_from"];
        $ageto = $arr_main_childages[$i]["age_to"];

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
    
    for ($i = 0; $i < count($arr_main_childages); $i++) {
        
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


    for ($i = 0; $i < count($arr_main_childages); $i++) {
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
    
    for ($ruleindex = 0; $ruleindex < count($arr_result); $ruleindex++) {

        printRuleCells($ruleindex);
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

function decideBaisCell($chindex, $main_agefrom, $main_ageto, $ruleindex) {
    global $cellstylelocked;
    global $basis_options;
    global $arr_result;
    
    $celltype_combo = "ro";
    $celltype_edn = "ro";
    $mybasis_options = "";
    $xml = "";
    $cellstyleadd = $cellstylelocked;

    for ($i = 0; $i < count($arr_result[$ruleindex]["children_ages"]); $i++) {
        
        $capacity_child_agefrom = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_agefrom"];
        $capacity_child_ageto = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_ageto"];
        $capacity_maxpax = $arr_result[$ruleindex]["children_ages"][$i]["capacity_maxpax"];

        if ($main_agefrom >= $capacity_child_agefrom && 
            $main_ageto <= $capacity_child_ageto) {

            if ($chindex <= $capacity_maxpax) {
                $celltype_combo = "combo";
                $celltype_edn = "edn";
                $mybasis_options = $basis_options;
                $xml = " xmlcontent=\"true\" editable=\"0\" ";
                $cellstyleadd = "";
            }
        }
    }

   

    return array("CELLTYPE_COMBO" => $celltype_combo,
        "CELLTYPE_EDN" => $celltype_edn,
        "OPTIONS" => $mybasis_options, "XML" => $xml,
        "CELLSTYLELOCKED" => $cellstyleadd);
}


function printRuleCells($ruleindex) {

    global $arr_result;
    global $arr_currency_buy;
    global $arr_currency_sell;
    global $arr_main_childages;
    global $cellstyle;
    global $cellstylelocked;
    
    //get the max child count of that rule
    $rule_ageranges = ";";
    $max_child = 0;
    for ($i = 0; $i < count($arr_result[$ruleindex]["children_ages"]); $i++) {

        $capacity_child_agefrom = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_agefrom"];
        $capacity_child_ageto = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_ageto"];
        
        
        $child_max = $arr_result[$ruleindex]["children_ages"][$i]["capacity_maxpax"];
        $child_min = $arr_result[$ruleindex]["children_ages"][$i]["capacity_minpax"];
        
        $rule_ageranges .= "{$capacity_child_agefrom}_{$capacity_child_ageto}:{$child_min}^{$child_max};";
        
        if ($max_child < $child_max) {
            $max_child = $child_max;
        }
        
    }
    
    for ($chindex = 1; $chindex <= $max_child; $chindex++) {

        $lower_border_style = "";
        if ($chindex == $max_child) {
            $lower_border_style = " border-bottom: 3px solid black;";
        }
        
        print "<row id='id_{$ruleindex}_{$chindex}' >";
        
        
        //=============================================================================
        //CHILD

      
        print "<cell type='ro' align='center' context='number'  "
                . "sort='na' number='$chindex' agefrom='0' ageto='0' "
                . "currencyid='' buy_sell='' "
                . "max_count='' min_count='' "
                . "rule_ageranges='$rule_ageranges'  "
                . "style='$cellstyle $cellstylelocked $lower_border_style'>" . decideNumberCaption($chindex) . " Child</cell>";


        for ($i = 0; $i < count($arr_main_childages); $i++) {

            $agefrom = $arr_main_childages[$i]["age_from"];
            $ageto = $arr_main_childages[$i]["age_to"];

            $arr_basis = decideBaisCell($chindex, $agefrom, $ageto, $ruleindex);
            
            print "<cell type='" . $arr_basis["CELLTYPE_COMBO"] . "' "
                    . "align='center' context='basis'  "
                    . "sort='na' number='$chindex' agefrom='$agefrom' ageto='$ageto' "
                    . "currencyid='' buy_sell=''  "
                    . $arr_basis["XML"]
                    . "rule_ageranges='$rule_ageranges'  "
                    . " style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . " $lower_border_style'>" . $arr_basis["OPTIONS"] . "</cell>";
            
            
            for ($j = 0; $j < count($arr_currency_buy); $j++) {
                $buycurrencyid = $arr_currency_buy[$j]["ID"];

                print "<cell type='" . $arr_basis["CELLTYPE_EDN"] . "' "
                        . "align='center' context='value'  "
                        . " buy_sell='buy' "
                        . "sort='na' number='$chindex' agefrom='$agefrom' ageto='$ageto' "
                        . "currencyid='" . $buycurrencyid . "'  "
                        . "rule_ageranges='$rule_ageranges'  "
                        . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . " $lower_border_style'></cell>";
            }

            for ($j = 0; $j < count($arr_currency_sell); $j++) {
                $sellcurrencyid = $arr_currency_sell[$j]["ID"];

                print "<cell type='ron' "
                        . "align='center' context='value' buy_sell='sell' "
                        . "sort='na' number='$chindex' "
                        . " agefrom='$agefrom' ageto='$ageto' "
                        . "rule_ageranges='$rule_ageranges'  "
                        . "currencyid='" . $sellcurrencyid . "'  "
                        . "style='$cellstyle $cellstylelocked $lower_border_style'></cell>";
            }
        }
        print "</row>";
    }
    
}

?>


