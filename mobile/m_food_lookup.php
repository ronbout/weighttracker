<?php
// m_food_lookup.php
// 2-4-11 rlb
// mobile food lookup page
// allow user to enter keyword search, either their favs or all
// will return list of matches, which user can select to view
// page forward and backword through list
require_once("../wt_include.php");
// login to database
require("../wt_connect.php");
$display_flag = false;
$select_flag = false;
$mesg = "";
$search_words = "";
$new_cals = ""; 
$new_fat = "";
$new_carb = "";
$new_pro = "";
$new_fib = "";
$new_size = "";
$new_units = "";
if (isset($_POST['search_but_x']) || isset($_POST['submit_select']) || isset($_POST['submit_next'])
	|| isset($_POST['submit_prev']) || isset($_POST['submit_serving']))
{
	require("m_food_process.php");
}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
		<title>Weight Tracker Food Lookup</title>
		<link rel="stylesheet" type="text/css" href="m_food_lookup.css" media="only screen and (max-width:480px)">
		<script type="text/javascript">
			function val_serving(thisButton)
			{

				var thisForm = thisButton.form;
				if (thisForm.new_size.value == "")
				{
					alert("Enter value to calculate new serving size.");
					thisForm.new_size.focus();
					return false;
				}
				if (isNaN(Number(thisForm.new_size.value)))
				{
					alert("Must enter numeric value in serving size.");
					thisForm.new_size.focus();
					return false
				}
				return true;
			}
		</script>
	</head>
	<body>
	<div id="page"> <!--  start of the page wrapper -->
		<div id="header">  <!--  start of header  -->
			<!--<img id="logo" src="../images/wt_logo2.png">-->
			<h1>Food Tracker</h1>
		</div>  <!-- end of header -->
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="main">  <!-- start of main  -->
				<?php 
					require("m_food_form.php"); 
					if ($select_flag) require("m_food_drop.php");
					if ($display_flag) require("m_display_food.php");
					if ($mesg) echo $mesg;
				?>
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<div id="footer">  <!-- start of footer  -->
		</div>   <!-- end of footer -->
	</div>
	</body>
</html>