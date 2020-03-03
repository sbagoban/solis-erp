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

    $selected_currency_buy_ids = $_GET["selected_currency_buy_ids"];
    $arr_settings_data = json_decode($_GET["arr_settings_data"], true);


    require_once("../../connector/pdo_connect_main.php");
    require_once("../../globalvars/globalvars.php");

    error_reporting(E_ALL ^ E_NOTICE);

    global $__arr_alphabets;

    $con = pdo_con();

    $arr_currency_buy = getCurrencyArray($selected_currency_buy_ids, $con);

    //============== COMBO ELEMENTS ======================
    $rounding_options = "<option value='ROUND'>ROUND</option>" .
            "<option value='ROUNDUP'>ROUND UP</option>" .
            "<option value='ROUNDDOWN'>ROUND DOWN</option>" .
            "<option value='NOROUND'>NO ROUND</option>";

    $tax_codes_options = loadTaxCodes($con);

    $basis_options = "<option value='% PPPN ALL'>% PPPN ALL</option>" .
                     "<option value='% PPPN ROOM'>% PPPN ROOM</option>" .
                     "<option value='% PNI ALL'>% PNI ALL</option>" .
                     "<option value='% PNI ROOM'>% PNI ROOM</option>" .
                     "<option value='FLAT PPPN ALL'>FLAT PPPN ALL </option>" . 
                     "<option value='FLAT PPPN ROOM'>FLAT PPPN ROOM </option>" .
                     "<option value='FLAT PNI'>FLAT PNI </option>";
                     

    //====================================================


    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstyle_bold = "font-weight:bold; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstylelocked = "background-color:#FBEDD2;";

    print '<rows>';

    print '<head>';


    print '<column width="30" id="setting_row_index" type="ro" align="center" sort="na"></column>';
    print '<column width="200" id="setting_item_name" type="ro" align="center" sort="na">Item</column>';
    print '<column width="150" id="basis" type="ro" align="center" sort="na">Basis</column>';

    for ($i = 0; $i < count($arr_currency_buy); $i++) {
        $buycurrencyid = $arr_currency_buy[$i]["ID"];
        $buycurrencycode = $arr_currency_buy[$i]["CODE"];

        print '<column width="80" id="setting_values_' . $buycurrencyid . '" type="ro" '
                . 'align="center"  sort="na">' . $buycurrencycode . ' / %</column>  ';
    }

    print '<column width="150" id="setting_applyon_formula" type="ro" align="left" sort="na">Apply On Row / Formula</column>';
    print '<column width="130" id="setting_rounding" type="ro" align="center" sort="na">Rounding</column>';

    print '<settings><colwidth>px</colwidth></settings> ';

    print '</head>';


    //load the data now

    for ($d = 0; $d < count($arr_settings_data); $d++) {
        $setting_rwid = $arr_settings_data[$d]["setting_rwid"];
        $setting_buying_selling = $arr_settings_data[$d]["setting_buying_selling"];
        $setting_row_index = $arr_settings_data[$d]["setting_row_index"];
        $setting_item_fk = $arr_settings_data[$d]["setting_item_fk"];
        $setting_item_name = $arr_settings_data[$d]["setting_item_name"];
        $setting_item_abbrv = $arr_settings_data[$d]["setting_item_abbrv"];
        $setting_item_code = $arr_settings_data[$d]["setting_item_code"];
        $setting_core_addon = $arr_settings_data[$d]["setting_core_addon"];
        $setting_basis = $arr_settings_data[$d]["setting_basis"];
        $setting_applyon_formula = $arr_settings_data[$d]["setting_applyon_formula"];
        $setting_rounding = $arr_settings_data[$d]["setting_rounding"];
        $setting_action = $arr_settings_data[$d]["setting_action"];
        $setting_values = $arr_settings_data[$d]["setting_values"];

        if ($setting_action != "DELETE") {
            print "<row id='$setting_rwid'>";
            
            $colo = "color:blue;";
            if($setting_core_addon == "ADDON")
            {
                $colo = "color:green;";
            }
            
            print "<cell type='ro' align='center' "
                    . "rowindex='$setting_row_index' sort='na' "
                    . "context='setting_row_index' "
                    . "currencyfk='' "
                    . "itemfk='$setting_item_fk' "
                    . "core_addon='$setting_core_addon' "
                    . "style='$cellstyle_bold'>" . $__arr_alphabets[$setting_row_index] . "</cell>";

            print "<cell type='ro' align='left' "
                    . "rowindex='$setting_row_index' sort='na' "
                    . "context='setting_item_name' "
                    . "currencyfk='' "
                    . "itemfk='$setting_item_fk' "
                    . "core_addon='$setting_core_addon' "
                    . "style='$cellstyle $cellstylelocked $colo'>$setting_item_name</cell>";

            
            
            $arr_opts = determineCellStyle($setting_core_addon,  
                                           "setting_basis", "combo",
                                           "xmlcontent=\"true\" editable=\"0\" ",$basis_options);            
            print "<cell type='" . $arr_opts["celltype"] . "' align='center' "
                    . "rowindex='$setting_row_index' sort='na' "
                    . "context='setting_basis' "
                    . "currencyfk='' "
                    . "itemfk='$setting_item_fk' "
                    . "core_addon='$setting_core_addon' "
                    . $arr_opts["xml"]
                    . "style='" . $arr_opts["style"] . "'>" . $setting_basis . $arr_opts["options"] . "</cell>";

            
            
            for ($i = 0; $i < count($arr_currency_buy); $i++) {
                $buycurrencyid = $arr_currency_buy[$i]["ID"];
                $buycurrencycode = $arr_currency_buy[$i]["CODE"];
                
                $arr_opts = determineCellStyle($setting_core_addon, 
                                           "setting_values", "edn","","");            
                
                print "<cell type='" . $arr_opts["celltype"] . "' align='center' "
                        . "rowindex='$setting_row_index' sort='na' "
                        . "context='setting_values' "
                        . "currencyfk='$buycurrencyid' "
                        . "itemfk='$setting_item_fk' "
                        . "core_addon='$setting_core_addon' "
                        . "style='" . $arr_opts["style"] . "'>" . getValue($setting_values, $buycurrencyid) . "</cell>";
                 
                
                 
            }
            
            
            $arr_opts = determineCellStyle($setting_core_addon, 
                                           "setting_applyon_formula", "ed","","");            
            
            print "<cell type='" . $arr_opts["celltype"] . "' align='left' "
                    . "rowindex='$setting_row_index' sort='na' "
                    . "context='setting_applyon_formula' "
                    . "currencyfk='' "
                    . "itemfk='$setting_item_fk' "
                    . "core_addon='$setting_core_addon' "
                    . "style='" . $arr_opts["style"] . "'><![CDATA[$setting_applyon_formula]]></cell>";

            
            $arr_opts = determineCellStyle($setting_core_addon, 
                                           "setting_rounding", "combo",
                                           "xmlcontent=\"true\" editable=\"0\" ",$rounding_options);            
            
            print "<cell type='" . $arr_opts["celltype"] . "' align='center' "
                    . "rowindex='$setting_row_index' sort='na' "
                    . "context='setting_rounding' "
                    . "currencyfk='' "
                    . "itemfk='$setting_item_fk' "
                    . "core_addon='$setting_core_addon' "
                    . $arr_opts["xml"]
                    . "style='" . $arr_opts["style"] . "'>" . $setting_rounding . $arr_opts["options"] . "</cell>";
            
            print "</row>";
        }
    }

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}

function loadTaxCodes($con) {
    $options = "";

    $sql = "select * from tbltaxcodes order by txcode";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='" . $rw["id"] . "'>" . $rw["txcode"] . " - " . $rw["txdescription"] . "</option>";
    }

    return $options;
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

function getValue($setting_values, $currencyid) {

    for ($i = 0; $i < count($setting_values); $i++) {
        if ($currencyid == $setting_values[$i]["value_currency_fk"]) {
            return $setting_values[$i]["value_value"];
        }
    }

    return "";
}

function determineCellStyle($setting_core_addon, $context, $defaultcelltype, $xmltrue, $xmloptions) {
    global $cellstyle;
    global $cellstylelocked;
    


    if ($context == "setting_basis") {
        if ($setting_core_addon == "CORE") {
            return array("style" => "$cellstyle $cellstylelocked", "locked" => 1,
                "celltype" => "ro", "xml" => "",
                "options" => "");
        } else if ($setting_core_addon == "ADDON") {
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);
        }
    } else if ($context == "setting_values") {
        if ($setting_core_addon == "CORE") {
            return array("style" => "$cellstyle $cellstylelocked", "locked" => 1,
                "celltype" => "ro", "xml" => "",
                "options" => "");
        } else if ($setting_core_addon == "ADDON") {
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);
        }
    } else if ($context == "setting_applyon_formula") {
        if ($setting_core_addon == "CORE") {
            
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);

        } else if ($setting_core_addon == "ADDON") {
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);
        }
    } else if ($context == "setting_rounding") {
        if ($setting_core_addon == "CORE") {
            
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);

        } else if ($setting_core_addon == "ADDON") {
            return array("style" => "$cellstyle", "locked" => 0,
                "celltype" => $defaultcelltype,
                "xml" => $xmltrue,
                "options" => $xmloptions);
        }
    }
}
?>


