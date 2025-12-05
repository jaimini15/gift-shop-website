<?php
session_start();
include("../AdminPanel/db.php");

$isPopup = isset($embedded) ? true : false;
$email_val = "";

// AJAX LOGIN REQUEST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $email_val = $email;

    $query = "SELECT * FROM user_details WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if ($password === $user['Password']) {

            // STORE USER SESSION
            $_SESSION['User_Id']     = $user['User_Id'];
            $_SESSION['First_Name']  = $user['First_Name'];
            $_SESSION['Last_Name']   = $user['Last_Name'];
            $_SESSION['DOB']         = $user['DOB'];
            $_SESSION['User_Role']   = $user['User_Role'];
            $_SESSION['Phone']       = $user['Phone'];
            $_SESSION['Address']     = $user['Address'];
            $_SESSION['Pincode']     = $user['Pincode'];
            $_SESSION['Email']       = $user['Email'];
            $_SESSION['Create_At']   = $user['Create_At'];

            // REDIRECT PAGE (after Buy Now)
            $redirect = isset($_SESSION['redirect_after_login'])
                        ? $_SESSION['redirect_after_login']
                        : "../product_page/product_list.php";

            unset($_SESSION['redirect_after_login']);

            echo json_encode([
                "success" => true,
                "message" => "Login Successful!",
                "redirect" => $redirect
            ]);
            exit();
        }
    }

    echo json_encode([
        "success" => false,
        "message" => "Invalid Email or Password! Please try again."
    ]);
    exit();
}
?>

<?php if (!$isPopup): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Gift Shop</title>
<link rel="stylesheet" href="../home page/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<?php endif; ?>

<style>
/* ——— YOUR SAME DESIGN, NOT TOUCHED ——— */
body { margin:0; font-family:Arial,sans-serif; }

.overlay {
    position: <?= $isPopup ? "fixed" : "relative" ?>;
    top:0; left:0; width:100%; height:100%;
    <?php if ($isPopup): ?>
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(8px);
    <?php endif; ?>
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:9999;
}

.login-card {
    width:380px;
    background:#fff;
    padding:35px 30px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
    text-align:center;
    position:relative;
}

.logo-login { font-size:70px; color:#d36b5e; margin-bottom:10px; }
h2 { margin:0 0 20px; color:#333; font-size:26px; }

.input-box { margin:15px 0; }
.input-box input {
    width:100%;
    padding:12px 14px;
    border:1px solid #ddd;
    background:#f3f3f3;
    border-radius:8px;
    font-size:16px;
}

.login-btn {
    width:100%;
    padding:12px;
    background:#b35d52;
    border:none;
    border-radius:8px;
    color:#fff;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
}
.login-btn:hover { background:#9e4f45; }

.row-rem_for {
    display:flex;
    justify-content:space-between;
    margin-top:15px;
    font-size:14px;
}

.register-link { margin-top:20px; font-size:14px; }
.register-link a { color:#b35d52; font-weight:bold; }

.error-msg {
    color:red;
    margin-bottom:10px;
    font-size:14px;
    text-align:center;
}

<?php if($isPopup): ?>
.close-btn {
    position:absolute;
    top:12px;
    right:12px;
    font-size:22px;
    cursor:pointer;
    color:#444;
}
<?php endif; ?>
</style>

<?php if (!$isPopup): ?>
</head>
<body>
<?php endif; ?>

<div class="overlay">
    <div class="login-card">

        <?php if($isPopup): ?>
        <div class="close-btn" onclick="
            document.getElementById('login-popup').style.display='none';
            document.getElementById('blur-overlay').style.display='none';
        ">✖</div>
        <?php endif; ?>

        <div class="logo-login"><i class="fa-solid fa-user-circle"></i></div>
        <h2>Login</h2>

        <div id="errorBox" class="error-msg" style="display:none;"></div>

        <form id="loginForm" autocomplete="off">
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($email_val) ?>">
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>

            <div class="row-rem_for">
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <a href="../login/forgot_password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <p class="register-link">Don't have an account?
                <a href="" onclick="showRegister(); return false;">Register here</a>
            </p>
        </form>

    </div>
</div>



<script>
// AJAX LOGIN HANDLER
document.getElementById("loginForm").addEventListener("submit", function(e){
    e.preventDefault();

    let formData = new FormData(this);

    fetch("../login/login.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

            // SHOW SUCCESS POPUP
            alert(data.message);

            // REDIRECT TO product_list.php OR SAVED PAGE
            window.location.href = data.redirect;
        } else {
            let box = document.getElementById("errorBox");
            box.style.display = "block";
            box.textContent = data.message;
        }
    });
});
</script>
<!-- Register Popup Wrapper -->
<div id="register-popup-wrapper" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); backdrop-filter:blur(6px); 
     z-index:10000; justify-content:center; align-items:center;">
    
    <div id="register-popup"></div>
</div>

<script>
function showRegister() {

    fetch("../registration/registration.php?embedded=1")
        .then(res => res.text())
        .then(html => {

            // SHOW POPUP
            document.getElementById("register-popup-wrapper").style.display = "flex";

            // LOAD REGISTRATION PAGE INSIDE
            document.getElementById("register-popup").innerHTML = html;
        });
}
</script>


<?php if (!$isPopup): ?>
</body>
</html>
<?php endif; ?>
