<?php
// wt_form_check
// 5-23-11 rlb
// checks for submits form various forms
// that are possible on member page
// called from wt_member_mode.php
if (isset($_POST['submit_wt']))
{
	// we have form_wt data from cal weight
	require("wt_cal_weight_process.php");
}
if (isset($_POST['submit_goal']))
{
	// we have form_goal data from cal goals
	require("wt_cal_goals_process.php");
} 
else if (isset($_POST['goal_old']))
{
	$goalType = "old";
}
else if (isset($_POST['goal_active']))
{
	$goalType = "active";
}
else if (isset($_POST['submit_graph']))
{
	// we have form_graph options 
	require("wt_graph_process.php");
}
else if (isset($_POST['submit_nutrients']))
{
	// we have form_nutrients data from wt_main_food_nutrients.php
	require("food/wt_nutrients_process.php");
}
else if (isset($_POST['submit_ingredients']))
{
	// we have form_ingredients data from wt_main_food_ingredients.php
	require("food/wt_ingredients_process.php");
}
else if (isset($_POST['fav_nutrients']) || isset($_POST['fav_ingredients']))
{
	require("food/wt_fav_process.php");
}
else if (isset($_POST['search_food_but_x']) || isset($_POST['select_food_refresh']))
{
	// just extract post variables and let it load w new variables
	extract($_POST);
}
else if (isset($_POST['submit_select_food']))
{
	if ($mode == "food")
	{
		// user has clicked on food list
		require("food/wt_select_food_process.php");
	}
}
else if (isset($_POST['submit_add_ingred']))
{
	// add new ingredients to ingreds and set servings to 1
	extract($_POST);
	$last_key = -1;
	$ingred_cnt = sizeof($ingred_ids);
	foreach ($select_food as $key=>$new_ingred)
	{
		$ingred_array = explode("*",$new_ingred);
		$new_ingred_id = $ingred_array[0];
		$new_ingred_name = $ingred_array[2];
		$ingreds[$ingred_cnt] = $new_ingred_name;
		$servings[$ingred_cnt] =  "1";
		$ingred_ids[$ingred_cnt] =  $new_ingred_id;
		$ingred_cnt++;
	}
	if ($last_key == -1)
		$on_load = 'onLoad="document.form_food_ingredients.elements[\'ingreds[]\'].focus(); ';
	else
		$on_load = 'onLoad="document.form_food_ingredients.elements[\'ingreds[]\']['.($last_key+1).'].focus(); ';

}
else if (isset($_POST['reset_nutrients']))
{
	extract($_POST);
	$nut_name = $old_nut_name;
	$nut_desc = $old_nut_desc;
	$nut_size = $old_nut_size;
	$nut_units = $old_nut_units;
	$nut_calories = $old_nut_calories;
	$nut_points = $old_nut_points;
	$nut_fat = $old_nut_fat;
	$nut_carbs = $old_nut_carbs;
	$nut_protein = $old_nut_protein;
	$nut_fiber = $old_nut_fiber;	
}
else if (isset($_POST['reset_ingredients']))
{
	extract($_POST);
	$ingred_food_name = $old_ingred_food_name;
	$ingred_food_desc = $old_ingred_food_desc;
	$ingred_size = $old_ingred_size;
	$ingred_units = $old_ingred_units;
	$ingreds = (isset($old_ingreds)) ? $old_ingreds : array();
	$ingred_ids = (isset($old_ingred_ids)) ? $old_ingred_ids : array();
	$servings = (isset($old_servings)) ? $old_servings : array();
}
else if (isset($_POST['ingred_food_name']) && !isset($_POST['clear_ingredients']))
{
	extract($_POST);
}
?>