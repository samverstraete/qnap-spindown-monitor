<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("settings.php");

class DB { static $link; }
DB::$link = mysqli_connect("127.0.0.1", $SQL_USER, $SQL_PASS, $SQL_DB);
if (!DB::$link) die("Error: Unable to connect to MySQL.");


upload();

mysqli_close(DB::$link);


function upload(){
	$bit = substr($_REQUEST['state'],5,1);
	file_put_contents("debug.txt", date('c') & " " & $_REQUEST['state'] & "\n", FILE_APPEND);
	if($bit === false) $bit = 8; //save error values as 8
	if(!is_numeric($bit)) $bit = 7; //no valid numbers, save as 7
	$bit++; //add +1 for viewability in graph
	
	$sql = "INSERT INTO `gemini` (`time`, `state`) VALUES (now(), $bit)";
	if(mysqli_query(DB::$link, $sql)) {
		echo "ok";
	} else {
		echo mysqli_error(DB::$link);
		file_put_contents("sql.txt", mysqli_error(DB::$link));
	}
}

?>	