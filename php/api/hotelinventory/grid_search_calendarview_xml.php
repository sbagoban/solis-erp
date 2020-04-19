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

    require_once("../../utils/utilities.php");
    require_once("../../globalvars/globalvars.php");
    require_once("../../connector/pdo_connect_main.php");

    $con = pdo_con();

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


    $myicon_size = "18px";
    if (isset($_GET["myicon_size"])) {
        $myicon_size = $_GET["myicon_size"];
    }


    $params = json_decode($_GET["params"], true);

    $dtfrom = $params["dtfrom"];
    $dtto = $params["dtto"];
    $inventory_type = $params["inventory_type"];
    $rooms_ids = trim($params["rooms_ids"]);
    $market_countries_ids = trim($params["market_countries_ids"]);
    $to_ids = trim($params["to_ids"]);
    $hotelfk = $params["hotelfk"];
    $specific_to = $params["specific_to"];
    

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

    global $dtfrom;

    $month_name = "";
    $month_num = "";
    $year = "";

    $month_name = date('F', strtotime($dtfrom));
    $month_num = date('n', strtotime($dtfrom));
    $year = date('Y', strtotime($dtfrom));


    return array("MONTH_NAME" => $month_name, "MONTH_NUM" => $month_num, "YEAR" => $year);
}

function getInventoryStatus($date) {
    //return an array of status for that date

    global $hotelfk;
    global $rooms_ids;
    global $inventory_type;
    global $market_countries_ids;
    global $to_ids;
    global $con;
    global $specific_to;

    $arr_statuses = array();

    $sql = "SELECT inventory_status 
            FROM tblinventory_dates WHERE 
            hotelfk=:hotelfk AND 
            specific_to = :specific_to AND
            roomfk IN ($rooms_ids) AND
            inventory_date=:inventory_date AND
            deleted = 0 ";

    //====================================================================
    if (strtoupper($inventory_type) != "ALL") {
        //search based on countries
        $sql .= " AND inventory_status = '$inventory_type' ";
    }

    //====================================================================
    
    if ($to_ids != "") {
        //search based on tour operators (A)
        $sql .= " AND to_fk IN ($to_ids) ";
    } 
    else 
    {
        if($market_countries_ids != "")
        {
            //search based on countries (C)
            $sql .= " AND to_fk IN (SELECT tofk FROM tblto_countries 
                      WHERE countryfk in ($market_countries_ids)) ";
        }
        else
        {
            //search based on worldwide (B)
            //nothing
        }
    }
    
    //====================================================================


    $sql .= " GROUP BY inventory_status ORDER BY inventory_status ";


    $query = $con->prepare($sql);
    $query->execute(array(":hotelfk" => $hotelfk, ":inventory_date" => $date,
                           ":specific_to"=>$specific_to));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr_statuses[] = $rw["inventory_status"];
    }

    return $arr_statuses;
}

function generateMonthCalendar($focus_month, $focus_year) {

    global $__arr_months_long;
    global $__arr_days;
    global $cellstyle;
    global $cellstyle_title;
    global $cellstylelocked;
    global $cellstyleenabled;


    print "<row id='rw_title'>";

    print "<cell type='ro' align='left' colspan='2' date='' "
            . "style='$cellstyle_title'><![CDATA[<input type='button' value='<< PREV' onclick='hotelinventory_obj.toggleSearchCalendarNextPrev(\"PREV\",$focus_month,$focus_year);'>]]></cell>";
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
            . "style='$cellstyle_title'><![CDATA[<input type='button' value='NEXT >>' onclick='hotelinventory_obj.toggleSearchCalendarNextPrev(\"NEXT\",$focus_month,$focus_year);'>]]></cell>";
    print "<cell type='ro' align='center'></cell>";

    print "</row>";

    //get first date and last date of month and year
    $first_date = date('Y-m-01', strtotime($__arr_months_long[$focus_month - 1] . " $focus_year"));
    $last_date = date('Y-m-t', strtotime($__arr_months_long[$focus_month - 1] . " $focus_year"));

    $first_day = date('D', strtotime($first_date));

    $first_date_dd = 1;
    $last_date_dd = date('d', strtotime($last_date));

    $has_started = false;

    for ($i = 1; $i <= 6; $i++) {

        print "<row id='rw_$i'>";

        for ($j = 0; $j < count($__arr_days); $j++) {

            $day = $__arr_days[$j]["Short"];

            if ($i == 1) {
                //first row
                if ($first_day != $day && !$has_started) {
                    print "<cell type='ro' align='center' date='' sort='na' selected='' "
                            . "style='$cellstyle $cellstylelocked'></cell>";
                } else if ($first_day == $day && !$has_started) {
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));

                    $has_started = true;
                    $mestyle = $cellstyle;

                    $arr_statuses = getInventoryStatus($_thedate);
                    $display = generateDisplay($arr_statuses);

                    print "<cell type='ro' align='center' date='$_thedate' sort='na' "
                            . "style='$mestyle $cellstyleenabled' "
                            . "title='$_tooltip'><![CDATA[$first_date_dd $display]]></cell>";
                } else {
                    $first_date_dd++;
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));

                    $mestyle = $cellstyle;

                    $arr_statuses = getInventoryStatus($_thedate);
                    $display = generateDisplay($arr_statuses);

                    print "<cell type='ro' align='center' date='$_thedate' sort='na' "
                            . "title='$_tooltip' "
                            . "style='$mestyle $cellstyleenabled'><![CDATA[$first_date_dd $display]]></cell>";
                }
            } else {
                //other rows
                if ($first_date_dd >= $last_date_dd) {
                    print "<cell type='ro' align='center' date='' sort='na' selected='' "
                            . "style='$cellstyle $cellstylelocked'></cell>";
                } else {
                    $first_date_dd++;
                    $_thedate = "$focus_year-" . sprintf('%02d', $focus_month) . "-" . sprintf('%02d', $first_date_dd);
                    $_tooltip = date('D, d M Y', strtotime($_thedate));

                    $mestyle = $cellstyle;

                    $arr_statuses = getInventoryStatus($_thedate);
                    $display = generateDisplay($arr_statuses);


                    print "<cell type='ro' align='center' date='$_thedate' sort='na' "
                            . "title='$_tooltip' "
                            . "style='$mestyle $cellstyleenabled'><![CDATA[$first_date_dd $display]]></cell>";
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

function generateDisplay($arr_statuses) {

    global $myicon_size;

    $str = "";

    //to be decided what icon to use
    $myicon = "<img height='$myicon_size' width='$myicon_size' src='images/";

    for ($i = 0; $i < count($arr_statuses); $i++) {
        $status = $arr_statuses[$i];
        $str .= "<br>";


        if ($status == "free_sales") {
            $str .= $myicon . "tick.png'>";
        } else if ($status == "stop_sales") {
            $str .= $myicon . "cross.png'>";
        } else if ($status == "on_request") {
            $str .= $myicon . "RQ.png'>";
        } else if ($status == "renovation") {
            $str .= $myicon . "RN.png'>";
        }
    }

    return $str;
}
?>


