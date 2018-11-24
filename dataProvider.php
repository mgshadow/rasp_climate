<?php 
 header("Access-Control-Allow-Origin: *");
 header("Content-Type: application/json");

$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger");
$sql  = 'SELECT min(m.date_time), round(avg(t0.temperature),1) as tt0 , round(avg(t1.temperature),1) as tt1 , round(avg(t2.temperature),1) as tt2 , round(avg(t3.temperature),1) as tt3 FROM measure m left join datalogger as t0 on t0.sensor=0 and t0.measureid=m.id left join datalogger as t1 on t1.sensor=1 and t1.measureid=m.id left join datalogger as t2 on t2.sensor=2 and t2.measureid=m.id left join datalogger as t3 on t3.sensor=3 and t3.measureid=m.id WHERE m.active=1 and TIMESTAMPDIFF(HOUR,m.date_time,NOW())<30 GROUP BY m.date_time order by m.date_time';
$result = mysqli_query($db, $sql);
$first=1;
?>
{
"Data":
[
<?php
while($r = mysqli_fetch_array($result))
{
  $line='';
  if ($first == 0)
	$line=$line.',';	  

  $first=0;
  $line=$line.'{';
  #$line=$line.'"date": "Date('.substr($r[0],0,4).','.(((int)substr($r[0],5,2))-1).','.substr($r[0],8,2).','.substr($r[0],11,2).','.substr($r[0],14,2).','.substr($r[0],17,2).')", ';
  $line=$line.'"date": "$r[0]", ';
  $line=$line.'"Values": [';
  $line=$line.'{"out":'.$r[1].'}, ';
  $line=$line.'{"room":'.$r[2].'}, ';
  $line=$line.'{"one":'.$r[3].'}, ';
  $line=$line.'{"two":'.$r[4].'}';
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
