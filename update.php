<?php
$con=mysqli_connect("localhost", "datalogger", "datalogger") or die("Connection Failed");
mysqli_select_db($con, "vivs")or die("Connection Failed");
$user = $_POST['user'];
$password = $_POST['userpassword'];
$query = "UPDATE test SET password = '$password' WHERE name = '$user'";
if(mysqli_query($con,$query)){
echo "updated";}
else{
echo "fail";}
?>
