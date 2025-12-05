<?php
session_start();

/* Detect popup mode */
$isPopup = isset($embedded);
?>

<?php if (!$isPopup): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Gift Shop</title>
<link rel="stylesheet" href="register.css">
<?php endif; ?>

<style>
<?php if ($isPopup): ?>
/* Popup Blur Overlay */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(10px);
    background: rgba(0,0,0,0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* Close Button */
.close-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    font-size: 20px;
    cursor: pointer;
}
<?php endif; ?>
</style>

<?php if (!$isPopup): ?>
</head>
<body>
<?php endif; ?>


<!-- REGISTRATION POPUP / PAGE -->
<div class="overlay">

    <div class="register-card">

        <?php if ($isPopup): ?>
        <div class="close-btn"
             onclick="
                document.getElementById('register-popup').style.display='none';
                document.getElementById('blur-overlay').style.display='none';
             ">
            ✖
        </div>
        <?php endif; ?>

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

            <!-- Default role for user -->
            <input type="hidden" name="role" value="CUSTOMER">

            <button type="submit" class="register-btn">Register</button>

            <p class="login-info">
                Already have an account?
                <a href="#"
                   onclick="
                        document.getElementById('register-popup').style.display='none';
                        document.getElementById('login-popup').style.display='flex';
                   ">
                    Login here
                </a>
            </p>

        </form>
    </div>

</div>

<?php if (!$isPopup): ?>
</body>
</html>
<?php endif; ?>
