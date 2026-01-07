<?php
session_start();
include("../AdminPanel/db.php");

// If already logged in redirect
if (isset($_SESSION['User_Id'])) {
    header("Location: ../home page/index.php");
    exit();
}

// if ($_SERVER["REQUEST_METHOD"] === "POST") {

//     $fname    = $_POST['first_name'];
//     $lname    = $_POST['last_name'];
//     $dob      = $_POST['dob'];
//     $phone    = $_POST['phone'];
//     $address  = $_POST['address'];
//     $pincode  = $_POST['pincode'];
//     $email    = $_POST['email'];
//     $password = $_POST['password'];

//     // Insert into database
//     $query = "INSERT INTO user_details 
//               (First_Name, Last_Name, DOB, Phone, Address, Pincode, Email, Password, User_Role, Status)
//               VALUES 
//               ('$fname', '$lname', '$dob', '$phone', '$address', '$pincode', '$email', '$password', 'CUSTOMER', 'ENABLE')";

//     mysqli_query($connection, $query);

//     header("Location: ../login/login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register|GiftShop</title>
<link rel="stylesheet" href="../home page/style.css">
<style>
    body { background:white; margin: 0; }

    .register-box {
        width: 450px;
        background-color: #fff;
        margin: 30px auto;
        padding: 25px;
        border:1px solid #7e2626d5;
        border-radius: 15px;
        box-shadow: 2px 5px 10px rgba(0,0,0,0.2);
    }

    .register-box h2 {
        text-align: center;
        margin-bottom: 15px;
    }

    .register-box input,select {
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

<?php include("../home page/navbar.php"); ?>  

<div class="register-box">

    <h2>Create Account</h2>

    <form action="register_check.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" 
       required pattern="[A-Za-z]+" title="Only alphabets allowed">

<input type="text" name="last_name" placeholder="Last Name" 
       required pattern="[A-Za-z]+" title="Only alphabets allowed">

<input type="date" name="dob" required 
       max="<?php echo date('Y-m-d', strtotime('-17 years')); ?>">
       
<input type="text" name="phone" placeholder="Phone Number" maxlength="10" 
       required pattern="[0-9]{10}" title="Exactly 10 digits">

<input type="text" name="address" placeholder="Address" required>

<select name="area_id" required>
    <option value="">Select Area</option>
    <?php
    $area_q = mysqli_query($connection, "SELECT Area_Id, Area_Name FROM area_details");
    while ($row = mysqli_fetch_assoc($area_q)) {
        echo "<option value='{$row['Area_Id']}'>{$row['Area_Name']}</option>";
    }
    ?>
</select>
<input
    type="email"
    name="email"
    placeholder="name@example.com"
    required
    pattern="^[a-zA-Z0-9._%+-]+@(gmail|yahoo)\.(com|in)$"
>

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
