<?php
// wt.php
// 5-9-11 rlb
// opening page of weight tracker program
// self-referencing form 
// non-database validation will be done w javascript
require_once("wt_include.php");
// check for $_COOKIE to see if user stayed logged in
if (isset($_COOKIE['user']))
{
	$_SESSION['user'] = $_COOKIE['user'];
}
if (isset($_SESSION['user']))
{
	header ("Location: wt_member.php?js=yes");
}
$formFocus = "document.login_form.username_login.focus();";
$mainMesg = "HOME PAGE";
$username_login = "";

require("wt_login_process.php");


?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<title>Weight Tracker Home Page</title>
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/wt_member.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/wt_tab_links.css" type="text/css">
		<script type="text/javascript" src="ajax.js"></script>
		<script type="text/javascript" src="funcs.js"></script>
	</head>
	<body OnLoad="<?php echo $formFocus;?>">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php require("wt_login_form.php"); ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<?php require("wt_home_page.php"); ?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>