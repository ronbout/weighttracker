<?php
// wt_cal_weight_form.php
// 5-23-11 rlb
// creates the member weight entry/update form
// for the calendar screen from wt_tab_data_cal.php
// will include today and previous 6 days 
// self-referencing, POSTed form

// get name for current script for self-referencing
$pg = $_SERVER['PHP_SELF'];
$pg = pathinfo($pg);
$pg = $pg['basename'];
$selfLink =  $pg."?".$_SERVER['QUERY_STRING'];
// build weight array -- vars were set up in build_cal, but 
// will redefine them in case this routine is reused elsewhere
if (isset($_GET['date']))
	$dt = strtotime($_GET['date']);
else
	$dt = strtotime("now");
$dispDate = date("M jS, Y", $dt);
$formWts = array();
$todayDt = strtotime("now");
for ($i = 0; $i < 7; $i++)
{
	$loopDt = strtotime("-".$i." days",$dt);
	if ($wt = $member->getWeight($loopDt))
		$formWts[] = array("date"=>date("Y-m-d",$loopDt),"weight"=>$wt);
	else
		$formWts[] = array("date"=>date("Y-m-d",$loopDt),"weight"=>"");
}
$numDays = sizeof($formWts);
if ($numDays)
{
	?>
	<div id="div_form_wt">
		<h3>Week Ending: <?php echo $dispDate;?></h3>
		<form name="form_wt" method="post" action="<?php echo $selfLink;?>">
			<table cellspacing="4px">
				<tr>
					<th class="wt_table_th">Date</th>
					<th>&nbsp;</th>
					<th class="wt_table_th">Weight</th>
					<th class="wt_table_th">&nbsp;&nbsp;Del&nbsp;&nbsp;</th>
				</tr>
	<?php

	for ($i = 0; $i < $numDays; $i++)
	{
	?>
	<tr>
		<td class="wt_table_dat">
			<input class="inp_wt" type="text" name="wt_date[]" size="10" maxlength="10" value="<?php echo $formWts[$i]["date"]; ?>"
				disabled="disabled" >
		</td>	
		<td>&nbsp;</td>
		<td class="wt_table_dat">
			<input class="inp_wt" type="text" name="wt_weight[]" size="5" maxlength="5" 
			tabindex=<?php echo $i+1;?> value="<?php 
					echo ($formWts[$i]["weight"]) ? number_format($formWts[$i]["weight"],1): "",'"'; 
					// check for future dates, which cannot accept weight data
					if (date("Y-m-d",$todayDt) != date("Y-m-d",strtotime($formWts[$i]["date"])) && 
						$todayDt < strtotime($formWts[$i]["date"]))
						echo ' readonly="readonly"';?>>
		</td>
		<td class="wt_table_check">
			<input type="checkbox" name="wt_delete[]" value="<?php echo $formWts[$i]["date"],'"';
				// check for future dates, which cannot accept weight data
				if (!$formWts[$i]["weight"])
					echo ' disabled="disabled"';?>>
		</td>
		<input type="hidden" name="wt_old_wt[]" 
			value = "<?php echo ($formWts[$i]["weight"]) ? number_format($formWts[$i]["weight"],1): ""; ?>">
		<input type="hidden" name="wt_date[]" value = "<?php echo $formWts[$i]["date"]; ?>">
	</tr>				
	<?php
	}
	?>
	</table>
	<br>
	<p class="form_buttons">
	<input class="sub" type="submit" name="submit_wt" value="Update" onclick="return validate_form_wt(this)">&nbsp; 
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="sub" type="reset" name="reset_wt" value="Reset">
	<input type="hidden" name="submit_flag" value="false">
	</p>
	</form>
		</div> <!-- end of form_wt div -->
<?php
}
else
{
	echo "<h3>Active Date:  $dispDate</h3>";
	echo "<h3>Weights cannot be entered for future dates.</h3>";
}
?>