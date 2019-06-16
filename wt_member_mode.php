<?php
// wt_member_mode.php
// 5-22-11 rlb
// controlling code for wt_member.php page
// reads mode from URL and determines what content
// will be displayed

$mainMesg = "";
// determine what mode we are in (calendar, graph, news)
if (!isset($mode)) 
{
	if (isset($_GET['mode']))
		$mode = $_GET['mode'];
	else
		if (isset($_SESSION['mode']))
			$mode = $_SESSION['mode'];
		else
			$mode = "cal";
}
// get type (weight, food, diary) - sets the tab
if (!isset($type)) 
{
	if (isset($_GET['type']))
		$type = $_GET['type'];
	else
		if (isset($_SESSION['type']))
			$type = $_SESSION['type'];
		else
		{
			if ($mode == "cal")
				$type = "weight";
			else if ($mode == "food")
				$type = "nutrients";
			else
				$type = "weight";
		}
}
$goalType = "active";  // if goal tab, shows either active or old goals
// check for form processing
require ("member/wt_form_check.php");
// do necessary processing for later display

switch($mode)
{
	case "graph":
		$mainMesg .= "Use the tabs to change graph.";
		$mainInclude = "member/wt_main_graph.php";
		switch($type)
		{
			case "goals":
				break;
			case "food":
				$mainMesg .= "COMING SOON!";
				break;
			case "exercise":
				$mainMesg .= "COMING SOON!";
				break;
			case "table":
				$mainMesg .= "Table displays all of your weight and goal history.";
				$mainMesg .= "<br>You can also see any weight/goal value by placing the mouse";
				$mainMesg .= " over a data point in the Graph.";
				break;
			case "weight":
			default:
				if (!$onLoad) $onLoad = 'onLoad="document.form_wt.elements[1].focus();"';
		}
		$options = "";
		if ($type == "weight" && isset($origGoalFlag)) $options = array("origGoal"=>true);
		$graph = buildGraph($member, $type, $options);
		break;
	case "news":
		$mainInclude = "member/wt_main_news.php";
		$news = wt_build_news(15);
		require("member/wt_build_cal.php");
		$mainMesg .= "Enjoy the latest health news from a number of sources.";
		$mainMesg .= "<br>The feeds are updated every 2 hours, so be sure to look ";
		$mainMesg .= "for more good tips later!";
		break;
	case "food":
		if ($type == "nutrients")
		{
			if (!$onLoad) $onLoad = 'onLoad="document.form_food_nutrients.elements.nut_name.focus();';
			$onLoad .= ' nut_submit_status(document.form_food_nutrients.elements.submit_nutrients);"'; 
				
		}
		if ($type == "ingredients")
		{
			if (!$onLoad) $onLoad = 'onLoad="document.form_food_ingredients.elements.ingred_food_name.focus();';
			$onLoad .= ' ingred_submit_status(document.form_food_ingredients.elements.submit_ingredients);"';
		}
		$mainInclude = "food/wt_main_food.php";
		break;
	case "cal":
	default:
		// in case this dropped through default, makes sure variables are correct 
		// had problem with SESSION variable being set in another program
		$mode = "cal";
		switch($type)
		{
			case "goals":
				if (!$onLoad) $onLoad = 'onLoad="document.form_goal.elements[\'newgoal_weight\'].focus();"';
				$mainMesg .= "For new goal, enter weight and date.";
				$mainMesg .= "<br>Use yyyy-mm-dd format for date.";
				$mainMesg .= "<br>You can change or delete an active goal.";
				$mainMesg .= "<br>Click Update when finished.";
				$mainMesg .= "<br>Note: Can only change future goals.";
				break;
			case "food":
				$mainMesg .= "COMING SOON!";
				break;
			case "exercise":
				$mainMesg .= "COMING SOON!";
				break;
			case "table":
				$mainMesg .= "Table displays all of your weight and goal history.";
				$mainMesg .= "<br>You can also see any weight/goal value by placing the mouse";
				$mainMesg .= " over a date in the Calendar.";
				break;
			case "weight":
			default:
				$type = "weight";
				if (!$onLoad) $onLoad = 'onLoad="document.form_wt.elements[1].focus();"';
				$mainMesg .= "Use forms at right to update your data.";
				$mainMesg .= "<br>Click on the calendar to work with a different day.";
				$mainMesg .= "<br>Place mouse over calendar dates to see weights and goals.";
				$mainMesg .= "<br>Note: You will not be able to enter weights into future dates.";
				//$onLoad = 'onLoad="alert(document.form_wt.elements[\'wt_weight[]\'][0].value);"';
		}
		// cal should be default so put cal code here and let 'cal' mode drop through
		require("member/wt_build_cal.php");
		$mainInclude = "member/wt_main_cal.php";
}
$_SESSION['mode'] = $mode;
$_SESSION['type'] = $type;
?>