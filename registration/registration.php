<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Shop - Registration</title>

    <!-- Link CSS -->
    <link rel="stylesheet" href="register.css">
</head>

<body>

<div class="register-card">
    <h2>Create Account</h2>

    <form action="register.php" method="POST">

        <div class="input-box">
            <input type="text" name="first_name" placeholder="First Name" required>
        </div>

        <div class="input-box">
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="input-box">
            <input type="date" name="dob" required>
        </div>

        <div class="input-box">
            <input type="text" name="phone" placeholder="Phone Number" required>
        </div>

        <div class="input-box">
            <input type="text" name="address" placeholder="Address" required>
        </div>

        <div class="input-box">
            <input type="text" name="pincode" placeholder="Pincode" required>
        </div>

        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <!-- Hidden User Role -->
        <input type="hidden" name="role" value="customer">

        <button type="submit" class="register-btn">Register</button>

        <p class="login-info">
            Already have an account?
            <a href="login.html">Login here</a>
        </p>

    </form>

</div>

</body>
</html>
