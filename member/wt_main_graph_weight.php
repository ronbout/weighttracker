<?php
// wt_main_graph_weight.php
// 5-30-11 rlb
// option form for weight graph
// optons include show original goal path checkbox
// and radio button for other $types

// set whether orig_goal_graph should be checked
if (isset($origGoalFlag)) 
	$orig_check = 'checked="checked"';
else
	$orig_check = '';

?>
	<div id="div_form_graph">
		<form name="form_graph" method="post" action="<?php echo $selfLink; ?>">
			<fieldset>
			<legend>Graph Options</legend>
				<p class="graph_option"><input type="checkbox" name="orig_goal_graph" 
								<?php echo $orig_check;?> value="orig"> &nbsp;See Original Goal Path</p>
<?php
// turn on when food and/or exercise types are available
if (false) 
{ ?>
				<p class="graph_option"><input type="radio" name="type_graph" value="weight"
					<?php if ($type == "weight") echo 'checked="checked"';?>></p>
				<p class="graph_option"><input type="radio" name="type_graph" value="food"
					<?php if ($type == "food") echo 'checked="checked"';?>></p>
				<p class="graph_option"><input type="radio" name="type_graph" value="exercise"
					<?php if ($type == "exercise") echo 'checked="checked"';?>></p>
<?php } ?>
				<p class="graph_refresh"><input type="submit" name="submit_graph" value="Refresh"
							onclick="return graph_submit(this)"></p>
			</fieldset>
		</form>
	</div>


