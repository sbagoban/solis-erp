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

    
    //set the time zone to Mauritius
    date_default_timezone_set('Indian/Mauritius');

    //========================================================
    if (isset($_SESSION['solis_userid'])) {
        //user is already logged in. No need to authenticate further
        throw new Exception("Already Logged In"); 
    }
        
    
    //========================================================
    require_once("../../utils/utilities.php");
    require_once("../../connector/pdo_connect_main_login_internet.php");
    
    
    //get the username (email) and password

    $email = $_POST["email"]; //extract the email
    $password = $_POST["password"]; //extract the password
   
    
    //create connection object to mysql using login_user
    $con = connect_login_pdo();

    
    //initialise global user variables
    $username = "";
    $userfullname = "";
    $userid = -1;
    $userStatus = "N";
    $intern_extern = "";
    $user_image = "";
    $usercreated = "";
    $date_activated = "";
    
    //initialise global usergroup variables the user belongs to
    $grpname = "";
    $grpid = -1;
    $grpactive = "N";
    
    
    
    
    
    //this variable will store the tour operator id in case the user 
    //belongs to a tour operator
    $user_tofk = "";
    $user_to_active = 0; 
    
    
    //skin here refers to the colour of the template used in the UI
    $skin = "skin-blue";

    $system_password = "";

    if (!empty($_POST)) {

        //=================================================================================================
        //=================================================================================================
        //load the user details if email is correct
        $sql = "SELECT u.*, ug.ugroup, ug.grpactiveyn, ug.grpcode, tp.active          
                FROM tblugroup ug 
                INNER JOIN tbluser u ON ug.id = u.ugrpid 
                LEFT JOIN tbltouroperator tp on u.tofk = tp.id
                WHERE u.email=:email";
        
        $query = $con->prepare($sql);
        $query->execute(array(":email" => $email));
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $username = $row["uname"]; //short username of the user
            $userfullname = $row["ufullname"]; //full name of the user
            $userid = $row["id"]; //user id in the system
            $userStatus = $row["status"]; //status of the user
            $system_password = $row["upass"]; //password of the user

            $grpname = $row["ugroup"]; //groupname the user belongs to
            $grpid = $row["ugrpid"]; //groupid the user belongs to
            $grpactive = $row["grpactiveyn"]; //is the usergroup active
             
            $usercreated = $row["date_created"]; //date user created
            $date_activated = $row["date_activated"]; //date user has been activated
            $intern_extern = $row["intern_extern"]; //is the user and internal or external user (internal = solis staff, external = touroperator user)
            $user_tofk = $row["tofk"]; //the TO id the user could belong to
            $user_image = $row["user_image"]; //profile image of the user
            
            $user_to_active = $row["active"]; //if the user belongs to a TO, is the TO active or not
            
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
        
        if ($intern_extern == "EXT" && !is_null($user_to_active) && $user_to_active == 0) {
            throw new Exception("Your Tour Operator has been deactivated");
        }

        if ($password != $system_password) {
            throw new Exception("Wrong Password!");
        }

        
        //====================================================================
        //all is well
        //save the data into the session to mark successful login and session creation
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

        //return values
        $responseArray = array('OUTCOME' => 'OK', 'UID' => $userid, 'TOID'=>$user_tofk);
        $encoded = json_encode($responseArray);
        echo $encoded;
    }
} catch (\Exception $e) {
    //return error message
    $responseArray = array("OUTCOME" => $e->getMessage());
    $encoded = json_encode($responseArray);
    echo $encoded;
}


?>