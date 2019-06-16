<?php
// m_display_food.php
// 2-5-11 rlb
// displays food item nutrients in html

?>	
<table>
	<tr>
		<td>Name: </td><td colspan="2"><?php echo $description;?></td>
	</tr>
	<tr>
		<td>Owner: </td><td colspan="2"><?php echo $user_name;?></td>
	</tr>
	<tr>
		<td>Calories: </td><td><?php echo $calories;?></td><td><?php echo $new_cals;?></td>
	</tr>
	<tr>
		<td>Fat gms: </td><td><?php echo $fat_grams;?></td><td><?php echo $new_fat;?></td>
	</tr>
	<tr>
		<td>Carb gms: </td><td><?php echo $carb_grams;?></td><td><?php echo $new_carb;?></td>
	</tr>
	<tr>
		<td>Protein gms: </td><td><?php echo $protein_grams;?></td><td><?php echo $new_pro;?></td>
	</tr>
	<tr>
		<td>Fiber gms: </td><td><?php echo $fiber_grams;?></td><td><?php echo $new_fib;?></td>
	</tr>
	<tr>
		<td>Serving Size: </td><td><?php echo $serving_size," ",$serving_units;?></td>
		<td><?php echo $new_size," ",$new_units;?></td>
	</tr>
</table>
<form name="new_size" action="m_food_lookup.php" method="post">
	<p>Enter new Serving Size:
		<input type="text" name="new_size" size="5" maxlength="4" value="">
	   	<input type="hidden" name="select_food" value="<?php echo $i;?>">
		<input type="hidden" name="search_words" value="<?php echo $search_words;?>">
		<select name="new_units">
			<option value="gms" selected="selected">gms</option>
			<option value="oz">oz</option>
		</select>
		<input type="submit" name="submit_serving" value="Calculate" onClick="return val_serving(this);">
	</p>
</form>
<?php
if ($list_flag)
{
	// need to put next and prev buttons
	echo '
		<form name="item_traverse" action="m_food_lookup.php" method="post">
			<input type="hidden" name="select_food" value="',$i,'">
			<input type="hidden" name="search_words" value="',$search_words,'">
			<input type="submit" name="submit_prev" value="<-"';
	if ($i > 0)
		echo '>';
	else
		echo 'disabled="disabled">';
	echo '
		<input type="submit" name="submit_next" value="->"';
	if ($i < sizeof($food_list)-1)
		echo '>';
	else
		echo 'disabled="disabled">';
	echo '	</form>';
}
?>