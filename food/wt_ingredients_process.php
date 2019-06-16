<?php
// wt_ingredients_process.php
// 8-2-11 rlb
// process the form_nutrients form from wt_main_food_nutrients.php
// this form creates a new food item by supplying the nutrient info
// called from wt_form_check.php
extract($_POST);
// if update flag is not set, then submit was just used to redraw form with no parameters
if ($update_flag == "true")
{
	$errFlg = false;
	// check for missing data
	$ingred_food_name = trim($ingred_food_name);
	if (!isset($ingred_food_name) || !$ingred_food_name)
	{
		$food_mesg = "<p class='food_err'>Food Name is required.</p>";
		$errFlg = true;
	}
	// check for numeric servings
	if (!$errFlg)
	{
		foreach($servings as $serving)
		{
			if ($serving == "" || !is_numeric($serving))
			{
				$food_mesg = "<p class='food_err'>Invalid ".$serving." value.  Numeric required.</p>";
				$errFlg = true;
				break;
			}
		}
	}
	// check for numeric size
	if (!$errFlg)
	{
		if (isset($ingred_size) && $ingred_size && !is_numeric($ingred_size))
		{
			$food_mesg = "<p class='food_err'>Invalid food size.</p>";
			$errFlg = true;
		}
	}
	// check for circular relationship in ingredients
	if (!$errFlg)
	{
		$errCode = "";
		foreach($ingred_ids as $key=>$ingred_id)
		{
			if ($ingred_id)
			{
				$ingred_list = array();
				if (($ingred_list = get_all_ingredients($mysqli, $ingred_id, $errCode)) === false)
				{
					// unknown error
					$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
					// *******while testing, display message, when deploying, activate sendError code****
					$food_mesg = "<p class='food_err'>$errCode</p>";
					//**********sendError($user, $errCode);
					$errFlg = true;
					break;
				}
				if (in_array($ingred_food_id, $ingred_list))
				{
					$food_mesg =  "<p class='food_err'>Invalid ingredient.  Circular Relationship.</p>";
					$errFlg = true;
					break;
				}
				if ($ingred_id == $ingred_food_id)
				{
					$food_mesg =  "<p class='food_err'>Food cannot be ingredient of itself.</p>";
					$errFlg = true;
					break;
				}
			}
		}
	}
	if (!$errFlg)
	{
		// ingredients
		$errCode = "";
		// see if updating or adding food item
		$upd_type = "add";
		if ($ingred_food_id && $owner == $user)
		{
			$upd_type = "upd";
			// if name changed, did user want to add or update
			if ($old_ingred_food_name != $ingred_food_name) $upd_type = $upd_or_add;
		}
		if ($upd_type != "add")
		{
			
			if (!changeFoodIngredients($mysqli, $user, $ingred_food_id, $ingred_food_name, $ingred_food_desc, 
										$ingred_ids, $servings, $ingred_size, $ingred_units, $errCode))
			{
				// unknown error
				$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
				// *******while testing, display message, when deploying, activate sendError code****
				$food_mesg = "<p class='food_err'>$errCode</p>";
				//**********sendError($user, $errCode);			
			}
			else
				$food_mesg = "<p class='ingred_food_mesg'>$ingred_food_name was updated.</p>";
		}
		else
		{
			// adding new item
			if (!addFoodIngredients($mysqli, $user, $ingred_food_name, $ingred_food_desc, $ingred_ids, 
									$servings, $ingred_size, $ingred_units, $errCode))
			{
				// could not add -- find out why
				switch($errCode)
				{
					case -1:
						$food_mesg = "<p class='food_err'>Food '".$ingred_food_name."' already exists.</p>";
						break;
					default:
						// unknown error
						$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
						// *******while testing, display message, when deploying, activate sendError code****
						$food_mesg = "<p class='food_err'>$errCode</p>";
						//**********sendError($user, $errCode);
				}
			}
			else
				$food_mesg = "<p class='ingred_food_mesg'>$ingred_food_name was created.</p>";
		}
		if (!$errCode)
		{
			clear_ingredients();
		}
	}
}


?>