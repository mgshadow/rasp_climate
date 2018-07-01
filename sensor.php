<?php 
include("include/sensorclass.php");
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger");
echo ("<br>reading Sensor Data");

function selectSensor($pin)
{
	$arr=decbin($pin);
	echo ("\n\tSelect Sensor $pin : $arr");	
	for ($i=0;$i<3;$i++)
	{
		$j=$i+7;
		$v=0;
		if ($i<strlen($arr))
			$v=$arr[strlen($arr)-$i-1];
		echo("\n\t\tSet Pin GPIO$j : S$i to $v");
		system("gpio mode ".$j." out");
		system("/usr/local/bin/gpio write $j $v "); 
	}
}


function readSensor($db) 
{ 
	echo ("\nReading Sensors");
	for ($sensor=0;$sensor<7;$sensor++)
	{
		selectSensor($sensor);
		
		$output = array(); 
		$return_var = 0; 
		$i=1;
		$pin=21;#connected to GPIO 21
		exec('sudo /usr/local/bin/loldht '.$pin, $output, $return_var); 
		$bError=false;
		$bFound=false;
		while (!$bError && !$bFound) 
		{ 
			if ($i<sizeof($output))
			{
					if (substr($output[$i],0,1)!="H")
					{
						$bFound=true;
					}
					$i++; 
					if ($i>20)
					{	
						echo ("\n\t*** no Sensor Value ErrorEntry and Abort");
						$err=new ErrorEntry($sensor,1);
						$err->writeToDB($db);
						$bError=true;;
					}
			}
		}
		if ($bFound)
		{
			$osensor=SensorFactory::getSensor($sensor);
			$humid=substr($output[$i],11,5); 
			if ((int)$humid>$osensor->humWarningMax)
				{
				$err=new ErrorEntry($sensor,11);
				$err->writeToDB($db);
				}
			if ((int)$humid<$osensor->humWarningMin)
				{
				$err=new ErrorEntry($sensor,10);
				$err->writeToDB($db);
				}
			$temp=substr($output[$i],33,5); 
			if ((int)$temp>$osensor->tempWarningMax)
				{
				$err=new ErrorEntry($sensor,21);
				$err->writeToDB($db);
				}
			if ((int)$temp<$osensor->tempWarningMin)
				{
				$err=new ErrorEntry($sensor,20);
				$err->writeToDB($db);
				}
			$id=floor($sensor/2);#cause all sensors are redundant so e.g. sensor2 and sensor3 are on the same chip
			$q = "INSERT INTO datalogger VALUES (now(), $id, '$temp', '$humid',0)"; 
			echo ("\n".$q);
			#mysqli_query($db, $q); 
		}
		
	}
	return; 
} 

#readSensor($db, 6); 
#readSensor($db, 4); 
#readSensor($db, 2); 
readSensor($db);

$inSensor=SensorFactory::getInsideSensor();
$inValue=$inSensor->getValue($db);
$box1Sensor=SensorFactory::getBox1Sensor();
$box1Value=$box1Sensor->getValue($db);
$box2Sensor=SensorFactory::getBox2Sensor();
$box2Value=$box2Sensor->getValue($db);
$humTolerance=5;

if ($inValue->isValid() && $box1Value->isValid())
	{
	echo ("\nCheck box1");
	if ($box1Value->hum < $inValue->hum - $humTolerance)
		{
		$err=new ErrorEntry($box1Sensor,12);
		$err->writeToDB($db);
		echo ("\nValue not OK");
		}
	else
		echo ("\nValueOK");
	}
if ($inValue->isValid() && $box2Value->isValid())
	{
	echo ("\nCheck box2");
	if ($box2Value->hum < $inValue->hum - $humTolerance)
		{
		$err=new ErrorEntry($box2Sensor,12);
		$err->writeToDB($db);
		echo ("\nValue not OK");
		}
	else
		echo ("\nValueOK");
	}
 
mysqli_close($db); 
?> 

