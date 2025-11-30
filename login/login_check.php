<?php
session_start();
include("../AdminPanel/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Password check (plain text)
        if ($password === $user['Password']) {

            // Create session
            $_SESSION['User_Id']   = $user['User_Id'];
            $_SESSION['Email']     = $user['Email'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Redirect to AdminPanel
            header("Location: ../AdminPanel/layout.php");
            exit;
        }
    }

    // If login failed:
    echo "<script>
            alert('Invalid Email or Password!');
            window.location.href = 'login.php';
          </script>";
    exit;
}
?>
