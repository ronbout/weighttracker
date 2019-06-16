<?php
// wt_graph_funcs.php
// 5-27-11 rlb
// contains functions for the graph in weight tracker
// buildGraphData() - used to build data 
// determined by $type variable
// parameters: $member, $type, optional $options, optional $startDT, optional $endDT
// returns $graphData -- assoc array containing 
// $options contains assoc array of type-specific options
// "origGoal" will build data line for createDate ---> goalDate
// 'dataPoints', 'firstDT', 'lastDT', 'minValue', 'maxValue'
// called from wt_build_graph.php
// buildGraph(()
// parameters: $member, $type, $options, $startDT="", $endDT="")
// returns GraphLine object
// Will build graph for either weight/goals, 
// foods (N/A) or exercise (N/A) based on $type
// called from wt_member_mode.php

function buildGraphData($member, $type, $options=false, $startDT="", $endDT="")
{
	$dataPoints = array();
	$graphData = array();
	$todayDT = strtotime("now");
	switch($type)
	{
		case "goals":
			break;
		case "food":
			break;
		case "exercise":
			break;
		case "table":
		case "weight":
			// build 2 data sets: 1) weights 2) last weight + goals
			$weights = $member->getWeights();
			$goals = $member->getGoals();
			if (isset($options['origGoal']) && $options['origGoal']) 
				$origFlg = true;
			else
				$origFlg = false;
			if (!$weights) return false;
			$weightData = array();
			$goalData = array();
			// build weights
			$minValue = 999;
			$maxValue = 0;
			$lastWeight = "";
			foreach($weights as $weight)
			{
				if ((!$startDT || $weight['date'] > $startDT || date("Y-m-d",$weight['date']) == date("Y-m-d",$startDT))&& 
					(!$endDT || $weight['date'] < $endDT || date("Y-m-d",$weight['date']) == date("Y-m-d",$endDT)))
				{
					$weightData[] = array($weight['date'], $weight['weight']);
					$lastWeight = array($weight['date'], $weight['weight']);
					if ($weight['weight'] < $minValue) $minValue = $weight['weight'];
					if ($weight['weight'] > $maxValue) $maxValue = $weight['weight'];
				}
			}
			if (!$lastWeight) return false;
			$dataPoints[] = $weightData;
			$firstDate = $weights[0]['date'];
			$lastDate = $lastWeight[0];
			$origGoals = array();
			// build goals
			if ($goals)
			{
				$goalData[] = $lastWeight;
				foreach($goals as $goal)
				{
					if ((!$startDT || $goal['date'] > $startDT || date("Y-m-d",$goal['date']) == date("Y-m-d",$startDT)) &&
						(!$endDT || $goal['date'] < $endDT || date("Y-m-d",$goal['date']) == date("Y-m-d",$endDT)) &&
						($goal['date'] > $todayDT && date("Y-m-d",$goal['date']) != date("Y-m-d",$todayDT)))
					{
						$goalData[] = array($goal['date'], $goal['weight']);
						$lastDate = $goal['date'];
						if ($origFlg)
						{
							// can only display if weight exists for createDate
							if ($createWt = $member->getWeight($goal['createDate']))
							{
								$origGoals[]=array(array($goal['createDate'],$createWt),array($goal['date'],$goal['weight']));
							}
						}
					}
				}
			}
			if (sizeof($goalData) > 1)	
				$dataPoints[] = $goalData;
			if ($origGoals)
			{
				foreach($origGoals as $origGoal)
					$dataPoints[] = $origGoal;
			}
			break;
		case "home":
		default:
			// home is a demo graph for the home page
			$firstDate = strtotime("-6 days");
			$lastDate = strtotime("now");
			$minValue = 160;
			$maxValue = 170;
			$dataPoints[] = array(array(strtotime("-6 days"),168.0),
								array(strtotime("-5 days"),167.4),
								array(strtotime("-4 days"),166.8),
								array(strtotime("-3 days"),165.2),
								array(strtotime("-2 days"),164.8),
								array(strtotime("-1 days"),164.7),
								array(strtotime("now"),163.0));
	}
	// build assoc array for return
	$graphData = array('dataPoints'=>$dataPoints, 'firstDT'=>$firstDate, 'lastDT'=>$lastDate, 
						'minValue'=>$minValue, 'maxValue'=>$maxValue);
	return $graphData;
}

function buildGraph($member, $type, $options="", $startDT="", $endDT="")
{
	if ($type == "home")
	{
		$width = 294;
		$height = 220;
	}
	else
	{
		$width = 440; 
		$height = 375; 
	}
	$graphData = buildGraphData($member, $type, $options, $startDT, $endDT);
	$xValBeg = $graphData['firstDT'];
	$yValBeg = round($graphData['minValue'] - ($graphData['minValue'] * 0.05));
	$xValEnd = $graphData['lastDT'];
	$yValEnd = round($graphData['maxValue'] + ($graphData['maxValue'] * 0.05));

	$dataPoints = $graphData['dataPoints'];
	if (!$dataPoints) 
		$dataPoints = array();
	if ($dataPoints)
	{
		if (sizeof($dataPoints[0]) == 1)
		{
			if (isset($dataPoints[1]))
			{
				// have 1 weight and 1 goal, can still graph
				$title = "Weights/Goals for ".$member->getUserName();
				$dataPoints = array($dataPoints[1]);
				$legend = array("Goals");
				$lineColor[] = array("R"=>255,"G"=>0,"B"=>0);
			}
			else
			{
				$legend = "";
				$lineColor = array();
				$title = "2 Weights required for Graph";
				$dataPoints = array();
			}
		}
		else
		{	
			$title = "Weights/Goals for ".$member->getUserName();
			$legend = array("Weights", "Goals");
			$lineColor[] = array("R"=>0,"G"=>0,"B"=>255);
			$lineColor[] = array("R"=>255,"G"=>0,"B"=>0);
			if (sizeof($dataPoints) > 2)
			{
				// we need to show graphs of goals from creation date
				$cnt = 2;
				while (isset($dataPoints[$cnt]))
				{
					$legend[] = "Goal Path";
					$lineColor[] = array("R"=>150,"G"=>150,"B"=>50);
					$cnt++;
				}
			}
		}
	}
	else 
	{
		$title = "No Weights Available";
	}
	if ($type == "home") $title = "Your Data Here";
	$xLabel = "Dates";
	$yLabel = "Pounds";

	$backColor = array("R"=>235,"G"=>235,"B"=>235);
	$titleColor = array("R"=>255,"G"=>0,"B"=>0);
	$labelColor = array("R"=>255,"G"=>0,"B"=>0);
	$graphColor = array("R"=>0,"G"=>0,"B"=>0);

	$dayTime = 24 * 60 * 60;  // number of seconds in week, used for tick increment

	// create graphClass object
	$graph = new LineGraph($width, $height);
	$graph->setGraphVals($xValBeg, $xValEnd, $yValBeg, $yValEnd);
	$graphDays = ($xValEnd - $xValBeg) / (24*60*60);
	if ($graphDays < 8)
		$tickInterval = 1;
	else if ($graphDays < 30)
		$tickInterval = 2;
	else if ($graphDays < 60)
		$tickInterval = 7;
	else if ($graphDays < 180)
		$tickInterval = 14;
	else
		$tickInterval = 28;
	if (!$dataPoints)
		$graph->setTickFlg(false);
	else
	{
		$graph->setTickFlg(true);
		$graph->setTickInfo($xValBeg,$dayTime*$tickInterval , 0);
	}
	$graph->setGridFlgs(false, true);
	$graph->setTickLabels(true,true);
	$graph->setDateFlg(true);
	$graph->setTitles($title, $xLabel, $yLabel);
	$graph->setLegend($legend);
	$graph->setDataPoints($dataPoints);
	$graph->setColors($backColor,$titleColor,$labelColor,$graphColor,$lineColor);
	$graph->build();
	$serialGraph = serialize($graph);
	$_SESSION['graph'] = $serialGraph;
	return $graph;
}
?>