<?php
// wt_main_food_ingredients.php
// 8-11-11 rlb
// form for setting up a new food item, built from ingredients
// called from wt_main_food.php

// need to clean up arrays, removing any blank ingreds
// sure there is a better way to do this!
// redo in rewrite
if (!isset($ingreds)) $ingreds = array();
if (!isset($ingred_ids)) $ingred_ids = array();
if (!isset($servings)) $servings = array();
for($i = 0; $i < sizeof($ingreds);)
{
	if (!$ingreds[$i])
	{
		array_splice($ingreds, $i, 1);
		if (isset($servings[$i])) array_splice($servings, $i, 1);
		if (isset($ingred_ids[$i])) array_splice($ingred_ids, $i, 1);
	}
	else
		$i++;
}
$ingreds = array_values($ingreds);
$servings = array_values($servings);
$ingred_ids = array_values($ingred_ids);
if (!isset($ingred_food_id)) $ingred_food_id = "";
if (!isset($ingred_food_name)) $ingred_food_name = "";
if (!isset($ingred_food_desc)) $ingred_food_desc = "";
if (!isset($ingred_size)) $ingred_size = "";
if (!isset($ingred_units)) $ingred_units = "gms";
if (!isset($food_mesg)) $food_mesg = "";
if (!isset($owner) || $owner === "") $owner = $user;
if (!isset($ingred_fav)) $ingred_fav = false;
$errCode = "";
if ($ingred_food_id) 
{
	$ingred_fav = check_fav($mysqli, $user, $ingred_food_id, $errCode);
	if ($errCode)
	{
		$food_mesg = "<p class='food_err'>Could not access favorites.</p>";
	}
}

$owner_info = loadMember($mysqli, $owner, $errCode);
$owner_name = $owner_info->getUserName();
$user_name = $member->getUserName();
// calc nutrients
$ingred_calories = 0;
$ingred_points = 0;
$ingred_fat = 0;
$ingred_carbs = 0;
$ingred_protein = 0;
$ingred_fiber = 0;
$errCode = "";
foreach($ingred_ids as $key=>$ingred_id)
{
	$food = loadFood($mysqli, $ingred_id, $errCode);
	if ($errCode) 
	{
		$food_mesg = "<p class='food_err'>Error calculating nutrients.</p>";
		continue;
	}
	$nutrients = $food->getNutrition($mysqli, $errCode);
	if ($errCode) 
	{
		$food_mesg = "<p class='food_err'>Error calculating nutrients.</p>";
		continue;
	}
	$ingred_calories += $servings[$key] * $nutrients['calories'];
	$ingred_points   += $servings[$key] * $nutrients['points'];
	$ingred_fat += $servings[$key] * $nutrients['fat'];
	$ingred_carbs += $servings[$key] * $nutrients['carbs'];
	$ingred_protein += $servings[$key] * $nutrients['protein'];
	$ingred_fiber += $servings[$key] * $nutrients['fiber'];
}
if (!isset($old_ingred_food_name)) $old_ingred_food_name = "";
if (!isset($old_ingred_food_desc)) $old_ingred_food_desc = "";
if (!isset($old_ingred_size)) $old_ingred_size = "";
if (!isset($old_ingred_units)) $old_ingred_units = "gms";
if (!isset($old_ingreds)) $old_ingreds = array();
if (!isset($old_ingred_ids)) $old_ingred_ids = array();
if (!isset($old_servings)) $old_servings = array();

?>
<div id="div_ingredients">
<form name="form_food_ingredients" action="<?php echo $selfLink;?>" method="post">
	<table cellspacing="4px">
		<tr>
			<td class="ingred_label">Food Name: * </td>
			<td><input type="text" name="ingred_food_name" size="30" maxlength="60" 
			value="<?php echo $ingred_food_name;?>" onChange="ingred_submit_status(this);"> 
			&nbsp; &nbsp; ( * - required field )</td>
		</tr>
		<tr>
			<td class="ingred_label">Description: </td>
			<td><input type="text" name="ingred_food_desc" size="50" maxlength="200" 
			value="<?php echo $ingred_food_desc;?>" onChange="ingred_submit_status(this);"></td>
		</tr>
	</table>
	<table id="table_ingred_nuts" cellspacing="1px">
		<tr>
			<th class="ing_ingred_labels">Cals</th>
			<th class="ing_ingred_labels">Points</th>
			<th class="ing_ingred_labels">Fat</th>
			<th class="ing_ingred_labels">Carbs</th>
			<th class="ing_ingred_labels">Protein</th>
			<th class="ing_ingred_labels">Fiber</th>
		</tr>
		<tr>
			<td><input class="ing_nuts" type="text" name="ingred_calories" size="6" maxlength="5" disabled="disabled"
				value="<?php echo $ingred_calories;?>"></td>
			<td><input class="ing_nuts" type="text" name="ingred_points" size="6" maxlength="4" disabled="disabled"
				value="<?php echo $ingred_points;?>"></td>
			<td><input class="ing_nuts" type="text" name="ingred_fat" size="6" maxlength="4" disabled="disabled"
				value="<?php echo $ingred_fat;?>"></td>
			<td><input class="ing_nuts" type="text" name="ingred_carbs" size="6" maxlength="4" disabled="disabled"
				value="<?php echo $ingred_carbs;?>"></td>
			<td><input class="ing_nuts" type="text" name="ingred_protein" size="6" maxlength="4" disabled="disabled"
				value="<?php echo $ingred_protein;?>"></td>
			<td><input class="ing_nuts" type="text" name="ingred_fiber" size="6" maxlength="4" disabled="disabled"
				value="<?php echo $ingred_fiber;?>"></td>
		</tr>
		<tr><td colspan="6"> </td></tr>
	</table>
	<table id="table_ingred_list" cellspacing="0">
		<tr>
			<th id="th_ingreds">Ingredients</th>
			<th id="th_servings">Servings</th>
		</tr>
		<tr>
			<td colspan="2">
				<div id="div_ingred_inputs">
<?php
	$lastkey = -1;
	foreach($ingreds as $key=>$ingred)
	{
		if ($ingred)
		{
			echo '<input type="text" name="ingreds[]" size="40" value="',$ingred,'"
				onChange="field_ingred_val(this,',$key,');">',"\n";
			$serv_val = (isset($servings[$key])) ? $servings[$key] : "";
			echo '<input class="ing_servings" type="text" name="servings[]" size="5" value="',$serv_val,'"
				onChange="field_servings_val(this,',$key,');">';
			echo '<input type="hidden" name="ingred_ids[]" value="',$ingred_ids[$key],'">';
			$lastkey = $key;
		}
	}
?>
					<input type="text" name="ingreds[]" size="40" value="" 
						onChange="field_ingred_val(this,<?php echo $lastkey+1;?>);">
					<input class="ing_servings" type="text" name="servings[]" size="5" value="" disabled="disabled"
						onChange="field_servings_val(this,<?php echo $lastkey+1;?>);">
					<input type="hidden" name="ingred_ids[]" value="">
				</div>
			</td>
		</tr>
		<tr><td></td><td></td></tr>
	</table>
	<table id="table_ingred_size">
		<tr>
			<td class="ingred_label">Serv Size: </td>
			<td>
				<input type="text" name="ingred_size" size="5" maxlength="6" 
				value="<?php echo $ingred_size;?>" onChange="ingred_submit_status(this);">
				<select name="ingred_units" onChange="ingred_submit_status(this);">
					<option value="gms" 
					<?php if ($ingred_units == "gms") echo 'selected="selected"';?>>gms</option>
					<option value="oz" 
					<?php if ($ingred_units == "oz") echo 'selected="selected"';?>>oz</option>
					<option value="cups" 
					<?php if ($ingred_units == "cups") echo 'selected="selected"';?>>cups</option>
					<option value="tbls" 
					<?php if ($ingred_units == "tbls") echo 'selected="selected"';?>>tbls</option>
					<option value="tsp" 
					<?php if ($ingred_units == "tsp") echo 'selected="selected"';?>>tsp</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="ingred_label">Created by: </td>
			<td><input type="text" name="owner_name" value="<?php echo $owner_name;?>" disabled="disabled">
				&nbsp; &nbsp; <input id="fav_box" type="checkbox" disabled="disabled" 
				<?php if ($ingred_fav) echo 'checked="checked"';?>> &nbsp; <?php echo $user_name;?> FAV</td>
		</tr>
	</table>
	<p id="p_ingred_buttons"><input class="ingred_buttons" type="submit" name="submit_ingredients" value="Update" 
				onClick="return validate_food_ingredients(this);"
		<?php if ($owner != $user) echo 'disabled="disabled"';?>
		> 
		<input class="ingred_buttons" type="submit" name="reset_ingredients" onClick="this.form.update_flag.value='false';"
				value="Reset">
		<input class="ingred_buttons" type="submit" name="clear_ingredients" value="Clear">
		<input class="ingred_buttons" type="submit" name="fav_ingredients" value="<?php 
			if ($ingred_fav)
				echo 'Unmark Fav';
			else
				echo 'Mark Fav';
		echo '"';
		if ($owner == $user) echo 'disabled="disabled"';?>>
		<!--  ** RIGHT NOW, NO DELETE OF FOOD ITEMS
		<input class="ingred_buttons" type="submit" name="delete_nutrients" value="Delete" 
		<?php //if ($owner != $user || !$ingred_id) echo 'disabled="disabled"';?>
		>  -->
<?php
		foreach($old_ingreds as $key=>$old_ingred)
		{
			if ($old_ingred)
			{
?>				<input type="hidden" name="old_ingreds[]" value="<?php echo $old_ingred;?>">
				<input type="hidden" name="old_ingred_ids[]" value="<?php echo $old_ingred_ids[$key];?>">
				<input type="hidden" name="old_servings[]" value="<?php echo $old_servings[$key];?>">
<?php
			}
		}
?>		
		<input type="hidden" name="submit_flag" value="false">
		<input type="hidden" name="ingred_food_id" value="<?php echo $ingred_food_id;?>">
		<input type="hidden" name="old_ingred_food_name" value="<?php echo $old_ingred_food_name;?>">
		<input type="hidden" name="old_ingred_food_desc" value="<?php echo $old_ingred_food_desc;?>">
		<input type="hidden" name="old_ingred_size" value="<?php echo $old_ingred_size;?>">
		<input type="hidden" name="old_ingred_units" value="<?php echo $old_ingred_units;?>">
		<input type="hidden" name="owner" value="<?php echo $owner;?>">
		<input type="hidden" name="ingred_fav" value="<?php echo $ingred_fav;?>">
		<input type="hidden" name="user" value="<?php echo $user;?>">
		<input type="hidden" name="upd_or_add" value="update">
		<input type="hidden" name="update_flag" value="true">
		<!--<button value="test" onClick="this.form.elements['submit_nutrients'].type='hidden';
				this.form.elements['update_nutrients'].type='submit'; return false;">Test</button>-->
	</p>
<?php echo $food_mesg;?>
</div><!-- end of div_nutrients -->