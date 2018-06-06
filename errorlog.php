<HTML>
<?PHP
include("./include/membersite_config.php");

include("./include/sensorclass.php");
include("./include/drawerclass.php");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<title>RasPiViv.com - Home</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
   

</head>
<body>
<div class="jumbotron">
<div class="container">
<?php include 'menu.php';?>
</div>
</div>
<div class="container">

<h3>Errorlog</h3>
<div class="table-responsive">
  <table class="table">
<tr>
    <td>Datum</td>
    <td>Sensor</td>
    <td>Fehler</td>
    <td>Anzahl</td>
</tr>
<?php
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 
$sql="select date_time, sensor, errortype, count, unread from errorlog order by unread desc, date_time desc, sensor";
$result = mysqli_query($db, $sql);
while ($row = mysqli_fetch_array($result))
		{
		$osensor=SensorFactory::getSensor($row[1]);
		$error=ErrorTextFactory::getErrorText($row[2]);
		if ($row[4]==1)
			echo ("<tr class='danger'>");
		else
			echo ("<tr>");
?>

    <td><?php echo($row[0]);?></td>
    <td><?php 
    	
    	echo ($osensor->name);
    	?></td>
    <td><?php echo($error);?></td>
    <td><?php echo($row[3]);?></td>
</tr>
<?php

		}
?>
</table>
</div>
<div>
<a href="clearerrors.php">Marks all as read</a>
</div>
<div class="container"><hr>
<?php include 'footer.php';?></div>
</BODY> 
</HTML>

