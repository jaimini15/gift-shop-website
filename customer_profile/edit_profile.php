<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

$result = mysqli_query($connection,
    "SELECT * FROM user_details WHERE User_Id='$uid' LIMIT 1"
);
$profileUser = mysqli_fetch_assoc($result);

if (!$profileUser) {
    die("User not found");
}

$activePage = "profile";
include("account_layout.php");
?>

<h2>My Profile</h2>

<form method="post" action="update_profile.php">

    <label>First Name</label>
    <input type="text" name="fname"
           value="<?= htmlspecialchars($profileUser['First_Name']) ?>" required>

    <label>Last Name</label>
    <input type="text" name="lname"
           value="<?= htmlspecialchars($profileUser['Last_Name']) ?>" required>

    <label>Date of Birth</label>
    <input type="date" name="dob"
           value="<?= htmlspecialchars($profileUser['DOB']) ?>">

    <label>Email</label>
    <input type="email"
           value="<?= htmlspecialchars($profileUser['Email']) ?>" disabled>

    <label>Phone</label>
    <input type="text" name="phone"
           value="<?= htmlspecialchars($profileUser['Phone']) ?>">

    <label>Address</label>
    <input type="text" name="address"
           value="<?= htmlspecialchars($profileUser['Address']) ?>">

    <label>Pincode</label>
    <input type="text" name="pincode"
           value="<?= htmlspecialchars($profileUser['Pincode']) ?>">

    <button type="submit">Save Changes</button>
</form>
</div> <!-- account-content -->
</div> <!-- account-wrapper -->
<?php require_once '../home page/footer.php'; ?>

