<?php
// wt_contact.php
// 5-30-11 rlb
// email contact form for weight tracker program
// called from $wt_sidebar.php of wt_member, et al
// uses phpmailer routine,sent through boutilier.dyndns-free.com
// using gmail account as mail server
// self-referencing form


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


// see if user has cancelled.  send back to wt_member.ph
if (isset($_POST['cancel']))
{
	header("Location: wt_member.php");
}
// see if form has been submitted
if (isset($_POST['submit']))
{
	$subject = trim($_POST['subject']);
	$body = $_POST['body'];
	$body = str_replace("\n","<br>",$body);
	$sendTo = "ronbout@yahoo.com";
	$sendFromName = $username;
	$sendToName = "WT Admin";
	if ($body)
	{
		$result = gmail($sendTo, $subject, $body, $sendFromName, $sendToName);
		if ($result)
		{
			$msg = "Error sending email.  Error Code: ". $result;
		}
		else
		{
			$msg = "Message sent.";
		}	
		
	}
	else
	{
		$msg = "Please enter message to send or press <Cancel>.";
	}
}

require("wt_sidebar.php");

?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<title>Weight Tracker Contact Page</title>
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
				var ch = confirm("Quit and return to Home Page?");
				if (ch) 
				{
					thisButton.form.submit();
				}
				return false;
			}
		//--></script>
	</head>
	<body OnLoad="document.contact_form.subject.focus();">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php echo $sideMsg; echo $sidebar; 
				?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<div id="div_contact">
					<h3>User form below to send Email to Weight Tracker.</h3>
					<form action="wt_contact.php" method="post" name="contact_form">
					<table cellspacing="4px">
						<tr>
							<td></td><td><h4><?php echo $msg; ?></h4></td>
						</tr>
						<tr>
							<td class="contact_label">Subject:</td>
							<td><input type="text" name="subject" size="30"></td>
						</tr>
						<tr><td> </td><td></td></tr>
						<tr>
							<td class="contact_label">Body:</td>
							<td><textarea name="body" rows="15" cols="60"></textarea></td>
						</tr>	
						<tr><td> </td><td> </td></tr>
						<tr><td> </td>
							<td><input class="contact_button" type="submit" name="submit" value="Send" 
										onclick="return validate_form(this)">&nbsp; &nbsp;
								<input class="contact_button" type="submit" name="cancel" value="Cancel" 
										onclick="return cancel_form(this);">
							</td>
						</tr>
					</table>
					</form>
				</div>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>


?>