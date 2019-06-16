<?php
// wt_tab_data_cal.php
// 6-22-11 rlb
// content of the tabbed data section of the calendar mode
// $type determines which tab is selected and what will be 
// displayed

switch($type)
{
	case "weight":
		require("wt_cal_weight_form.php");
		break;
	case "goals":
		require("wt_cal_goals_form.php");
		break;
	case "food":
		echo "<h3>Under Construction!</h3>";
		break;
	case "exercise":
		echo "<h3>Under Construction!</h3>";
		break;
	case "table":
	default:
		require("wt_table.php");
}




?>