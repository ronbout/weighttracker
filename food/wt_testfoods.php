<?php 

include ("../wt_include.php");
// connect to database
require("../wt_connect.php");
// ***TESTING ONLY -- TO RESET 
//session_destroy(); exit();
$ingred_calories = 0;
$ingred_food_name = "";
$ingred_id = "";
$old_ingred_id = "";
$old_ingred_food_name="";
$old_ingred_calories = 0;
$ingreds_flag = true;
$mode = "food";
$type = "ingredients";
$old_ingreds = array();
$old_servings = array();
$old_ingred_ids = array();
if (isset($_POST)) extract($_POST);
if (!isset($ingreds)) 
	$ingreds = (isset($_SESSION['ingreds'])) ? unserialize($_SESSION['ingreds']) : array();
if (!isset($servings))
	$servings = (isset($_SESSION['servings'])) ? unserialize($_SESSION['servings']) : array();
if (!isset($ingred_ids)) 
	$ingred_ids = (isset($_SESSION['ingred_ids'])) ? unserialize($_SESSION['ingred_ids']) : array();
// need to clean up arrays, removing any blank ingreds
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
if (isset($_POST['search_food_but_x']) || isset($_POST['select_food_refresh']))
{
	// don't do anything, just rerun with new variables
}
if (isset($_POST['submit_select_food']))
{
	// load food into form
	// load old_* variables
}
if (isset($_POST['submit_add_ingred']))
{
	// add new ingredients to ingreds and set servings to 1
	foreach ($select_food as $new_ingred)
	{
		$ingred_array = explode("*",$new_ingred);
		$new_ingred_id = $ingred_array[0];
		$new_ingred_name = $ingred_array[2];
		array_push($ingreds, $new_ingred_name);
		array_push($servings, 1);
		array_push($ingred_ids, $new_ingred_id);
	}
}
if (isset($_POST['submit_ingredients']))
{
	// if update flag, then update food items
	// if not update flag then just redraw.  triggered from javascript
	if ($submit_ingred_flag == "true")
	{
		// process update
	}
	else
	{
	
	}
}
if (isset($_POST['reset_ingredients']))
{
	$ingreds = $old_ingreds;
	$ingred_ids = $old_ingred_ids;
	$servings = $old_servings;
}
// $user is set to 11 (Me) only for this testing page
$user = 11;
$_SESSION['user'] = $user;
$owner = 11;
// calc nutrients
$errCode = "";
$last_key = -1;
foreach($ingred_ids as $key=>$ingred_id)
{
	$food = loadFood($mysqli, $ingred_id, $errCode);
	if ($errCode) continue; // just for testing. will include error checking in main program
	$nutrients = $food->getNutrition($mysqli, $errCode);
	if ($errCode) continue;
	$ingred_calories += $servings[$key] * $nutrients['calories'];
	$last_key = $key;
}
if ($last_key == -1)
	$on_load = 'onLoad="document.test_form.elements[\'ingreds[]\'].focus();"';
else
	$on_load = 'onLoad="document.test_form.elements[\'ingreds[]\']['.($last_key+1).'].focus();"';

$user = 11;
$_SESSION['ingreds'] = serialize($ingreds);
$_SESSION['servings'] = serialize($servings);
$_SESSION['ingred_ids'] = serialize($ingred_ids);
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>WT Food Test Page</title>
	<script type="text/javascript" src="food_funcs.js"></script>
	<script type="text/javascript" src="../ajax/ajax.js"></script>
	<link rel="stylesheet" href="../stylesheets/wt_food.css" type="text/css">
	<style>
		#ingreds_label {
			width:225px;
			text-align:left;
		}
		#div_ingred_inputs {
			width:294px;
			height:300px;
			overflow:auto;
		}
	</style>
</head>
<body <?php echo $on_load;?>>
<div id="div_food_main">
	<form name="test_form" action="wt_testfoods.php" method="post">
	<table>
		<tr>
			<td class="ingreds_label">Food Name: * </td>
			<td><input type="text" name="ingred_food_name" size="30" maxlength="60" value="<?php echo $ingred_food_name;?>"
			<?php //if ($user != $owner) echo 'onChange="this.form.submit_nutrients.disabled=false;"';?>
			> &nbsp; &nbsp; ( * - required field )</td>
		</tr>
		<tr>
			<td colspan="2">
			Calories: <input type="text" name="cals" disabled="disabled" size="5" value="<?php echo $ingred_calories;?>">
			</td>
		</tr>
		<tr>
			<td id="ingreds_label">Ingredients</td><td>Servings</td>
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
			echo '<input type="text" name="ingreds[]" size="30" value="',$ingred,'"
				onChange="field_ingred_val(this,',$key,');">',"\n";
			$serv_val = (isset($servings[$key])) ? $servings[$key] : "";
			echo '<input type="text" name="servings[]" size="5" value="',$serv_val,'"
				onChange="field_servings_val(this,',$key,');">';
			echo '<input type="hidden" name="ingred_ids[]" value="',$ingred_ids[$key],'">';
			$lastkey = $key;
		}
	}
?>
					<input type="text" name="ingreds[]" size="30" value="" 
						onChange="field_ingred_val(this,<?php echo $lastkey+1;?>);">
					<input type="text" name="servings[]" size="5" value="" disabled="disabled"
						onChange="field_servings_val(this,<?php echo $lastkey+1;?>);">
					<input type="hidden" name="ingred_ids[]" value="">
				</div>
			</td>
		</tr>
	</table>
	<p id="p_ingred_buttons"><input class="ingred_buttons" type="submit" name="submit_ingredients" value="Update" 
			onClick="return validate_food_ingredients(this);">
	<?php if ($owner != $user) echo 'disabled="disabled"';?>
	<input class="ingred_buttons" type="submit" name="reset_ingredients" value="Reset">
	<input type="hidden" name="submit_ingred_flag" value="true">
	<input type="hidden" name="ingred_id" value="<?php echo $ingred_id;?>">
	<input type="hidden" name="old_ingred_id" value="<?php echo $old_ingred_id;?>">
	<input type="hidden" name="old_ingred_food_name" value="<?php echo $old_ingred_food_name;?>">
<?php
	foreach($old_ingreds as $key=>$old_ingred)
	{
		if ($old_ingred)
		{
?>			<input type="hidden" name="old_ingreds[]" value="<?php echo $old_ingred;?>">
			<input type="hidden" name="old_ingred_ids[]" value="<?php echo $old_ingred_ids[$key];?>">
			<input type="hidden" name="old_servings[]" value="<?php echo $old_servings[$key];?>">
<?php
		}
	}
?>
	</form>
</div>
<div id="div_food_side">
<?php require("wt_food_list.php");?>
</div><!-- end of div_main_side -->
</body>
</html>