<?php
// wt_login_process.php
// 5-21-11 rlb
// processing code for user login.  Called from
// wt.php, wt_register.php, wt_forgot

$msg_login = "";
$username_login = "";
$errCode = "";
if (isset($_POST['submit_login']))
{
	// check for missing data
	if (!isset($_POST['username_login']) || !$_POST['username_login'] || !isset($_POST['pass_login']) || !$_POST['pass_login'])
	{
		$msg_login = "Missing Username or Password";
	}
	else
	{
		// connect to database
		require("wt_connect.php");
		// attempt to login user
		$username_login = strtolower(trim(($_POST['username_login'])));
		$pass_login = md5(trim($_POST['pass_login']));
		// see if a superuser is logging in as someone else
		$passCheck = true;
		if (strpos($username_login,"="))
		{
			// login in to see if superuser, then login as other user
			$usernames = explode("=",$username_login);
			if (!$su_info = userLogin($mysqli, $usernames[0], "", $pass_login, true, $errCode))
			{
				// could not log in -- find out why
				switch($errCode)
				{
					case -1:
						$msg_login = "Invalid Username";
						break;
					case -2:
						$msg_login = "Invalid Password.";
						$formFocus = "document.login_form.pass.focus();";
						break;
					case -3:
						// user has not yet confirmed account
						$_SESSION['email'] = $username_login;
						$_SESSION['email_pass'] = $pass_login;
						$_SESSION['username'] = $username_login;
						header("Location: wt_confirm_warn.php");
						break;
					default:
						$msg_login = "Database error: ".$errCode;
				}
				$mysqli->close();
			}
			else
			{
				if ($su_info['level'] == 3)
				{
					$username_login = $usernames[1];
					$passCheck = false;
				}
				else
					$msg_login = " ";
			}
		}
		if (!$msg_login)
		{
			if (!$user_info = userLogin($mysqli, $username_login, "", $pass_login, true, $errCode, $passCheck))
			{
				// could not log in -- find out why
				switch($errCode)
				{
					case -1:
						$msg_login = "Invalid Username";
						break;
					case -2:
						$msg_login = "Invalid Password.";
						$formFocus = "document.login_form.pass.focus();";
						break;
					case -3:
						// user has not yet confirmed account
						$_SESSION['email'] = $username_login;
						$_SESSION['email_pass'] = $pass_login;
						$_SESSION['username'] = $username_login;
						header("Location: wt_confirm_warn.php");
						break;
					default:
						$msg_login = "Database error: ".$errCode;
				}
				$mysqli->close();
			}
			else
			{
				$_SESSION['user'] = $user_info['member_id'];
				$mysqli->close();
				// check if user wants to remain logged in, if so, set cookie to 1 year
				if (isset($_POST['persist_login']))
				{
					setcookie('user',$_SESSION['user'], time() + (365 * 24 * 60 * 60));
				}
				// check if session mode is food and js needs to be added to get
				if (isset($_SESSION['mode']) && $_SESSION['mode'] == "food")
					header ("Location: wt_member.php?js=yes");
				else
					header ("Location: wt_member.php");
			}
		}
	}
}
?>