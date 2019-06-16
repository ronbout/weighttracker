<?php
// wt_food_funcs.php
// 7-1-11 rlb
// contains functions for weight tracker wt.php
// focused on food and food setup functions

function calcNutrients($mysqli, $food, &$errorCode)
{
	// returns array of food nutrient items
	// calories, points, fat, carbs, protein, fiber
	// recursive design to drill down through ingredient lists
	$nutrients = array();
	$nutrients['calories'] = 0;
	$nutrients['points'] = 0;
	$nutrients['fat'] = 0;
	$nutrients['carbs'] = 0;
	$nutrients['protein'] = 0;
	$nutrients['fiber'] = 0;
	$query = "select * from food_detail where id = ".$food;
	if (!$foodResult = $mysqli->query($query))
	{
		$errorCode = $mysqli->error;
		return false;
	}
	while ($foodInfo = $foodResult->fetch_assoc())
	{
		if ($foodInfo['calories'])
		{
			$nutrients['calories']	+= $foodInfo['calories'];
			$nutrients['points']	+= $foodInfo['points'];
			$nutrients['fat']		+= $foodInfo['fat_grams'];
			$nutrients['carbs']		+= $foodInfo['carb_grams'];
			$nutrients['protein']	+= $foodInfo['protein_grams'];
			$nutrients['fiber']		+= $foodInfo['fiber_grams'];
		}
		else
		{
			$errorCode = "";
			if (!$nuts = calcNutrients($mysqli, $foodInfo['ingredient_id'], $errorCode)) 
				return false;
			$nutrients['calories']	+= $nuts['calories'] * $foodInfo['servings'];
			$nutrients['points']	+= $nuts['points'] * $foodInfo['servings'];
			$nutrients['fat']		+= $nuts['fat'] * $foodInfo['servings'];
			$nutrients['carbs']		+= $nuts['carbs'] * $foodInfo['servings'];
			$nutrients['protein']	+= $nuts['protein'] * $foodInfo['servings'];
			$nutrients['fiber']		+= $nuts['fiber'] * $foodInfo['servings'];
		}
	}
	return $nutrients;
}
function checkJavascript($get)
{
	// check whether javascript was enabled, if not finish of page with error message
	if (!isset($get['js'])) 
	{
		echo '	<br><br><h1>Javascript is required for this page</h1>
				</div><!--  end of div_food_main -->
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->';
		require("wt_footer.php"); 
		echo '
				</div><!-- end of page container  -->
				</body>
			</html>';
		return false;
	}
	$_SESSION['js'] = true;
	return true;
}
function addFoodMain($mysqli, $user, $name, $desc, $ingred_flag, $size, $units, &$errorCode)
{
	// add to the main food table
	$name = $mysqli->real_escape_string($name);
	$query = "SELECT * FROM food WHERE lower(name) = '".strtolower($name)."'";
	if ($result = $mysqli->query($query))
	{
		if ($result->num_rows)
		{
			// changed to allow same food names as long as it is different owner
			$info = $result->fetch_assoc();
			if ($info['owner'] == $user)
			{
				$errorCode = -1;  // food name already exists
				return false;
			}
		}
		// insert into food table
		$errorCode = "";
		$desc = $mysqli->real_escape_string($desc);
		if ($size)
		{
			$query = "INSERT INTO food (name, description, owner, ingredient_flag, serving_size, serving_units)
				  VALUES ('".$name."','".$desc."','".$user."', ".$ingred_flag.", ".$size.", 
				  (SELECT id FROM food_units WHERE description = '$units'))";
		}
		else
		{
			$query = "INSERT INTO food (name, description, owner, ingredient_flag, serving_units)
				  VALUES ('".$name."','".$desc."','".$user."', ".$ingred_flag.",  
				  (SELECT id FROM food_units WHERE description = '$units'))";
		}
		if ($result = $mysqli->query($query))
		{
			$errorCode = 0;
			return true;
		}
		else
		{
			$errorCode = "Error update food: ".$mysqli->error;   // error inserting data
			return false;
		}
	}
	else
	{
		$errorCode = $myslqi->error;
		return false;
	}
}
function addFoodNutrients($mysqli, $user, $name, $desc, $nutrients, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	
	$size = 	(isset($nutrients['size']) && $nutrients['size'] !="") ? $nutrients['size'] : 0;
	$units = 	$nutrients['units'];
	// insert into food table
	if(!addFoodMain($mysqli, $user, $name, $desc, 0, $size, $units, $errorCode))
	{
		return false;
	}
	// insert into food_detail table
	// get food id
	$food_id = $mysqli->insert_id;
	// get nutrients
	$calories = $nutrients['calories'];
	$points =	(isset($nutrients['points']) && $nutrients['points'] !="") ? $nutrients['points'] : 0;
	$fat =		(isset($nutrients['fat'])&& $nutrients['fat'] !="") ? $nutrients['fat'] : 0;
	$carbs = 	(isset($nutrients['carbs'])&& $nutrients['carbs'] !="") ? $nutrients['carbs'] : 0;
	$protein = 	(isset($nutrients['protein'])&& $nutrients['protein'] !="") ? $nutrients['protein'] : 0;
	$fiber = 	(isset($nutrients['fiber'])&& $nutrients['fiber'] !="") ? $nutrients['fiber'] : 0;
	$query = "INSERT INTO food_detail (id, calories, points, fat_grams, carb_grams, protein_grams, fiber_grams)
				VALUES ($food_id, $calories, $points, $fat, $carbs, $protein, $fiber)";
	if (!$result = $mysqli->query($query))
	{
		// need to remove original
		$err = $mysqli->error;
		$query = "DELETE FROM food WHERE id = $food_id";
		if ($result = $mysqli->query($query))
			$errorCode = "Food not updated.  Error -food detail: ".$err;
		else
			$errorCode = "Food nutrients not updated!: ".$err;
		return false;
	}
	// insert into member_food_favs
	if (!add_food_favs($mysqli, $user, $food_id, $errorCode)) return false;
	return true;
}
function addFoodIngredients($mysqli, $user, $name, $desc, $ingredients, $servings, $size, $units, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);

	// insert into food table
	if(!addFoodMain($mysqli, $user, $name, $desc, 1, $size, $units, $errorCode))
	{
		return false;
	}
	// get food id
	$food_id = $mysqli->insert_id;
	// insert food_detail table, using transaction!!
	$mysqli->autocommit(false);
	foreach($ingredients as $key=>$ingredient)
	{
		if (!$ingredient) continue;
		// servings array matches keys with ingredients
		$serving = $servings[$key];
		$query = "INSERT INTO food_detail (id, ingredient_id, servings) VALUES ($food_id, $ingredient, $serving)";
		if (!$result = $mysqli->query($query))
		{
			$err = $mysqli->error;
			$mysqli->rollback();
			$mysqli->autocommit(true);
			// need to remove original food
			$query = "DELETE FROM food WHERE id = $food_id";
			if ($result = $mysqli->query($query))
				$errorCode = "Food not updated.  Error -food detail: ".$err;
			else
				$errorCode = "Food ingredient $ingredient not updated!: ".$err;
			return false;			
		}
	}
	$mysqli->commit();
	$mysqli->autocommit(true);	
	// insert into member_food_favs
	if (!add_food_favs($mysqli, $user, $food_id, $errorCode)) return false;
	return true;
}
function changeFoodMain($mysqli, $user, $food_id, $name, $desc, $size, $units, &$errorCode)
{
	// check that the name is not another food's name
	$name = $mysqli->real_escape_string($name);
	$query = "SELECT * FROM food WHERE lower(name) = '".strtolower($name)."'";
	if ($result = $mysqli->query($query))
	{
		if ($result->num_rows)
		{
			$info = $result->fetch_assoc();
			if ($info['id'] != $food_id && $info['owner'] == $user)
			{
				$errorCode = -1;  // food name already exists
				return false;
			}
		}
		// add to the main food table
		$name = $mysqli->real_escape_string($name);
		$desc = $mysqli->real_escape_string($desc);
		
		$errorCode = "";
		if ($size)
		{
			$query = "UPDATE food SET name='$name', description='$desc', serving_size=$size, 
					serving_units=(SELECT id FROM food_units WHERE description = '$units')
					WHERE id=$food_id";
		}
		else
		{
			$query = "UPDATE food SET name='$name', description='$desc',  
					serving_units=(SELECT id FROM food_units WHERE description = '$units')
					WHERE id=$food_id";
		}
		if ($result = $mysqli->query($query))
		{
			$errorCode = 0;
			return true;
		}
		else
		{
			$errorCode = "Error update food: ".$mysqli->error;   // error inserting data
			return false;
		}
	}
	else
	{
		$errorCode = "Error updating food: ".$myslqi->error;
		return false;
	}
}
function changeFoodNutrients($mysqli, $user, $food_id, $name, $desc, $nutrients, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	
	$size = 	(isset($nutrients['size']) && $nutrients['size'] !="") ? $nutrients['size'] : 0;
	$units = 	$nutrients['units'];
	$errorCode = "";
	// update food table
	if(!changeFoodMain($mysqli, $user, $food_id, $name, $desc, $size, $units, $errorCode))
	{
		return false;
	}
	// update food_detail table
	// get nutrients
	$calories = $nutrients['calories'];
	$points =	(isset($nutrients['points']) && $nutrients['points'] !="") ? $nutrients['points'] : 0;
	$fat =		(isset($nutrients['fat'])&& $nutrients['fat'] !="") ? $nutrients['fat'] : 0;
	$carbs = 	(isset($nutrients['carbs'])&& $nutrients['carbs'] !="") ? $nutrients['carbs'] : 0;
	$protein = 	(isset($nutrients['protein'])&& $nutrients['protein'] !="") ? $nutrients['protein'] : 0;
	$fiber = 	(isset($nutrients['fiber'])&& $nutrients['fiber'] !="") ? $nutrients['fiber'] : 0;
	$query = "UPDATE food_detail SET calories=$calories, points=$points, fat_grams=$fat, carb_grams=$carbs,
				protein_grams=$protein, fiber_grams=$fiber WHERE id=$food_id";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = "Food nutrients not updated!: ".$mysqli->error;
		return false;
	}
	return true;
}
function changeFoodIngredients($mysqli, $user, $food_id, $name, $desc, $ingredients, $servings, $size, $units, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);

	$errorCode = "";
	// update food table
	if(!changeFoodMain($mysqli, $user, $food_id, $name, $desc, $size, $units, $errorCode))
	{
		return false;
	}
	// change food_detail table, using transaction!!
	// first delete old items, then insert new ones
	$mysqli->autocommit(false);
	$query = "DELETE FROM food_detail WHERE id = $food_id";
	if (!$result = $mysqli->query($query))
	{
		$mysqli->rollback();
		$mysqli->autocommit(true);
		$errorCode = "Food ingredients not updated!: ".$mysqli->error;
		return false;	
	}
	foreach($ingredients as $key=>$ingredient)
	{
		if (!$ingredient) continue;
		// servings array matches keys with ingredients
		$serving = $servings[$key];
		$query = "INSERT INTO food_detail (id, ingredient_id, servings) VALUES ($food_id, $ingredient, $serving)";
		if (!$result = $mysqli->query($query))
		{
			$mysqli->rollback();
			$mysqli->autocommit(true);
			$errorCode = "Food ingredient $ingredient not updated!: ".$mysqli->error;
			return false;			
		}
	}
	$mysqli->commit();
	$mysqli->autocommit(true);
	return true;
}
function loadFood($mysqli, $food_id, &$errorCode)
{
	// loads food info from food and food detail tables
	// returns a Food object
	$foodInfo = new Food;
	// get food info from tables food and food_detail
	$errorCode = "";
	$query = "SELECT a.id, a.name, a.description, a.owner, a.ingredient_flag, a.serving_size, b.description as serving_units
				FROM food a, food_units b WHERE a.id = ".$food_id." AND b.id=a.serving_units";
	if ($result = $mysqli->query($query))
	{
		if (!$result->num_rows)
		{
			$errorCode = -1; // unknown food
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
	// remove quotes from name and description
	$row['name'] = str_replace('"','',$row['name']);
	$row['description'] = str_replace('"','',$row['description']);
	// retrieved food main info, load foodInfo
	$foodInfo->setAll($row);
	// now load food detail
	$errorCode = "";

	if ($row['ingredient_flag'])
	{
		$query = "SELECT a.ingredient_id, a.servings, b.name FROM food_detail a, food b 
			WHERE a.id = ".$food_id." AND b.id = a.ingredient_id";
		if (!$result = $mysqli->query($query))
		{
			$errorCode = $mysqli->error; // error with Query
			return false;
		}
		$ingredients = array();
		while ($row = $result->fetch_assoc())
		{
			$ingredients[] = array("ingredient_id"=>$row['ingredient_id'], "ingredient_name"=>$row['name'],
									  "servings"=>$row['servings']);
		}
		if ($ingredients) $foodInfo->setIngredients($ingredients);
	}
	else
	{
		$query = "SELECT * FROM food_detail WHERE id = ".$food_id;
		if (!$result = $mysqli->query($query))
		{
			$errorCode = $mysqli->error; // error with Query
			return false;
		}
		$row = $result->fetch_assoc();
		if (!$row)
		{
			$errorCode = -2;   // missing food detail
			return false;
		}
		$nutrients = array();
		$nutrients['calories'] = $row['calories'];
		$nutrients['points'] = $row['points'];
		$nutrients['fat'] = $row['fat_grams'];
		$nutrients['carbs'] = $row['carb_grams'];
		$nutrients['protein'] = $row['protein_grams'];
		$nutrients['fiber'] = $row['fiber_grams'];
		$foodInfo->setNutrition($nutrients);
	}
	return $foodInfo;	
}
function buildFoodList($mysqli, $user, $keywords, $show_all, &$errorCode)
{
	// returns result for food select query with id, name, and description
	// $user: if $show_all is false, only $user favorites are displayed
	// $keywords: if $keywords are present, list is searched for those
	// $show_all: if set all foods by all owners are displayed
	$db_keywords = $mysqli->real_escape_string($keywords);
	$query = "SELECT a.id, a.name, a.description, a.ingredient_flag, b.user_name as created_by, a.serving_size, 
				a.serving_units from food a, member b WHERE a.owner = b.member_id";
	if (!$show_all)
		$query .= " AND a.id in (SELECT food_id FROM member_food_favs WHERE member_id = $user)";
	if ($keywords)
	{
		$query .= " AND MATCH(a.name, a.description) AGAINST ('$db_keywords' IN BOOLEAN MODE) 
					ORDER BY MATCH(a.name, a.description) AGAINST ('$db_keywords' IN BOOLEAN MODE) DESC,
					a.description";
	}
	else
	{
		$query .= " ORDER BY a.description";
	}
	$errorCode = "";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = "Error retrieving food items: ".$mysqli->error;
		return false;
	}
	return $result;
}
function get_all_ingredients($mysqli, $food_id, &$errorCode)
{
	// recursive function to retrieve all ingredients from a food item (and ingredients of ingredients, etc)
	// returns array of ingredients or false
	$ingredients = array();

	$query = "select * from food_detail where id = ".$food_id;
	if (!$foodResult = $mysqli->query($query))
	{
		$errorCode = $mysqli->error;
		return false;
	}
	while ($foodInfo = $foodResult->fetch_assoc())
	{
		if ($foodInfo['ingredient_id'])
		{
			$ingredients[] = $foodInfo['ingredient_id'];
			if (($ingreds = get_all_ingredients($mysqli, $foodInfo['ingredient_id'], $errorCode)) === false)
				return false;
			if (sizeof($ingreds))
				$ingredients = array_merge($ingredients, $ingreds);
		}
		else
		{
			return array();
		}
	}
	return array_unique($ingredients);
}
function clear_nutrients()
{
	global $nut_id;
	global $nut_name;
	global $nut_desc;
	global $nut_calories;
	global $nut_points;
	global $nut_fat;
	global $nut_carbs;
	global $nut_protein;
	global $nut_fiber;
	global $nut_size;
	global $nut_units;
	$nut_id = "";
	$nut_name = "";
	$nut_desc = "";
	$nut_calories = "0";
	$nut_points = "0";
	$nut_fat = "0";
	$nut_carbs = "0";
	$nut_protein = "0";
	$nut_fiber = "0";
	$nut_size = "0";
	$nut_units = "gms";
	$old_nut_name = "";
	$old_nut_desc = "";
	$old_nut_size = "0";
	$old_nut_units = "gms";
	$old_nut_calories = "0";
	$old_nut_points = "0";
	$old_nut_fat = "0";
	$old_nut_carbs = "0";
	$old_nut_protein = "0";
	$old_nut_fiber = "0";
}
function clear_ingredients()
{
	global $ingred_food_id;
	global $ingred_food_name;
	global $ingred_food_desc;
	global $ingred_size;
	global $ingred_units;
	global $ingreds;
	global $ingred_ids;
	global $servings;
	$ingred_food_id = "";
	$ingred_food_name = "";
	$ingred_food_desc = "";
	$ingred_size = "0";
	$ingred_units = "gms";
	$ingreds = array();
	$ingred_ids = array();
	$servings = array();
	$old_ingred_food_name = "";
	$old_ingred_food_desc = "";
	$old_ingred_size = "0";
	$old_ingred_units = "gms";
	$old_ingreds = array();
	$old_ingred_ids = array();
	$old_servings = array();
}
function add_food_favs($mysqli, $user, $food_id, &$errorCode)
{
	$query = "INSERT INTO member_food_favs (member_id, food_id) VALUES ($user, $food_id)";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = $mysqli->error;
		return false;
	}
	return true;
}
function delete_food_favs($mysqli, $user, $food_id, &$errorCode)
{
	$query = "DELETE FROM member_food_favs WHERE member_id = $user AND food_id = $food_id";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = $mysqli->error;
		return false;
	}
	return true;
}
function check_fav($mysqli, $user, $food_id, &$errorCode)
{
	$errorCode = "";
	$query = "SELECT * FROM member_food_favs WHERE member_id = $user AND food_id = $food_id";
	if (!$result = $mysqli->query($query))
	{
		$errorCode = $mysqli->error;
		return false;
	}
	return ($result->num_rows);
}
?>