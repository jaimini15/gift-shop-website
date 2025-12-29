<?php
session_start();
include("../AdminPanel/db.php");

// If already logged in redirect
if (isset($_SESSION['User_Id'])) {
    header("Location: ../home page/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fname    = $_POST['first_name'];
    $lname    = $_POST['last_name'];
    $dob      = $_POST['dob'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $pincode  = $_POST['pincode'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Insert into database
    $query = "INSERT INTO user_details 
              (First_Name, Last_Name, DOB, Phone, Address, Pincode, Email, Password, User_Role, Status)
              VALUES 
              ('$fname', '$lname', '$dob', '$phone', '$address', '$pincode', '$email', '$password', 'CUSTOMER', 'ENABLE')";

    mysqli_query($connection, $query);

    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - GiftShop</title>
<link rel="stylesheet" href="../home page/style.css">
<style>
    body { background: #f5f5f5; margin: 0; }

    .register-box {
        width: 450px;
        background: white;
        margin: 30px auto;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .register-box h2 {
        text-align: center;
        margin-bottom: 15px;
    }

    .register-box input {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        background: white;
    }

    .register-box button {
    width: 100%;
    padding: 12px;
    background: #7e2626d5;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.register-box button:hover {
    background:black;color:white;
}

.register-box p {
    text-align: center;
    margin-top: 10px;
}

.register-box a {
    color: #7e2626d5;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>
<body>

<?php include("../home page/navbar.php"); ?>  <!-- Navbar stays SAME -->

<div class="register-box">

    <h2>Create Account</h2>

    <form  action="register_check.php" method="POST">

        <input type="text" name="first_name" placeholder="First Name" 
       required pattern="[A-Za-z]+" title="Only alphabets allowed">

<input type="text" name="last_name" placeholder="Last Name" 
       required pattern="[A-Za-z]+" title="Only alphabets allowed">

<input type="date" name="dob" required 
       max="<?php echo date('Y-m-d', strtotime('-17 years')); ?>">
       
<input type="text" name="phone" placeholder="Phone Number" maxlength="10" 
       required pattern="[0-9]{10}" title="Exactly 10 digits">

<input type="text" name="address" placeholder="Address" required>

<input type="text" name="pincode" placeholder="Pincode" maxlength="6"
       required pattern="[0-9]{6}" title="Exactly 6 digits">

<div style="display:flex; gap:5px;">
    <input type="email" id="email" name="email"
           placeholder="Email Address"
           required
           pattern="^[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)$"
           title="Format: username@gmail.com"
           style="flex:1">

    <button type="button"
            onclick="sendOTP()"
            style="padding:10px; background:#7e2626d5; color:white; border:none; border-radius:5px;">
        Send OTP
    </button>
</div>

<input type="number" id="otp" placeholder="Enter OTP" style="display:none;">
<button type="button" id="verifyBtn" onclick="verifyOTP()" style="display:none;">Verify OTP</button>

<input type="hidden" id="otp_verified" name="otp_verified" value="0">



<input type="password" name="password" placeholder="Password" required>

        <!-- Hidden default role -->
        <input type="hidden" name="user_role" value="CUSTOMER">

        <button type="submit">Register</button>

        <p>Already have an account? <a href="../login/login.php">Login here</a></p>

    </form>
</div>

<?php include("../home page/footer.php"); ?>

</body>
</html>
