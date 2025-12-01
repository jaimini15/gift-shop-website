<?php
session_start();

// Correct include path for Option B
include __DIR__ . "/../AdminPanel/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {

        // Fetch user data
        $user = mysqli_fetch_assoc($result);

        // Plain text password match (since you said DB stores plain text)
        if ($password === $user['Password']) {

            // Store user session
            $_SESSION['User_Id']   = $user['User_Id'];
            $_SESSION['Email']     = $user['Email'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Redirect into dashboard inside layout
            header("Location: ../AdminPanel/layout.php?view=dashboard");
            exit;
        }
    }

    // Login failed
    echo "<script>
            alert('Invalid Email or Password!');
            window.location.href = 'login.php';
          </script>";
    exit;
}
?>
