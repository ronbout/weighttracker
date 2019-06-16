<?php
// wt_connect.php
// 5-12-11 rlb
// datbase connection code for weight tracker programs
require_once("wt_include.php");
// connect to database
if (!($mysqli = mysqlConnect("weighttracker",$errCode)))
{
	die("Could not connect to database.  Error Code: ".$errCode);
}
?>