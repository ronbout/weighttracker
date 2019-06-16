<?php
// wt_tab_data_cal.php
// 5-22-11 rlb
// content of the tabbed data section of the calendar mode
// $type determines which tab is selected and what will be 
// displayed

switch($type)
{
	case "weight":
		require("member/wt_cal_weight_form.php");
		break;
	case "goals":
		require("member/wt_cal_goals_form.php");
		break;
	case "food":
		echo "<h3>Entry/upate food for day</h3>";
		break;
	case "exercise":
		echo "<h3>Textarea entry for current day</h3>";
		break;
	case "table":
	default:
		echo "<h3>Table of weight data for month</h3>";
}




?>