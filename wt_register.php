<?php
// wt_register.php
// 5-8-11 rlb
// register a new user for weight tracker program
// non-database verification is done through javascript
// self referencing form

require_once("wt_include.php");

$msg = "";
$errCode = "";
$user = "";
$jsScript = "";
$firstName = "";
$lastName = "";
$emailAddr = "";
// check if user already logged in and 1st time on page
if (isset($_SESSION['user']) && !isset($_POST['submit']) && !isset($_POST['cancel']))
{
	// already logged in.  see if still wants to register
	$user = $_SESSION['user'];
	$jsScript = '<script type="text/javascript">
			<!-- 
		   user_check("'.$user.'");     
		   //-->
		   </script>';
}
else
{
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
		if (!isset($_POST['user']) || !$_POST['user'] || !isset($_POST['pass']) || !$_POST['pass'] ||
			!isset($_POST['emailAddr']) || !$_POST['emailAddr'])
		{
			$msg = "Username, Email Address, and Password are all required.";
			$errFlg = true;
		}
		$user = trim($_POST['user']);
		$firstName = trim($_POST['firstName']);
		$lastName = trim($_POST['lastName']);
		$emailAddr = strtolower(trim($_POST['emailAddr']));
		// check for valid email
		if (!$errFlg && !testEmail($_POST['emailAddr']))
		{
			$msg = "Invalid Email format";
			$errFlg = true;
		}
		// check Password length
		if (!$errFlg && strlen($_POST['pass']) < 6)
		{
			$msg = "Password length must be at least 6 characters.";
			$errFlg = true;
		}
		// check both passwords match
		if (!$errFlg && $_POST['pass'] != $_POST['pass2'])
		{
			$msg = "Passwords must match.";
			$errFlg = true;
		}
		if (!$errFlg)
		{
			// connect to database
			require("wt_connect.php");
			// check if user already exists
			$pass = md5(trim($_POST['pass']));
			$errCode = "";

			if (!($user_info = userRegister($mysqli, $user, $firstName, $lastName, $pass, $emailAddr, $errCode)))
			{
				// could not register -- find out why
				switch($errCode)
				{
					case -1:
						$msg = "Username already exists. Please choose another.<br>";
						break;
					case -2:
						$msg = "Email address already registered.";
						break;
					default:
						// unknown error
						$msg = "Unknown error ".$errCode.".";
				}
				$mysqli->close();
			}
			else
			{
				$mysqli->close();
				// send to info page about email activation
				$_SESSION['email'] = $user;
				$_SESSION['email_pass'] = $pass;
				header ("Location: wt_email.php");
			}
		}
	}
	else
	{
		// code for any 1st time pre-HTML processing
	}
}
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<title>Weight Tracker Register Page</title>
		<script type="text/javascript" src="funcs.js"></script>
	</head>
	<body OnLoad="document.register.user.focus();">
	<?php echo $jsScript ?>
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php require("wt_login_form.php"); ?> 
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
			<h3>Please enter your information.</h3>
			<form action="wt_register.php" method="post" name="register">
			<table cellspacing="2px">
				<tr>
					<td></td><td class="err_msg"><?php echo $msg; ?></td>
				</tr>
				<tr>
					<td class="label">*</td>
					<td> - required field
					</td>
				</tr>
				<tr>
					<td class="label"> User Name: *</td>
					<td><input type="text" name="user" maxlength="20" size="30" value="<?php echo $user;?>"></td>
				</tr>
				<tr>
					<td class="label">First Name: &nbsp;</td>
					<td><input type="text" name="firstName" maxlength="25" size="30" value="<?php echo $firstName;?>"></td>
				</tr>
				<tr>
					<td class="label">Last Name: &nbsp;</td>
					<td><input type="text" name="lastName" maxlength="25" size="30" value="<?php echo $lastName;?>"></td>
				</tr>
				<tr>
					<td class="label">Email Address: *</td>
					<td><input type="text" name="emailAddr" maxlength="40" size="30" value="<?php echo $emailAddr;?>"></td>
				</tr>				
				<tr>
					<td class="label">Password (6-15 chars): *</td>
					<td><input type="password" name="pass" maxlength="15" size="30"></td>
				</tr>	
				<tr>
					<td class="label">Repeat Password: *</td>
					<td><input type="password" name="pass2" maxlength="15" size="30"></td>
				</tr>
				<tr><td> </td><td> </td></tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="Register" onclick="return validate_register(this)">&nbsp; &nbsp;
						<input type="submit" name="cancel" value="Cancel" onclick="return cancel_register(this);">
					</td>
				</tr>
			</table>
			</form>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>