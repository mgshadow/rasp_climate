<?PHP
include("./include/membersite_config.php");
include("./include/include.php");
include("./include/sensorclass.php");
include("./include/drawerclass.php");

$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 
$creator=new DiagrammScriptCreator($db);
$sensor1=SensorFactory::getBox2Sensor();
$sensor2=SensorFactory::getInsideSensor();
$errorcount=0;
$errorcount+=$sensor1->getErrorCount($db);
$errorcount+=$sensor2->getErrorCount($db);



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<title><?php echo($sensor1->title)?></title>
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
<h2><?php echo($sensor1->title)?></h2>
</div>
</div>
<div class="container">
<?php
echo ("Messung: <b>".date("Y-m-d H:i")."</b>");
if ($errorcount>0)
	echo ("<h3>Im Fehlerprotokoll stehen für diese Sensoren $errorcount Fehler an</h3>");

?>
<h3>CURRENT CONDITIONS</h3>
  <div class="row">
        <?php  $creator->CreateGauge($sensor1); ?>
    </div>
<hr>
    </div>
<div class="container">
    <?php  $creator->CreateXY(array($sensor2,$sensor1), 3); ?>
</div>
<div class="container">
    <?php  $creator->CreateXY(array($sensor2,$sensor1), 24); ?>
</div>
<div class="container">
    <?php  $creator->CreateXY(array($sensor2,$sensor1), 24*4); ?>
</div>
 <?php include 'footer.php';?>
</body>
</html>
