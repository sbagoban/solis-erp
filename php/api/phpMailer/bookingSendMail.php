<?php
try {
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
    $to      = 'sbagoban@solis360.com';
    $subject = 'Reservation Done By '. $_SESSION["solis_username"];
    $crlf = "\r\n";
    
    // To send HTML mail, the Content-type header must be set
    $from      = 'booking.resa@solis360.com';
    $headers  = 'MIME-Version: 1.0' . $crlf;
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . $crlf;
    
    // Create email headers
    $headers .= 'From: '.$from. $crlf .
        'Reply-To: '.$from. $crlf .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, file_get_contents("mail_template.php"), $headers);

} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>