<?php
// wt_sidebar.php
// 5-21-11 rlb
// contains sidebar html of for all member related
// pages in weight tracker program wt_member.php

// determine what page we are in
if (!isset($pg)) 
{
	$pg = $_SERVER['PHP_SELF'];
	$pg = pathinfo($pg);
	$pg = $pg['basename'];
}
$sideMsg = "<h4>Welcome, ".$_SESSION['username']."</h4>";
// get user level for special links
if (isset($member))
	$level = $member->getLevel();
else
	$level = 1;
$sidebar = "";
if ($pg == "wt_member.php")
{

	$sidebar .= "<a class='mem' href='wt_member.php?mode=cal&type=weight'>&nbsp;Calendar</a>";
	$sidebar .= "<a class='mem' href='wt_member.php?mode=graph&type=weight'>&nbsp;Graph</a>";
	$sidebar .= "<a class='mem' href='wt_member.php?mode=news'>&nbsp;News Feed</a>";
	$sidebar .= "<a class='memlast' href='wt_member.php?mode=food&type=nutrients' 
			onClick='window.location.href=\"wt_member.php?mode=food&type=nutrients&js=yes\"; return false;'>&nbsp;Food Setup</a>";
	$sidebar .= "<br>";
}

if ($pg != "wt_member.php") 
{
	if (isset($_SESSION['mode']) && $_SESSION['mode'] == 'food')
	{
		$sidebar .= "<a class='mem' href='wt_member.php'
			onClick='window.location.href=\"wt_member.php?js=yes\"; return false;'>&nbsp;Member Page</a>";
	}
	else
		$sidebar .= "<a class='mem' href='wt_member.php'>&nbsp;Member Page</a>";
}
if ($pg != "wt_change_acct.php") $sidebar .= "<a class='mem' href='wt_change_acct.php'>&nbsp;Change User Info</a>";
if ($pg != "wt_change_pass.php") $sidebar .= "<a class='mem' href='wt_change_pass.php'>&nbsp;Change Password</a>";
if ($pg != "wt_change_email.php") $sidebar .= "<a class='mem' href='wt_change_email.php'>&nbsp;Change Email</a>";
$sidebar .= "<a class='memlast' href='wt_logout.php'>&nbsp;Log Out</a>";

$sidebar .= "<br>";
$sidebar .= "<a class='mem' href='#'>&nbsp;Help</a>";
$sidebar .= "<a class='memlast' href='wt_contact.php'>&nbsp;Contact Us</a>";
// special links
if ($level == 3)
{
}
if ($level > 1)
{
	$sidebar .= "<br>";
	$sidebar .= "<a class='memlast' href='wt_view_source.php'>&nbsp;View Source</a>";
}

?>