<?php
// wt_logout.php
// 5-13-11 rlb
// used for Weight Tracker program
// logout user and redirec back to wt.php

require_once("wt_include.php");
if (isset($_SESSION['user'])) unset($_SESSION['user']);
if (isset($_COOKIE['user'])) setcookie('user','', time() - 24 * 60 * 60);
if (isset($_SESSION['username'])) unset($_SESSION['username']);
if (isset($_SESSION['member'])) unset($_SESSION['member']);
header("Location: wt.php");

?>