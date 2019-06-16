<?php
// wt_change_acct.php
// 5-20-11 rlb
// allow the user to change name information for weight tracker
// if user info grows, more fields will be added
// non-database verification is done through javascript
// self referencing form

require_once("wt_include.php");
// make sure user came from member page
if (!isset($_SESSION['user']) || !isset($_SESSION['member']))
{
	header("Location: wt.php");
}
$member = unserialize($_SESSION['member']);
$mInfo = $member->getMemberInfo();
$msg = "<br>";
$errCode = "";
$user = $_SESSION['user'];
$username = $mInfo['user_name'];
$firstName = $mInfo['first_name'];
$lastName = $mInfo['last_name'];

// see if user has cancelled.  send back to wt_member.ph
if (isset($_POST['cancel']))
{
	header("Location: wt_member.php");
}
// see if form has been submitted
if (isset($_POST['submit']))
{
	// validate data -- nothing to validate, but might change if more info added later
	$errFlg = false;
	// check for changed data
	$fName = trim($_POST['firstName']);
	$lName = trim($_POST['lastName']);
	if ($fName == $firstName && $lName == $lastName)
	{
		$msg = "No info was changed.  <br>Hit Cancel to return to Member Page";
	}
	else
	{
		// connect to database
		require("wt_connect.php");
		// check if user already exists
		$errCode = "";
		if (!changeUser($mysqli, $user, $fName, $lName, $errCode))
		{
			// could not update
			switch($errCode)
			{
				// currently only one option, but keeping same format as usual error checking
				default:
					// unknown error
					$msg = "Unknown error ".$errCode.".";
			}
			$mysqli->close();
		}
		else
		{
			$mysqli->close();
			$mInfo['first_name'] = $fName;
			$mInfo['last_name'] = $lName;
			$member->setMemberInfo($mInfo);
			$_SESSION['member'] = serialize($member);
			header ("Location: wt_member.php");
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
		<title>Weight Tracker Change User Info Page</title>
		<script type="text/javascript" src="funcs.js">
		</script>
		<script type="text/javascript"><!--
			function validate_form(thisButton)
			{
				var thisForm = thisButton.form;
				return true;
			}
			function cancel_form(thisButton)
			{
				thisButton.form.submit();
				return false;
			}
		//--></script>
	</head>
	<body OnLoad="document.change_form.firstName.focus();">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php echo $sideMsg; echo $sidebar; 
				?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
			<h3>Please enter your information.</h3>
			<form action="wt_change_acct.php" method="post" name="change_form">
			<table cellspacing="2px">
				<tr>
					<td></td><td class="err_msg"><?php echo $msg; ?></td>
				</tr>
				<tr>
					<td class="label"> User Name: </td>
					<td><input type="text" name="user" maxlength="20" size="30" readonly="readonly" 
							   value="<?php echo $username;?>"></td>
				</tr>
				<tr>
					<td class="label">First Name: &nbsp;</td>
					<td><input type="text" name="firstName" maxlength="25" size="30" value="<?php echo $firstName;?>"></td>
				</tr>
				<tr>
					<td class="label">Last Name: &nbsp;</td>
					<td><input type="text" name="lastName" maxlength="25" size="30" value="<?php echo $lastName;?>"></td>
				</tr>
				<tr><td> </td><td> </td></tr>
				<tr>
					<td></td>
					<td><input type="submit" name="submit" value="Update" onclick="return validate_form(this)">&nbsp; &nbsp;
						<input type="submit" name="cancel" value="Cancel" onclick="return cancel_form(this);">
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