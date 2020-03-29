<?php
session_start();

//generate random token and save it into session
$token = openssl_random_pseudo_bytes(16);
$token = bin2hex($token);

$_SESSION["token"] = $token;
echo $token;

?>