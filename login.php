<?php
$host="localhost";
$user="root";
$password="Jaimini@2005";
$dbname="gift_shop";

$conn = new mysqli($host,$user,$password,$dbname);

if($conn->connect_error)
{
    die("Connection failed");
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user_details WHERE Email = '$username' AND `Password`='$password'";
    $result = $conn->query($sql);

    if($result->num_rows == 1 )
    {
        echo "Login Successful";
    }
    else
    {
        echo "Invalid <a href='index.html'>Try again </a>";
    }
}

?>
