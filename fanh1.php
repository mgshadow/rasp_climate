<?php 
$humthreshold = 85.0; 


$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 

$q = "SELECT humidity FROM datalogger where sensor = 8 ORDER BY date_time DESC LIMIT 1"; 
$ds = mysqli_query($db, $q); 
$hum=(int)mysqli_fetch_object($ds)->humidity; 


if ($hum>$humthreshold) 
{ 
	$pwmNew=1; 
} 
if ($hum<=$humthreshold) 
{ 
	$pwmNew=0; 
} 


$s="/usr/local/bin/gpio write 5 $pwmNew "; 
exec($s); 

mysqli_query($db, $q); 
mysqli_close($db); 
?> 
