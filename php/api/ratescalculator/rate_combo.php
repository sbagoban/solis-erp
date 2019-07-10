<?php

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

$toid = $_GET["toid"];

$query_c = $con->prepare("SELECT * FROM tbltouroperator WHERE id=:id");
$query_c->execute(array(":id" => $toid));

if ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
    
    $ug = array();
    
    $ratecode = $row["ratecode"];
    $ratecode_id = _getRateID($ratecode);
    if ($ratecode_id != "-1") {
        $ug[] = array('value' => $ratecode_id, 'text' => $ratecode);
    }

    $specialratecode = $row["specialratecode"];
    $specialratecode_id = _getRateID($specialratecode);
    if ($specialratecode_id != "-1") {
        if(!in_array(array('value' => $specialratecode_id, 'text' => $specialratecode), $ug))
        {
            $ug[] = array('value' => $specialratecode_id, 'text' => $specialratecode);
        }
    }

    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}

function _getRateID($ratecode) {
    global $con;

    $sql = "SELECT * FROM tblratecodes WHERE ratecodes=:ratecodes";

    $query = $con->prepare($sql);
    $query->execute(array(":ratecodes" => $ratecode));
    if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        return $row["id"];
    }

    return "-1";
}

?>