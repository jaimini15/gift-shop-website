<?php
    $servername = "localhost";
    $username = "root";
    $password = "Jaimini@2005";
    $dbname = "gift_shop";

    $conn = new mysqli($servername,$username,$password,$dbname);

    if($conn->connect_error)
    {
        echo "Connection failed";
    }

    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $adrs1 = $_POST['adrs1'];
    $adrs2 = $_POST['adrs2'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];

    $sql = "INSERT INTO user_details (Fname,Email,`Password`,`Address line 1`,`Address line 2`,City,Pincode) VALUES ('$fname','$email','$password','$adrs1','$adrs2','$city','$pincode')";
    
    if($conn->query($sql) === TRUE)
    {
        header( "refresh:2; url=login.html");
    }
    else
    {
        echo "Error";
    }

    $conn->close();
?>