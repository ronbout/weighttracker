<?php
// wt_change_email.php
// 5-16-11 rlb
// allow logged in user to change email
// called from wt.php
require_once("wt_include.php");
$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : "";
$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "";
$msg = "";
$errCode = "";
if ($username)
	$formFocus = "document.email_form.oldPass.focus();";
else
	$formFocus = "document.email_form.username.focus();";
if (isset($_SESSION['member']))
	$member = unserialize($_SESSION['member']);
// see if user has cancelled.  send back to wt.ph
if (isset($_POST['cancel']))
{
	header("Location: wt.php");
}
if (!$user)
{
	// came from wt.php w/o confirmation.  keep login sidebar
	require("wt_login_process.php");
}
if (isset($_POST['submit']))
{
	// validate data
	$errFlg = false;
	// check for missing data
	if (!isset($_POST['username']) || !$_POST['username'] || !isset($_POST['oldPass']) || !$_POST['oldPass'] ||
		!isset($_POST['newEmail']) || !$_POST['newEmail'])
	{
		$msg = "All fields are required";
		$errFlg = true;
	}
	// check for valid email
	if (!$errFlg && !testEmail($_POST['newEmail']))
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
		$pass = md5(trim($_POST['oldPass']));
		$newEmail = trim($_POST['newEmail']);
		if (!($user_info = userLogin($mysqli, $username, "", $pass, false, $errCode)))
		{
			// could not log in -- find out why
			switch($errCode)
			{
				case -1:
					$msg = "Invalid Username";
					$formFocus = "document.email_form.username.focus();";
					break;
				case -2:
					$msg = "Invalid Password.";
					$formFocus = "document.email_form.oldPass.focus();";
					break;
				default:
					$msg = "Database error: ".$errCode;
			}
			$mysqli->close();
		}
		else
		{
			// update email
			$userId = $user_info['member_id'];
			if (!updateEmail($mysqli, $userId, $newEmail, $errCode))
			{
				// could not change email -- find out why
				switch($errCode)
				{
					case -1:
						$msg = "Email already registered.";
						$formFocus = "document.email_form.username.focus();";
						break;
					default:
						$msg = "Database error updating email. Error: ".$errCode;
				}
				
			}
			else
			{
				// set SESSION variable
				// display message to user
				$msg = "<h3>Your email has been changed.</h3>";
				if ($user_info['confirm_flag'])
				{
					$msg .= "<p>Return to <a href='wt_member.php'>Member Page</a></p>";
				}
				else
				{
					$_SESSION['email'] = $username;
					$_SESSION['email_pass'] = $pass;
					$msg = "<p>Click link below to send Activation Email. </p>";
					$msg .= "<p><a href='wt_email.php'>Resend Email</a></p>";
				}
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
		<script type="text/javascript" src="funcs.js">
		</script>
		<script type="text/javascript"><!--
			function validate_form(thisButton)
			{
				var thisForm = thisButton.form;
				if (thisForm.username.value == '')
				{
					alert("Username is required.");
					thisForm.username.focus();
					return false;
				}				
				if (thisForm.oldPass.value == '')
				{
					alert("Password is required.");
					thisForm.oldPass.focus();
					return false;
				}
				if (thisForm.newEmail.value == '')
				{
					alert("New Email is required.");
					thisForm.newEmail.focus();
					return false;
				}
				// check email for valid format 
				if (!testEmail(thisForm.newEmail.value))
				{
					alert("Invalid Email Address.");
					thisForm.newEmail.focus();
					return false;
				}
				return true;
			}
		//--></script>
		<title>Weight Tracker Change Email</title>
	</head>
	<body OnLoad="<?php echo $formFocus;?>">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php 
					if (!$user)
					{
						require("wt_login_form.php");
					}
					else
					{
						echo $sideMsg; 
						echo $sidebar; 
					}
				?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<h3>Change Email</h3>
				<h4>Enter your password for verification.<br>
					Then enter the new Email Address.</h4>
				<form name="email_form" action="wt_change_email.php" method="POST">
				<table cellspacing="5px">
				<tr>
					<td></td><td class="err_msg"><?php echo $msg; ?></td>
				</tr>
				<tr>
					<td class="label">Username: &nbsp;</td>
					<td><input type="text" name="username" size="30" maxlength="20" 
							   value="<?php echo $username; ?>"></td>
				</tr>
				<tr>
					<td class="label">Password: </td>
					<td><input type="password" name="oldPass" maxlength="15" size="30"></td>
				</tr>
				<tr>
					<td class="label">New Email Address: </td>
					<td><input type="text" name="newEmail" maxlength="50" size="30"></td>
				</tr>
				<tr>
					<td> </td><td> </td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="Change" onclick="return validate_form(this)">&nbsp; &nbsp;
						<input type="submit" name="cancel" value="Cancel" onclick="return cancel_form(this);">
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