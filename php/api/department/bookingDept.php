<?php
/*Date : 2019, 2 October
Use : Get all department concerned with booking
Developer : slouis@solis360.com*/
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

$query_bookingDept = $con->prepare("
	SELECT * 
	FROM tbldepartments
	WHERE accomodation_concerned = 1"
						);
$query_bookingDept->execute();
$row_count_bookingDept = $query_bookingDept->rowCount();

if ($row_count_bookingDept > 0) {
    while ($row = $query_bookingDept->fetch(PDO::FETCH_ASSOC)) {
        $bookingDept[] = array(
            'id_dept' => $row['id'],
            'dept_name' => $row['deptname']
        );
    }
    $result = $bookingDept;
    echo json_encode($result);
} 

else {
    echo "NO DATA";
}
?>
