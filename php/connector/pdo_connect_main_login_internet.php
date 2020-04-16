<?php

function connect_login_pdo() {

    $host = 'localhost';
    $dbname = 'dbsolis';
    $user = 'login_user'; //login_user
    $pass = 'SmS-2035+2027'; //SmS-2035+2027

    try {
        $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $dbh = null;
        echo $e->getMessage();
    }

    return ($dbh);
}

?>
