<?php
// wt_member.php
// 5-8-11 rlb
// display member page of weight tracker program 
// main page is wt.php
// called from wt.php, wt_change_pass.php and wt_register
require_once("wt_include.php");
// either SESSION (login) or COOKIE (persistent login) must be set,
if (!isset($_SESSION['user']) && !isset($_COOKIE['user']))
{
	header("Location: wt.php");
}

// if from COOKIE, set SESSION
if (!isset($_SESSION['user'])) $_SESSION['user'] = $_COOKIE['user'];
$user = $_SESSION['user'];
// login to database
require("wt_connect.php");
// if only have user_id, get rest of member info
if (!isset($_SESSION['member']))
{
	$errCode = "";
	// load Member class
	$member = loadMember($mysqli, $user, $errCode);
	if ($errCode) die("Database error: ".$errCode);
	$_SESSION['member'] = serialize($member);
	$_SESSION['username'] = $member->getUserName();
}
else
{
	$member = unserialize($_SESSION['member']);
}
$onLoad = "";
// get mode, which determines main content
require("wt_member_mode.php");
// display sidebar links
require("wt_sidebar.php");

?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/wt_member.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/wt_tab_links.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/wt_food.css" type="text/css">
		<link rel="stylesheet" href="stylesheets/tooltip.css" type="text/css">
		<script type="text/javascript" src="ajax/ajax.js"></script>
		<script type="text/javascript" src="funcs.js"></script>
		<script type="text/javascript" src="tooltip.js"></script>
		<script type="text/javascript" src="geometry.js"></script>
		<script type="text/javascript" src="food/food_funcs.js"></script>
		<title>Weight Tracker Member Page</title>
		<?php if ($mode=="cal" || $mode=="news") $cal->css(); ?>
	</head>
	<body <?php echo $onLoad;?> onUnload="unload_member();">
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php echo $sideMsg; echo $sidebar; ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
				<?php require($mainInclude); ?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div><!-- end of page container  -->
	</body>
</html>