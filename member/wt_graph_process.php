<?php
// wt_graph_process.php
// 5-30-11 rlb
// code to process the options form for graphs
// different options will be available dependent on graph type

switch($type)
{
	case "exercise":
		break;
	case "food":
		break;
	case "weight":
	default:
		if (isset($_POST['orig_goal_graph']))
			$origGoalFlag = true;
}


?>