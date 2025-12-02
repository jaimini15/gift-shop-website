<?php
session_start();
include("../AdminPanel/db.php"); // ✔ keep it

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        if ($password === $user['Password']) {



            $_SESSION['User_Id'] = $row['User_Id'];
            $_SESSION['First_Name'] = $row['First_Name'];
            $_SESSION['Last_Name'] = $row['Last_Name'];
            $_SESSION['DOB'] = $row['DOB'];
            $_SESSION['User_Role'] = $row['User_Role'];
            $_SESSION['Phone'] = $row['Phone'];
            $_SESSION['Address'] = $row['Address'];
            $_SESSION['Pincode'] = $row['Pincode'];
            $_SESSION['Email'] = $row['Email'];
            $_SESSION['Create_At'] = $row['Create_At'];

            header("Location: ../AdminPanel/layout.php?view=dashboard");
            exit();
        }
    }

    echo "<script>
            alert('Invalid Email or Password!');
            window.location.href = 'login.php';
          </script>";
    exit();
}
?>