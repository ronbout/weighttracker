<?php
// wt_build_graph.php
// 5-27-11 rlb
// Main graph code for weight tracker program
// Will build graph for either weight/goals, 
// foods (N/A) or exercise (N/A) based on $type
// called from wt_member_mode.php


$width = 500; 
$height = 400; 

$graphData = buildGraphData($member, $type);
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
		$title = "2 Weights required for Graph";
		$dataPoints = array();
	}
	else
		$title = "Weights/Goals";
}
else 
	$title = "No Weights Available";
$xLabel = "Dates";
$yLabel = "Pounds";
$legend = array("Weights", "Goals");

$backColor = array("R"=>235,"G"=>235,"B"=>235);
$titleColor = array("R"=>255,"G"=>0,"B"=>0);
$labelColor = array("R"=>255,"G"=>0,"B"=>0);
$graphColor = array("R"=>0,"G"=>0,"B"=>0);
$lineColor[] = array("R"=>0,"G"=>0,"B"=>255);
$lineColor[] = array("R"=>255,"G"=>0,"B"=>0);

$dayTime = 24 * 60 * 60;  // number of seconds in week, used for tick increment

// create graphClass object
$graph = new LineGraph($width, $height);
$graph->setGraphVals($xValBeg, $xValEnd, $yValBeg, $yValEnd);
if (!$dataPoints)
	$graph->setTickFlg(false);
else
{
	$graph->setTickFlg(true);
	$graph->setTickInfo($xValBeg,$dayTime*7 , 0);
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
?>