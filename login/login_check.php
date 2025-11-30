<?php
session_start();
include("../AdminPanel/db.php");   // go one level up

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Query user_details table
    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Simple password check (plain text)
        if ($password === $user['Password']) {

            // Create session
            $_SESSION['User_Id'] = $user['User_Id'];
            $_SESSION['Email'] = $user['Email'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Redirect to admin dashboard
            header("Location: ../AdminPanel/layout.php");
            exit;
        }
    }

    // Wrong password or email → return back to login.php
    echo "<script>
            alert('Invalid Email or Password!');
            window.location.href = 'login.php';
          </script>";
    exit;
}
?>
