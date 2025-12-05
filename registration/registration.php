<?php
session_start();
include("../AdminPanel/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "INSERT INTO user_details (First_Name, Last_Name, Email, Password, User_Role)
              VALUES ('$fname', '$lname', '$email', '$password', 'CUSTOMER')";
    mysqli_query($connection, $query);

    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    
    <title>Register</title>
     <link rel="stylesheet" href="../home page/style.css" />
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 50px; }
        .container {
            width: 350px; margin: auto; background: #fff;
            padding: 25px; border-radius: 10px; box-shadow: 0 0 10px #ccc;
        }
        input, button {
            width: 100%; padding: 10px; margin: 10px 0;
            border-radius: 5px; border: 1px solid #ccc;
        }
        button { background: #e0486c; color: white; cursor: pointer; }
        a { color: #e0486c; text-decoration: none; }
    </style>
</head>
<body>
<?php include("../home page/navbar.php"); ?>  <!-- 🔥 Navbar -->
<div class="container">

    <h2>Create Account</h2>

    <form method="POST">

        <input type="text" name="first_name" placeholder="First Name" required>

        <input type="text" name="last_name" placeholder="Last Name" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>

        <p>Already have an account? <a href="../login/login.php">Login here</a></p>
    </form>

</div>
<?php include("../home page/footer.php"); ?> <!-- 🔥 Footer -->
</body>
</html>
