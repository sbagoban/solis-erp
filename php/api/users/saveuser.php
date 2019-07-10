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

    require_once("../../connector/pdo_connect_main.php");

    $con = pdo_con();
    $con->beginTransaction();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }



    $id = $_POST["id"];
    $uname = trim($_POST["uname"]);
    $upass = md5(trim($_POST["upass"]));
    $email = trim($_POST["email"]);
    $ugrpid = $_POST["ugrpid"];
    $ustatus = $_POST["status"];
    $ufullname = trim($_POST["ufullname"]);
    $resetpassword = trim($_POST["resetpassword"]);
    $intern_extern = trim($_POST["intern_extern"]);
    $_cboTO = trim($_POST["_cboTO"]);
    $selected_depts_ids = trim($_POST["selected_depts_ids"]);
    $gender = trim($_POST["gender"]);

    $pwd_reset = "NO";

    if ($_cboTO == "" || $_cboTO == "-1" || $intern_extern == "INT") {
        $_cboTO = null;
    }


    //check duplicates for username
    $sql = "SELECT * FROM tbluser WHERE uname = :uname AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":uname" => $uname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE USER NAME!");
    }

    //check duplicates for email add
    $sql = "SELECT * FROM tbluser WHERE email = :email AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":email" => $email, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE INTERNET EMAIL ADDRESS!");
    }

    if ($id == "-1") {
        
        $user_image = "avatar_male.png";
        if($gender == "F")
        {
            $user_image = "avatar_female.png";
        }
        
        $sql = "INSERT INTO tbluser (uname,upass,email,ugrpid,
                ufullname,status,date_created,intern_extern,tofk,gender,user_image) 
                VALUES (:uname,:upass,:email,:ugrpid,:ufullname,
                :status,:date_created,:intern_extern,:tofk,:gender,:user_image) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":uname" => $uname, ":upass" => $upass, ":email" => $email,
            ":ugrpid" => $ugrpid, ":ufullname" => $ufullname,
            ":status" => $ustatus, ":date_created" => date("Y-m-d H:i:s"),
            ":intern_extern" => $intern_extern, ":tofk" => $_cboTO,
            ":gender"=>$gender,":user_image"=>$user_image));

        $id = $con->lastInsertId();

        if ($ustatus == "ACTIVE") {
            $sql = "UPDATE tbluser SET date_activated=:date_activated WHERE id=:id";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_activated" => date("Y-m-d H:i:s"), ":id" => $id));
        }
    } else {

        $pwd_reset = "NO";
        $pwd_param = "";
        $arr_params = array(":uname" => $uname, ":email" => $email,
            ":ugrpid" => $ugrpid, ":ufullname" => $ufullname,
            ":date_modified" => date("Y-m-d H:i:s"),
            ":intern_extern" => $intern_extern, ":tofk" => $_cboTO,
            ":gender"=>$gender,
            ":status" => $ustatus, ":id" => $id);

        if ($resetpassword == "YES") {
            $pwd_param = ",upass=:upass";
            $arr_params[":upass"] = $upass;
            $pwd_reset = "YES";
        }
        //no password reset
        $sql = "UPDATE tbluser SET uname=:uname, email=:email,ugrpid=:ugrpid,
                    status=:status,ufullname=:ufullname,
                    intern_extern=:intern_extern,
                    tofk=:tofk,
                    gender=:gender,
                    date_modified=:date_modified $pwd_param
                    WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute($arr_params);
    }


    //====================================
    //user departments
    if ($selected_depts_ids == "") {
        $sql = "DELETE FROM tbluserdepts WHERE userfk=:userfk";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":userfk" => $id));
    } else {
        $sql = "DELETE FROM tbluserdepts WHERE userfk=:userfk AND deptfk NOT IN ($selected_depts_ids)";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":userfk" => $id));

        $arr_ids = explode(",", $selected_depts_ids);
        for ($i = 0; $i < count($arr_ids); $i++) {
            $deptid = $arr_ids[$i];

            //check if link already exits. If not then create one.
            $sql = "SELECT * FROM tbluserdepts WHERE userfk=:userfk AND deptfk=:deptfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":userfk" => $id, ":deptfk" => $deptid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert
                $sql = "INSERT INTO tbluserdepts (userfk,deptfk) VALUES (:userfk,:deptfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":userfk" => $id, ":deptfk" => $deptid));
            }
        }
    }
    //==================
    
    

    $con->commit();
    echo json_encode(array("OUTCOME" => "OK", "PWD_RESET" => $pwd_reset, "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
