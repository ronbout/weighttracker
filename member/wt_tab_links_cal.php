<?php
// wt_tab_links_cal.php
// 6-22-11 rlb
// to clean things up, pulled out code that
// sets up the tabbed links

$linkDate = date("Y-m-d", $dt);    // $dt was set when calendar was built
echo "<ul id='tabmenu'>";
if ($type == "weight")
	echo "<li><a class='selected' href='wt_member.php?mode=cal&type=weight&date=",$linkDate,"'>Weight</a></li>";
else
	echo "<li><a href='wt_member.php?mode=cal&type=weight&date=",$linkDate,"'>Weight</a></li>";
if ($type == "goals")
	echo "<li><a class='selected' href='wt_member.php?mode=cal&type=goals&date=",$linkDate,"'>Goals</a></li>";
else
	echo "<li><a href='wt_member.php?mode=cal&type=goals&date=",$linkDate,"'>Goals</a></li>";
if ($type == "food")
	echo "<li><a class='selected' href='wt_member.php?mode=cal&type=food&date=",$linkDate,"'>Food</a></li>";
else
	echo "<li><a href='wt_member.php?mode=cal&type=food&date=",$linkDate,"'>Food</a></li>";
if ($type == "exercise")
	echo "<li><a class='selected' href='wt_member.php?mode=cal&type=exercise&date=",$linkDate,"'>Exercise</a></li>";
else
	echo "<li><a href='wt_member.php?mode=cal&type=exercise&date=",$linkDate,"'>Exercise</a></li>";
if ($type == "table")
	echo "<li><a class='selected' href='wt_member.php?mode=cal&type=table&date=",$linkDate,"'>Table</a></li>";
else
	echo "<li><a href='wt_member.php?mode=cal&type=table&date=",$linkDate,"'>Table</a></li>";
echo '</ul>';


?>