<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$activePage = "password";
include("account_layout.php");
?>

<h2>Change Password</h2>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Password updated successfully ✅</p>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <p style="color:red;">
        <?php
            if ($_GET['error'] == 1) echo "Current password is incorrect";
            if ($_GET['error'] == 2) echo "New passwords do not match";
            if ($_GET['error'] == 3) echo "You already have this password";
            if ($_GET['error'] == 4) echo "User not found";
        ?>
    </p>
<?php endif; ?>


<form method="post" action="update_password.php">

    <label>Current Password</label>
    <input type="password" name="current_password" required>

    <label>New Password</label>
    <input type="password" name="new_password" required>

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" required>

    <button type="submit">Change Password</button>
</form>
</div>
</div>
<?php require_once '../home page/footer.php'; ?>
