<?php
// m_food_drop.php
// 2-5-11 rlb
// form with food item list drop down and select button


?>
<form name="form_drop" action="m_food_lookup.php" method="post">
<select name="select_food" size="10">
<?php
$food_list = array();
$first_flag = true;
$i=0;
while($food_item = $result->fetch_assoc())
{
	extract($food_item);
	if ($first_flag)
	{
		$first_flag = false;
		echo "<option value='$i' ondblclick='this.form.elements[\"submit_select\"].click();' selected='selected'>$name</option>";
	}
	else
		echo "<option value='$i' ondblclick='this.form.elements[\"submit_select\"].click();'>$name</option>";
	$food_list[$i++] = $food_item;
}
$_SESSION['food_list'] = serialize($food_list);
?>
</select>
<input type="submit" name="submit_select" value="Select Food">
<input type="hidden" name="search_words" value="<?php echo $search_words;?>">
</form>