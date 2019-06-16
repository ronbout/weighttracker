// funcs.js
// 5-2-11 rlb
// functions used in the wt.php weight tracker program


function testEmail(str)
{
	// check email for valid format 
	var pattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}(?!\S)/i;
	return pattern.test(str);
}

function user_check(user)
{
	var ch = confirm("You are already logged in as " + user + ".  Continue with registration?");
	if (!ch) 
	{
		window.location.assign("wt_member.php");
	}
}

function validate_register(thisButton)
{
	var thisForm = thisButton.form;
	// should probably make one function to check for empty fields 
	if (thisForm.user.value == '')
	{
		alert("Username is required.");
		thisForm.user.focus();
		return false;
	}
	if (thisForm.emailAddr.value == '')
	{
		alert("Email Address is required.");
		thisForm.emailAddr.focus();
		return false;
	}
	// check email for valid format 
	if (!testEmail(thisForm.emailAddr.value))
	{
		alert("Invalid Email Address.");
		thisForm.emailAddr.focus();
		return false;
	}
	if (thisForm.pass.value.length < 6)
	{
		alert("Password length must be at least 6 characters.");
		thisForm.pass.focus();
		return false;
	}
	if (thisForm.pass.value == '')
	{
		alert("Password is required.");
		thisForm.pass.focus();
		return false;
	}
	if (thisForm.pass.value != thisForm.pass2.value)
	{
		alert("Passwords do not match.");
		thisForm.pass.focus();
		return false;
	}
	return true;
}

function cancel_register(thisButton)
{
	var ch = confirm("Quit and return to Home Page?");
	if (ch) 
	{
		thisButton.form.submit();
	}
	return false;
}
function validate_forgot(thisButton)
{
	var thisForm = thisButton.form;
	// should probably make one function to check for empty fields 
	if (thisForm.username.value == '')
	{
		alert("Username is required.");
		thisForm.username.focus();
		return false;
	}
	if (thisForm.email.value == '')
	{
		alert("Email is required.");
		thisForm.email.focus();
		return false;
	}
	// check email for valid format 
	if (!testEmail(thisForm.email.value))
	{
		alert("Invalid Email Address.");
		thisForm.email.focus();
		return false;
	}
	return true;
}
function cancel_forgot(thisButton)
{
	thisButton.form.submit();
	return false;
}

function validate_login(thisForm)
{
	// should probably make one function to check for empty fields 
	if (thisForm.username_login.value == '')
	{
		alert("Username is required for login.");
		thisForm.username_login.focus();
		return false;
	}
	if (thisForm.pass_login.value == '')
	{
		alert("Password is required for login.");
		thisForm.pass_login.focus();
		return false;
	}
	return true;
}
function validate_change_pass(thisButton)
{
	var thisForm = thisButton.form;
	if (thisForm.oldPass.value == '')
	{
		alert("Old Password is required.");
		thisForm.oldPass.focus();
		return false;
	}
	if (thisForm.newPass.value == '')
	{
		alert("New Password is required.");
		thisForm.newPass.focus();
		return false;
	}
	if (thisForm.newPass.value.length < 6)
	{
		alert("Password length must be at least 6 characters.");
		thisForm.newPass.focus();
		return false;
	}
	if (thisForm.newPass.value != thisForm.newPass2.value)
	{
		alert("New Passwords must match.");
		thisForm.newPass.focus();
		return false;
	}
	return true;
}
function validate_form_wt(thisButton)
{
	var thisForm = thisButton.form;
	// loop through weights and make sure they are positive numeric
	if (!thisForm.elements["wt_weight[]"])
	{
		// no weights to test, return true
		return true;
	}
	if ("length" in thisForm.elements['wt_weight[]'])
	{
		var weights = thisForm.elements['wt_weight[]'];
	}
	else
	{
		// only 1 row of weights, did not get made into array by HTML
		var weights = Array();
		weights[0] = thisForm.elements['wt_weight[]'];
	}
	for(var i=0; i < weights.length; i++)
	{
		if (weights[i].value != "" && (!Number(weights[i].value) || weights[i].value < 0))
		{
			alert("Invalid weight: " + weights[i].value);
			weights[i].focus();
			return false;
		}
	}
	thisForm.elements['submit_flag'].value = "true";
	return true;
}
function unload_member()
{
	// check if forms need to be submitted
	for (var i = 0; i < document.forms.length; i++)
	{
		if (document.forms[i].name == "form_wt")
		{	
			// check whether weights were changed
			var thisForm = document.forms[i];
			// see if submit button was triggered
			if (thisForm.elements['submit_flag'].value == "true") continue;
			// check if weights is array (multiple rows) or single value (1 date)
			if ("length" in thisForm.elements['wt_weight[]'])
			{
			var weights = thisForm.elements['wt_weight[]'];
			var old_wts = thisForm.elements['wt_old_wt[]'];
			var deletes = thisForm.elements['wt_delete[]'];
			}
			else
			{
				// only 1 row of weights, did not get made into array by HTML
				var weights = Array();
				var old_wts = Array();
				var deletes = Array();
				weights[0] = thisForm.elements['wt_weight[]'];
				old_wts[0] = thisForm.elements['wt_old_wt[]'];
				deletes[0] = thisForm.elements['wt_delete[]'];
			}
			var change_flag = false;
			// loop through weights to see if values were changed
			for(var j=0; j < weights.length; j++)
			{
				if (weights[j].value != old_wts[j].value)
				{
					change_flag = true;
				}
				// check whether any deletes were checked
				if (deletes[j].checked) 
					change_flag = true;
			}

			if (change_flag)
			{
				var ch = confirm("Update weight changes?");
				if (ch) 
				{
					thisForm.elements['submit_wt'].click();
				}
			}
		}
		if (document.forms[i].name == "form_goal")
		{	
			// check whether goals were changed
			var thisForm = document.forms[i];
			var change_flag = false;
			// see if submit button was triggered
			if (thisForm.elements['submit_flag'].value == "true") continue;
			if (thisForm.elements['newgoal_weight'].value && thisForm.elements['newgoal_date'].value)
			{
				change_flag = true;
			}
			else
			{
				if ("length" in thisForm.elements['goal_weight[]'])
				{
					var weights = thisForm.elements['goal_weight[]'];
					var old_wts = thisForm.elements['goal_old_wt[]'];
					var dates = thisForm.elements['goal_date[]'];
					var old_dates = thisForm.elements['goal_old_date[]'];
					var deletes = thisForm.elements['goal_delete[]'];
				}
				else
				{
					// only 1 row of weights, did not get made into array by HTML
					var weights = Array();
					var old_wts = Array();
					var dates = Array();
					var old_dates = Array();
					var deletes = Array();
					weights[0] = thisForm.elements['wt_weight[]'];
					old_wts[0] = thisForm.elements['wt_old_wt[]'];
					dates[0] = thisForm.elements['goal_date[]'];
					old_dates[0] = thisForm.elements['goal_old_date[]'];
					deletes[0] = thisForm.elements['wt_delete[]'];
				}
				// loop through goals to see if weights or dates were changed
				for(var j=0; j < weights.length; j++)
				{
					if (weights[j].value != old_wts[j].value || dates[j].value != old_dates[j].value)
					{
						change_flag = true;
					}
					// check whether any deletes were checked
					if (deletes[j].checked) 
						change_flag = true;
				}
			}
			if (change_flag)
			{
				var ch = confirm("Update goal changes?");
				if (ch) 
				{
					thisForm.elements['submit_goal'].click();
				}
			}
		}
		if (document.forms[i].name == "form_food_nutrients")
		{
			
			var thisForm = document.forms[i];
			var change_flag = false;
			// see if submit button was triggered
			if (thisForm.elements['submit_flag'].value == "true") continue;
			// check whether submit came from Reset or food list Refresh
			if (thisForm.elements.update_flag.value == "false") continue;
			// check whether create or modify
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
				if (change_flag)
				{
					var ch = confirm("Update Food Item?");
					if (ch) 
					{
						thisForm.elements['submit_nutrients'].click();
					}
				}
			}
			else
			{
				// see if name and calories were entered
				if (thisForm.elements['nut_name'].value && thisForm.elements['nut_calories'].value && 
					thisForm.elements['nut_calories'].value != "0")
				{
					var ch = confirm("Create Food Item?");
					if (ch) 
					{
						thisForm.elements['submit_nutrients'].click();
					}
				}
			}
		}
		if (document.forms[i].name == "form_food_ingredients")
		{
			
			var thisForm = document.forms[i];
			var change_flag = false;
			// see if submit button was triggered
			if (thisForm.elements['submit_flag'].value == "true") continue;
			// check whether submit came from Reset or food list Refresh
			if (thisForm.elements.update_flag.value == "false") continue;
			// check whether create or modify
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
				if (change_flag)
				{
					var ch = confirm("Update Food Item?");
					if (ch) 
					{
						thisForm.elements['submit_ingredients'].click();
					}
				}	
			}
			else
			{
				// see if name and at least one ingredient were entered
				// if an ingredient has been entered, ingreds[] will be considered an array and have property "length"
				if (thisForm.elements['ingred_food_name'].value && ("length" in thisForm.elements['ingreds[]']))
				{
					var ch = confirm("Create Food Item?");
					if (ch) 
					{
						thisForm.elements['submit_ingredients'].click();
					}
				}
			}
		}
	}
}
function date_enter(thisField)
{
	// clears out "yyyy-mm-dd" default text if present
	thisField.value = "";
}
function date_exit(thisField)
{
	// returns "yyyy-mm-dd" if user left blank
	if (thisField.value == "") thisField.value = "yyyy-mm-dd";
}
function check_date(date_str)
{
	// check that entered date is in format yyyy-mm-dd
	if (date_str.length != 10) return false;
	var pattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
	var reg_check = pattern.test(date_str);
	if (!reg_check) return false;
	// correct format, now check ranges
	var dt = Date.parse(date_str);
	if (isNaN(dt)) return false;
	return true;
}
function check_future_date(date_str, future_flag)
{
	// check date against future_flag (true -future only, false -today and past)
	// must parse
	var today = new Date();	
	var testday = new Date(date_str);
	// have to adjust for 6 hours time difference with gmt
	testday.setHours(testday.getHours() + 6);
	if (future_flag)
	{
		if (today.getTime() >= testday.getTime() || today.toString() == testday.toString()) return false;		
	}
	else
	{
		if (today.getTime() < testday.getTime() && today.toString() != testday.toString()) return false;
	}
	return true;
}
function validate_form_goal(thisButton)
{
	var thisForm = thisButton.form;
	// check add new goal first
	var new_date = thisForm.elements.newgoal_date;
	var new_goal = thisForm.elements.newgoal_weight;
	// check goal for valid entry
	if (new_goal.value != "" && (!Number(new_goal.value) || new_goal.value < 0))
	{
		alert("Invalid weight: " + new_goal.value);
		new_goal.focus();
		return false;
	}
	if ((new_date.value == "yyyy-mm-dd" && new_goal.value))
	{
		alert("Must enter both date and weight to create new goal.");
		new_date.focus();
		return false;
	}
	if (new_date.value && new_date.value != "yyyy-mm-dd" && !check_date(new_date.value))
	{
		alert("Invalid date format.  Use 'yyyy-mm-dd'.");
		new_date.focus();
		return false;
	}
	// check that only future date is entered
	if (new_date.value && new_date.value != "yyyy-mm-dd" && !check_future_date(new_date.value,true))
	{
		alert("Can only create goals for future dates.");
		new_date.focus();
		return false;
	}	
	// check whether the goals were active
	if (thisForm.elements.goalType.value != "active")
	{
		// no more testing, passed
		thisForm.elements['submit_flag'].value = "true";
		return true;
	}
	// loop through goals and make sure they are positive numeric and dates are correct
	if (!thisForm.elements["goal_weight[]"])
	{
		// no goals to test, return true
		thisForm.elements['submit_flag'].value = "true";
		return true;
	}
	if ("length" in thisForm.elements['goal_weight[]'])
	{
		var weights = thisForm.elements['goal_weight[]'];
		var dates = thisForm.elements['goal_date[]'];
	}
	else
	{
		// only 1 row of goals, did not get made into array by HTML
		var weights = Array();
		var dates = Array();
		weights[0] = thisForm.elements['goal_weight[]'];
		dates[0] = thisForm.elements['goal_date[]'];
	}
	for(var i=0; i < weights.length; i++)
	{
	if ((dates[i].value && !weights[i].value) || (!dates[i].value && weights[i].value))
		{
			alert("Must enter both date and weight to change goal.");
			if (!dates[i].value)
				dates[i].focus();
			else
				weights[i].focus();
			return false;
		}
		if (weights[i].value != "" && (!Number(weights[i].value) || weights[i].value < 0))
		{
			alert("Invalid weight: " + weights[i].value);
			weights[i].focus();
			return false;
		}
		if (!check_date(dates[i].value))
		{
			alert("Invalid date format.  Use 'yyyy-mm-dd'.");
			dates[i].focus();
			return false;			
		}
		if (!check_future_date(dates[i].value, true))
		{
			alert("Can only change goals to future dates.");
			dates[i].focus();
			return false;
		}
	}
	thisForm.elements['submit_flag'].value = "true";
	return true;
}
function graph_submit(thisButton)
{
	// need to set action attribute of form
	var thisForm = thisButton.form;
	if (thisForm.elements['type_graph'])
	{
		thisForm.action = "wt_member.php?mode=graph&type=" + thisForm.elements['type_graph'];
	}
	return true;
}

