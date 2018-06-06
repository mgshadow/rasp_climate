<?php

#include("./sensorclass.php");
#include("./drawerclass.php");


function checkSensor($sensor)
{
	$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
	mysqli_select_db($db, "datalogger"); 
	$q=   "select date_time, TIMESTAMPDIFF(MINUTE,date_time,NOW())>10 as diff from datalogger "; 
	$q=$q."where sensor = $sensor "; 
	$q=$q."order by date_time desc "; 
	$q=$q."limit 1";
	$ds=mysqli_query($db, $q);  
	
	while($r = mysqli_fetch_object($ds)) 
		return !$r->diff;	
	
	return 0;
}


function getSensorValue($type,$sensor)
{

	switch ($type)
	{
	case 1:
		$field="temperature";
		$fallback=100;
		break;
	case 2:
		$field="humidity";
		$fallback=0;
		break;
	default:
		return;
	}

// Create connection
$conn = mysqli_connect("localhost", "datalogger", "datalogger", "datalogger");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT $field FROM datalogger where sensor = $sensor and TIMESTAMPDIFF(MINUTE,date_time,NOW())<10  ORDER BY date_time DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_array($result)) {
        $fallback=$row[0];
    }
} 
mysqli_close($conn);
return $fallback;
}


function drawGauge($type, $sensor, $label, $divname, $min, $max, $greenFrom, $greenTo, $yellowFrom, $yellowTo, $redFrom, $redTo)
	{
	?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["gauge"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['<?php echo($label); ?>', <?php echo(getSensorValue($type,$sensor)); ?> ],

        ]);

        var options = {
		  min: <?php echo($min); ?>, max: <?php echo($max); ?>,	
          width: 200, height: 200,
		  redFrom: <?php echo($redFrom); ?>, redTo: <?php echo($redTo); ?>,
          yellowFrom:<?php echo($yellowFrom); ?>, yellowTo: <?php echo($yellowTo); ?>,
		  greenFrom:<?php echo($greenFrom); ?>, greenTo: <?php echo($greenTo); ?>,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('<?php echo($divname); ?>'));

        chart.draw(data, options);


      }
    </script>
	
	
	<?php
	}


function drawXY($type, $sensors,$labels,$duration, $min, $max, $divname)  
{
	switch ($type)
	{
	case 1:
		$field="temperature";
		$title="Temperatur (°C) $duration HR";
		break;
	case 2:
		$field="humidity";
		$title="Luftfeuchtigkeit (%) $duration HR";
		break;
	default:
		return;
	}	
?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
  
        var data = google.visualization.arrayToDataTable([
          ['TIME',
<?php
for ($i=0;$i<count($sensors);$i++)
		echo("'".$labels[$i]."',");

?> ],
<?php 
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 

$q="";
$q=$q."SELECT FROM_UNIXTIME(200*(UNIX_TIMESTAMP(h.date_time) DIV 200)) as date_time, ";

for ($i=0;$i<count($sensors);$i++)
		{
		$q=$q."round(avg(CASE WHEN h.sensor = $sensors[$i] THEN h.$field END),1) as t$i ";
		if ($i+1<count($sensors))
			$q=$q.", ";		
		}
$q=$q."FROM datalogger h ";
$q=$q. "WHERE (";
for ($i=0;$i<count($sensors);$i++)
		{
		$q=$q."sensor = $sensors[$i] ";
		if ($i+1<count($sensors))
			$q=$q."or ";

		}
$q=$q.") and TIMESTAMPDIFF(HOUR,date_time,NOW())<$duration ";		
$q=$q."GROUP BY UNIX_TIMESTAMP(date_time) DIV 200 ";
$q=$q."order by date_time desc "; 

$ds=mysqli_query($db, $q);  

while($r = mysqli_fetch_array($ds)) 
{ 
	echo "['".$r[0]."', "; 
	for ($i=0;$i<count($sensors);$i++)
		echo " ".$r[$i+1].","; 
	echo "],"; 
} 
?> 
        ]);

	var options = {
	title: '<?php echo($title) ?>',
	curveType: 'none',
	legend: { position: 'bottom' },
	hAxis: { textPosition: 'none', direction: '-1' },
	vAxis: { viewWindow: { min: <?php echo($min) ?>, max: <?php echo($max) ?>}},
        };

        var chart = new google.visualization.LineChart(document.getElementById('<?php echo($divname) ?>'));

        chart.draw(data, options);
options['pagingSymbols'] = {prev: 'prev', next: 'next'}; options['pagingButtonsConfiguration'] = 'auto';
      }
    </script>


<?php
}

?>
