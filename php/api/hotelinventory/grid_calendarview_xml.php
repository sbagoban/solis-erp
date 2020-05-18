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

    mb_internal_encoding("iso-8859-1");
    mb_http_output( "iso-8859-1" );
    ob_start("mb_output_handler");

    if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
        header("Content-type: application/xhtml+xml");
    } else {
        header("Content-type: text/xml");
    }
    echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");


    session_start();

    require_once("../../utils/utilities.php");
    require_once("../../globalvars/globalvars.php");

    if (!isset($_SESSION["solis_userid"])) {
        die("NO LOG IN!");
    }

    if (!isset($_GET["t"])) {
        die("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        die("INVALID TOKEN");
    }

    $focus_month = "";
    if (isset($_GET["focus_month"])) {
        $focus_month = $_GET["focus_month"];
    }

    $focus_year = "";
    if (isset($_GET["focus_year"])) {
        $focus_year = $_GET["focus_year"];
    }
    
    $myicon = "tick.png";
    if (isset($_GET["myicon"])) {
        $myicon = $_GET["myicon"];
    }
    
    $myicon_size = "18px";
    if (isset($_GET["myicon_size"])) {
        $myicon_size = $_GET["myicon_size"];
    }
    
    
    $myicon = "<img height='$myicon_size' width='$myicon_size' src='images/$myicon'>";
    

    $dates = json_decode($_GET["dates"], true);

    $cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
    $cellstyle_title = "font-weight:bold; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4; background-color:#C6E6FC;";
    $cellstylelocked = "background-color:#E7E7E7;";
    $cellstyleenabled = "background-color:white;";
    
    
    
    print '<rows>';
    print '<head>';

    for ($i = 0; $i < count($__arr_days); $i++) {
        print '<column width="120" id="' . $__arr_days [$i]["Short"] . '" type="ro" '
                . 'align="center" sort="na">' . $__arr_days [$i]["Full"] . '</column>  ';
    }
    
    print '</head>';


    //===========================================================================
    if ($focus_month == "" || $focus_year == "") {
        //no focus date yet,
        //so decide from dates in array
        //if not dates in array, then set date to todays month and year

        $arr_focus = _getFocusMonthYear();
        $focus_month = $arr_focus["MONTH_NUM"];
        $focus_year = $arr_focus["YEAR"];
    }

    echo generateMonthCalendar($focus_month, $focus_year);

    print '</rows>';
} catch (Exception $ex) {

    die("ERROR: " . $ex->getMessage());
}

function _getFocusMonthYear() {
    //get the smallest date from the array to load the calendar from there

    global $dates;

    $month_name = "";
    $month_num = "";
    $year = "";

    function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }

    usort($dates, "date_sort");

    if (count($dates) > 0) {
        $month_name = date('F', strtotime($dates[0]));
        $month_num = date('n', strtotime($dates[0]));
        $year = date('Y', strtotime($dates[0]));
    } else {
        //return todays month and year
        $today = date('Y-m-d');
        $month_name = date('F', strtotime($today));
        $month_num = date('n', strtotime($today));
        $year = date('Y', strtotime($today));
    }

    return array("MONTH_NAME" => $month_name, "MONTH_NUM" => $month_num, "YEAR" => $year);
}

function generateMonthCalendar($focus_month, $focus_year) {

    global $__arr_months_long;
    global $__arr_days;
    global $cellstyle;
    global $cellstyle_title;
    global $cellstylelocked;
    global $cellstyleenabled;
    global $myicon;
    
    print "<row id='rw_title'>";

    print "<cell type='ro' align='left' colspan='2' date='' "
            . "style='$cellstyle_title'><![CDATA[<input type='button' value='<< PREV' onclick='hotelinventory_obj.toggleCalendarNextPrev(\"PREV\",$focus_month,$focus_year);'>]]></cell>";
    print "<cell type='ro' align='center'></cell>";

    print "<cell type='combo' align='center' colspan='2' date='' context='month' "
            . " xmlcontent=\"true\" editable=\"0\" "
            . "style='$cellstyle_title'>"
            . generateMonthCombo($focus_month)
            . "</cell>";
    print "<cell type='ro' align='center'></cell>";


    print "<cell type='combo' align='center' date='' colspan='1' context='year' "
            . " xmlcontent=\"true\" editable=\"0\" "
            . "style='$cellstyle_title'>"
            . generateYearCombo($focus_year)
            . "</cell>";

    print "<cell type='ro' align='right' colspan='2' date='' "
            . "style='$cellstyle_title'><![CDATA[<input type='button' value='NEXT >>' onclick='hotelinventory_obj.toggleCalendarNextPrev(\"NEXT\",$focus_month,$focus_year);'>]]></cell>";
    print "<cell type='ro' align='center'></cell>";

    print "</row>";
    
    //get first date and last date of month and year
    $first_date = date('Y-m-01', strtotime($__arr_months_long[$focus_month-1] . " $focus_year"));
    $last_date = date('Y-m-t', strtotime($__arr_months_long[$focus_month-1] . " $focus_year"));
    
    $first_day = date('D', strtotime($first_date));    
    
    $first_date_dd = 1;
    $last_date_dd = date('d', strtotime($last_date));
    
    $has_started = false;
    
    for ($i = 1; $i <= 6; $i++) {

        print "<row id='rw_$i'>";
        
        for ($j = 0; $j < count($__arr_days); $j++) {
            
            $day = $__arr_days[$j]["Short"];
            
            if($i == 1)
            {
                //first row
                if($first_day != $day && !$has_started)
                {
                    print "<cell type='ro' align='center' date='' sort='na' selected='' "
                          . "style='$cellstyle $cellstylelocked'></cell>";
                }
                else if($first_day == $day && !$has_started)
                {
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));
                    
                    $has_started = true;
                    $selected = "";
                    $tick = "";
                    $mestyle = $cellstyle;
                    if(isdate_in_dates_array($_thedate))
                    {
                        $selected = "1";
                        $tick = "<br>$myicon";
                    }
                    print "<cell type='ro' align='center' date='$_thedate' sort='na' selected='$selected' "
                          . "style='$mestyle $cellstyleenabled' "
                          . "title='$_tooltip'><![CDATA[$first_date_dd $tick]]></cell>";
                }
                else 
                {
                    $first_date_dd ++;
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));
                    
                    $selected = "";
                    $tick = "";
                    $mestyle = $cellstyle;
                    if(isdate_in_dates_array($_thedate))
                    {
                        $selected = "1";
                        $tick = "<br>$myicon";
                    }
                    
                    print "<cell type='ro' align='center' date='$_thedate' sort='na' "
                          . "title='$_tooltip' selected='$selected' "
                          . "style='$mestyle $cellstyleenabled'><![CDATA[$first_date_dd $tick]]></cell>";
                }
                
                
            }
            else
            {
                //other rows
                if($first_date_dd >= $last_date_dd)
                {
                    print "<cell type='ro' align='center' date='' sort='na' selected='' "
                          . "style='$cellstyle $cellstylelocked'></cell>";
                }
                else
                {
                    $first_date_dd ++;
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));
                    
                    $selected = "";
                    $tick = "";
                    $mestyle = $cellstyle;
                    if(isdate_in_dates_array($_thedate))
                    {
                        $selected = "1";
                        $tick = "<br>$myicon";
                    }
                    
                    print "<cell type='ro' align='center' date='$_thedate' sort='na' "
                            . "title='$_tooltip' selected='$selected' "
                            . "style='$mestyle $cellstyleenabled'><![CDATA[$first_date_dd $tick]]></cell>";
                    
                }                
            }
        }

        print "</row>";
    }
}

function generateMonthCombo($selmonth) {
    global $__arr_months_long;

    $str = "$selmonth";

    for ($i = 0; $i < count($__arr_months_long); $i++) {
        $str .= "<option value='" . ($i + 1) . "'>" . $__arr_months_long[$i] . "</option>";
    }

    return $str;
}

function generateYearCombo($sel_year) {
    $str = "$sel_year";

    for ($i = 2018; $i <= 2100; $i++) {
        $str .= "<option value='$i'>$i</option>";
    }

    return $str;
}

function isdate_in_dates_array($dt)
{
    //$dt in YYYY-mm-dd
    
    global $dates;
            
    for($i = 0; $i < count($dates); $i++)
    {
        if($dates[$i] == $dt)
        {
            return true;
        }
    }
    
    return false;
}
?>


