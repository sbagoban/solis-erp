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
    //================================================================================

    session_start();
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

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

    
    
    $absolute_server_path = utils_getsysparams($con, "USER", "PHOTO", "ABSOLUTE_PATH");
    if(is_null($absolute_server_path))
    {
        throw new Exception("NO USER PHOTO ABSOLUTE PATH DEFINED!");
    }
    
    $max_file_size = utils_getsysparams($con, "IMAGE", "SIZE", "UPLOAD_LIMIT_MB");
    if(is_null($max_file_size))
    {
        throw new Exception("NO IMAGE UPLOAD MAX SIZE DEFINED!");
    }
    

    $target_file = $absolute_server_path . basename($_FILES["photos"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    $err_msg = "";

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["photos"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $err_msg = "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($_FILES["photos"]["size"] > ($max_file_size * 1000000)) {
        $err_msg .= "User Photo bigger than {$max_file_size}MB! ";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $err_msg .= "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }



    //create a random string for the hotel
    //generate a random string for the hotel photo for additional security
    $random_str = utils_generate_unique_token(8, $con, "tbluser", "user_image");
    $random_str .= ".$imageFileType";
    

    //update the path name in the database finally
    $sql = "UPDATE tbluser SET user_image=:user_image WHERE id=:id";

    $query = $con->prepare($sql);
    $query->execute(array(":user_image" => $random_str,
        ":id" => $_SESSION["solis_userid"]));


    $final_file_path_name_absolute = $absolute_server_path . $random_str;

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        throw new Exception($err_msg);
    } else {
        // if everything is ok, try to upload file

        if (move_uploaded_file($_FILES["photos"]["tmp_name"], $target_file)) {
            //rename the file now
            rename($target_file, $final_file_path_name_absolute);
        } else {
            throw new Exception("ERROR UPLOADING THE FILE TO THE SPECIFIED LOCATION ON THE SERVER!");
        }
    }

    $con->commit();
    
    $_SESSION["solis_userimage"] = $random_str; 
    echo "OK";
    
} catch (Exception $ex) {
    $con->rollBack();
    $msg = $ex->getMessage();
    die($msg);
}
?>