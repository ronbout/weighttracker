<?php
// wt_build_cal.php
// 5-22-11 rlb
// builds the calendar for the weight tracker member page
// reads date from GET variable, or assumes today
// sets display based on member object. i.e. days with 
// member goals will be displayed in red
// days with entries will be bold
// the cal->css() must be called in <HTML><HEAD> section
// then cal->display() called in appropriate <HTML><DIV>

if (isset($_GET['date']))
	$dt = strtotime($_GET['date']);
else
	$dt = strtotime("now");
$calVals = array("top"=>"20px", "left"=>"33px", "width"=>"280px", "fontSize"=>"1em");
$cal = new CalExtDay($dt, $calVals);
// set base colors
$cal->setColor("black","yellow","");
$cal->setBoldFlg(true);
// turn on links
$cal->setArrowFlg(true);
$cal->setYrFlg(true);
$cal->buildDayLinks("wt_member.php?mode=".$mode."&type=".$type);
// get member weight and goal info
$goals = $member->getGoals();
$weights = $member->getWeights();
$month = date("n",$dt);
$backList = array();
$colorList = array();
$titleList = array();
//$htmlList = array();
// loop through goals, changing background color 
foreach($goals as $goal)
{
	if (date("n",$goal['date']) == $month)
	{
		$backList[date("j",$goal['date'])] = "#FF8888";
		$titleList[date("j",$goal['date'])] = "Goal: ".$goal['weight'];
	}
}
// loop through weights, changing color 
foreach($weights as $weight)
{
	if (date("n",$weight['date']) == $month)
	{
		$colorList[date("j",$weight['date'])] = "blue";
		if (isset($titleList[date("j",$weight['date'])]))
			$titleList[date("j",$weight['date'])] .= " Weight: ".$weight['weight'];
		else
			$titleList[date("j",$weight['date'])] = "Weight: ".$weight['weight'];
	}
}	

$cal->setBackClrList($backList);
$cal->setColorList($colorList);
$cal->setTitleList($titleList);
//$cal->setHtmlList($htmlList);
?>