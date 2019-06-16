<?php
// wt_fav_nutrients_process.php
// 7-9-11 rlb
// toggles the favorite selection of a user on a food
// that is owned by someone else.  If user is owner, food
// always a favorite

// determine whether it is currently a fav
extract($_POST);
if ($nut_fav)
{
	// need to remove fav
	$errorCode = "";
	if (!delete_food_favs($mysqli, $user, $nut_id, $errorCode))
	{
		// unknown error
		$food_mesg = "<p class='food_err'>Error deleting food fav.</p>";
		// *******while testing, display message, when deploying, activate sendError code****
		$food_mesg = "<p class='food_err'>Error deleting food fav: $errorCode</p>";
		//**********sendError($user, $errCode);
	}
}
else
{
	// need to add fav
	$errorCode = "";
	if (!add_food_favs($mysqli, $user, $nut_id, $errorCode))
	{
		// unknown error
		$food_mesg = "<p class='food_err'>Error adding food fav.</p>";
		// *******while testing, display message, when deploying, activate sendError code****
		$food_mesg = "<p class='food_err'>Error adding food fav: $errorCode</p>";
		//**********sendError($user, $errCode);
	}
}
// make sure to reset food list variables
$keywords = (isset($_SESSION['keywords'])) ? $_SESSION['keywords'] : "";
$show_all = (isset($_SESSION['show_all'])) ? $_SESSION['show_all'] : "";
?>