<?php
// m_food_process.php
// 2-4-11 rlb
// process search form for food

if (isset($_POST['search_but_x']))
{
	extract($_POST);
	if (!isset($search_words)) $search_words = "";
	$dbsearch_words = $mysqli->real_escape_string($search_words);
	if ($search_words)
		$query = "SELECT c.user_name, a.id, a.serving_size, d.description as serving_units, a.name, a.description, 
				 b.calories, b.fat_grams, b.carb_grams, b.protein_grams, b.fiber_grams FROM food a, food_detail b, 
				 member c, food_units d 
				 WHERE match(a.name, a.description) against ('$dbsearch_words' IN BOOLEAN MODE) AND 
				 a.id = b.id AND a.owner=c.member_id AND d.id = a.serving_units  ORDER BY match(a.name, a.description) 
				 against ('$dbsearch_words' IN BOOLEAN MODE) desc";
	else
		$query = "SELECT c.user_name, a.id, a.serving_size, d.description as serving_units, a.name, a.description, 
				b.calories, b.fat_grams, b.carb_grams, b.protein_grams, b.fiber_grams FROM food a, food_detail b, 
				member c, food_units d
				WHERE a.id = b.id AND a.owner=c.member_id AND d.id = a.serving_units order by a.name";
	if (!$result = $mysqli->query($query))	die("<h1>Database error!</h1><h2>Please try again.</h2>".$mysqli->error);
	if ($result->num_rows)
	{
		if ($result->num_rows == 1)
		{
			// no need to show list, just fetch and display item
			$food_item = $result->fetch_assoc();
			extract($food_item);
			$list_flag = false;
			$display_flag = true;
			unset($_SESSION['food_list']);
		}
		else
		{
			// display form with drop down of found items
			$select_flag = true;
		}
	}
	else
	{
		$mesg = "<p>No results found for $search_words.</p>";
	}
}
if (isset($_POST['submit_select']))
{
	extract($_POST);
	$food_list = unserialize($_SESSION['food_list']);
	$i = $select_food;
	$food_item = $food_list[$i];
	extract($food_item);
	$list_flag = true;
	$display_flag = true;
}
if (isset($_POST['submit_next']))
{
	extract($_POST);
	$food_list = unserialize($_SESSION['food_list']);
	$i = $select_food;
	$food_item = $food_list[++$i];
	extract($food_item);
	$list_flag = true;
	$display_flag = true;
}
if (isset($_POST['submit_prev']))
{
	extract($_POST);
	$food_list = unserialize($_SESSION['food_list']);
	$i = $select_food;
	$food_item = $food_list[--$i];
	extract($food_item);
	$list_flag = true;
	$display_flag = true;
}
if (isset($_POST['submit_serving']))
{
	extract($_POST);
	$food_list = unserialize($_SESSION['food_list']);
	$i = $select_food;
	$food_item = $food_list[$i];
	extract($food_item);
	$list_flag = true;
	$display_flag = true;
	// calc new serving values
	if ($serving_units == $new_units) 
	{
		$multiplier = $new_size / $serving_size;
	}
	else if ($serving_units == "gms" && $new_units == "oz")
	{
		$multiplier = $new_size / ($serving_size * 0.035);
	}
	else if ($serving_units == "oz" && $new_units == "gms")
	{
		$multiplier = ($new_size * 0.035) / $serving_size;
	}
	else
	{
		// SHOULD NOT GET HERE!! 
		$multiplier = 1;
	}
	$new_cals  = round($calories * $multiplier);
	$new_fat   = round($fat_grams * $multiplier);
	$new_carb  = round($carb_grams * $multiplier);
	$new_pro   = round($protein_grams * $multiplier);
	$new_fib   = round($fiber_grams * $multiplier);
} 
?>