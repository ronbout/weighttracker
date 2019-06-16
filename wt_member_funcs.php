<?php
// wt_member_funcs.php
// 5-15-11 rlb
// contains functions for weight tracker wt.php
// mostly focused on member data
require_once("wt_include.php");

function loadMember($mysqli, $user_id, &$errorCode)
{
	// takes in user_id and returns a Member object loaded with the data
	$memberInfo = new Member;
	// get member info from tables member, member_weight, and member_goal
	$errorCode = "";
	$query = "SELECT member_id, first_name, last_name, user_name, email, level FROM member WHERE member_id = ".$user_id;
	if ($result = $mysqli->query($query))
	{
		if (!$result->num_rows)
		{
			$errorCode = -1; // unknown user
			return false;
		}
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
	// successful query
	$row = $result->fetch_assoc();
	// retrieved user, load $memberInfo
	$memberInfo->setMemberInfo($row);
	// now load member_weight
	$errorCode = "";
	$query = "SELECT date, weight FROM member_weight WHERE member_id = ".$user_id." ORDER BY date";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
	$weights = array();
	while ($row = $result->fetch_assoc())
	{
		$weights[] = array("date"=>strtotime($row['date']), "weight"=>$row['weight']);
	}
	if ($weights)	$memberInfo->setWeights($weights);
	// now load member_goal
	$errorCode = "";
	$query = "SELECT goal_date, goal_weight, create_date FROM member_goal WHERE member_id = ".$user_id." ORDER BY goal_date";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
	$goals = array();
	while ($row = $result->fetch_assoc())
	{
		$goals[] = array("date"=>strtotime($row['goal_date']), "weight"=>$row['goal_weight'],
					"createDate"=>strtotime($row['create_date']));
	}
	if ($goals)	$memberInfo->setGoals($goals);
	return $memberInfo;
}

function addMemberWeight($mysqli, $member, $userId, $weight, $date, &$errorCode)
{
	// will either add row or update existing row
	// make sure process finishes 
	ignore_user_abort(true);
	$dbDate = date("Y-m-d",$date);
	$errorCode = "";
	// insert/update weight
	$query = "INSERT INTO member_weight (member_id, date, weight) VALUES ($userId, '".$dbDate."', $weight)
				ON DUPLICATE KEY UPDATE weight = $weight";
	if ($result = $mysqli->query($query))
	{
		// success, now update member object
		$member->addWeight($date, $weight);
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}	
}
function addMemberGoal($mysqli, $member, $userId, $weight, $goalDate, &$errorCode)
{
	// will either add row or update existing row
	// make sure process finishes 
	ignore_user_abort(true);
	$createDate = strtotime("now");
	$dbCreateDate = date("Y-m-d", $createDate);
	$dbGoalDate = date("Y-m-d", $goalDate);
	$errorCode = "";
	// insert/update goal
	$query = "INSERT INTO member_goal (member_id, goal_date, goal_weight, create_date) VALUES 
				($userId, '".$dbGoalDate."', $weight, '".$dbCreateDate."')
				ON DUPLICATE KEY UPDATE goal_weight = $weight";
	if ($result = $mysqli->query($query))
	{
		// success, now update member object
		$member->addGoal($goalDate, $weight, $createDate);
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function deleteMemberWeight($mysqli, $member, $userId, $date, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	$dbDate = date("Y-m-d", $date);
	$errorCode = "";
	$query = "DELETE FROM member_weight WHERE member_id = $userId AND date = '".$dbDate."'";
	if ($result = $mysqli->query($query))
	{
		// success, now update member object
		$member->deleteWeight($date);
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function deleteMemberGoal($mysqli, $member, $userId, $goalDate, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	$errorCode = "";
	$dbGoalDate = date("Y-m-d", $goalDate);
	$query = "DELETE FROM member_goal WHERE member_id = $userId AND goal_date = '".$dbGoalDate."'";
	if ($result = $mysqli->query($query))
	{
		// success, now update member object
		$member->deleteGoal($goalDate);
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function check_date($date_str)
{
	// check that entered date is in format yyyy-mm-dd
	if (strlen($date_str) != 10) return false;
	$pattern = "/[0-9]{4}-[0-9]{2}-[0-9]{2}/";
	if (!preg_match($pattern, $date_str)) return false;
	// correct format, now check ranges
	return strtotime($date_str);
}
function check_future_date($date_str, $future_flag)
{
	// check date against future_flag (true -future only, false -today and past)
	$today = strtotime("now");	
	$testday = strtotime($date_str);
	if ($future_flag)
	{
		if ($today >= $testday || date("Y-m-d",$today) == date("Y-m-d",$testday)) return false;		
	}
	else
	{
		if ($today < $testday && date("Y-m-d",$today) != date("Y-m-d",$testday)) return false;
	}
	return true;
}


?>