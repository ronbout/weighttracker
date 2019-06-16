<?php
// wt_change_pass.php
// 5-14-11 rlb
// allow logged in user to change password
// called from wt_member.php
require_once("wt_include.php");
// make sure user is logged in
if (!isset($_SESSION['user']))
{
	// send back to login page
	header("Location: wt.php");
}
$user = $_SESSION['user'];
$username = $_SESSION['username'];
$member = unserialize($_SESSION['member']);
$msg = "";
$errCode = "";
$formFocus = "document.password_form.oldPass.focus();";
// see if user has cancelled.  send back to wt.ph
if (isset($_POST['cancel']))
{
	header("Location: wt_member.php");
}
if (isset($_POST['submit']))
{
	// validate data
	$errFlg = false;
	// check for missing data
	if (!isset($_POST['oldPass']) || !$_POST['oldPass'] || !isset($_POST['newPass']) || !$_POST['newPass'])
	{
		$msg = "All fields are required";
		$formFocus = "document.password_form.oldPass.focus();";
		$errFlg = true;
	}
	// check Password length
	if (!$errFlg && strlen($_POST['newPass']) < 6)
	{
		$msg = "Password length must be at least 6 characters.";
		$formFocus = "document.password_form.newPass.focus();";
		$errFlg = true;
	}
	// check both passwords match
	if (!$errFlg && $_POST['newPass'] != $_POST['newPass2'])
	{
		$msg = "New Passwords must match.";
		$formFocus = "document.password_form.newPass.focus();";
		$errFlg = true;
	}
	if (!$errFlg)
	{
		// connect to database
		require("wt_connect.php");
		// attempt to login user
		$pass = md5(trim($_POST['oldPass']));
		$newPass = md5(trim($_POST['newPass']));
		if (!($user_info = userLogin($mysqli, $username, $user, $pass, false, $errCode)))
		{
			// could not log in -- find out why
			switch($errCode)
			{
				case -1:
					$msg = "Invalid Username";
					break;
				case -2:
					$msg = "Invalid Password.";
					$formFocus = "document.password_form.oldPass.focus();";
					break;
				default:
					$msg = "Database error: ".$errCode;
			}
			$mysqli->close();
		}
		else
		{
			// update password
			if (!updatePass($mysqli, $user, $newPass, $errCode))
			{
				$msg = "Database error updating password. Error: ".$errCode;
			}
			else
			{
				// set SESSION variable
				// display message to user
				$msg = "<h3>Your password has been changed.</h3>";
			}
			$mysqli->close();
		}
	}
}
else
{
	// code for any 1st time pre-HTML processing
}
require("wt_sidebar.php");
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<script type="text/javascript" src="funcs.js"></script>
		<title>Weight Tracker Change Password</title>
	</head>
	<body OnLoad="<?php echo $formFocus;?>">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php echo $sideMsg; echo $sidebar; ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<h3>Change Password</h3>
				<h4>Enter your old password.<br>
					Then enter the new password twice to confirm.</h4>
				<form name="password_form" action="wt_change_pass.php" method="POST">
				<table cellspacing="5px">
				<tr>
					<td></td><td class="err_msg"><?php echo $msg; ?></td>
				</tr>
				<tr>
					<td class="label">Username: &nbsp;</td>
					<td><input type="text" name="username" size="30" maxlength="20" readonly="readonly" 
							   value="<?php echo $username; ?>"></td>
				</tr>
				<tr>
					<td class="label">Old Password: </td>
					<td><input type="password" name="oldPass" maxlength="15" size="30"></td>
				</tr>
				<tr>
					<td class="label">New Password (6-15 chars): </td>
					<td><input type="password" name="newPass" maxlength="15" size="30"></td>
				</tr>
				<tr>
					<td class="label">Confirm Password: </td>
					<td><input type="password" name="newPass2" maxlength="15" size="30"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="Change" onclick="return validate_change_pass(this)">&nbsp; &nbsp;
						<input type="submit" name="cancel" value="Cancel" onclick="return cancel_change_pass(this);">
					</td>
				</tr>
				<tr><td> </td><td> </td></tr>
				</table>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>