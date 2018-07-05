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
	$humDelta=[-8.8,2.4,0,-30,-1,-9.8,3.5,5];
	$tempDelta=[-0.7,-1.2,0.2,0.2,-0.1,-0.2,0.1,0];
if (!file_exists("/tmp/lock.txt"))
{
$fp = fopen("/tmp/lock.txt", "w");
fclose($fp);
}

	
$fp = fopen("/tmp/lock.txt", "r+");
	
	echo ("\nReading Sensors");
	
	if (flock($fp, LOCK_EX | LOCK_NB)) 
	{  // acquire an exclusive lock
		
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
							$bFound=true;
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
				$humid=floatval(substr($output[$i],11,5))+$humDelta[$sensor]; 
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
				$temp=floatval(substr($output[$i],33,5))+$tempDelta[$sensor]; 
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
				
				$q = "INSERT INTO datalogger VALUES (now(), $id, '$temp', '$humid',0)"; 
				echo ("\n\t\t".$q);
				mysqli_query($db, $q); 
			}
			
		}
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
$humTolerance=5;

if ($inValue->isValid() && $box1Value->isValid())
	{
	echo ("\nCheck box1");
	if ($box1Value->hum < $inValue->hum - $humTolerance)
		{
		$err=new ErrorEntry($box1Sensor->pin,12);
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
		$err=new ErrorEntry($box2Sensor->pin,12);
		$err->writeToDB($db);
		echo ("\nValue not OK");
		}
	else
		echo ("\nValueOK");
	}
 
mysqli_close($db); 
?> 

