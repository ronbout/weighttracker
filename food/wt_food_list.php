<?php
// wt_food_list.php
// 7-3-11 rlb
// food listing div
// drop down listing of food items w/ various options
// called from wt_main_food.php

$errCode = "";
$list_err_mesg = "";
if (!isset($keywords)) 
{
	if (isset($_SESSION['keywords']))
		$keywords = $_SESSION['keywords'];
	else
		$keywords = "";
}
if (!isset($show_all))
{
	if (isset($_SESSION['show_all']))
		$show_all = $_SESSION['show_all'];
	else
		$show_all = "false";
}
if (!isset($ingreds_flag)) $ingreds_flag = false;
if (!$result = buildFoodList($mysqli, $user, $keywords, ($show_all == "true"), $errCode))
{
	$list_err_mesg = "<h2>Error building list.  Please try again later.</h2>";
	$list_err_mesg .= $errCode."<br>";
	// **********sendError($user, $errCode);
}
else
{
	if (!$result->num_rows)
	{
		$list_err_mesg = "No foods found.";
	}
}
?>
	<input id="search_words" type="text" name="keywords" placeholder="Enter food keywords" value="<?php echo $keywords;?>">
	<input id="search_button" type="image" onClick="this.form.update_flag.value='false';"
		name="search_food_but" src="food/search.png" alt="search">
<?php 
if (!$list_err_mesg)
{
	echo '<br><br><select name="select_food[]" size="25" ';
	if ($type == "ingredients") echo 'multiple="multiple"';
	echo '>
	';
	$first_flag = true;
	while($food_item = $result->fetch_assoc())
	{
		extract($food_item);
		// get calories for tooltip
		$errCode = "";
		$ingred_cals = "";
		$ingred_info = calcNutrients($mysqli, $id, $errCode);
		if ($ingred_info)
		{
			$ingred_cals = $ingred_info['calories'];
			$tooltip = "Created by: $created_by<br>Calories: $ingred_cals<br>$description";
		}
		else
		{
			$tooltip = "Created by: $created_by<br>$description";
		}
		if ($first_flag)
		{
			$first_flag = false;
			echo "<option value='$id*$ingredient_flag*$name' 
					ondblclick='this.form.elements[\"submit_select_food\"].click();' selected='selected'
					tooltip='$tooltip'
					onMouseOver='Tooltip.schedule(this, event)'>$name</option>";
		}
		else
		{
			echo "<option value='$id*$ingredient_flag*$name' 
					ondblclick='this.form.elements[\"submit_select_food\"].click();'
					tooltip='$tooltip'
					 onMouseOver='Tooltip.schedule(this, event)'>$name</option>";
		}
	}
	echo '
	</select><br>';
}
else
{
	echo "<h2>$list_err_mesg</h2>";
}
?>
<input type="radio" name="show_all" value="true" <?php if ($show_all == "true") echo 'checked="checked"';?>
>Show All (Slow w/o Keywords)<br>
<input type="radio" name="show_all" value="false" <?php if ($show_all == "false") echo 'checked="checked"';?>
>Show Your Foods Only<br>
<input type="submit" name="submit_select_food" value="View"
<?php
	if ($list_err_mesg) echo ' disabled="disabled" ';
?>
	onClick="return val_select_list(this,'<?php echo $pg;?>');">
<?php 
	if ($type=="ingredients") 
	{	echo '<input type="submit" name="submit_add_ingred" value="Add Ingred" ';
		if ($list_err_mesg) echo ' disabled="disabled" ';
		echo 'onClick="return val_add_ingred(this);">';
	}
?>
<input type="submit" name="select_food_refresh" onClick="this.form.update_flag.value='false';"
	value="Refresh">
<input type="hidden" name="pg_mode" value="<?php echo $mode;?>">
<input type="hidden" name="pg_type" value="<?php echo $type;?>">
<?php 
	// set SESSION variables for keywords and show_all, so that it 
	// will not be cleared out by food form processing
	$_SESSION['keywords'] = $keywords;
	$_SESSION['show_all'] = $show_all;
?>