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
    
    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }
    
    $gidfrom = $_POST["gidfrom"];
    $gidto = $_POST["gidto"];

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $con->beginTransaction();
    
    //share menu rights first
    $sql = "select * from tblgroupmenurights WHERE GrpFK = :GrpFK";
    $query = $con->prepare($sql);
    $query->execute(array(":GrpFK"=>$gidfrom));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $menuid = $rw["MenuFK"];
        
        //check no duplicate in group to
        $sql = "select * from tblgroupmenurights WHERE GrpFK = :GrpFK AND MenuFK=:MenuFK";
        $query_chk = $con->prepare($sql);
        $query_chk->execute(array(":GrpFK"=>$gidto, ":MenuFK"=>$menuid));
        if (!$row = $query_chk->fetch(PDO::FETCH_ASSOC)) {
            //insert the value
            $sql = "INSERT INTO tblgroupmenurights (GrpFK,MenuFK) VALUES (:GrpFK,:MenuFK)";
            $query_ins = $con->prepare($sql);
            $query_ins->execute(array(":GrpFK"=>$gidto, ":MenuFK"=>$menuid));
        }        
    }
    
    //then share process rights
    $sql = "select * from tblgroupprocess WHERE ugrpid = :ugrpid";
    $query = $con->prepare($sql);
    $query->execute(array(":ugrpid"=>$gidfrom));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $processid = $rw["processid"];
        
        //check no duplicate in group to
        $sql = "select * from tblgroupprocess WHERE ugrpid = :ugrpid AND processid=:processid";
        $query_chk = $con->prepare($sql);
        $query_chk->execute(array(":ugrpid"=>$gidto, ":processid"=>$processid));
        if (!$row = $query_chk->fetch(PDO::FETCH_ASSOC)) {
            //insert the value
            $sql = "INSERT INTO tblgroupprocess (ugrpid,processid) VALUES (:ugrpid,:processid)";
            $query_ins = $con->prepare($sql);
            $query_ins->execute(array(":ugrpid"=>$gidto, ":processid"=>$processid));
        }        
    }
    
    $con->commit();
    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage() . " on Line " . $ex->getLine())));
}
?>
