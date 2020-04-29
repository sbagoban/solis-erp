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
    // $message  = include("mail_template.php");
    // $message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    // $message .= '<html lang="en">';
    // $message .= '<head>';
    // $message .= '<meta http-equiv="Content-Type" content="text/html charset=UTF-8" />';
    // $message .= '<title>CodePen - Responsive Email Template</title>';
    // $message .= '</head>';
    // $message .= '<body>';
    // $message .= '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody>';
    // $message .= '<tr>';
    // $message .= '<td align="center">';
    // $message .= '<table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody>';
    // $message .= '<tr>';
    // $message .= '<td align="center" valign="center" background="https://solisconnect.com/assets/bg_login-6e5eb0c21566c8bb8cd6ed33003e029e818d602fe20327a17761d1004e767081.png" bgcolor="#66809b" style="background-size:cover; background-position:bottom; height:100">';
    // $message .= '<table class="col-100" width="600" height="100" border="0" align="center" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody><tr><td height="80"></td></tr>';
    // $message .= '<tr><td align="center" style="line-height: 0px;"><img style="display:block; line-height:0px; font-size:0px; border:0px;" src="https://solisconnect.com/assets/logo_solis_login_new-e9f52d80fd2df2a44f6d99c3e2ab1b6930b0b1bd0ef1996e7e75a2c7abc0b525.png" width="109" height="103" alt="logo">';
    // $message .= '</td></tr>';
    // $message .= '<tr><td height="80"></td></tr>';
    // $message .= '</tbody></table></td></tr></tbody></table></td></tr>';
    // $message .= '<tr><td align="center">';
    // $message .= '<table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:20px; margin-right:20px;">';
    // $message .= '<tbody>';
    // $message .= '<tr><td height="35"></td></tr>';
    // $message .= '<tr><td align="center" style="font-size:22px; font-weight: bold; color:#2a3a4b;">Booking Details and Booking Specification</td></tr>';
    // $message .= '<tr><td height="10"></td></tr>';
    // $message .= '<tr><td align="center" style="font-size:14px; color:#757575; line-height:24px; font-weight: 300;">Please find below booking done by :';
    // $message .= '</td></tr></tbody></table></td></tr>';
    // $message .= '<tr><td align="center">';
    // $message .= '<table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" >';
    // $message .= '<tbody><tr>';
    // $message .= '<td height="10"></td></tr><tr>';
    // $message .= '<td><table width="590" border="0" align="center" cellpadding="0" cellspacing="0"><tbody>';
    // $message .= '<tr><td height="10"></td></tr>';
    // $message .= '<tr><td align="center">';
    // $message .= '<table width="10" border="0" align="left" cellpadding="0" cellspacing="0"></table>';
    // $message .= '</td><td align="center">';
    // $message .= '<table width="150" border="0" align="left" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody>';
    // $message .= '<tr align="left">';
    // $message .= '<td style="font-size:16px; color:#2b3c4d; line-height:20px; font-weight: bold;">Accom Details</td>';
    // $message .= '</tr>';
    // $message .= '<tr align="left">';
    // $message .= '<td style="font-size:14px; color:#757575; line-height:20px; font-weight: 300;">Room Policy</td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '<tr>';
    // $message .= '<td align="center">';
    // $message .= '<table width="70" border="0" align="left" cellpadding="0" cellspacing="0">';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '<td align="center">';
    // $message .= '<table width="450" border="1" align="left" cellpadding="4" cellspacing="0">';
    // $message .= '<tbody>';
    // $message .= '<tr align="left">';
    // $message .= '<td width="100" style="font-size:14px; color:#757575; line-height:20px; font-weight: 300;">Room Charge</td>';
    // $message .= '<td style="font-size:16px;line-height: 26px;text-align: center;color: white;font-weight: 100;" bgcolor="#2a3b4c">Persons</td>';
    // $message .= '</tr>';
    // $message .= '<tr align="left">';
    // $message .= '<td width="100" style="font-size:14px; color:#757575; line-height:20px; font-weight: 300;">Room Status</td>';
    // $message .= '<td style="font-size:16px;line-height: 26px;text-align: center;color: white;font-weight: 100;" bgcolor="#34495e">On Request</td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '<tr>';
    // $message .= '<td height="30"></td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '<tr><td height="5"></td></tr>';
    // $message .= '<tr><td align="center">';
    // $message .= '<table align="center" class="col-600" width="600" border="0" cellspacing="0" cellpadding="0">';
    // $message .= '<tbody><tr>';
    // $message .= '<td align="center" bgcolor="#2a3b4c">';
    // $message .= '<table class="col-600" width="600" align="center" border="0" cellspacing="0" cellpadding="0">';
    // $message .= '<tbody>';
    // $message .= '<tr><td height="33"></td></tr>';
    // $message .= '<tr>';
    // $message .= '<td><table class="col1" width="200" border="0" align="left" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody><tr><td height="18"></td></tr>';
    // $message .= '<tr><td align="center"></td></tr></tbody></table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '<tr>';
    // $message .= '<td height="33"></td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '</tbody>';
    // $message .= '</table>';
    // $message .= '</td>';
    // $message .= '</tr>';
    // $message .= '<tr>';
    // $message .= '<td align="center">';
    // $message .= '<table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:20px; margin-right:20px;">';
    // $message .= '<tr>';
    // $message .= '<td align="center">';
    // $message .= '<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">';
    // $message .= '<tbody>';
    // $message .= '<tr>';
    // $message .= '<td align="center" bgcolor="#34495e" height="50">';
    // $message .= '<table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0">';
    // $message .= '<tbody><tr><td height="25"></td></tr>';
    // $message .= '<tr><td align="center" style="font-size:26px; font-weight: 500; color:#f1c40f;">Thank You.</td>';
    // $message .= '</tr>';
    // $message .= '<tr><td height="25"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>';
    // $message .= '</body>';
    // $message .= '</html>';

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