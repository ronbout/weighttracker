<?php
// wt_graph.php
// 5-27-11 rlb
// actual code that creates the .jpg graph image
// wt_build_graph must have been called first

require_once("LineGraphClass.php");
session_start();
$graph = unserialize($_SESSION['graph']);
$graph->display();
?>