<?php

function utils_authenticate_api_user($con, $arrPOST, $api_name) {
    //check if there is username, password and token

    if (!isset($arrPOST["u"])) {
        return "ERR_NO_USERNAME";
    }

    if (!isset($arrPOST["p"])) {
        return "ERR_NO_PASSWORD";
    }

    if (!isset($arrPOST["t"])) {
        return "ERR_NO_TOKEN";
    }
    
    $u = trim($arrPOST["u"]);
    $p = md5(trim($arrPOST["p"]));
    $t = trim($arrPOST["t"]);
    
    
    //====================================================================
    //load the user details
    $userid = -1;
    $user_active_yn = "N";
    $user_intern_extern = "";
    $user_tofk = -1;
    $user_system_password = "";
    
    $grpid = -1;
    $grpactive_yn = "N";
    
    $sql = "SELECT u.*, ug.ugroup, ug.grpactiveyn, ug.grpcode               
            FROM tblugroup ug 
            INNER JOIN tbluser u ON ug.id = u.ugrpid 
            WHERE u.uname=:uname";

    $query = $con->prepare($sql);
    $query->execute(array(":uname" => $u));
    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $userid = $row["id"];
        $user_active_yn = $row["status"];
        $user_system_password = $row["upass"];
        $user_intern_extern = $row["intern_extern"];
        $user_tofk = $row["tofk"];
        
        $grpid = $row["ugrpid"];
        $grpactive_yn = $row["grpactiveyn"];
    }
    
    if ($userid == -1) {
        return "ERR_USER_INVALID";
    }
    
    if ($grpid == -1) {
        return "ERR_GROUP_INVALID";
    }

    if ($user_active_yn != "ACTIVE") {
        return "ERR_USER_DEACTIVATED";
    }

    if ($grpactive_yn == "N") {
        return "ERR_GROUP_DEACTIVATED";
    }

    if ($p != $user_system_password) {
        return "ERR_PASSWORD_INVALID";
    }
    
    if ($user_tofk == -1) {
        return "ERR_TO_INVALID";
    }
    
    //====================================================================
    //get the TO details
    //then check if api access is enabled and token match
    $api_token = "";
    $api_active = 0;
    $to_deleted = 0;
    
    $sql = "SELECT * FROM tbltouroperator WHERE id=:id";
    $query = $con->prepare($sql);
    $query->execute(array(":id" => $user_tofk));
    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $api_token = $row["api_token"];
        $api_active = $row["api_active"];
        $to_deleted = $row["deleted"];
    }
    
    if($to_deleted == 1)
    {
        return "ERR_TO_DELETED";
    }
    
    if($api_active == 0)
    {
        return "ERR_TO_API_DEACTIVATED";
    }
    
    if($api_token != $t)
    {
        return "ERR_TO_INVALID_TOKEN";
    }
    
    //====================================================================
    //check if usergroup of this user has rights to this api
    $rights_outcome = utils_firewall_menu_rights($con, $api_name, $grpid);
    if(!$rights_outcome)
    {
        return "ERR_NO_API_RIGHT";
    }
    
    return array("OUTCOME"=>"OK","TOID"=>$user_tofk);
}


function utilities_render_query($con, $sql, $idcol, $displayfields, $display_col_keys, $arr_params) {
    
    //creates an array of data for the query provided
    $query = $con->prepare($sql);
    $query->execute($arr_params);
    
    $arr_data = [];

    $arr_cols_to_display = explode(",", $displayfields);
    $arr_keys_to_display = explode(",", $display_col_keys); 
    
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $arr_to_push = array();
        $arr_to_push["id"] = $rw[$idcol];

        for ($i = 0; $i < count($arr_cols_to_display); $i++) {
            $colname = trim($arr_cols_to_display[$i]);
            $keyname = trim($arr_keys_to_display[$i]);
            
            if ($colname != "") {
                $arr_to_push[$keyname] = $rw[$colname];
            }
        }

        $arr_data[] = $arr_to_push;
    }

    return $arr_data;
}


/**
 * Adds an interval to a date
 * @param interval: interval day:d, year: yyyy, month:m
 * @param number: by which amount to increase or decrease
 * @param date: subject date in Y-m-d format
 * @return timestamp after change
 */
function utilities_dateAdd($interval, $number, $date) {
    //date must be in the format of Y-m-d H:i:s

    $stamp = strtotime($date);
    $date_time_array = getdate($stamp);

    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];

    switch ($interval) {

        case 'yyyy':
            $year += $number;
            break;
        case 'q':
            $year += ($number * 3);
            break;
        case 'm':
            $month += $number;
            break;
        case 'y':
        case 'd':
        case 'w':
            $day += $number;
            break;
        case 'ww':
            $day += ($number * 7);
            break;
        case 'h':
            $hours += $number;
            break;
        case 'n':
            $minutes += $number;
            break;
        case 's':
            $seconds += $number;
            break;
    }

    $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
    return $timestamp;
}

function utilities_rand_sha1($length) {
    $max = ceil($length / 40);
    $random = '';
    for ($i = 0; $i < $max; $i ++) {
        $random .= sha1(microtime(true) . mt_rand(10000, 90000));
    }
    return substr($random, 0, $length);
}

function utilities_render_dhtmlx_query($query, $idcol, $displayfields) {
    $arr_data = [];

    $arr_cols_to_display = explode(",", $displayfields);

    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $arr_to_push = array();
        $arr_to_push["id"] = $rw[$idcol];

        for ($i = 0; $i < count($arr_cols_to_display); $i++) {
            $colname = trim($arr_cols_to_display[$i]);
            if ($colname != "") {
                $arr_to_push[$colname] = $rw[$colname];
            }
        }

        $arr_data[] = $arr_to_push;
    }

    echo json_encode(array("data" => $arr_data));
}

function utils_formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 *  Function that calculates the age difference in years, months and days between two dates
 * @param start_date: start date in format Y-m-d
 * @param end_date: end date in format Y-m-d
 * @return: returns age in years, months and days
 *
 */
function utils_get_Age_difference($start_date, $end_date) {
    if (strtotime($start_date) == false) {
        return "";
    }
    if (strtotime($end_date) == false) {
        return "";
    }

    list($start_year, $start_month, $start_date) = explode('-', $start_date);
    list($current_year, $current_month, $current_date) = explode('-', $end_date);
    $result = '';

    /** days of each month * */
    for ($x = 1; $x <= 12; $x++) {

        $dim[$x] = date('t', mktime(0, 0, 0, $x, 1, date('Y')));
    }

    /** calculate differences * */
    $m = $current_month - $start_month;
    $d = $current_date - $start_date;
    $y = $current_year - $start_year;

    /** if the start day is ahead of the end day * */
    if ($d < 0) {

        $current_month = (integer) $current_month;

        $today_day = $current_date + $dim[$current_month];
        $today_month = $current_month - 1;
        $d = $today_day - $start_date;
        $m = $today_month - $start_month;
        if (($today_month - $start_month) < 0) {

            $today_month += 12;
            $today_year = $current_year - 1;
            $m = $today_month - $start_month;
            $y = $today_year - $start_year;
        }
    }

    /** if start month is ahead of the end month * */
    if ($m < 0) {

        $today_month = $current_month + 12;
        $today_year = $current_year - 1;
        $m = $today_month - $start_month;
        $y = $today_year - $start_year;
    }

    /** Calculate dates * */
    if ($y < 0) {

        return "";
    } else {

        switch ($y) {

            case 0 : $result .= '';
                break;
            case 1 : $result .= $y . ($m == 0 && $d == 0 ? ' year ' : ' year');
                break;
            default : $result .= $y . ($m == 0 && $d == 0 ? ' years ' : ' years');
        }

        switch ($m) {

            case 0: $result .= '';
                break;
            case 1: $result .= ($y == 0 && $d == 0 ? $m . ' month ' : ($y == 0 && $d != 0 ? $m . ' month ' : ($y != 0 && $d == 0 ? ' ' . $m . ' month ' : ' ' . $m . ' month ')));
                break;
            default: $result .= ($y == 0 && $d == 0 ? $m . ' months ' : ($y == 0 && $d != 0 ? $m . ' months ' : ($y != 0 && $d == 0 ? ' ' . $m . ' months ' : ' ' . $m . ' months ')));
                break;
        }

        switch ($d) {

            case 0: $result .= ($m == 0 && $y == 0 ? 'Today' : '');
                break;
            case 1: $result .= ($m == 0 && $y == 0 ? $d . ' day ' : ($y != 0 || $m != 0 ? '' . $d . ' day ' : ''));
                break;
            default: $result .= ($m == 0 && $y == 0 ? $d . ' days ' : ($y != 0 || $m != 0 ? '' . $d . ' days ' : ''));
        }
    }

    return $result;
}

function utils_DMY_YMD($dt, $default_blank = "") {

    //converts a date string from dd-mm-yyyy to yyyy-mm-dd

    if (trim($dt) == "") {
        return $default_blank;
    }
    $dt = explode("-", $dt);

    $dt = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
    return $dt;
}

function utils_stringBlank($dt, $default_blank = "") {
    if (trim($dt) == "") {
        return $default_blank;
    }
    return trim($dt);
}

function utils_admin_teach_roundMarks($round, $marks) {

    //THIS FUNCTION IS FOUND IN BOTH ADMIN AND TEACHER FOLDERS
    if ($marks == "") {
        return $marks;
    }

    //check if the number is already whole
    $arr_float = explode(".", $marks);
    if (count($arr_float) == 1) {
        //number already whole
        return $marks;
    }

    if ($round == "ROUND") {
        return round($marks);
    } else if ($round == "1DP") {
        //return round($marks, 1);
        $pow = pow(10, 1);
        return ( ceil($pow * $marks) + ceil($pow * $marks - ceil($pow * $marks)) ) / $pow;
    } else if ($round == "ROUNDDOWN") {
        return floor($marks);
    } else if ($round == "ROUNDUP") {
        return ceil($marks);
    }

    return $marks;
}

function utils_deleteDir($dir) {

    /*
      if (! is_dir($dirPath)) {
      throw new InvalidArgumentException("$dirPath must be a directory");
      }
      if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
      }
      $files = glob($dirPath . '*', GLOB_MARK);
      foreach ($files as $file) {
      if (is_dir($file)) {
      self::deleteDir($file);
      } else {
      unlink($file);
      }
      }
      rmdir($dirPath);
     */

    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    utils_deleteDir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function utils_getTableComment($con, $tblname, $dbname) {
    $comments = "";

    $sql = "SELECT table_comment FROM INFORMATION_SCHEMA.TABLES 
            WHERE table_schema=:dbname AND table_name=:table_name";
    $query = $con->prepare($sql);
    $query->execute(array(":dbname" => $dbname, ":table_name" => $tblname));
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $comments = $rw["table_comment"];
    }

    return $comments;
}

function utils_reserved_ip($ip) {
    if ($ip == "::1") {
        return true; //localhost
    }

    $reserved_ips = array(// not an exhaustive list
        '167772160' => 184549375, /*    10.0.0.0 -  10.255.255.255 */
        '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
        '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
        '2851995648' => 2852061183, /* 169.254.0.0 - 169.254.255.255 */
        '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
        '3758096384' => 4026531839, /*   224.0.0.0 - 239.255.255.255 */
    );

    $ip_long = sprintf('%u', ip2long($ip));

    foreach ($reserved_ips as $ip_start => $ip_end) {
        if (($ip_long >= $ip_start) && ($ip_long <= $ip_end)) {
            return TRUE;
        }
    }
    return FALSE;
}

function utilities_getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function utilities_is_internet_connected() {
    $connected = @fsockopen("www.google.com", 80);
    if ($connected) {
        $is_conn = true; //action when connected
        fclose($connected);
    } else {
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}

function utilities_getServerOS() {

    $os_platform = "UNKNOWN";

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $os_platform = "WINDOWS";
    } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        $os_platform = "LINUX";
    }

    return $os_platform;
}

function utils_loadRights($menu, $con, $grpfk) {
    $arr = array();

    $sql = "select m.menuid, mp.processname, mp.id, 
            IFNULL(gpr.prcsfk, 'NOT_ALLOWED') AS allowyn,
            ug.ugroup
            from tblmenu m 
            inner join tblmenuprocess mp on m.menuid = mp.menuid
            inner join tblugroup ug on ug.id = :grpid
            left join tblgrpprcsrights gpr on gpr.prcsfk = mp.id and gpr.gprfk = :grpid
            where m.menusysid = :menu;";

    $query = $con->prepare($sql);
    $query->execute(array(":menu" => $menu, ":grpid" => $grpfk));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $allow = "Y";
        if ($rw["allowyn"] == "NOT_ALLOWED" && $rw["ugroup"] != "ADMIN") {
            $allow = "N";
        }

        $arr[] = array("PROCESSNAME" => $rw["processname"], "ALLOWED" => $allow);
    }

    return json_encode($arr);
}

function utils_getDefaultCountry($con) {
    $id = -1;

    $sql = "SELECT * FROM tblcountries WHERE default_selected = 'Y' ";
    $query = $con->prepare($sql);
    $query->execute();
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $id = $rw["id"];
    }

    return $id;
}

function utils_getDefaultHotelType($con) {
    $id = -1;

    $sql = "SELECT * FROM tblhoteltype WHERE isdefault = 'Y' ";
    $query = $con->prepare($sql);
    $query->execute();
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $id = $rw["id"];
    }

    return $id;
}

function utils_generate_token($length) {
    $characters = array(
        "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M",
        "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m",
        "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
        "1", "2", "3", "4", "5", "6", "7", "8", "9");

    if ($length < 0 || $length > count($characters)) {
        return null;
    }

    shuffle($characters);
    $token = implode("", array_slice($characters, 0, $length));
    return $token;
}

function utils_generate_unique_token($length, $con, $tblname, $fieldname) {
    //generate a unique token
    $token = "";
    $loop = true;
    while ($loop) {

        $token = utils_generate_token($length);
        $sql = "SELECT * FROM $tblname WHERE $fieldname = :api_token";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":api_token" => $token));
        if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $loop = false;
        }
    }
    return $token;
}

function utils_getsysparams($con, $param1, $param2, $key) {
    //get the physical location of the photo where it will be kept
    $sql = "select * from tblsysparams where param1 = :param1 
            AND param2=:param2 AND pkey = :pkey ";
    $query = $con->prepare($sql);
    $query->execute(array(":param1" => $param1, ":param2" => $param2, ":pkey" => $key));

    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return $rw["pvalue"];
    } else {
        return null;
    }
}

function utils_firewall_menu_rights($con, $menu, $ugrpid) {
    //check if the user group has access to the menu 
    $sql = "SELECT gmr.*, m.menusysid
            FROM tblgrpmenurights gmr
            INNER JOIN tblmenu m on gmr.menufk = m.menuid
            WHERE gmr.groupfk = :groupfk AND m.menusysid = :menuname";
    $query = $con->prepare($sql);
    $query->execute(array(":menuname" => $menu, ":groupfk" => $ugrpid));

    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        return true;
    } else {
        return false;
    }
}

function utils_days_diff($dtfrom, $dtto) {
    //return the number of days between dtfrom and dtto
    //dtfrom in yyyy-mm-dd
    //$dtto in yyyy-mm-dd
    $dtfrom = trim($dtfrom);
    $dtto = trim($dtto);

    if ($dtfrom == "" || $dtto == "") {
        return 0;
    }

    $dtfrom = strtotime($dtfrom);
    $dtto = strtotime($dtto);
    $datediff = $dtto - $dtfrom;

    return round($datediff / (60 * 60 * 24));
}

?>
