<?php 
include("include/sensorclass.php");
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger");

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
		#echo("\n\t\tSet Pin GPIO$j : S$i to $v");
		system("gpio mode ".$j." out");
		system("/usr/local/bin/gpio write $j $v "); 
	}
}


function readSensor($db) 
{ 
	$humFactor=[0.66,0.84,1,0.66,0.92,0.75,1,1];
	$humDelta=[0,0,0,-5,0,0,0,0];
	$tempFactor=[1,1,1,1,1,1,1,1];
	$tempDelta=[0,0,0,0,0,0,0,0];
if (!file_exists("/tmp/lock.txt"))
{
$fp = fopen("/tmp/lock.txt", "w");
fclose($fp);
}

	
$fp = fopen("/tmp/lock.txt", "r+");
	
	echo ("\nReading Sensors");
	
	if (flock($fp, LOCK_EX | LOCK_NB)) 
	{  // acquire an exclusive lock

		system("gpio mode 0 out");
		system("/usr/local/bin/gpio write 0 1 "); 
		
		$sql="INSERT INTO `measure`(`date_time`, `active`) VALUES (now(),0)";
		$result = mysqli_query($db, $sql);
		$sql="select max(id) from measure where active=0";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_array($result);
		$measureId=$row[0];
		
		for ($sensor=0;$sensor<=7;$sensor++)
		{
			$id=floor($sensor/2);#cause all sensors are redundant so e.g. sensor2 and sensor3 are on the same chip
			echo ("\n\tSensorID=$id");
			selectSensor($sensor);
			sleep(2);
			$output = array(); 
			$return_var = 0; 
			$i=1;
			$pin=21;#multiplexer signal is connected to GPIO 21
			exec('sudo /usr/local/bin/loldht '.$pin, $output, $return_var); 
			$bError=false;
			#$bError=true;#debug*************************************************************************************
			$bFound=false;
			$j=0;
			while (!$bError && !$bFound) 
			{ 
				$i++;
				if ($i<sizeof($output))
				{
						echo("\n\t\t");
						echo ($output[$i]);
						if (substr($output[$i],0,1)=="H")
						{
							#echo("\n*Found*");
							
							$temp=round(floatval(substr($output[$i],33,5))*$tempFactor[$sensor]+$tempDelta[$sensor],1);
							if ($temp>0)
							{
								//if there are measurement errors, the temperature is 0, so lets ignore them
								$bFound=true;
							}
							
						}
						
						if (substr($output[$i],0,4)=="Lock")
						{
							#echo("\n*Found*");
							$bError=true;
							$sensor--;
						}
						
						
				}
				
				if ($i>6)
						{	
							
							echo ("\n\t*** no Sensor Value ErrorEntry and Abort");
							$err=new ErrorEntry($id,1);
							$err->writeToDB($db);
							$bError=true;;
						}
			}
			if ($bFound)
			{
				$osensor=SensorFactory::getSensor($id);
				$humid=round(floatval(substr($output[$i],11,5))*$humFactor[$sensor]+$humDelta[$sensor],1); 
				if ((int)$humid>$osensor->humWarningMax)
					{
					$err=new ErrorEntry($id,11);
					$err->writeToDB($db);
					}
				if ((int)$humid<$osensor->humWarningMin)
					{
					$err=new ErrorEntry($id,10);
					$err->writeToDB($db);
					}
				$temp=round(floatval(substr($output[$i],33,5))*$tempFactor[$sensor]+$tempDelta[$sensor],1); 
				if ((int)$temp>$osensor->tempWarningMax)
					{
					$err=new ErrorEntry($id,21);
					$err->writeToDB($db);
					}
				if ((int)$temp<$osensor->tempWarningMin)
					{
					$err=new ErrorEntry($id,20);
					$err->writeToDB($db);
					}
				
				$q = "INSERT INTO datalogger (measureid, date_time, sensor, temperature, humidity, pwm) VALUES ($measureId, now(), $id, '$temp', '$humid',0)"; 
				echo ("\n\t\t".$q);
				mysqli_query($db, $q); 
			}
			
		}
		
		$q = "update measure set active=1 where id=$measureId"; 
		echo ("\n\t\t".$q);
		mysqli_query($db, $q); 
		
		system("/usr/local/bin/gpio write 0 0 "); 
				
	}
	else
	{
		echo "\nCouldn't get the lock!, skipping";
		$err=new ErrorEntry(99,50);
		$err->writeToDB($db);
	}
	
	fclose($fp);
	
	return; 
} 


readSensor($db);

$inSensor=SensorFactory::getInsideSensor();
$inValue=$inSensor->getValue($db);
$box1Sensor=SensorFactory::getBox1Sensor();
$box1Value=$box1Sensor->getValue($db);
$box2Sensor=SensorFactory::getBox2Sensor();
$box2Value=$box2Sensor->getValue($db);
$humTolerance=15;

if ($inValue->isValid() && $box1Value->isValid())
	{
	echo ("\nCheck box1");
	if ($box1Value->hum < $inValue->hum - $humTolerance)
		{
		if (!$box1Sensor->isValueOk($box1Value))
		{
			$err=new ErrorEntry($box1Sensor->pin,12);
			$err->writeToDB($db);
			echo ("\nValue not OK");	
		}
		else
			echo ("\nSmall div but value OK");
		}
	else
		echo ("\nValueOK");
	}
if ($inValue->isValid() && $box2Value->isValid())
	{
	echo ("\nCheck box2");
	if ($box2Value->hum < $inValue->hum - $humTolerance)
		{
		if (!$box2Sensor->isValueOk($box2Value))
		{
			$err=new ErrorEntry($box2Sensor->pin,12);
			$err->writeToDB($db);
			echo ("\nValue not OK");	
		}	
		else		
			echo ("\nSmall div but value OK");		
		}
	else
		echo ("\nValueOK");
	}
 
mysqli_close($db); 
?> 

