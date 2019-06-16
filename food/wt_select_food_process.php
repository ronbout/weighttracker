<?php
// wt_select_food_process.php
// 8-8-11 rlb
// user has clicked on select list of food items
// find out what mode user is in and process
// called from wt_form_check.php

// mode will be either "food" or "meal"
if ($mode == "food")
{
	switch ($type)
	{
		case "ingredients":
			$errCode = "";
			extract($_POST);
			$food_id = explode('*',$select_food[0]);
			$food_id = $food_id[0];
			if (!$food = loadFood($mysqli, $food_id, $errCode))
			{
				$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
				// *******while testing, display message, when deploying, activate sendError code****
				$food_mesg = "<p class='food_err'>$errCode</p>";
				//**********sendError($user, $errCode);	
			}
			else
			{
				// need to load ingredients page
				$errCode = "";
				$nutrition			= $food->getNutrition($mysqli, $errCode);
				$ingredients		= $food->getIngredients();
				$ingred_food_id 	= $food_id;
				$ingred_food_name 	= $food->getName();
				$ingred_food_desc 	= $food->getDescription();
				$owner 				= $food->getOwner();
				$ingred_size		= $food->getServingSize();
				$ingred_units		= $food->getServingUnits();
				$ingred_calories 	= $nutrition['calories'];
				$ingred_points		= $nutrition['points'];
				$ingred_fat			= $nutrition['fat'];
				$ingred_carbs		= $nutrition['carbs'];
				$ingred_protein		= $nutrition['protein'];
				$ingred_fiber		= $nutrition['fiber'];
				$ingreds    = array();
				$ingred_ids = array();
				$servings	= array();
				foreach($ingredients as $ingredient)
				{
					array_push($ingreds, $ingredient["ingredient_name"]);
					array_push($servings, $ingredient["servings"]);
					array_push($ingred_ids, $ingredient["ingredient_id"]);
				}
				$old_ingred_food_name = $ingred_food_name;
				$old_ingred_food_desc = $ingred_food_desc;
				$old_ingred_size = $ingred_size;
				$old_ingred_units = $ingred_units;
				$old_ingreds = $ingreds;
				$old_ingred_ids = $ingred_ids;
				$old_servings = $servings;
			}
			break;
		case "nutrients":
		default:
			$errCode = "";
			extract($_POST);
			$food_id = explode('*',$select_food[0]);
			$food_id = $food_id[0];
			if (!$food = loadFood($mysqli, $food_id, $errCode))
			{
				$food_mesg = "<p class='food_err'>Unknown error: Administrator has been notified.</p>";
				// *******while testing, display message, when deploying, activate sendError code****
				$food_mesg = "<p class='food_err'>$errCode</p>";
				//**********sendError($user, $errCode);	
			}
			else
			{
				// need to load nutrient page
				$errCode = "";
				$nutrition	= $food->getNutrition($mysqli, $errCode);
				$nut_id 	= $food_id;
				$nut_name 	= $food->getName();
				$nut_desc 	= $food->getDescription();
				$owner 		= $food->getOwner();
				$nut_size	= $food->getServingSize();
				$nut_units	= $food->getServingUnits();
				$nut_calories 	= $nutrition['calories'];
				$nut_points		= $nutrition['points'];
				$nut_fat		= $nutrition['fat'];
				$nut_carbs		= $nutrition['carbs'];
				$nut_protein	= $nutrition['protein'];
				$nut_fiber		= $nutrition['fiber'];
				$old_nut_id = $nut_id;
				$old_nut_name = $nut_name;
				$old_nut_desc = $nut_desc;
				$old_nut_size = $nut_size;
				$old_nut_units = $nut_units;
				$old_nut_calories = $nut_calories;
				$old_nut_points = $nut_points;
				$old_nut_fat = $nut_fat;
				$old_nut_carbs = $nut_carbs;
				$old_nut_protein = $nut_protein;
				$old_nut_fiber = $nut_fiber;
			}
	}
}
if ($mode == "meal")
{
}

?>