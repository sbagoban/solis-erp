
<?php

function pdo_con() {

    $host = 'localhost';
    $dbname = 'dbsolis_sandeep';
    $user = 'root';
    $pass =  'SmS+2035_KgB';

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

