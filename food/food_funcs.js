// food_funcs.js
// 2-3-11 rlb
// functions used in the food module of wt.php weight tracker program

function validate_food_nutrients(thisButton)
{
	var thisForm = thisButton.form;
	if (thisForm.nut_name.value == '')
	{
		alert("Food Name is a required field.");
		thisForm.nut_name.focus();
		return false;
	}
	if (thisForm.user.value != thisForm.owner.value && thisForm.old_nut_name.value == thisForm.nut_name.value)
	{
		alert("Name must be changed to create a new food.");
		thisForm.nut_name.focus();
		return false;
	}
	if ((thisForm.user.value == thisForm.owner.value) && (thisForm.old_nut_name.value != thisForm.nut_name.value) &&
		(thisForm.upd_or_add.value == "update") && thisForm.nut_id.value)
	{
		var oldname = thisForm.old_nut_name.value;
		var newname = thisForm.nut_name.value;
		// does user want to create new food with new name, or change name of original food?
		window.open("food/wt_food_pop.php?oldname="+oldname+"&newname="+newname+"&formname=nutrients","food_pop_up",
					"width=500,height=200,left=150,top=200,toolbar=0,status=0,");
		return false;
	}
	if (thisForm.nut_calories.value == '')
	{
		alert("Calories is a required field.");
		thisForm.nut_calories.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_calories.value)) || thisForm.nut_calories.value < 0)
	{
		alert("Invalid Calories: " + thisForm.nut_calories.value);
		thisForm.nut_calories.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_points.value)) || thisForm.nut_points.value < 0)
	{
		alert("Invalid Points: " + thisForm.nut_points.value);
		thisForm.nut_points.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_fat.value)) || thisForm.nut_fat.value < 0)
	{
		alert("Invalid Fat gms: " + thisForm.nut_fat.value);
		thisForm.nut_fat.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_carbs.value)) || thisForm.nut_carbs.value < 0)
	{
		alert("Invalid Carb gms: " + thisForm.nut_carbs.value);
		thisForm.nut_carbs.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_protein.value)) || thisForm.nut_protein.value < 0)
	{
		alert("Invalid Protein gms: " + thisForm.nut_protein.value);
		thisForm.nut_protein.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_fiber.value)) || thisForm.nut_fiber.value < 0)
	{
		alert("Invalid Fiber gms: " + thisForm.nut_fiber.value);
		thisForm.nut_fiber.focus();
		return false;
	}
	if (isNaN(Number(thisForm.nut_size.value)) || thisForm.nut_size.value < 0)
	{
		alert("Invalid Serving Size: " + thisForm.nut_size.value);
		thisForm.nut_size.focus();
		return false
	}
	thisForm.elements['submit_flag'].value = "true";
	return true;
}
function val_select_list(thisButton, pg)
{
	thisForm = thisButton.form;
	// find out which option was selected
	var s_list = thisForm.elements['select_food[]'];
	for (var i = 0; i < s_list.length; i++)
	{
		if (s_list.options[i].selected) break;
	}
	var ingred_flag = s_list.options[i].value.split("*")[1];
	if (ingred_flag == "1" && thisForm.elements['pg_mode'].value == "food" && thisForm.elements['pg_type'].value == "nutrients")
	{
		// must change action
		thisForm.action = pg+"?mode=food&type=ingredients&js=yes";
	}
	if (ingred_flag == "0" && thisForm.elements['pg_mode'].value == "food" && thisForm.elements['pg_type'].value == "ingredients")
	{
		// must change action
		thisForm.action = pg+"?mode=food&type=nutrients&js=yes";
	}
	return true;
}
function recalc_size(thisButton)
{
	// will recalc the nutrients for new serving size and display
	thisForm = thisButton.form;
	var unit_types = {"gms":1, "oz":1, "cups":2, "tbls":2, "tsp":2, "fl oz":2}; // 1=weight 2=fluid
	var multipliers ={"gms-oz":28.35,
					  "oz-gms":0.035,
					  "cups-tbls":0.0625,
					  "cups-tsp":0.0208,
					  "cups-fl oz":0.125,
					  "tbls-cups":16,
					  "tbls-tsp":0.333,
					  "tbls-fl oz":2,
					  "tsp-cups":48,
					  "tsp-tbls":3,
					  "tsp-fl oz":6,
					  "fl oz-cups":8,
					  "fl oz-tbls":0.5,
					  "fl oz-tsp":0.1667};
	var form_fields = ['nut_calories','nut_points','nut_fat','nut_carbs','nut_protein','nut_fiber'];
	var old_size 	= thisForm.old_nut_size.value;
	var old_units	= thisForm.old_nut_units.value;
	var serv_size	= thisForm.nut_size.value;
	var units		= thisForm.nut_units.value
	// check that all fields have values
	if (!old_size || !old_units || !serv_size || !units) return;
	// check for numeric size
	if (isNaN(Number(serv_size)) || serv_size < 0)
	{
		alert("Invalid Serving Size: " + serv_size);
		thisForm.nut_size.focus();
		return false;
	}
	// check that unit types match
	if (unit_types[units] != unit_types[old_units])
	{
		alert("Cannot convert " + old_units + " to " + units);
		thisForm.nut_units.focus();
		return false;
	}
	var multiplier;
	if (old_units == units)
		multiplier = 1;
	else
		multiplier = multipliers[old_units + "-" + units];
	var field;
	for (var i = 0; i < form_fields.length; i++)
	{
		field = form_fields[i];
		thisForm.elements[field].value = 
				(thisForm.elements['old_' + field].value * ((serv_size / old_size) * multiplier)).toFixed(1);
	}
	return false; 
}
function validate_food_ingredients(thisButton)
{
	var thisForm = thisButton.form;
	if (thisForm.ingred_food_name.value == '')
	{
		alert("Food Name is a required field.");
		thisForm.ingred_food_name.focus();
		return false;
	}
	if (thisForm.user.value != thisForm.owner.value && thisForm.old_ingred_food_name.value == thisForm.ingred_food_name.value)
	{
		alert("Name must be changed to create a new food.");
		thisForm.ingred_food_name.focus();
		return false;
	}
	if (thisForm.user.value == thisForm.owner.value && thisForm.old_ingred_food_name.value != thisForm.ingred_food_name.value &&
		thisForm.upd_or_add.value == "update" && thisForm.ingred_food_id.value)
	{
		var oldname = thisForm.old_ingred_food_name.value;
		var newname = thisForm.ingred_food_name.value;
		// does user want to create new food with new name, or change name of original food?
		window.open("food/wt_food_pop.php?oldname="+oldname+"&newname="+newname+"&formname=ingredients","food_pop_up",
					"width=500,height=200,left=150,top=200,toolbar=0,status=0,");
		return false;
	}
	if (isNaN(Number(thisForm.ingred_size.value)) || thisForm.ingred_size.value < 0)
	{
		alert("Invalid Serving Size: " + thisForm.ingred_size.value);
		thisForm.ingred_size.focus();
		return false
	}
	thisForm.elements['submit_flag'].value = "true";
	return true;
}
function field_ingred_val(thisField, key)
{
	// the user has changed the value in the list of ingredients of a food item
	// check if valid food using http header (Ajax) and process accordingly
	var thisForm = thisField.form;
	var food_id = thisForm.ingred_food_id.value;
	var food_name = thisForm.ingred_food_name.value;
	if (!food_id) food_id = "-1"; // dummy value to keep later code form bombing
	if (thisField.value)
	{
		// get food info or error if unknown
		ingred = thisField.value;
		var err_msg = "";
		// set up Ajax request info
		var url = 'ajax_check_food.php?food=' + food_id + '&ingred="' + ingred + '"';
		// most of the work is done in the following callback function
		function responseFn(responseText){
			switch(responseText)
			{
				case "-1":
					// food unknown -- alert user
					err_msg = ingred + " not found!";
				case "-2":
					if (!err_msg) err_msg = ingred + ": Unable to determine unique name.  Please select from list.";
				case "-3":
					if (!err_msg) err_msg = food_name + "is already contained within " + ingred + ".";
				case "-4":
					if (!err_msg) err_msg = ingred + " cannot be ingredient of itself.";
				case "-99":
					if (!err_msg) err_msg = "Unknown error.  Administrator has been notified.";
					alert(err_msg);
					// if new, clear field 
					thisField.value = "";
					// if changing, restore orig value
					thisField.value = thisField.defaultValue;
					thisField.focus();
					return;
				default:
					// food is found
					// parse responseText
					var ret_info = responseText.split("~");
					var ret_id = ret_info[0];
					var ret_calories = ret_info[1];
					if (key)
					{
						if (thisForm['servings[]'][key].disabled)
						{
							thisForm['servings[]'][key].disabled=false;
							thisForm['servings[]'][key].value = 1;
						}
						thisForm['ingred_ids[]'][key].value = ret_id;
					}
					else
					{
						if (thisForm['servings[]'].disabled)
						{
							thisForm['servings[]'].disabled=false;
							thisForm['servings[]'].value = 1;
						}
						thisForm['ingred_ids[]'].value = ret_id;
					}
					// set update flag to false and submit form
					thisForm.update_flag.value = "false";
					thisForm.submit();
			}
		}
		runAjax(url, responseFn);
	}
	else
	{
		// deleted food item, need to remove from list and remove from nutrients
		thisForm.update_flag.value = "false";
		thisForm.submit();
	}
	return;
}
function field_servings_val(thisField, key)
{
	// need to update nutrients when servings fields has been changed
	// just click submit button with submit flag turned off, php will redraw
	var thisForm = thisField.form;
	var servings = Number(thisField.value);
	if (isNaN(servings))
	{
		alert("Servings field must be numeric.");
		thisField.value = thisField.defaultValue;
		return;
	}
	if (servings < 0)
	{
		alert("Negative number is invalid");
		thisField.value = thisField.defaultValue;
		return;
	}
	if (servings == 0)
	{
		thisForm.elements['ingreds[]'][key].value = "";
	}
	thisForm.update_flag.value = "false";
	thisForm.submit();
	return;
}
function nut_submit_status(thisField)
{
	var thisForm = thisField.form;
	var change_flag = false;
	if (thisForm.elements['nut_id'].value)
	{
		// see if anything changed
		var fields = ['nut_name','nut_desc','nut_calories','nut_points','nut_fat','nut_carbs','nut_protein',
						'nut_fiber','nut_size','nut_units'];
		for (var j = 0; j < fields.length; j++)
		{
			if (thisForm.elements[fields[j]].value != thisForm.elements['old_' + fields[j]].value)
			{
				change_flag = true;
			}
		}
	}
	else
	{
		// see if name and calories were entered
		if (thisForm.elements['nut_name'].value && thisForm.elements['nut_calories'].value && 
				thisForm.elements['nut_calories'].value != "0")
		{
			change_flag = true;
		}
	}
	thisForm.elements.submit_nutrients.disabled = !change_flag;
}
function ingred_submit_status(thisField)
{
	var thisForm = thisField.form;
	var change_flag = false;
	if (thisForm.elements['ingred_food_id'].value)
	{
		// see if anything changed
		var fields = ['ingred_food_name','ingred_food_desc','ingred_size','ingred_units'];
		for (var j = 0; j < fields.length; j++)
		{
			if (thisForm.elements[fields[j]].value != thisForm.elements['old_' + fields[j]].value)
			{
				change_flag = true;
			}
		}
		// check ingredient and servings arrays
		if (!("length" in thisForm.elements['ingreds[]']) || !("length" in thisForm.elements['old_ingreds[]']))
		{
			// if there is only the blank lines, cannot update
			change_flag = false;
		}
		else
		{
			if (Number(thisForm.elements['ingreds[]'].length) != (Number(thisForm.elements['old_ingreds[]'].length) + 1))
			{
				// ingreds[] has one more element due to blank line
				change_flag = true;
			}
			else
			{
				for (var i = 0; i < thisForm.elements['ingreds[]'].length; i++)
				{
					if (thisForm.elements['ingreds[]'][i].value && (thisForm.elements['ingreds[]'][i].value != 
						thisForm.elements['old_ingreds[]'][i].value || thisForm.elements['servings[]'][i].value != 
						thisForm.elements['old_servings[]'][i].value))
					{
						change_flag = true;
						continue;
					}
				}
			}
		}
	}
	else
	{
		// see if name and at least one ingredient were entered
		// if an ingredient has been entered, ingreds[] will be considered an array and have property "length"
		if (thisForm.elements['ingred_food_name'].value || ("length" in thisForm.elements['ingreds[]']))
		{
			change_flag = true;
		}
	}
	thisForm.elements.submit_ingredients.disabled = !change_flag;
}
function val_add_ingred(thisButton)
{
	// make sure that the ingredient being added does not create a circular relationship
	var thisForm = thisButton.form;
	if (thisForm.update_flag.value == "false") return true;
	// find out which option was selected
	var s_list = thisForm.elements['select_food[]'];
	for (var i = 0; i < s_list.length; i++)
	{
		if (s_list.options[i].selected) break;
	}
	var ingred_id = s_list.options[i].value.split("*")[0];
	var food_id = thisForm.ingred_food_id.value;
	var food_name = thisForm.ingred_food_name.value;
	var ingred = s_list.options[i].innerHTML;
	if (ingred_id == food_id)
	{
		//alert(ingred + " cannot be ingredient of itself.");
		//return false;
	}
	if (!food_id) food_id = "-1"; // dummy value to keep later code form bombing
	// get food info or error if unknown
	var err_msg = "";
	// set up Ajax request info
	var url = 'ajax_check_food.php?food=' + food_id + '&ingred="' + ingred + '"&ingred_id=' + ingred_id + '';
	// most of the work is done in the following callback function
	function responseFn(responseText){
		switch(responseText)
		{
			case "-3":
				if (!err_msg) err_msg = food_name + "is already contained within " + ingred + ".";
			case "-4":
				if (!err_msg) err_msg = ingred + " cannot be ingredient of itself.";
			case "-99":
				if (!err_msg) err_msg = "Unknown error.  Administrator has been notified.";
				alert(err_msg);
				return false;
			default:
				thisForm.update_flag.value = "false";
				thisButton.click();
		}
	}
	runAjax(url, responseFn);
	return false;
}