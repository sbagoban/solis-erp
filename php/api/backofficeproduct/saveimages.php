<?php

header('Content-type:application/json;charset=utf-8');

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

        if (!isset($_SESSION["solis_userid"])) {
            die("NO LOG IN!");
        }

        if (!isset($_GET["t"])) {
            die("INVALID TOKEN");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            die("INVALID TOKEN");
        }

    //print_r($_FILES['files']['error'][0]);
    if (!isset($_FILES['files']['error'][0]) || is_array($_FILES['files']['error'][0])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($_FILES['files']['error'][0]) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['files']['tmp_name'][0]),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    $absolute_server_path = utils_getsysparams($con, "PRODUCT", "PHOTO", "ABSOLUTE_PATH");
    echo $absolute_server_path;
    $file_name = $_POST['name'];
    $filepath = $absolute_server_path . sha1($file_name) . '_' . basename($file_name);
    $files = $_FILES['files'];

    if (!move_uploaded_file( $_FILES['files']['tmp_name'][0], $filepath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    } else {
        // All good, send the response
        echo json_encode([
            'status' => 'ok',
            'path' => $filepath
        ]);
    }

} catch (RuntimeException $e) {
	// Something went wrong, send the err message as JSON
	http_response_code(400);

	echo json_encode([
		'status' => 'error',
		'message' => $e->getMessage()
	]);
}