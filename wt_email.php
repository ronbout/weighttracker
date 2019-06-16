<?php
// wt_email.php
// 5-12-11 rlb
// sends email to user for confirmation and 
// displays that fact to the user
// used with wt.php weight tracker program
// called from wt.php and wt_register.php
require_once("wt_include.php");
// check for 'email' SESSION variable
if (!isset($_SESSION['email']))
{
	// came here incorrectly
	header("Location: wt.php");
}
// test if sidebar login has been submitted
require("wt_login_process.php");
if (!$msg_login)
{
	// login to database
	require("wt_connect.php");
	// get user info, including confirm_value
	$msg = "";
	$username = $_SESSION['email'];
	$pass = $_SESSION['email_pass'];
	$errCode = "";
	unset($_SESSION['email']);
	unset($_SESSION['email_pass']);

	if (!($user_info = userLogin($mysqli, $username, "", $pass, false, $errCode)))
	{
		// could not log in -- find out why
		switch($errCode)
		{
			case -1:
				$msg = "Invalid Username";
				break;
			case -2:
				$msg = "Invalid Password.";
				break;
			default:
				$msg = "Database error: ".$errCode;
		}
	}
	else
	{
		// send email
		ignore_user_abort(true);
		$name = ($user_info['first_name']) ? $user_info['first_name'] : $username;
		$to = $user_info['email'];
		$subject = "Weight Tracker Confirmation";
		$from = "Administrator";
		$toName = $name;
		$link = "http://www.ronboutilier.com/wt/wt_activate.php?id=".$user_info['member_id']."&code=".$user_info['confirm_value'];
		$body = "Congratulations on signing up at Weight Tracker!<br><br>";
		$body .= "To activate your account, just click on the link below.<br>";
		$body .= "You will then be able to track the journey to a better you!<br><br>";
		$body .= "<a href='".$link."'>".$link."</a>";
		if ($result = gmail($to, $subject, $body, $from, $toName))
		{
			$msg = "Could not send email. Error: ".$result;
		}
		else
		{
			$msg = "<h3>Thanks for registering.</h3>";
			$msg .= "<p>A confirmation email has been sent to ".$to.".<p>";
			$msg .= "<p>Clicking on the link in your email will activate your account.</p>";
			$msg .= "Return to <a href='wt.php'>Home Page</a>";
		}
	}
	$mysqli->close();
}
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<title>Weight Tracker Email Page</title>
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
				<?php echo $msg; ?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>