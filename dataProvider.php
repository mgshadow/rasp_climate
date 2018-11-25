<?php 
include("./include/sensorclass.php");


$inSensor=SensorFactory::getInsideSensor();
$outSensor=SensorFactory::getOutsideSensor();
$box1Sensor=SensorFactory::getBox1Sensor();
$box2Sensor=SensorFactory::getBox2Sensor();
$sensors=array($outSensor,$inSensor, $box1Sensor,$box2Sensor);


 header("Access-Control-Allow-Origin: *");
 header("Content-Type: application/json");

$duration=3;
if ($_GET['duration']!=null)
	$duration=(int)$_GET['duration'];

$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger");
$sql  = "SELECT min(m.date_time), ";
for ($sensorIndex=0;$sensorIndex<count($sensors);$sensorIndex++)
	{
	$p=$sensors[$sensorIndex]->pin;
	$sql=$sql."round(avg(t$p.temperature),1) as tt$p, round(avg(t$p.humidity),1) as th$p ";
	if ($sensorIndex+1<count($sensors))
		$sql=$sql.", ";
	}
$sql=$sql."FROM measure m ";
for ($sensorIndex=0;$sensorIndex<count($sensors);$sensorIndex++)
	{
	$p=$sensors[$sensorIndex]->pin;
	$sql=$sql."left join datalogger as t$p on t$p.sensor=$p and t$p.measureid=m.id ";
	}
$sql=$sql."WHERE m.active=1 and TIMESTAMPDIFF(HOUR,m.date_time,NOW())<$duration GROUP BY m.date_time order by m.date_time";
$result = mysqli_query($db, $sql);
$first=1;

$data=array();
while($r = mysqli_fetch_array($result))
{
  $line='';
  if ($first == 0)
	$line=$line.',';	  

  $first=0;
  $arrDate = array('years' => substr($r[0],0,4), 
		   'months' => (((int)substr($r[0],5,2))-1), 
		   'days' => (((int)substr($r[0],8,2))), 
		   'hours' => ((int)substr($r[0],11,2)), 
		   'minutes' => ((int)substr($r[0],14,2)),
		   'seconds' => ((int)substr($r[0],17,2)));
  $arrSensors=array();
  for ($sensorIndex=0;$sensorIndex<count($sensors);$sensorIndex++)
	{
	$name=$sensors[$sensorIndex]->name;
	$temperature=$r[$sensorIndex*2+1]==null?null:$r[$sensorIndex*2+1];
	$humidity=$r[$sensorIndex*2+2]==null?null:$r[$sensorIndex*2+2];
	$arrSensors[]=array('name' => $name,
			    'temperature' => $temperature,
			    'humidity' => $humidity,);
  	}
  $data[]=array('date' => $arrDate,
		'sensors' => $arrSensors);
}
echo json_encode(array('querystringDuration'=>$_GET['duration'],
		       'duration'=>$duration,
		       'sql'=>$sql,
		       'data' => $data));
?>



<?php

mysqli_close($db); 
?> 
