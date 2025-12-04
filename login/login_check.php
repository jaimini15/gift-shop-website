<?php
session_start();
include("../AdminPanel/db.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
    $email = mysqli_real_escape_string($connection, $_POST['email']);
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
    // Get user input safely
    $email    = mysqli_real_escape_string($connection, $_POST['email']);
>>>>>>> Stashed changes
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Fetch user record
    $query  = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    // If user exists
    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        // Password match (plain text in DB)
        if ($password === $user['Password']) {

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream


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
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
            // Save session
            $_SESSION['User_Id']   = $user['User_Id'];
            $_SESSION['Email']     = $user['Email'];
            $_SESSION['User_Role'] = $user['User_Role'];
>>>>>>> Stashed changes

            // Redirect user back to the page they came from
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);

                // Make sure file exists before redirect
                if (file_exists("../$redirect")) {
                    header("Location: ../$redirect");
                    exit();
                }
            }

            // Default redirect
            header("Location: ../AdminPanel/layout.php?view=dashboard");
            exit();
        }
    }

    // If user does not exist or password wrong
    echo "<script>
            alert('Invalid Email or Password! Please register first if you are a new user.');
            window.location.href = '/GIFT-SHOP-WEBSITE/login/login.php';
          </script>";
    exit();
}
?>