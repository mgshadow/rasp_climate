<?php
Class ErrorTextFactory
	{
	static function getErrorText($errortype)
		{
		switch ($errortype)
			{
			case 1:
			return "Sensor nicht erreichbar";
			case 10:
			return "Luftfeuchtigkeit zu gering.";
			case 11:
			return "Luftfeuchtigkeit zu hoch.";
			case 12:
			return "Luftfeuchtigkeitsunterschied zu gering";
			case 20:
			return "Temperatur zu gering.";
			case 21:
			return "Temperatur zu hoch.";
			case 50:
			return "Messung übersprungen";
			default:
			return "unbekannte Fehlernummer $errortype";
			}
		}
	}


class ErrorEntry
	{
	var $sensor;
	var $errortype;
#	var $date;
#	var $count
	function ErrorEntry($sensor, $errortype)
		{
		#echo("\nErrorEntry($sensor,$errortype)");
		$this->sensor=$sensor;
		$this->errortype=$errortype;
		}
	function writeToDB($db)
		{
		$sensor=$this->sensor;
		$errortype=$this->errortype;
		$q = "Update errorlog set unread=1, count=count+1  where sensor=$sensor and date_time=CURDATE() and errortype=$errortype";
		$result=mysqli_query($db, $q);
		$q = "Select count from errorlog where sensor=$sensor and date_time=CURDATE() and errortype=$errortype";
		$result=mysqli_query($db, $q);
		$rows=mysqli_num_rows($result);
		
		if ($rows==0)
			{
			$q = "INSERT INTO errorlog VALUES (CURDATE(), $sensor, $errortype, 1,1)"; 
			mysqli_query($db, $q);
			}
		}
	}

class SensorValue
	{
	var $temp;
	var $hum;
	var $age;
	var $humTrend;
	var $tempTrend;
	var $dateTime;
	
	function SensorValue($t, $h, $a, $d, $tt, $ht)
	{
		$this->temp=$t;
		$this->hum=$h;
		$this->age=$a;
		$this->dateTime=$d;
		$this->tempTrend=$tt;
		$this->humTrend=$ht;
	}
	
	function isValid()
		{
		return !($this->age>15);#if the measurement is older than 15min, than it is not valid anymore.
		}	
	}


	

class SensorFactory
	{
	static function GetSensor($pin)
		{
		switch ($pin)
			{
			case 0:
			return SensorFactory::getOutsideSensor();
			case 1:
			return SensorFactory::getInsideSensor();
			case 2:
			return SensorFactory::getBox1Sensor();
			case 3:
			return SensorFactory::getBox2Sensor();
			}
		}	
		
	 static function getInsideSensor()
		{
		$ret=new Sensor(1, "black", "innen", "Innenbereich",0,0);
		return $ret;
		}
		
	 static function getOutsideSensor()
		{
		$ret=new Sensor(0, "grey", "aussen", "Aussenbereich",0,0);
		$ret->tempWarningMin=-30;
		$ret->tempWarningMax=40;
		$ret->humWarningMin=30;
		$ret->humWarningMax=110;

		
		$ret->tempGreenFrom=0;
    	$ret->tempGreenTo=30;
    	$ret->tempYellowFrom=30;
    	$ret->tempYellowTo=35;
    	$ret->tempRedFrom=35;
    	$ret->tempRedTo=100;

    	$ret->humGreenFrom=30;
    	$ret->humGreenTo=80;
    	$ret->humYellowFrom=20;
    	$ret->humYellowTo=30;
    	$ret->humRedFrom=0;
    	$ret->humRedTo=10;
		
		return $ret;
		}
	
	 static function getBox1Sensor()
		{
		$ret=new Sensor(2, "blue", "gross", "grosses Zelt",0,0);
		return $ret;
		}
		
	 static function getBox2Sensor()
		{
		$ret=new Sensor(3, "green", "klein", "kleines Zelt",0,0);
		return $ret;
		}	
	}
	


class Sensor
	{
    var $pin;
    var $name;
    var $title;
    
    var $color;
    var $tempWarningMin;
    var $tempWarningMax;
    var $humWarningMin;
    var $humWarningMax;
    
    var $fallbackTemp;
    var $fallbackHum;
    
    var $tempGreenFrom;
    var $tempGreenTo;
    var $tempYellowFrom;
    var $tempYellowTo;
    var $tempRedFrom;
    var $tempRedTo;

    var $humGreenFrom;
    var $humGreenTo;
    var $humYellowFrom;
    var $humYellowTo;
    var $humRedFrom;
    var $humRedTo;
    var $humDelta;
    var $tempDelta;
    
        
    //-----Initialization -------
    function Sensor($p, $col, $name, $t, $td, $hd)
    	{
		$this->title=$t;
        $this->pin = $p;
        $this->color = $col;
        $this->name = $name;
	$this->humDelta = $hd;
	$this->tempDelta = $td;
        
        $this->tempWarningMin=15;
    	$this->tempWarningMax=35;
	    $this->humWarningMin=30;
    	$this->humWarningMax=90;
    
	    $this->fallbackTemp=90.0;
	    $this->fallbackHum=0.0; 
	    
		$this->tempGreenFrom=18;
    	$this->tempGreenTo=28;
    	$this->tempYellowFrom=28;
    	$this->tempYellowTo=32;
    	$this->tempRedFrom=32;
    	$this->tempRedTo=100;

    	$this->humGreenFrom=50;
    	$this->humGreenTo=80;
    	$this->humYellowFrom=30;
    	$this->humYellowTo=50;
    	$this->humRedFrom=0;
    	$this->humRedTo=30;
	
	    
	    
	       
    	}
		
	function isValueOk($value)
	{
		if ($value->temp<$this->tempGreenFrom)
			return false;
		if ($value->temp>$this->tempGreenTo)
			return false;
		if ($value->hum<$this->humGreenFrom)
			return false;
		if ($value->hum>$this->humGreenTo)
			return false;
		return true;
	}
    
    function getValue($conn)
    	{
    	$t=$this->fallbackTemp;
    	$h=$this->fallbackHum;
    	$tt=3;
    	$ht=3;
    	$a=9999;
    	$d="never";
    	$sql = "SELECT avg(temperature), avg(humidity),min(TIMESTAMPDIFF(MINUTE,date_time,NOW())) as age, min(date_time) FROM datalogger where sensor = ".$this->pin."  GROUP BY UNIX_TIMESTAMP(date_time) DIV 60 ORDER BY date_time DESC LIMIT 1";		
		$sql="SELECT round(avg(temperature),1), round(avg(humidity),1), TIMESTAMPDIFF(MINUTE,m.date_time,NOW()) as age, m.date_time, m.id FROM measure as m left join datalogger d on d.sensor=".$this->pin." and d.measureid=m.id where active = 1 group by m.id ORDER BY m.date_time DESC limit 1 ";
		$result = mysqli_query($conn, $sql);

		if ($row = mysqli_fetch_array($result))
			{
			if ($row[2]<10)
				{
				$t=$row[0]+$this->tempDelta;
				$h=$row[1]+$this->humDelta;
				}
			$a=$row[2];
			$d=$row[3];
			}
		
		$sql="select avg(temperature), avg(humidity) from datalogger where sensor = ".$this->pin."  GROUP BY UNIX_TIMESTAMP(date_time) DIV 600 ORDER BY date_time DESC LIMIT 2";
		$sql="SELECT round(avg(temperature),1), round(avg(humidity),1) FROM measure as m left join datalogger d on d.sensor=".$this->pin." and d.measureid=m.id where active = 1 group by m.id ORDER BY m.date_time DESC limit 2 ";
		#echo($sql);
		$result = mysqli_query($conn, $sql);
		if ($row = mysqli_fetch_array($result))
			{
			$temp1=$row[0];
			$hum1=$row[1];
			if ($row = mysqli_fetch_array($result))
				{
				$temp2=$row[0];
				$hum2=$row[1];
				$tt=1;
				$ht=1;
				if ($temp1<$temp2)
					$tt=-1;
				if ($hum1<$hum2)
					$ht=-1;
				if (abs($temp1-$temp2)<0.1)
					$tt=0;
				if (abs($hum1-$hum2)<0.5)
					$ht=0;
				}
			}
		
		return new SensorValue($t, $h, $a, $d, $tt, $ht);
		}
	function getErrorCount($conn)
		{
		$sql = "SELECT * FROM errorlog where sensor = ".$this->pin." and unread=1 ORDER BY date_time DESC";
		$result = mysqli_query($conn, $sql);
		return mysqli_num_rows($result);
		}
	}


?>
