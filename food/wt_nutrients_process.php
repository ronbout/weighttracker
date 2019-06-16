<?php
// wt_nutrients_process.php
// 8-2-11 rlb
// process the form_nutrients form from wt_main_food_nutrients.php
// this form creates a new food item by supplying the nutrient info
// called from wt_form_check.php
extract($_POST);
// validate data
if ($update_flag == "true")
{
	$errFlg = false;
	$num_fields = array("nut_calories", "nut_points", "nut_fat", "nut_carbs", "nut_protein", "nut_fiber", "nut_size");
	// check for missing data
	$nut_name = trim($nut_name);
	if (!isset($nut_name) || !$nut_name || !isset($nut_calories) || $nut_calories == "")
	{
		$food_mesg = "<p class='food_err'>Food Name and Calories are both required.</p>";
		$errFlg = true;
	}
	// check for numeric values
	if (!$errFlg)
	{
		foreach($num_fields as $num_field)
		{
			if (isset($$num_field) && $$num_field != "" && !is_numeric($$num_field))
			{
				$food_mesg = "<p class='food_err'>Invalid ".$$num_field." value.  Numeric required.</p>";
				$errFlg = true;
				break;
			}
		}
	}
	if (!$errFlg)
	{
		// nutrients
		$nutrients = array();
		$nutrients["calories"] = $nut_calories;
		if (isset($nut_points)) $nutrients["points"] = $nut_points;
		if (isset($nut_fat)) $nutrients["fat"] =  $nut_fat;
		if (isset($nut_carbs)) $nutrients["carbs"] =  $nut_carbs;
		if (isset($nut_protein))  $nutrients["protein"] = $nut_protein;
		if (isset($nut_fiber))  $nutrients["fiber"] = $nut_fiber;
		if (isset($nut_size) && $nut_size)
		{
			$nutrients["size"] = $nut_size;
		}
		$nutrients["units"] = $nut_units;
		$errCode = "";
		// see if updating or adding food item
		$upd_type = "add";
		if ($nut_id && $owner == $user)
		{
			$upd_type = "upd";
			// if name changed, did user want to add or update
			if ($old_nut_name != $nut_name) $upd_type = $upd_or_add;
		}
		if ($upd_type != "add")
		{
			
			if (!changeFoodNutrients($mysqli, $user, $nut_id, $nut_name, $nut_desc, $nutrients, $errCode))
			{
				// unknown error
				$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
				// *******while testing, display message, when deploying, activate sendError code****
				$food_mesg = "<p class='food_err'>$errCode</p>";
				//**********sendError($user, $errCode);			
			}
		}
		else
		{
			// adding new item
			if (!addFoodNutrients($mysqli, $user, $nut_name, $nut_desc, $nutrients, $errCode))
			{
				// could not add -- find out why
				switch($errCode)
				{
					case -1:
						$food_mesg = "<p class='food_err'>Food '".$nut_name."' already exists.</p>";
						break;
					default:
						// unknown error
						$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
						// *******while testing, display message, when deploying, activate sendError code****
						$food_mesg = "<p class='food_err'>$errCode</p>";
						//**********sendError($user, $errCode);
				}
			}
		}
		if (!$errCode)
		{
			$food_mesg = "<p class='food_mesg'>$nut_name was updated.</p>";
			clear_nutrients();
		}
	}
}
?>

