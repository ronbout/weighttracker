<?php
// wt_view_source.php
// 6-7-11 rlb
// program to view php source code from within weight tracker
// only visible to $userlist array ('ronbout')
// linked in wt_sidebar
// uses recursive function to drill down all directories creating list of php files
// when selected, highlight_file is used to display
require_once("wt_include.php");
// make sure user is logged in
if (!isset($_SESSION['user']))
{
	// send back to login page
	header("Location: wt.php");
}
$user = $_SESSION['user'];
// login to database
require("wt_connect.php");
// if only have user_id, get rest of member info
if (!isset($_SESSION['member']))
{
	$errCode = "";
	// load Member class
	$member = loadMember($mysqli, $user, $errCode);
	if ($errCode) die("Database error: ".$errCode);
	$_SESSION['member'] = serialize($member);
	$_SESSION['username'] = $member->getUserName();
}
else
{
	$member = unserialize($_SESSION['member']);
}
require("wt_sidebar.php");
if (isset($_POST['submit_select']))
{
	$source_file = highlight_file($_POST['select_source'], true);
}
// build source
$files = get_filelist(".", true, "php", true);
?>
<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="stylesheets/wt.css" type="text/css">
		<script type="text/javascript" src="funcs.js"></script>
		<title>Weight Tracker View Source</title>
		<style type="text/css">
			#source_code {
				position:relative;
				float:left;
				width:460px;
				height:460px;
				padding:10px;
				overflow:auto;
			}	
			#source_select {
				position:relative;
				float:left;
				width:237px;
				height:460px;
				padding:10px;
				background-color:#0a0;
			}			
			select {
				font-size:0.8em;
			}
		</style>
	</head>
	<body>
	<div id="page"> <!--  start of the page wrapper -->
		<?php require("wt_header.php"); ?>
		<div id="middle">   <!--  start of middle, container for sidebar and main  -->
			<div id="sidebar">   <!-- start of sidebar  -->
				<?php echo $sideMsg; echo $sidebar; ?>
			</div>   <!-- end of sidebar  -->
			<div id="main">  <!-- start of main  -->
			   <div id="source_code">
			   	   <?php  if (isset($source_file)) echo $source_file;?>
			   </div><!-- end of source_code-->
			   <div id="source_select">
			   <form name="form_source" action="wt_view_source.php" method="post">
					<select name="select_source" size="26" ondblclick='this.form.elements["submit_select"].click();''>
<?php
$first_flag = false;
foreach($files as $file)
{
	if ($first_flag)
	{
		$first_flag = false;
		//echo "<option value='$file' ondblclick='this.form.elements[\"submit_select\"].click();' selected='selected'>$file</option>";
		echo "<option value='$file'  selected='selected'>$file</option>";
	}
	else
	{
		//echo "<option value='$file' ondblclick='this.form.elements[\"submit_select\"].click();'>$file</option>";
		echo "<option value='$file' >$file</option>";
	}
		
}
?>
					</select>
					<br><br>
					<input type="submit" name="submit_select" value="Select File">
			   </form>
			   </div><!-- end of source_select-->
			</div>  <!-- end of main  -->
		</div>   <!-- end of middle container  -->
		<?php require("wt_footer.php"); ?>
	</div>
	</body>
</html>