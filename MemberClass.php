<?php
// MemberClass.php
// 5-8-11  rlb
// Member class definition for weight tracker, wt.php

require_once("wt_include.php");

class Member 
{
	// member table fields
	private $member_id;		// member_id in database
	private $first_name;	// first_name
	private $last_name;		// last_name
	private $user_name;		// user_name
	private $email;			// email
	private $level;			// level
	
	private $weights;	// member_weight table rows converted to 2 dim array
						// top level array consists of array("date", "weight") pairs
							
	private $goals;	// member_goal table rows converted to 2 dim array
						// 2nd level array("date", "weight", "createDate")
	
	public function __construct()
	{
		$this->weights = array();
		$this->goals = array();
	}
	public function getMemberInfo()
	{
		// return assoc array of member table fields 
		return array("member_id"=>$this->member_id, "first_name"=>$this->first_name, "last_name"=>$this->last_name,
						"user_name"=>$this->user_name, "email"=>$this->email, "level"=>$this->level);
	}
	public function setMemberInfo($userInfo)
	{
		// receive row of data, same as query result
		if(isset($userInfo['member_id']))	$this->member_id = $userInfo['member_id'];
		if(isset($userInfo['first_name']))	$this->first_name = $userInfo['first_name'];
		if(isset($userInfo['last_name']))	$this->last_name = $userInfo['last_name'];
		if(isset($userInfo['user_name']))	$this->user_name = $userInfo['user_name'];
		if(isset($userInfo['email']))		$this->email = $userInfo['email'];
		if(isset($userInfo['level']))		$this->level = $userInfo['level'];
	}
	public function getUserName()
	{
		return $this->user_name;
	}
	public function getLevel()
	{
		return $this->level;
	}
	public function getWeights()
	{
		return $this->weights;
	}
	public function setWeights($weights)
	{
		// weights coming in as assoc array already
		// make sure that it is ordered by date
		$this->weights = sortAssoc($weights, "date");
	}
	public function getWeight($date)
	{
		// retrieves a weight for a particular date
		// returns false if not found
		foreach($this->weights as $key=>$weight)
		{
			// see if date matches
			if (date("m-d-Y",$weight['date']) == date("m-d-Y",$date))
			{
				return $weight['weight'];
			}
		}
		return false;
	}
	public function getGoals()
	{
		return $this->goals;
	}
	public function setGoals($goals)
	{
		// goals coming in as assoc array already
		// make sure that it is ordered by date
		$this->goals = sortAssoc($goals, "date");
	}
	public function getGoal($date)
	{
		// retrieves an array of goal and create date for a particular date
		// returns false if not found
		foreach($this->goals as $key=>$goal)
		{
			// see if date matches
			if (date("m-d-Y",$goal['date']) == date("m-d-Y",$date))
			{
				return array("weight"=>$goal['weight'],"createDate"=>$goal['createDate']);
			}
		}
		return false;
	}
	public function addWeight($date, $newWeight)
	{
		// if date already exists, replace, else add
		$i = 0;
		$insertFlg = false;
		foreach($this->weights as $key=>$weight)
		{
			// see if date already exists
			if (date("m-d-Y",$weight['date']) == date("m-d-Y",$date))
			{
				$this->weights[$key]['weight'] = $newWeight;
				$insertFlg = true;
				break;
			}
			// if we have gone past new date, then insert
			if ($weight['date'] > $date)
			{
				array_splice($this->weights,$i,0,array(array("date"=>$date, "weight"=>$newWeight)));
				$insertFlg = true;
				break;
			}
			$i++;
		}
		if (!$insertFlg)
		{
			// weight should be added to end
			array_splice($this->weights,$i,0,array(array("date"=>$date, "weight"=>$newWeight)));
		}
	}
	public function addGoal($date, $goalWeight, $createDate)
	{
		// if date already exists, replace, else add
		$i = 0;
		$insertFlg = false;
		foreach($this->goals as $key=>$goal)
		{
			// see if date already exists
			if (date("m-d-Y",$goal['date']) == date("m-d-Y",$date))
			{
				$this->goals[$key]['weight'] = $goalWeight;
				// decided to keep createDate if goal value is changed
				//$this->goals[$key]['createDate'] = $createDate;
				$insertFlg = true;
				break;
			}
			// if we have gone past new date, then insert
			if ($goal['date'] > $date)
			{
				array_splice($this->goals,$i,0,array(array("date"=>$date, "weight"=>$goalWeight, "createDate"=>$createDate)));
				$insertFlg = true;
				break;
			}
			$i++;
		}
		if (!$insertFlg)
		{
			// weight should be added to end
			array_splice($this->goals,$i,0,array(array("date"=>$date, "weight"=>$goalWeight, "createDate"=>$createDate)));
		}
	}
	public function deleteWeight($date)
	{
		$i = 0;
		foreach($this->weights as $weight)
		{
			if (date("m-d-Y",$weight['date']) == date("m-d-Y",$date))
			{
				array_splice($this->weights,$i,1);
				break;
			}
			$i++;
		}
	}
	public function deleteGoal($date)
	{
		$i = 0;
		foreach($this->goals as $goal)
		{
			if (date("m-d-Y",$goal['date']) == date("m-d-Y",$date))
			{
				array_splice($this->goals,$i,1);
				break;
			}
			$i++;
		}
	}
}
?>