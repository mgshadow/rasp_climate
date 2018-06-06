<?php 
function delOld(){
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error");
mysqli_select_db($db, "datalogger");
$q="delete from datalogger where pwm>60"; 
mysqli_query($db, $q);

$q="update datalogger set pwm=pwm+1";
mysqli_query($db, $q);
mysqli_close($db); 
return 0;
}

function hist($sensor){ 
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error"); 
mysqli_select_db($db, "datalogger"); 
for ($i=0;$i<=23;$i++) 
{ 
        $q=   "insert into history ( "; 
        $q=$q."select date_add(curdate(),interval $i hour),'$sensor', round(avg(temperature),2),round(avg(humidity),2) "; 
        $q=$q."from datalogger "; 
        $q=$q."where sensor = '$sensor' "; 
        $q=$q."and date_time >=date_add(curdate(),interval $i hour) ";$ii=$i+1; 
        $q=$q."and date_time < date_add(curdate(),interval $ii hour) "; 
		$q=$q."and pwm = 0 ";
        $q=$q.") "; 
        mysqli_query($db, $q); 
} 

$q="delete from history where humidity is null";
mysqli_query($db, $q);

mysqli_close($db); 

return 0; 
} 
hist(7);
hist(8); 
hist(9); 
hist(21); 
delOld();
?>

