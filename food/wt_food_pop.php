<?php
// wt_food_pop.php
// 7-10-11 rlb
// pop up window to find out if user wants to create a new food
// or change the name of the original food that is owner of
// returns choice to parent window

if (!isset($_GET['oldname']) || !isset($_GET['newname']) || !isset($_GET['formname']))
{
	die("<h2>Invalid Page</h2>");
}
else
{
	$onload = "";
	extract($_GET);
}
?>
<html>
<head>
	<script langauge="javascript">
		function send_choice(choice, formtype)
		{
			var formname = "form_food_" + formtype;
			if (choice == "upd" || choice == "add")
			{
				window.opener.document.forms[formname].upd_or_add.value = choice;
				window.opener.document.forms[formname].elements['submit_'+formtype].click();
			}
			self.close();
		}
	</script>
	<title>Save or Update Food</title>
	<style type="text/css">
		body {
			background-color:#66E275;
			color:#000;
			font-family: Arial, sans-serif;	
			font-size:.9em;
		}
		button {
			margin-left:20px;
		}
	</style>
</head>
<body <?php echo $onload;?>> 
	<p>You have changed the name of the food from:<br><?php echo $oldname;?> <br>to:<br><?php echo $newname;?></p>
	<p>You can either -Update the Original Food- or -Add a New Food-. Please choose below.</p>
	<p>
		<button type="button" onClick='send_choice("upd","<?php echo $formname;?>");'>Update Original</button>
		<button type="button" onClick='send_choice("add","<?php echo $formname;?>");'>Add New Food</button>
		<button type="button" onClick='send_choice("cancel","");'>Cancel</button>
	</p>
</body>
</html>