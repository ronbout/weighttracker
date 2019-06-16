<?php
// wt_confirm_warn.php
// 5-21-11 rlb
// informs the user that the account has not
// been activated through the email address
// called from wt_login_process.php
require_once("wt_include.php");
// check for 'email' SESSION variable
if (!isset($_SESSION['email']))
{
	// came here incorrectly
	header("Location: wt.php");
}

// test if sidebar login has been submitted
require("wt_login_process.php");

$mainMsg = "";
$mainMsg .= "<h4 class='warn'>This account has not been activated yet.<br>";
$mainMsg .= "To activate, please access your email account. Find the<br>";
$mainMsg .= "email from Administrator with the Subject line:<br>";
$mainMsg .= "Weight Tracker Confirmation<br>";
$mainMsg .= "Click on the link in the email and you will be ready<br>";
$mainMsg .= "to start your journey!</h4><br>";
$mainMsg .= "<p class='confirm'>Email Never Arrived?</p>";
$mainMsg .= "<p class='link'><a href='wt_email.php'>Resend Email</a></p><br>";
$mainMsg .= "<p class='confirm'>Wrong email?</p>";
$mainMsg .= "<p class='link'><a href='wt_change_email.php'>Change Email</a></p><br>";
$mainMsg .= "<p class='confirm'>Return to Home Page</p>";
$mainMsg .= "<p class='link'><a href='wt.php'>Home Page</a></p>";

?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<title>Weight Tracker Activation Warning Page</title>
		<script type="text/javascript" src="funcs.js"></script>
	</head>
	<body>
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php require("wt_login_form.php"); ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<?php echo $mainMsg; ?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>