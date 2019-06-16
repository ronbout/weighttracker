<?php
// wt_activate.php
// 5-13-11 rlb
// membership activation page for weight tracker wt.php
// link on confirmation email leads to this page with
// user-specific GET variables, database is updated
// and user is given message
require_once("wt_include.php");
// check for GET variables
if (!isset($_GET['id']) || !isset($_GET['code']))
{
	header("Location: wt.php");
}
// login to database
require("wt_connect.php");
// confirm database info
$userId = $_GET['id'];
$confirmCode = $_GET['code'];
$errCode = "";
$msg = "";
if (!$user = confirmUser($mysqli, $userId, $confirmCode, $errCode))
{
	// could not log in -- find out why
	switch($errCode)
	{
		case -1:
			$msg = "Could not confirm. Invalid Data.";
			break;
		case -2:
			$msg = "<h3>You have already been activated!</h3>";
			$msg .= "<p>Proceed to <a href='wt.php'>Login Page</a></p>";
			break;
		default:
			$msg = "Could not confirm. Database error: ".$errCode;
	}
}
else
{
	// update database
	if (!activateUser($mysqli, $userId, $errCode))
	{
		$msg = "Database error activating user. Error: ".$errCode;
	}
	else
	{
		// display message to user
		$msg = "<h3>Your account has been activated!</h3>";
		$msg .= "<p>Proceed to <a href='wt.php'>Login Page</a></p>";
	}

}
$mysqli->close();
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<title>Weight Tracker Member Page</title>
		<script type="text/javascript" src="funcs.js"></script>
	</head>
	<body>
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php");?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<?php echo $msg; ?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>