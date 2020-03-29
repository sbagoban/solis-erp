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

    session_start();

    date_default_timezone_set('Indian/Mauritius');

    //========================================================
    if (isset($_SESSION['solis_userid'])) {
        throw new Exception("Already Logged In");
    }
        
    
    //========================================================
    require_once("../utils/utilities.php");
    require_once("../connector/pdo_connect_main_login_internet.php");


    $email = $_POST["email"]; //extract the email
    $password = $_POST["password"]; //extract the password
    $token = $_POST["token"]; //extract the token


    if ($token != $_SESSION["token"]) {
        throw new Exception("Wrong Token");
    }

    $con = connect_login_pdo();
    $serv = utilities_getRealIpAddr();

    //global user variables
    $username = "";
    $userfullname = "";
    $userid = -1;
    $userStatus = "N";
    $intern_extern = "";
    $user_image = "";

    $grpname = "";
    $grpid = -1;
    $grpactive = "N";
    
    $usercreated = "";
    $date_activated = "";
    
    $user_tofk = "";
    
    $skin = "skin-blue";

    $system_password = "";

    if (!empty($_POST)) {

        //=================================================================================================
        //=================================================================================================
        //load the user details if email is correct
        $sql = "SELECT u.*, ug.ugroup, ug.grpactiveyn, ug.grpcode               
                FROM tblugroup ug 
                INNER JOIN tbluser u ON ug.id = u.ugrpid 
                WHERE u.email=:email";
        
        $query = $con->prepare($sql);
        $query->execute(array(":email" => $email));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $username = $row["uname"];
            $userfullname = $row["ufullname"];
            $userid = $row["id"];
            $userStatus = $row["status"];
            $system_password = $row["upass"];

            $grpname = $row["ugroup"];
            $grpid = $row["ugrpid"];
            $grpactive = $row["grpactiveyn"];
             
            $usercreated = $row["date_created"];
            $date_activated = $row["date_activated"];
            $intern_extern = $row["intern_extern"];
            $user_tofk = $row["tofk"];
            $user_image = $row["user_image"];
            
            if($row["skin"] != "")
            {
                $skin = $row["skin"];
            }
        }

        if ($userid == -1) {
            throw new Exception("Email Address does not exist in System");
        }

        if ($userStatus != "ACTIVE") {
            throw new Exception("Your User Account has been deactivated by System Administrator");
        }

        if ($grpactive == "N") {
            throw new Exception("Your User Group has been deactivated by System Administrator");
        }

        if ($password != $system_password) {
            throw new Exception("Wrong Password!");
        }

        //all is well
        $_SESSION["solis_userid"] = $userid;
        $_SESSION["solis_grpid"] = $grpid;
        $_SESSION["solis_username"] = $username;
        $_SESSION["solis_userfullname"] = $userfullname;
        $_SESSION["solis_userimage"] = $user_image;
        $_SESSION["solis_grpname"] = $grpname;
        $_SESSION["solis_usercreated"] = $usercreated;
        $_SESSION["solis_useractivated"] = $date_activated;
        $_SESSION["solis_useremail"] = $email;
        $_SESSION["solis_userinternextern"] = $intern_extern;
        $_SESSION["solis_usertoid"] = $user_tofk;
        $_SESSION["solis_skin"] = $skin;

        $responseArray = array('OUTCOME' => 'OK', 'UID' => $userid, 'IP' => $serv);
    }
} catch (\Exception $e) {
    $responseArray = array("OUTCOME" => $e->getMessage());
}



//=====================================================================================

$encoded = json_encode($responseArray);
echo $encoded;

//=========================================================================================
?>