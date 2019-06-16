<?php
// process for ajax request from javascript
// will look up food and return info, if possible
// otherwise, returns appropriate error code
// will need to check for endless recursion relationship
// i.e. "Lunch" food includes "sandwich" and "sandwich" includes "lunch"
// 'food' refers to FOOD being created, 'ingred' to ingredient being added

require_once("../wt_include.php");
if (!isset($_SESSION['user']) || !$_SESSION['user'] ||
	!isset($_GET['ingred']) || !$_GET['ingred']) exit("Invalid Page");
$ingred_name = str_replace('"','',$_GET['ingred']);
$food_id = (isset($_GET['food'])) ? $_GET['food'] : "";
if (isset($_GET['ingred_id']))
	$ingred_id = $_GET['ingred_id'];
else
	$ingred_id = "";
$user = $_SESSION['user'];
// login to database
require("../wt_connect.php");

$out_string = "";
if (!$ingred_id)
{
	$query = "SELECT id, owner FROM food WHERE name='$ingred_name'";
	if (!$result = $mysqli->query($query))
	{
		echo "-99";  // unknown database error
		//**********sendError("ajax", "select from food: ".$mysqli->error);
	}
	if (!$result->num_rows)
	{
		echo "-1"; // food not found
		exit();
	}
	if ($result->num_rows > 1)
	{
		$owner_flag = false;
		while($row = $result->fetch_assoc())
		{
			$owner = $row['owner'];
			if ($user == $owner) 
			{
				$owner_flag = true;
				break;
			}
		}
		if (!$owner_flag)
		{
			$query_fav = "SELECT a.food_id FROM member_food_favs a, food b WHERE a.member_id = $user AND 
				b.name = '$ingred_name' AND a.food_id = b.food_id";
			if (!$result_fav = $mysqli->query($query_fav))
			{
				echo "-99"; //unknown database error
				//**********sendError("ajax", "select from food favs: ".$mysqli->error);
			}
			if ($result != 1)
			{
				echo "-2"; // cannot determine which food based on name
				exit();
			}
			$row = $result_fav->fetch_assoc();
		}	
	}
	else
	{
		$row = $result->fetch_assoc();
	}
	$ingred_id = $row['id'];
}
// check for circular relationship
$errCode = "";
$ingreds = array();
if (($ingreds = get_all_ingredients($mysqli, $ingred_id, $errCode)) === false)
{
	echo "-99";  // unknown database error
	//**********sendError("ajax", "checking circular: ".$errCode);
}
if ($ingreds)
{
	if (in_array($food_id, $ingreds))
	{
		echo "-3"; // circular food relationship
		exit();
	}
}
// make sure the food is not an ingredient of itself
if ($ingred_id == $food_id)
{
	echo "-4"; // food cannot be ingredient of itself
	exit();
}
// either have $row or errored out and exited by now

$errCode = "";
$food_info = loadFood($mysqli, $ingred_id, $errCode);
if ($errCode)
{
	echo "-99";  // unknown database error
	//**********sendError("ajax", "loading food: ".errCode);
	exit(); 
}
$nutrients = $food_info->getNutrition($mysqli, $errCode);
if ($errCode)
{
	echo "-99";  // unknown database error
	//**********sendError("ajax", "getting nutrition: ".errCode);
	exit(); 
}
echo $ingred_id, "~", $nutrients['calories'];

?>