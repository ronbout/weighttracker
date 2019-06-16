<?php 
// wt_cal_weight_process.php
// 5-23-11 rlb
// process form_wt from cal weight screen
// called from wt_form_check.php

//print_r($_POST);
// convert POST to vars
extract($_POST);
$updFlag = false;
// validate weights
if(isset($wt_weight) && $wt_weight)
{
	$errFlg = false;
	foreach ($wt_weight as $wt_key=>$wt_row)
	{
		if ($wt_row && (!is_numeric($wt_row) || $wt_row < 0))
		{
			$mainMesg = "<p class='main_mesg_err'>Invalid weight: $wt_row</p>";
			$errFlg = true;
			$onLoad = 'onLoad="document.form_wt.elements[\'wt_weight\']['.$wt_key.'].focus();"';
			break;
		}
	}
	if (!$errFlg)
	{
		if (!isset($wt_delete)) $wt_delete = array();
		// find weights that have been changed and are not marked for delete
		if (isset($wt_weight)) 
		{
			$updList = array();
			foreach($wt_weight as $wt_key=>$wt_row)
			{
				if ($wt_row != $wt_old_wt[$wt_key] && !in_array($wt_date[$wt_key], $wt_delete))
				{
					$updList[] = array("weight"=>$wt_row, "date"=>$wt_date[$wt_key]);
				}
			}
			// update weights
			$errorCode = "";
			foreach($updList as $updRow)
			{
				addMemberWeight($mysqli, $member, $user, $updRow['weight'], strtotime($updRow['date']), $errorCode);
				if ($errorCode)
				{
					$mainMesg = "<p class='main_mesg_err'>Error during update: ".$errorCode.
							"<br>Please check your weights to see what updated.</p>";
					break;
				}
				else
				{
					$updFlag = true;
				}
			}
		}
		// find weights that have been marked for delete
		if ($wt_delete)
		{
			// delete weights
			$errorCode = "";
			foreach($wt_delete as $delRow)
			{
				deleteMemberWeight($mysqli, $member, $user, strtotime($delRow), $errorCode);
				if ($errorCode)
				{
					$mainMesg = "<p class='main_mesg_err'>Error during delete: ".$errorCode."
						<br>Please check your weights to see what deleted.</p>";
					break;
				}
				else
				{
					$updFlag = true;
				}
			}
		}
		if ($updFlag)
		{
			$mainMesg .= "Weights have been updated.<br>";
			$_SESSION['member'] = serialize($member);
		}
	}
}
?>