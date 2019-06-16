<?php
// wt_login_form.php
// 5-21-11 rlb
// html code for sidebar login used in 
// wt.php, wt_register.php, wt_forgot.php

// determine what page we are in
if (!isset($pg)) 
{
	$pg = $_SERVER['PHP_SELF'];
	$pg = pathinfo($pg);
	$pg = $pg['basename'];
}
echo '
				<h3 class="login">Login</h3><br>
				<form name="login_form" action="',$pg,'" method="POST" onsubmit="return validate_login(this);">
					<p class="err_msg">', $msg_login, '</p>
					<p class="label">Username: &nbsp;</p>
					<p class="input"><input type="text" name="username_login" size="20" maxlength="41" 
										value="',$username_login,'"></p>
					<p class="label">Password: &nbsp;</p>
					<p class="input"><input type="password" name="pass_login" size="20" maxlength="15" value=""></p>

					<p class="label">Stay logged in: &nbsp;</p><p class="input"><input type="checkbox" name="persist_login" value="1"></p>

					<p class="input"><input type="submit" name="submit_login" value="Login"></p><br>
';
if ($pg != "wt_register.php")
{
	echo '
					<p class="label">New member? &nbsp;</p>
					<p class="input"><a class="login" href="wt_register.php">Register Here</a></p><br>
	';
}
if ($pg != "wt_forgot.php")
{
	echo '
					<p class="label">Forgot password? &nbsp;</p>
					<p class="input"><a class="login" href="wt_forgot.php">New Password</a></p>
	';
}
echo '
				</form>
';
?>