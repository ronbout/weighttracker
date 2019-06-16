<?php 
// wt_cal_goals_process.php
// 5-25-11 rlb
// process form_goal from cal goals screen
// called from wt_form_check.php

// print_r($_POST);
// convert POST to vars
extract($_POST);
// validate weights
$errFlg = false;
// check new goal for valid entry
if ($newgoal_weight != "" && (!is_numeric($newgoal_weight) || $newgoal_weight < 0))
{
	$mainMesg = "<p class='main_mesg_err'>Invalid weight: ".$newgoal_weight."</p>";
	$onLoad = 'onLoad="document.form_goal.elements[\'newgoal_weight\'].focus();"';
	$errFlg = true;
}
if (!$errFlg && ($newgoal_date == "yyyy-mm-dd" && $newgoal_weight))
{
	$mainMesg = "<p class='main_mesg_err'>Must enter both date and weight to create new goal.</p>" ;
	$onLoad = 'onLoad="document.form_goal.elements[\'newgoal_date\'].focus();"';
	$errFlg = true;
}
if (!$errFlg && $newgoal_date && $newgoal_date != "yyyy-mm-dd" && !check_date($newgoal_date))
{
	$mainMesg = "<p class='main_mesg_err'>Invalid date format.  Use 'yyyy-mm-dd'.</p>";
	$onLoad = 'onLoad="document.form_goal.elements[\'newgoal_date\'].focus();"';
	$errFlg = true;
}
// check that only future date is entered
if (!$errFlg && $newgoal_date && $newgoal_date != "yyyy-mm-dd" && !check_future_date($newgoal_date,true))
{
	$mainMesg = "<p class='main_mesg_err'>Can only create goals for future dates.</p>";
	$onLoad = 'onLoad="document.form_goal.elements[\'newgoal_date\'].focus();"';
	$errFlg = true;
}

// check whether the goals were active and if they were, validate
if (!$errFlg && $goalType == "active")
{
	if (!isset($goal_weight)) $goal_weight = array();
	foreach ($goal_weight as $key=>$goal_row)
	{
		// check for valid weight
		if ($goal_row && (!is_numeric($goal_row) || $goal_row < 0))
		{
			$mainMesg = "<p class='main_mesg_err'>Invalid weight: $goal_row</p>";
			$errFlg = true;
			$onLoad = 'onLoad="document.form_goal.elements[\'goal_weight\']['.$key.'].focus();"';
			break;
		}
		if ($goal_date[$key] == "yyyy-mm-dd" && $goal_row)
		{
			$mainMesg = "<p class='main_mesg_err'>Must enter both date and weight to change goal.</p>" ;
			$onLoad = 'onLoad="document.form_goal.elements[\'goal_date\']['.$key.'].focus();"';
			$errFlg = true;
			break;
		}
		if ($goal_date[$key] && $goal_date[$key] != "yyyy-mm-dd" && !check_date($goal_date[$key]))
		{
			$mainMesg = "<p class='main_mesg_err'>Invalid date format.  Use 'yyyy-mm-dd'.</p>";
			$onLoad = 'onLoad="document.form_goal.elements[\'goal_date\']['.$key.'].focus();"';
			$errFlg = true;
			break;
		}
		// check that only future date is entered
		if ($goal_date[$key] && $goal_date[$key] != "yyyy-mm-dd" && !check_future_date($goal_date[$key],true))
		{
			$mainMesg = "<p class='main_mesg_err'>Can only change goals to future dates.</p>";
			$onLoad = 'onLoad="document.form_goal.elements[\'goal_date\']['.$key.'].focus();"';
			$errFlg = true;
			break;
		}
	}
}
// if no errors, update database
if (!$errFlg)
{
	$updFlag = false;
	// check if a new goal was entered, and update if necessary
	if ($newgoal_weight && $newgoal_date)
	{
		$errorCode = "";
		addMemberGoal($mysqli, $member, $user, $newgoal_weight, strtotime($newgoal_date), $errorCode);
		if ($errorCode)
		{
			$mainMesg = "<p class='main_mesg_err'>Error during update: ".$errorCode.
							"<br>Please check your goals to see what updated.</p>";
			$errFlg = true;
		}
		else
		{
			$updFlag = true;
		}
	}
	// now check active goals
	if ($goalType == "active")
	{
		if (!isset($goal_delete)) $goal_delete = array();
		// find goals that have been changed and are not marked for delete
		if (isset($goal_weight)) 
		{
			$updList = array();
			foreach($goal_weight as $goal_key=>$goal_row)
			{
				if ($goal_row != $goal_old_wt[$goal_key] && !in_array($goal_date[$goal_key], $goal_delete))
				{
					$updList[] = array("weight"=>$goal_row, "date"=>$goal_date[$goal_key]);
				}
			}
			// update weights
			$errorCode = "";
			foreach($updList as $updRow)
			{
				addMemberGoal($mysqli, $member, $user, $updRow['weight'], strtotime($updRow['date']), $errorCode);
				if ($errorCode)
				{
					$mainMesg = "<p class='main_mesg_err'>Error during update: ".$errorCode.
							"<br>Please check your goals to see what updated.</p>";
					break;
				}
				else
				{
					$updFlag = true;
				}
			}
		}
		// find goals that have been marked for delete
		if ($goal_delete)
		{
			// delete goals
			$errorCode = "";
			foreach($goal_delete as $delRow)
			{
				deleteMemberGoal($mysqli, $member, $user, strtotime($delRow), $errorCode);
				if ($errorCode)
				{
					$mainMesg = "<p class='main_mesg_err'>Error during delete: ".$errorCode."
						<br>Please check your goals to see what deleted.</p>";
					break;
				}
				else
				{
					$updFlag = true;
				}
			}
		}
	}
	if ($updFlag)
	{
		$mainMesg .= "Goals have been updated.<br>";
		$_SESSION['member'] = serialize($member);
	}
}
?>