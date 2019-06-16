<?php
// wt_cal_goals_form.php
// 5-24-11 rlb
// creates the member goals entry/update form
// for the calendar screen from wt_tab_data_cal.php
// will include all active goals (future dates)
// date/goal entry field for new goal
// will also display previous (3?) goals

// get name for current script for self-referencing
$pg = $_SERVER['PHP_SELF'];
$pg = pathinfo($pg);
$pg = $pg['basename'];
$selfLink =  $pg."?".$_SERVER['QUERY_STRING'];
// build weight array -- vars were set up in build_cal, but 
// will redefine them in case this routine is reused elsewhere
if (isset($_GET['date']))
	$dt = strtotime($_GET['date']);
else
	$dt = strtotime("now");
$dispDate = date("M jS, Y", $dt);
$activeGoals = array();
$oldGoals = array();
$dtGoal = "";
$goals = $member->getGoals();
$todayDt = strtotime("now");
foreach ($goals as $goal)
{
	// check for goal date and put in appropriate array
	if (date("Y-m-d",$todayDt) == date("Y-m-d",$goal["date"]) ||  $todayDt > $goal["date"])
	{
		// convert dates to YYYY-mm-dd
		$goal["date"] = date("Y-m-d",$goal["date"]);
		$goal["createDate"] = date("Y-m-d", $goal["createDate"]);
		$oldGoals[] = $goal;
	}
	else
	{
		$goal["date"] = date("Y-m-d",$goal["date"]);
		$goal["createDate"] = date("Y-m-d", $goal["createDate"]);
		$activeGoals[] = $goal;
	}
	if (date("Y-m-d",$dt) == $goal["date"])
	{
		$dtGoal = $goal;
	}
}
// determine whether showing active or old goals
if ($goalType == "active")
{
	$heading = "Active Goals";
	$toggleType = "Old Goals";
	$formGoals = $activeGoals;
	$subName = "goal_old";
}
else
{
	$heading = "Old Goals";
	$toggleType = "Active Goals";
	$formGoals = $oldGoals;
	$subName = "goal_active";
}
$numGoals = sizeof($formGoals);
// set default of date field
if ($dt > $todayDt && date("Y-m-d",$dt) != date("Y-m-d",$todayDt))
	$defDate = date("Y-m-d",$dt);
else
	$defDate = "yyyy-mm-dd";
?>
	<div id="div_form_goal">
		<h3>Active Date: <?php echo $dispDate;?></h3><br>
		<form name="form_goal" method="post" action="<?php echo $selfLink;?>">
		<div id="new_goal">
			<table>
				<tr>
					<th></th>
					<th class="goal_table_th">Date</th>
					<th class="goal_table_th">Goal</th>
				</tr>
			<tr>
				<td class="goal_table_add">Add New Goal:</td>
				<td class="goal_new_dat">
					<input class="inp_goal" type="text" name="newgoal_date" size="10" maxlength="10"
							value="<?php echo $defDate; ?>"<?php
						if ($defDate == "yyyy-mm-dd") 
							echo 'onfocus="date_enter(this);" onblur="date_exit(this);"';?>>
				</td>	
				<td class="goal_table_dat">
					<input class="inp_goal" type="text" name="newgoal_weight" size="5" maxlength="5" value = "">
				</td>
			</tr>
			</table>
		</div> <!-- end of div new_goal  -->
		<div id="goal_list">
			<h4><?php
				if (!$numGoals) echo "No ",$heading;
				//echo $heading;
			?></h4>
<?php
if ($numGoals)
{
?>
<fieldset>
<legend><?php echo $heading;?></legend>
			<table cellspacing="4px">
				<tr>
					<th class="goal_table_th">Date</th>
					<th class="goal_table_th">Goal</th>
					<th class="goal_table_th">Created</th>
					<?php if ($goalType == "active") { ?>
					<th class="goal_table_th">Del</th>
					<?php } ?>
				</tr>
	<?php
	for ($i = 0; $i < $numGoals; $i++)  // will show as many goals as possible, will scroll if more than screen
	{
	?>
				<tr>
					<td class="goal_table_dat">
						<input class="inp_goal" type="text" name="goal_date[]" size="10" maxlength="10" 
									value="<?php echo $formGoals[$i]["date"]; ?>">
					</td>	
					<td class="goal_table_dat">
						<input class="inp_goal" type="text" name="goal_weight[]" size="5" maxlength="5"
									value="<?php echo number_format($formGoals[$i]["weight"],1); ?>">
					</td>
					<td class="goal_table_dat">
						<input class="inp_goal" type="text" name="goal_cdate[]" size="10" 
									value="<?php echo $formGoals[$i]["createDate"];?>" disabled="disabled">
					</td>
					<td class="goal_table_check">
						<input type="<?php 
									if ($goalType == "active")
										echo "checkbox"; 
									else 
										echo "hidden";?>" name="goal_delete[]" value="<?php echo $formGoals[$i]["date"];?>">
					</td>
					<input type="hidden" name="goal_old_wt[]" value="<?php echo number_format($formGoals[$i]["weight"],1); ?>">
					<input type="hidden" name="goal_old_date[]" value = "<?php echo $formGoals[$i]["date"]; ?>">
				</tr>				
	<?php
	}

	?>
	</table>
</fieldset>
	</div> <!-- end of div goal_list -->
<?php
}
?>
<br>
<p class="form_buttons">
<input class="sub" type="submit" name="submit_goal" value="Update" onclick="return validate_form_goal(this)">&nbsp; 
&nbsp;&nbsp;&nbsp;
<input class="sub" type="reset" name="reset_wt" value="Reset">&nbsp;&nbsp;&nbsp&nbsp;
<input class="sub" type="submit" name="<?php echo $subName;?>" value="<?php echo $toggleType;?>">
<input type="hidden" name="goalType" value="<?php echo $goalType;?>">
<input type="hidden" name="submit_flag" value="false">
</p>
</form>
	</div> <!-- end of form_wt div -->
<?php
?>