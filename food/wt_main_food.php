<?php
// wt_main_food.php
// 7-31-11 rlb
// main content code for member page in food setup mode
// 2 tabs determine type- foods made by nutrients (food elements)
// and foods made by ingredients (compound foods)
	
if (!checkJavascript($_GET)) exit();
// get name for current script for self-referencing
$pg = $_SERVER['PHP_SELF'];
$pg = pathinfo($pg);
$pg = $pg['basename'];
$selfLink =  $pg."?".$_SERVER['QUERY_STRING'];
echo '<form id="form_food_setup" name="form_food_',$type,'" action="',$selfLink,'" method="post">';
?>
<div id="div_food_main">
<h1>Food Setup</h1>
<?php 
	echo "<ul id='tabmenu'>";
	if ($type == "nutrients")
		echo "<li><a class='selected' href='wt_member.php?mode=food&type=nutrients' 
				onClick='window.location.href=\"wt_member.php?mode=food&type=nutrients&js=yes\"; return false;'>Create by Nutrients</a></li>";
	else
		echo "<li><a href='wt_member.php?mode=food&type=nutrients' 
				onClick='window.location.href=\"wt_member.php?mode=food&type=nutrients&js=yes\"; return false;'>Create by Nutrients</a></li>";
	if ($type == "ingredients")
		echo "<li><a class='selected' href='wt_member.php?mode=food&type=ingredients' 
				onClick='window.location.href=\"wt_member.php?mode=food&type=ingredients&js=yes\"; return false;'>Create by Ingredients</a></li>";
	else
		echo "<li><a href='wt_member.php?mode=food&type=ingredients' 
				onClick='window.location.href=\"wt_member.php?mode=food&type=ingredients&js=yes\"; return false;'>Create by Ingredients</a></li>";

	echo '</ul>';
	echo '		<div id="tab_food_content">';
	require("food/wt_tab_data_food.php");
	echo '		</div><!-- end of tab_content -->';
	echo '	</div><!--  end of div_food_main -->';
	echo ' <div id="div_food_side">';
	require("wt_food_list.php");
	echo ' </div><!-- end of div_food_side -->';
	echo '</form>';
?>
