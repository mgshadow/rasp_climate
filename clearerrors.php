<?php 
function markAsRead(){
$db = mysqli_connect("localhost","datalogger","datalogger") or die("DB Connect error");
mysqli_select_db($db, "datalogger");

$q="update errorlog set unread=0";
mysqli_query($db, $q);
mysqli_close($db); 

}


markAsRead();

header("Location: index.php"); /* Redirect browser */
exit();

?>

