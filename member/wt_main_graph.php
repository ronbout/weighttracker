<?php
// wt_main_graph.php
// 5-27-11 rlb
// main content code for member page in graph mode
// diaplays graph and graph options form
// ****will add buttons for different graphs when food and exercise are ready****
// graph must have been set up in wt_member_mode.php, wt_build_graph.php
// actual .jpg file is generated by wt_graph.php

// get name for current script for self-referencing
$pg = $_SERVER['PHP_SELF'];
$pg = pathinfo($pg);
$pg = $pg['basename'];
$selfLink =  $pg."?".$_SERVER['QUERY_STRING'];
?>
	<div id="graph">
		<img src="member/wt_graph.php" usemap="#graph">
		<map name="graph">
<?php 
	$graph->mapHTML();	
	// determine what form to display based on $type
echo '	</div>';
	switch($type)
	{
		case "exercise":
			break;
		case "food":
			break;
		case "weight":
		default:
			require("wt_main_graph_weight.php");
	}
?>
	
	
