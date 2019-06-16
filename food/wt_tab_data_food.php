<?php
// wt_tab_data_food.php
// 8-3-11 rlb
// content of the tabbed data section of the food setup mode
// $type determines which tab is selected and what will be 
// displayed
// called from wt_main_food.php

switch($type)
{
	case "ingredients":
		require("food/wt_main_food_ingredients.php");
		break;
	case "nutrients":
	default:
		require("food/wt_main_food_nutrients.php");
}

?>