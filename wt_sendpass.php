<?php
// wt_sendpass.php
// 5-14-11 rlb
// sends email to user with new password
// displays that fact to the user
// used with wt.php weight tracker program
// called from wt_forgot.php
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
	$errMsg = "";
	$username = $_SESSION['email'];
	$pass = $_SESSION['email_pass'];
	$dbpass = md5($pass);
	$errCode = "";
	unset($_SESSION['email']);
	unset($_SESSION['email_pass']);

	if (!($user_info = userLogin($mysqli, $username, "", $dbpass, false, $errCode)))
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
		$subject = "Weight Tracker Password";
		$from = "Administrator";
		$toName = $name;
		$link = "http://boutilier.dyndns-free.com/apache/wt/wt.php";

		$body = "Hi, ".$name.".<br><br>";
		$body .= "We have reset your password.  It is:<br><br>";
		$body .= "<p style='font-weight:bold;'>".$pass."</p>";
		$body .= "<br>";
		$body .= "You should login using this password.  Then change it to <br>";
		$body .= "something you will remember.  (Use login link below)<br>";
		$body .= "Remember, the initial user/password is boomer/sooner.<br>";
		$body .= "<a href='".$link."'>Weight Tracker Login</a>";
		if ($result = gmail($to, $subject, $body, $from, $toName))
		{
			$msg = "Could not send email. Error: ".$result;
		}
		else
		{
			$msg = "<h3>You're new password has been sent.</h3>";
			$msg .= "<p>An email containing the new password has been sent to ".$to.".<p>";
			$msg .= "<p>Use that password to login and then change it to a new one.</p>";
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
		<title>Weight Tracker Password Email</title>
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