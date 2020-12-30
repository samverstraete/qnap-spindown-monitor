<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("settings.php");

class DB { static $link; }
DB::$link = mysqli_connect("127.0.0.1", $SQL_USER, $SQL_PASS, $SQL_DB);
if (!DB::$link) die("Error: Unable to connect to MySQL.");

$table = array(
    'cols' => array(
        array('label' => 'Time', 'type' => 'datetime'),
        array('label' => 'State', 'type' => 'number')
    ),
    'rows' => array()
);

$datefrom = date("Y-m-d", strtotime("-1 days")) . " 17:00:00";
$dateto = date("Y-m-d") . " 17:00:00";
if(isset($_REQUEST['from'])) {
	$datefrom = date("Y-m-d", strtotime($_REQUEST['from'] . " -1 day")) . " 17:00:00";
	$dateto = $_REQUEST['from'] . " 17:00:00";
}
$query = "SELECT * FROM `gemini` WHERE time BETWEEN '" . $datefrom . "' AND '" . $dateto . "' ORDER by time DESC";

if ($result = DB::$link->query($query)) {
	while($r = $result->fetch_assoc()) {
		$date = new DateTime($r['time']);
		$date->setTimezone(new DateTimeZone("Europe/Brussels"));
		$sdate = "Date(".$date->format('Y, m, d, H, i, s').")"; 
		$table['rows'][] = array('c' => array(
			array('v' => $sdate),
			array('v' => $r['state'])
		));
	}
}
echo json_encode($table, JSON_NUMERIC_CHECK);

mysqli_close(DB::$link);
?>