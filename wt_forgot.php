<?php
// wt_forgot.php
// 5-13-11 rlb
// page for user who has forgotten password
// will send new password to email address
// called from wt.php
require_once("wt_include.php");
$username = "";
$pass = "";
$msg = "";
$errCode = "";
$formFocus = "document.password_form.username.focus();";
// see if user has cancelled.  send back to wt.ph
if (isset($_POST['cancel']))
{
	header("Location: wt.php");
}
// test if sidebar login has been submitted
require("wt_login_process.php");
// now test if new password form has been submitted
if (isset($_POST['submit']))
{
	// validate data
	$errFlg = false;
	// check for missing data
	if (!isset($_POST['username']) || !$_POST['username'] || !isset($_POST['email']) || !$_POST['email'])
	{
		$msg = "Both Username and Email are required";
		$errFlg = true;
	}
	// check for valid email
	if (!$errFlg && !testEmail($_POST['email']))
	{
		$msg = "Invalid Email format";
		$errFlg = true;
	}
	if (!$errFlg)
	{
		// connect to database
		require("wt_connect.php");
		// attempt to login user
		$username = strtolower(trim(($_POST['username'])));
		$email = strtolower(trim(($_POST['email'])));
		if (!($user_info = forgotPass($mysqli, $username, $email, false, $errCode)))
		{
			// could not log in -- find out why
			switch($errCode)
			{
				case -1:
					$msg = "Invalid Username";
					break;
				case -2:
					$msg = "Invalid Email.";
					$formFocus = "document.password_form.email.focus();";
					break;
				default:
					$msg = "Database error: ".$errCode;
			}
			$mysqli->close();
		}
		else
		{
			$mysqli->close();
				// send to info page about email activation
				$_SESSION['email'] = $username;
				$_SESSION['email_pass'] = $user_info['password'];
				header ("Location: wt_sendpass.php");
		}
	}
}

?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<script type="text/javascript" src="funcs.js"></script>
		<title>Weight Tracker New Password</title>
	</head>
	<body OnLoad="<?php echo $formFocus;?>">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php require("wt_login_form.php"); ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<h3>Send New Password</h3>
				<h4>Your password cannot be retrieved.<br>
					Enter your username and email address for a new password.</h4>
				<form name="password_form" action="wt_forgot.php" method="POST">
				<table cellspacing="5px">
				<tr>
					<td></td><td class="err_msg"><?php echo $msg; ?></td>
				</tr>
				<tr>
					<td class="label">Username: &nbsp;</td>
					<td><input type="text" name="username" size="30" maxlength="20" value="<?php echo $username; ?>"></td>
				</tr>
				<tr>
					<td class="label">Email Addr: &nbsp;</td>
					<td><input type="text" name="email" size="30" maxlength="50" value="<?php echo $pass; ?>"></td>
				</tr>
				<tr>
					<td> </td>
					<td><input type="submit" name="submit" value="Send" onclick="return validate_forgot(this)">&nbsp; &nbsp;
						<input type="submit" name="cancel" value="Cancel" onclick="return cancel_forgot(this);">
					</td>
				</tr>
				</table>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>