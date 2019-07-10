<?php

session_start();
//unset the session variables

unset($_SESSION["solis_userid"]);
unset($_SESSION["solis_grpid"]);
unset($_SESSION["solis_username"]);

unset($_SESSION["solis_userfullname"]);
unset($_SESSION["solis_grpname"]);

unset($_SESSION["solis_usercreated"]);
unset($_SESSION["solis_skin"]);


die(json_encode(array('OUTCOME' => 'OK')));
?>