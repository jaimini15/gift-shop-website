<?php
session_start();
include("../AdminPanel/db.php");

$error = "";
$rememberedEmail = $_COOKIE['remember_email'] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $pass  = mysqli_real_escape_string($connection, $_POST['password']);
    $role  = mysqli_real_escape_string($connection, $_POST['role']); // NEW

    $sql = "SELECT * FROM user_details 
            WHERE Email='$email' AND User_Role='$role' LIMIT 1";

    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // password check 
        if ($pass === $row['Password']) {

            $_SESSION['User_Id'] = $row['User_Id'];
            $_SESSION['Email']   = $row['Email'];
           $_SESSION['User_Role'] = $row['User_Role']; 

            // remember email
            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }

            // Redirect based on role
            if ($role == "ADMIN") {
                header("Location: ../AdminPanel/layout.php");
                exit();
            }
            elseif ($role == "DELIVERY_BOY") {
                header("Location: ../DeliveryBoyPanel/layout.php");
                exit();
            }
            else {
                header("Location: ../home page/index.php");
                exit();
            }
        }
    }

    $error = "Invalid Email, Password or Role!";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="../home page/style.css" />
<style>

input, select {
    width:100%; padding:10px; margin:10px 0;
    border:1px solid #ccc; border-radius:5px;
}
.btn {
    width:100%; padding:12px; background:#7e2626d5;
    color:#fff; border:none; border-radius:5px;
}
.btn:hover { background:black; }
.error { color:red; } 
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
</head>
<body>

<?php include("../home page/navbar.php"); ?>

<div class="form-box">
<h2 style="text-align:center;">Login</h2>

<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

    <!--  ROLE SELECTION -->
    <select name="role" required>
        <option value="">--Select Role-- </option>
        <option value="CUSTOMER">Customer</option>
        <option value="ADMIN">Admin</option>
        <option value="DELIVERY_BOY">Delivery Boy</option>
    </select>

    <input type="email" name="email" placeholder="Email" required value="<?= $rememberedEmail ?>">
    <input type="password" name="password" placeholder="Password" required>

    <button class="btn">Login</button>

   <div class="extra-options">
    <label>
        <input type="checkbox" name="remember">
        <span>Remember me</span>
    </label>

    <a href="forget_password.php">Forgot Password?</a>
</div>

</form>
<br>
<p style="text-align:center;">
Don't have an account?
<a href="../registration/registration.php">Register here</a>
</p>
</div>

<?php include("../home page/footer.php"); ?>
<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 <style>
        body { font-family: Arial; background: white; margin:0; }
        .form-box {
            width: 400px; margin: 30px auto; padding: 35px;
             border:1px solid #7e2626d5;;
            background: white; border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
           
        }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        .btn {
            width: 100%; padding: 12px; background:#7e2626d5; 
            color: #fff; border: none; border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover { background: black;color:white; }
        .error { color: red; margin-bottom: 10px; }
        a { color: brown; text-decoration:none; }
        .extra-options {
            display: flex; justify-content: space-between;
            align-items: center; margin-top: 5px;
        }
        .extra-options label {
            display: flex; align-items:center;
            gap: 6px; cursor:pointer;
        }
        .extra-options input[type="checkbox"] {
            width: auto;    
            margin: 0;     
        }
        .login-icon {
        text-align: center;
        font-size: 70px;
        color: #7e2626d5;
        margin-bottom: 10px;
        }

        .login-title {
        text-align: center;
        margin: 0;
        font-size: 28px;
        font-weight: bold;
        color: #333;
        }
</style>