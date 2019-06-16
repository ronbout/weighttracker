<?php
// wt_funcs.php
// 5-8-11 rlb
// contains functions for weight tracker wt.php
// mostly focused on user login and registration code
require_once("wt_include.php");
function userLogin($mysqli, $username, $user_id, $pass, $confirmCheck, &$errorCode, $passCheck=true)
{
	// log a user into the member table of weighttracker database
	// use userId if not null
	if ($user_id)
	{
		$user_id = $mysqli->real_escape_string($user_id);
		$query = "SELECT * FROM member WHERE member_id = ".$user_id;
	}
	else
	{
		$username = $mysqli->real_escape_string($username);
		$query = "SELECT * FROM member WHERE lower(user_name) = '".strtolower($username)."'";
	}
	if ($result = $mysqli->query($query))
	{
		if ($result->num_rows)
		{
			$row = $result->fetch_assoc();
			// check password
			if ($row['password'] != $pass && $passCheck)
			{
				$errorCode = -2;  // incorrect password
				return false;
			}
			if (!$row['confirm_flag'] && $confirmCheck)
			{
				$errorCode = -3;  // user not yet confirmed
				return false;
			}
			// success
			return $row;
		}
		else
		{
			$errorCode = -1; // unknown user
			return false;
		}
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}

function userRegister($mysqli, $user, $firstName, $lastName, $pass, $emailAddr, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// convert email to lowercase
	$emailAddr = strtolower($emailAddr);
	// check for unique username and email address
	$user = $mysqli->real_escape_string($user);
	$emailAddr = $mysqli->real_escape_string($emailAddr);
	$query = "SELECT user_name, email FROM member WHERE lower(user_name) = '".strtolower($user)."' OR
				email = '".$emailAddr."'";
	if ($result = $mysqli->query($query))
	{
		$userFlag = false;
		$emailFlag = false;
		while ($row = $result->fetch_assoc())
		{
			if ($row['user_name'] == $user)  $userFlag = true;
			if ($row['email'] == $emailAddr) $emailFlag = true;
		}
		if ($userFlag)
		{
			$errorCode = -1;  // username already exists
			return false;
		}
		if ($emailFlag)
		{
			$errorCode = -2;  // email already exists
			return false;
		}
		// so far, so good
		// insert new member into table
		// generate random string[10] for confirmation value
		$confirm_code = "";
		for ($i = 0; $i < 10; $i++)
		{
			$confirm_code .= chr(rand(97,122));
		}
		// make sure any special characters like "O'Reilly" are escaped
		$firstName = $mysqli->real_escape_string($firstName);
		$lastName = $mysqli->real_escape_string($lastName);
		$user = $mysqli->real_escape_string($user);
		$emailAddr = $mysqli->real_escape_string($emailAddr);
		$pass = $mysqli->real_escape_string($pass);
		$query = "INSERT INTO member (first_name,last_name,user_name,email,confirm_value,confirm_flag,password)
				  VALUES ('".$firstName."','".$lastName."','".$user."','".$emailAddr."','".$confirm_code."',0,'".$pass."')";
		if ($result = $mysqli->query($query))
		{
			$errorCode = 0;
			return true;
		}
		else
		{
			$errorCode = $mysqli->error;   // error inserting data
			return false;
		}
	}
	else
	{
		$errorCode = $mysqli->error; // error with Select Query
		return false;
	}
}

function confirmUser($mysqli, $user_id, $confirm_code, &$errorCode)
{
	// log a user into the member table of weighttracker database
	$user_id = $mysqli->real_escape_string($user_id);
	$query = "SELECT * FROM member WHERE member_id = '".$user_id."'";
	if ($result = $mysqli->query($query))
	{
		if ($result->num_rows)
		{
			// success
			$row = $result->fetch_assoc();
			if ($row['confirm_value'] != $confirm_code)
			{
				if ($row['confirm_flag'])
					$errorCode = -2;  // already confirmed
				else
					$errorCode = -1; // invalid data
			}
			else
				return $row['user_name'];
		}
		else
		{
			$errorCode = -1; // invalid data
			return false;
		}
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}

function activateUser($mysqli, $user_id, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// update confirm flag of member
	$user_id = $mysqli->real_escape_string($user_id);
	$query = "UPDATE member SET confirm_value = '', confirm_flag = 1 WHERE member_id = '".$user_id."'";
	if ($result = $mysqli->query($query))
	{
		// success
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}

function forgotPass($mysqli, $user, $email, $confirmCheck, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// log a user into the member table of weighttracker database
	$query = "SELECT * FROM member WHERE lower(user_name) = '".strtolower($user)."'";
	if ($result = $mysqli->query($query))
	{
		if ($result->num_rows)
		{
			$row = $result->fetch_assoc();
			// check password
			if ($row['email'] != $email)
			{
				$errorCode = -2;  // incorrect email
				return false;
			}
			if (!$row['confirm_flag'] && $confirmCheck)
			{
				$errorCode = -3;  // user not yet confirmed
				return false;
			}
			// retrieved record, now set new password
			// generate random string[8] for password
			$pass = "";
			for ($i = 0; $i < 8; $i++)
			{
				$pass .= chr(rand(97,122));
			}
			$dbpass = md5($pass);
			$query = "UPDATE member SET password = '".$dbpass."' WHERE member_id = '".$row['member_id']."'";
			if ($result = $mysqli->query($query))
			{
				// success
				$row['password'] = $pass;
				return $row;
			}
			else
			{
				$errorCode = $mysqli->error; // error with Query
				return false;
			}
		}
		else
		{
			$errorCode = -1; // unknown user
			return false;
		}
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function updatePass($mysqli, $user_id, $newPass, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// update password of member
	$user_id = $mysqli->real_escape_string($user_id);
	$newPass = $mysqli->real_escape_string($newPass);
	$query = "UPDATE member SET password = '".$newPass."' WHERE member_id = '".$user_id."'";
	if ($result = $mysqli->query($query))
	{
		// success
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}

function updateEmail($mysqli, $user_id, $newEmail, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// check for email already present
	$user_id = $mysqli->real_escape_string($user_id);
	$newEmail = $mysqli->real_escape_string($newEmail);
	$query = "SELECT email FROM member WHERE email = '".$newEmail."'";
	if ($result = $mysqli->query($query))
	{	
		if ($result->num_rows)
		{
			// email is already registered
			$errorCode = -1;
			return false;
		}
	}
	// update email of member
	$query = "UPDATE member SET email = '".$newEmail."' WHERE member_id = '".$user_id."'";
	if ($result = $mysqli->query($query))
	{
		// success
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function changeUser($mysqli, $user_id, $firstName, $lastName, &$errorCode)
{
	// make sure process finishes 
	ignore_user_abort(true);
	// update name of user
	$query = "UPDATE member SET first_name = '".$firstName."', last_name = '".$lastName."' WHERE member_id = '".$user_id."'";
	if ($result = $mysqli->query($query))
	{
		// success
		return true;
	}
	else
	{
		$errorCode = $mysqli->error; // error with Query
		return false;
	}
}
function sendError($user, $body)
{
	$subject = $user." error";
	$body = str_replace("\n","<br>",$body);
	$sendTo = "ronbout@boutilier.dyndns-free.com";
	$sendFromName = $user;
	$sendToName = "Administrator";
	gmail($sendTo, $subject, $body, $sendFromName, $sendToName);
}
?>