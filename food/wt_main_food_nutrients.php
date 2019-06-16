<?php
// wt_main_food_nutrients.php
// 8-1-11 rlb
// form for setting up a new food item
// called from wt_main_food.php

// set default values
if (!isset($nut_id)) $nut_id = "";
if (!isset($nut_name)) $nut_name = "";
if (!isset($nut_desc)) $nut_desc = "";
if (!isset($nut_calories)) $nut_calories = "0";
if (!isset($nut_points)) $nut_points = "0";
if (!isset($nut_fat)) $nut_fat = "0";
if (!isset($nut_carbs)) $nut_carbs = "0";
if (!isset($nut_protein)) $nut_protein = "0";
if (!isset($nut_fiber)) $nut_fiber = "0";
if (!isset($nut_size)) $nut_size = "";
if (!isset($nut_units)) $nut_units = "gms";
if (!isset($food_mesg)) $food_mesg = "";
if (!isset($nut_fav)) $nut_fav = false;
if (!isset($owner) || $owner === "") $owner = $user;
if (!isset($old_nut_name)) $old_nut_name = "";
if (!isset($old_nut_desc)) $old_nut_desc = "";
if (!isset($old_nut_size)) $old_nut_size = "";
if (!isset($old_nut_units)) $old_nut_units = "gms";
if (!isset($old_nut_calories)) $old_nut_calories = "0";
if (!isset($old_nut_points)) $old_nut_points = "0";
if (!isset($old_nut_fat)) $old_nut_fat = "0";
if (!isset($old_nut_carbs)) $old_nut_carbs = "0";
if (!isset($old_nut_protein)) $old_nut_protein = "0";
if (!isset($old_nut_fiber)) $old_nut_fiber = "0";

$errCode = "";
if ($nut_id) 
{
	$nut_fav = check_fav($mysqli, $user, $nut_id, $errCode);
	if ($errCode)
	{
		$food_mesg = "<p class='food_err'>Could not access favorites.</p>";
	}
}

$owner_info = loadMember($mysqli, $owner, $errCode);
$owner_name = $owner_info->getUserName();
$user_name = $member->getUserName();
?>
<div id="div_nutrients">
	<table cellspacing="4px">
		<tr>
			<td class="nut_label">Food Name: * </td>
			<td><input type="text" name="nut_name" size="30" maxlength="60" onChange="nut_submit_status(this);"
			value="<?php echo $nut_name;?>"> 
			&nbsp; &nbsp; ( * - required field )</td>
		</tr>
		<tr>
			<td class="nut_label">Description: </td>
			<td><input type="text" name="nut_desc" size="50" maxlength="200"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_desc;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Calories: * </td>
			<td><input class="nuts" type="text" name="nut_calories" size="6" maxlength="5"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_calories;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Points: </td>
			<td><input class="nuts" type="text" name="nut_points" size="6" maxlength="4"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_points;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Fat gms: </td>
			<td><input class="nuts" type="text" name="nut_fat" size="6" maxlength="4"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_fat;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Carb gms: </td>
			<td><input class="nuts" type="text" name="nut_carbs" size="6" maxlength="4"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_carbs;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Protein gms: </td>
			<td><input class="nuts" type="text" name="nut_protein" size="6" maxlength="4"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_protein;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Fiber gms: </td>
			<td><input class="nuts" type="text" name="nut_fiber" size="6" maxlength="4"  onChange="nut_submit_status(this);"
			value="<?php echo $nut_fiber;?>"></td>
		</tr>
		<tr>
			<td class="nut_label">Serv Size: </td>
			<td>
				<input class="nuts" type="text" name="nut_size" size="6" maxlength="6"  onChange="nut_submit_status(this);"
				value="<?php echo $nut_size;?>">
				<select name="nut_units">
					<option value="gms" 
					<?php if ($nut_units == "gms") echo 'selected="selected"';?>>gms</option>
					<option value="oz" 
					<?php if ($nut_units == "oz") echo 'selected="selected"';?>>oz</option>
					<option value="cups" 
					<?php if ($nut_units == "cups") echo 'selected="selected"';?>>cups</option>
					<option value="tbls" 
					<?php if ($nut_units == "tbls") echo 'selected="selected"';?>>tbls</option>
					<option value="tsp" 
					<?php if ($nut_units == "tsp") echo 'selected="selected"';?>>tsp</option>
					<option value="fl oz" 
					<?php if ($nut_units == "fl oz") echo 'selected="selected"';?>>fl oz</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="nut_label">Created by: </td>
			<td><input type="text" name="owner_name" value="<?php echo $owner_name;?>" disabled="disabled">
				&nbsp; &nbsp; <input id="fav_box" type="checkbox" disabled="disabled" 
				<?php if ($nut_fav) echo 'checked="checked"';?>> &nbsp; <?php echo $user_name;?> FAV</td>
		</tr>
	</table>

	<p id="p_nut_buttons"><input class="nut_buttons" type="submit" name="submit_nutrients" value="Update" 
				onClick="return validate_food_nutrients(this);" disabled="disabled"> 
		<input class="nut_buttons" type="submit" name="reset_nutrients" onClick="this.form.update_flag.value='false';"
			value="Reset">
		<input class="nut_buttons" type="submit" name="clear_nutrients" value="Clear">
		<input class="nut_buttons" type="submit" name="fav_nutrients" value="<?php 
			if ($nut_fav)
				echo 'Unmark Fav';
			else
				echo 'Mark Fav';
		echo '"';
		if ($owner == $user) echo 'disabled="disabled"';?>>
		<input class="nut_buttons" type="submit" name="recalc" value="ReCalc" onClick="return recalc_size(this);"
				<?php if (!$nut_size) echo 'disabled="disabled"';?>>
		<!--  ** RIGHT NOW, NO DELETE OF FOOD ITEMS
		<input class="nut_buttons" type="submit" name="delete_nutrients" value="Delete" 
		<?php //if ($owner != $user || !$nut_id) echo 'disabled="disabled"';?>
		>  -->
		
		<input type="hidden" name="submit_flag" value="false">
		<input type="hidden" name="nut_id" value="<?php echo $nut_id;?>">
		<input type="hidden" name="old_nut_name" value="<?php echo $old_nut_name;?>">
		<input type="hidden" name="old_nut_desc" value="<?php echo $old_nut_desc;?>">
		<input type="hidden" name="old_nut_calories" value="<?php echo $old_nut_calories;?>">
		<input type="hidden" name="old_nut_points" value="<?php echo $old_nut_points;?>">
		<input type="hidden" name="old_nut_fat" value="<?php echo $old_nut_fat;?>">
		<input type="hidden" name="old_nut_carbs" value="<?php echo $old_nut_carbs;?>">
		<input type="hidden" name="old_nut_protein" value="<?php echo $old_nut_protein;?>">
		<input type="hidden" name="old_nut_fiber" value="<?php echo $old_nut_fiber;?>">
		<input type="hidden" name="old_nut_size" value="<?php echo $old_nut_size;?>">
		<input type="hidden" name="old_nut_units" value="<?php echo $old_nut_units;?>">
		<input type="hidden" name="owner" value="<?php echo $owner;?>">
		<input type="hidden" name="nut_fav" value="<?php echo $nut_fav;?>">
		<input type="hidden" name="user" value="<?php echo $user;?>">
		<input type="hidden" name="upd_or_add" value="update">
		<input type="hidden" name="update_flag" value="true">
		<!--<button value="test" onClick="this.form.elements['submit_nutrients'].type='hidden';
				this.form.elements['update_nutrients'].type='submit'; return false;">Test</button>-->
	</p>
<?php echo $food_mesg;?>
</div><!-- end of div_nutrients -->