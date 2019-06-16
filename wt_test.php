<?php 
include ("../home_dev/myfuncs.php");
include ("wt_funcs.php");
?>
<html>
<head>
	<title>WT Test Page</title>
</head>
<body>
<?php
// connect to database
require("wt_connect.php");


function get_calories($mysqli, $id)
{
	$cals = 0;
	$query = "select * from food where id = ".$id;
	$foodResult = $mysqli->query($query) or die($mysqli->error);
	while ($foodInfo = $foodResult->fetch_assoc())
	{
		if ($foodInfo['calories'])
		{
			$cals += $foodInfo['calories'];
		}
		else
		{
			$cals += get_calories($mysqli, $foodInfo['ingredient_id']);
		}
	}
	return $cals;
}

$foods = array();
$i = 0;
$query = "SELECT distinct id, description, calories FROM food WHERE ingredient_id = 0";
$result = $mysqli->query($query) or die($mysqli->error);
while ($row = $result->fetch_assoc())
{
	$foods[$i] = $row;
	if (!$row['calories'])
	{
		echo $row['id'],"<br>";
		$foods[$i]['calories'] = get_calories($mysqli, $row['id']);
	}
	$i++;
}
foreach ($foods as $foodInfo)
{
	echo $foodInfo['description']," ",$foodInfo['calories'],"<br>";
}

?>
</body>
</html>