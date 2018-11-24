<?php 
 header("Access-Control-Allow-Origin: *");
 header("Content-Type: application/json");

$duration=3;
if ($_SERVER['QUERY_STRING']['duration']!=null)
	$duration=(int)$_SERVER['duration'];

$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger");
$sql  = "SELECT min(m.date_time), ";
$sql=$sql."round(avg(t0.temperature),1) as tt0, round(avg(t0.humidity),1) as th0, ";
$sql=$sql."round(avg(t1.temperature),1) as tt1, round(avg(t1.humidity),1) as th1, ";
$sql=$sql."round(avg(t2.temperature),1) as tt2, round(avg(t2.humidity),1) as th2, ";
$sql=$sql."round(avg(t3.temperature),1) as tt3, round(avg(t3.humidity),1) as th3 ";
$sql=$sql."FROM measure m left join datalogger as t0 on t0.sensor=0 and t0.measureid=m.id left join datalogger as t1 on t1.sensor=1 and t1.measureid=m.id left join datalogger as t2 on t2.sensor=2 and t2.measureid=m.id left join datalogger as t3 on t3.sensor=3 and t3.measureid=m.id ";
$sql=$sql."WHERE m.active=1 and TIMESTAMPDIFF(HOUR,m.date_time,NOW())<$duration GROUP BY m.date_time order by m.date_time";
$result = mysqli_query($db, $sql);
$first=1;
?>
{
"querystringDuration": "<?php echo($_SERVER['QUERY_STRING']['duration']); ?>",
"duration": <?php echo($duration); ?>,
"data":
[
<?php
while($r = mysqli_fetch_array($result))
{
  $line='';
  if ($first == 0)
	$line=$line.',';	  

  $first=0;
  $line=$line.'{';
  $line=$line.'"date": {"years": '.substr($r[0],0,4).', "months": '.(((int)substr($r[0],5,2))-1).', "days": '.((int)substr($r[0],8,2)).', "hours":'.((int)substr($r[0],11,2)).',"minutes": '.((int)substr($r[0],14,2)).',"seconds":'.((int)substr($r[0],17,2)).'}, ';
  #$line=$line.'"date": "new Date('.substr($r[0],0,4).','.(((int)substr($r[0],5,2))-1).','.substr($r[0],8,2).','.substr($r[0],11,2).','.substr($r[0],14,2).','.substr($r[0],17,2).'), ';
  #$line=$line.'"date": "'.$r[0].'", ';
  $line=$line.'"sensors": [';
  $line=$line.'{"name":"aussen","temperature":'.$r[1].',"humidity":'.$r[2].'}, ';
  $line=$line.'{"name":"innen","temperature":'.$r[3].',"humidity":'.$r[4].'}, ';
  $line=$line.'{"name":"eins","temperature":'.$r[5].',"humidity":'.$r[6].'}, ';
  $line=$line.'{"name":"zwei","temperature":'.$r[7].',"humidity":'.$r[8].'} ';
  $line=$line.']';	
  $line=$line.'}';
  echo ($line);
}
?>
]
}

<?php

mysqli_close($db); 
?> 
