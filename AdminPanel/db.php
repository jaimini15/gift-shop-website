<?php
$host = "localhost";
$user = "root";
$pass = "Jaimini@2005";
$db   = "giftshop";

$connection = mysqli_connect($host, $user, $pass, $db);

if(!$connection){
    die("Connection Failed: " . mysqli_connect_error());
}
?>
