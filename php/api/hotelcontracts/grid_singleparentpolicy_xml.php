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

    $arr_main_childages = json_decode($_GET["arr_main_childages"], true);
    $arr_result = json_decode($_GET["arr_result"], true);

    $selected_currency_buy_ids = $_GET["selected_currency_buy_ids"];
    $selected_currency_sell_ids = $_GET["selected_currency_sell_ids"];
    $costprice_currencyid = $_GET["costprice_currencyid"];

    $arr_currency_buy = getCurrencyArray($selected_currency_buy_ids, $con);
    $arr_currency_sell = getCurrencyArray($selected_currency_sell_ids, $con);
    $arr_currency_cost = getCurrencyArray($costprice_currencyid, $con);


    error_reporting(E_ALL ^ E_NOTICE);

    $category_options_adult = "<option value='SINGLE'>SINGLE</option><option value='1/2 DBL'>1/2 DBL</option>";
    $category_options_child = "<option value='SINGLE'>SINGLE</option><option value='DOUBLE'>DOUBLE</option><option value='1/2 DBL'>1/2 DBL</option><option value='TRPL'>TRIPLE</option><option value='SHARING'>SHARING</option>";
    
    $basis_options = "<option value='%'>%</option><option value='FLAT'>FLAT</option>";

    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FDEBD0;";


    $con = pdo_con();

    print '<rows>';
    print '<head>';


    //=============================================================
    //ADULTS

    print '<column width="80" id="adult_category" type="ro" align="center" sort="na">Adult</column>';
    print '<column width="44" id="adult_basis" type="ro" align="center" sort="na">#cspan</column>';


    for ($j = 0; $j < count($arr_currency_buy); $j++) {
        $buycurrencyid = $arr_currency_buy[$j]["ID"];
        $buycurrencycode = $arr_currency_buy[$j]["CODE"];

        print '<column width="40" id="adult_value_' . $buycurrencyid . '" type="edn" '
                . 'align="center" sort="na">#cspan</column>  ';
    }

    for ($j = 0; $j < count($arr_currency_sell); $j++) {
        $sellcurrencyid = $arr_currency_sell[$j]["ID"];
        $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

        print '<column width="40" id="adult_value_' . $sellcurrencyid . '" type="edn" '
                . 'align="center" sort="na">#cspan</column>  ';
    }


    //=============================================================
    //CHILDREN

    print '<column width="80" id="child_number" type="ro" align="center" sort="na">Children</column>';

    for ($i = 0; $i < count($arr_main_childages); $i++) {

        $agefrom = $arr_main_childages[$i]["age_from"];
        $ageto = $arr_main_childages[$i]["age_to"];

        print '<column width="80" id="child_category_' . $agefrom . '_' . $ageto . '" type="combo" '
                . 'align="center"  sort="na">#cspan</column>  ';

        print '<column width="44" id="child_basis_' . $agefrom . '_' . $ageto . '" type="combo" '
                . 'align="center"  sort="na">#cspan</column>  ';


        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];
            $buycurrencycode = $arr_currency_buy[$j]["CODE"];

            print '<column width="40" id="value_' . $buycurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];
            $sellcurrencycode = $arr_currency_sell[$j]["CODE"];

            print '<column width="40" id="value_' . $sellcurrencyid . '_' . $agefrom . '_' . $ageto . '" type="edn" '
                    . 'align="center" sort="na">#cspan</column>  ';
        }
    }

    //===================================================================

    print '<beforeInit>';
    
    print '<call command="attachHeader">';
    print '<param>';

    //========================================================
    //ADULT

    print 'Category,Basis';
    
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


    //=======================================================
    //CHILDREN

    print ',Number';
    for ($i = 0; $i < count($arr_main_childages); $i++) {
        $agefrom = $arr_main_childages[$i]["age_from"];
        $ageto = $arr_main_childages[$i]["age_to"];

        print ',' . $agefrom . ' - ' . $ageto;
        print ',#cspan';

        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            print ',#cspan';
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            print ',#cspan';
        }
    }
    //===========================================================


    print '</param>';
    print '</call>';
    
    
    //=======================================================
    //=======================================================
    //=======================================================
    
    
    print '<call command="attachHeader">';
    print '<param>';

    //========================================================
    //ADULT

    print '#rspan,#rspan';

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


    //=======================================================
    //CHILDREN

    print ',#rspan';
    for ($i = 0; $i < count($arr_main_childages); $i++) {
        $agefrom = $arr_main_childages[$i]["age_from"];
        $ageto = $arr_main_childages[$i]["age_to"];

        print ',Category';
        print ',Basis';
        
        $caption = "Buying";
        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            print ',' . $caption;
            $caption = "#cspan";
        }
        
        $caption = "Selling";
        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            print ',' . $caption;
            $caption = "#cspan";
        }
    }
    //===========================================================


    print '</param>';
    print '</call>';



    print '<call command="attachHeader">';
    print '<param>';

    //=========================================================
    //ADULT
    print '#rspan,#rspan';

    for ($j = 0; $j < count($arr_currency_buy); $j++) {
        print ',#rspan';
    }

    for ($j = 0; $j < count($arr_currency_sell); $j++) {
        print ',#rspan';
    }


    //===============================================================
    //CHILDREN
    print ',#rspan';

    for ($i = 0; $i < count($arr_main_childages); $i++) {
        print ',#rspan,#rspan';

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
        return "First";
    } else if ($i == 2) {
        return "Second";
    } else if ($i == 3) {
        return "Third";
    } else if ($i == 4) {
        return "Fourth";
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

function decideCellStyle($chindex, $main_agefrom, $main_ageto, $ruleindex) {
    global $cellstylelocked;
    global $basis_options;
    global $category_options_child;
    global $arr_result;

    $celltype_combo = "ro";
    $celltype_edn = "ro";
    $mybasis_options = "";
    $mycategory_options = "";
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
                $mycategory_options = $category_options_child;
                $xml = " xmlcontent=\"true\" editable=\"0\" ";
                $cellstyleadd = "";
            }
        }
    }

    return array("CELLTYPE_COMBO" => $celltype_combo,
        "CELLTYPE_EDN" => $celltype_edn,
        "BASISOPTIONS" => $mybasis_options,
        "CATEGORYOPTIONS" => $mycategory_options,
        "XML" => $xml,
        "CELLSTYLELOCKED" => $cellstyleadd);
}

function printRuleCells($ruleindex) {

    global $arr_result;
    global $arr_currency_buy;
    global $arr_currency_sell;
    global $arr_main_childages;
    global $category_options_adult;
    global $basis_options;
    global $cellstyle;
    global $cellstylelocked;



    //get the max child count of that rule
    $rule_ageranges = ";";
    $max_child = 0;
    for ($i = 0; $i < count($arr_result[$ruleindex]["children_ages"]); $i++) {

        $capacity_child_agefrom = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_agefrom"];
        $capacity_child_ageto = $arr_result[$ruleindex]["children_ages"][$i]["capacity_child_ageto"];
        $rule_ageranges .= "{$capacity_child_agefrom}_{$capacity_child_ageto};";
        $child_max = $arr_result[$ruleindex]["children_ages"][$i]["capacity_maxpax"];
        if ($max_child < $child_max) {
            $max_child = $child_max;
        }
    }

    for ($chindex = 1; $chindex <= $max_child; $chindex++) {

        $lower_border_style = "";
        if ($chindex == $max_child) {
            $lower_border_style = " border-bottom: 3px solid #A4A4A4;";
        }
        print "<row id='id_{$ruleindex}_{$chindex}' >";
        
        //===================================================================
        //ADULT
        
        $rowspan = "";
        if($chindex == 1)
        {
            $rowspan = " rowspan='$max_child' ";
        }

        print "<cell type='combo' align='center' context='category'  "
                . "sort='na' child_index='$chindex' agefrom='0' ageto='0' "
                . "currencyid='' buy_sell='' "
                . "adult_child='ADULT' "
                . "rule_index='$ruleindex' "
                //. "$rowspan "
                . "rule_ageranges='$rule_ageranges'  "
                . "xmlcontent=\"true\"  editable=\"0\" "
                . "style='$cellstyle $lower_border_style'>$category_options_adult</cell>";

        print "<cell type='combo' align='center' context='basis'  "
                . "sort='na' child_index='$chindex' agefrom='0' ageto='0' "
                . "currencyid='' buy_sell=''  "
                . "adult_child='ADULT' "
                . "rule_index='$ruleindex' "
                . "rule_ageranges='$rule_ageranges'  "
                . "xmlcontent=\"true\" editable=\"0\" "
                //. "$rowspan "
                . "style='$cellstyle $lower_border_style'>$basis_options</cell>";


        for ($j = 0; $j < count($arr_currency_buy); $j++) {
            $buycurrencyid = $arr_currency_buy[$j]["ID"];

            print "<cell type='edn' "
                    . "align='center' context='value'  "
                    . "sort='na' child_index='$chindex' agefrom='0' ageto='0' "
                    . "currencyid='" . $buycurrencyid . "' buy_sell='buy' "
                    . "adult_child='ADULT' "
                    . "rule_index='$ruleindex' "
                    //. "$rowspan "
                    . "rule_ageranges='$rule_ageranges'  "
                    . "style='$cellstyle $lower_border_style'></cell>";
        }

        for ($j = 0; $j < count($arr_currency_sell); $j++) {
            $sellcurrencyid = $arr_currency_sell[$j]["ID"];

            print "<cell type='ron' "
                    . "align='center' context='value'  "
                    . "sort='na' child_index='$chindex' agefrom='0' ageto='0' "
                    . "currencyid='" . $sellcurrencyid . "' buy_sell='sell' "
                    . "adult_child='ADULT' "
                    . "rule_index='$ruleindex' "
                    //. "$rowspan "
                    . "rule_ageranges='$rule_ageranges'  "
                    . "style='$cellstyle $cellstylelocked $lower_border_style'></cell>";
        }


        //=============================================================================
        //CHILD

        print "<cell type='ro' align='center' context='number'  "
                . "sort='na' category='caption' agefrom='0' ageto='0' "
                . "currencyid='' buy_sell='' "
                . "adult_child='CHILD' child_index='$chindex' "
                . "rule_ageranges='$rule_ageranges'  "
                . "style='$cellstyle $cellstylelocked $lower_border_style'>" . decideNumberCaption($chindex) . " Child</cell>";


        for ($i = 0; $i < count($arr_main_childages); $i++) {

            $agefrom = $arr_main_childages[$i]["age_from"];
            $ageto = $arr_main_childages[$i]["age_to"];

            $arr_basis = decideCellStyle($chindex, $agefrom, $ageto, $ruleindex);

            print "<cell type='" . $arr_basis["CELLTYPE_COMBO"] . "' align='center' "
                    . "context='category'  "
                    . "sort='na' child_index='$chindex' agefrom='$agefrom' ageto='$ageto' "
                    . "currencyid='' buy_sell='' "
                    . "adult_child='CHILD' "
                    . $arr_basis["XML"]
                    . "rule_ageranges='$rule_ageranges' "
                    . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . " $lower_border_style'>"
                    . $arr_basis["CATEGORYOPTIONS"] . "</cell>";

            print "<cell type='combo' align='center' "
                    . "context='basis'  "
                    . "sort='na' child_index='$chindex' agefrom='$agefrom' ageto='$ageto' "
                    . "currencyid='' buy_sell=''  "
                    . "adult_child='CHILD' "
                    . $arr_basis["XML"]
                    . "rule_ageranges='$rule_ageranges'  "
                    . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . " $lower_border_style'>"
                    . $arr_basis["BASISOPTIONS"] . "</cell>";



            for ($j = 0; $j < count($arr_currency_buy); $j++) {
                $buycurrencyid = $arr_currency_buy[$j]["ID"];

                print "<cell type='" . $arr_basis["CELLTYPE_EDN"] . "' "
                        . "align='center' context='value'  "
                        . "sort='na' child_index='$chindex' "
                        . "agefrom='$agefrom' ageto='$ageto' "
                        . "currencyid='" . $buycurrencyid . "' buy_sell='buy' "
                        . "adult_child='CHILD' "
                        . "rule_ageranges='$rule_ageranges'  "
                        . "style='$cellstyle " . $arr_basis["CELLSTYLELOCKED"] . " $lower_border_style'></cell>";
            }


            for ($j = 0; $j < count($arr_currency_sell); $j++) {
                $sellcurrencyid = $arr_currency_sell[$j]["ID"];

                print "<cell type='ron' "
                        . "align='center' context='value'  "
                        . "sort='na' child_index='$chindex' "
                        . "agefrom='$agefrom' ageto='$ageto' "
                        . "currencyid='" . $sellcurrencyid . "' buy_sell='sell'  "
                        . "adult_child='CHILD' "
                        . "rule_ageranges='$rule_ageranges'  "
                        . "style='$cellstyle $cellstylelocked $lower_border_style'></cell>";
            }
        }
        print "</row>";
    }
}
?>


