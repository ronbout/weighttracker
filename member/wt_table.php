<?php
// wt_table.php
// 5-26-11 rlb
// displays user info (weights/goals/foods/etc) in table form
// will be used from cal, graph modes, maybe more
// build all dates that have any values
// add overflow:auto to div container for scroll bar
// just loop through each
$dispTable = array();
if (!isset($startDT)) $startDT = "";
if (!isset($endDT)) $endDT = "";
// build table from weights
$weights = $member->getWeights();
$goals = $member->getGoals();
// *** ADD FOOD AND EXERCISE HERE ***
$dispTable = $weights;
foreach($goals as $key=>$goal)
{
	$i=0;
	$insertFlg = false;
	foreach($dispTable as $tKey=>$tRow)
	{
		// see if date already exists
		if (date("m-d-Y",$tRow['date']) == date("m-d-Y",$goal['date']))
		{
			$dispTable[$tKey]['goal'] = $goal['weight'];
			$dispTable[$tKey]['createDate'] = $goal['createDate'];
			$insertFlg = true;
			break;
		}
		// if we have gone past new date, then insert
		if ($tRow['date'] > $goal['date'])
		{
			array_splice($dispTable,$i,0,array(array("date"=>$goal['date'], "goal"=>$goal['weight'],
													 "createDate"=>$goal['createDate'])));
			$insertFlg = true;
			break;
		}
		$i++;
	}
	if (!$insertFlg)
	{
		// weight should be added to end
		array_splice($dispTable,$i,0,array(array("date"=>$goal['date'], "goal"=>$goal['weight'],
													 "createDate"=>$goal['createDate'])));
	}
}
//need to reverse sort
$dispTable = sortAssoc($dispTable, "date", 2)
// display table
?>
<div id="div_table">
	<table id="disp_table" cellspacing="0px">
		<tr>
			<th class="table_th">Date</th>
			<th class="table_th">Weight</th>
			<th class="table_th">Goal</th>
			<!--<th class="table_th">extra</th>-->
		</tr>
<?php
	foreach($dispTable as $key=>$dispRow)
	{
		if ((!$startDT || $dispRow['date'] > $startDT || date("Y-m-d",$dispRow['date']) == date("Y-m-d",$startDT))&& 
					(!$endDT || $dispRow['date'] < $endDT || date("Y-m-d",$dispRow['date']) == date("Y-m-d",$endDT)))
		{
?>
		<tr>
			<td class="table_date"><?php echo date("m-d-Y",$dispRow['date']);?></td>
			<td class="table_weight"><?php echo (isset($dispRow['weight'])) ? number_format($dispRow['weight'],1) : "";?></td>
			<td class="table_goal"><?php echo (isset($dispRow['goal'])) ? number_format($dispRow['goal'],1) : "";?></td>
			<!--<td class="table_extra"></td>-->
		</tr>
<?php
		}
	}
?>
	</table>
</div>