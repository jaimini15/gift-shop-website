<?php
if (!isset($_SESSION)) session_start();
include("../AdminPanel/db.php");

/* ================= ADMIN AUTH CHECK ================= */


// $adminId = $_SESSION["admin_id"];

/* ================= FETCH ADMIN DATA ================= */
$result = mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id='$adminId' LIMIT 1"
);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("Admin not found");
}

/* ================= FETCH AREAS ================= */
$areas = mysqli_query($connection, "SELECT Area_Id, Area_Name FROM area_details");

/* ================= UPDATE PROFILE ================= */
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fname   = mysqli_real_escape_string($connection, $_POST['fname']);
    $lname   = mysqli_real_escape_string($connection, $_POST['lname']);
    $dob     = $_POST['dob'];
    $phone   = $_POST['phone'];
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $area_id = (int)$_POST['area_id'];

    // Password
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    /* ===== PASSWORD LOGIC ===== */
    if (!empty($current) || !empty($new) || !empty($confirm)) {

        if ($current !== $admin['Password']) {
            $error = "Current password is incorrect!";
        } elseif ($new !== $confirm) {
            $error = "New password and confirm password do not match!";
        } else {
            mysqli_query(
                $connection,
                "UPDATE user_details SET Password='$new' WHERE User_Id='$adminId'"
            );
        }
    }

    /* ===== UPDATE PROFILE ===== */
    if (empty($error)) {

        mysqli_query(
            $connection,
            "UPDATE user_details SET
                First_Name='$fname',
                Last_Name='$lname',
                DOB='$dob',
                Phone='$phone',
                Address='$address',
                Area_Id='$area_id'
            WHERE User_Id='$adminId'"
        );

        header("Location: admin_edit_profile.php?success=1");
        exit();
    }
}

/* ================= LEFT PANEL ACTIVE PAGE ================= */
$activePage = "profile";

?>

<h2>Edit Admin Profile</h2>

<?php if (!empty($error)) { ?>
    <p style="color:red"><?= $error ?></p>
<?php } ?>

<?php if (isset($_GET['success'])) { ?>
    <p style="color:green">Profile updated successfully</p>
<?php } ?>

<form method="post">

    <label>First Name</label>
    <input type="text" name="fname"
           value="<?= htmlspecialchars($admin['First_Name']) ?>" required>

    <label>Last Name</label>
    <input type="text" name="lname"
           value="<?= htmlspecialchars($admin['Last_Name']) ?>" required>

    <label>Date of Birth</label>
    <input type="date" name="dob"
           value="<?= htmlspecialchars($admin['DOB']) ?>">

    <label>Email</label>
    <input type="email"
           value="<?= htmlspecialchars($admin['Email']) ?>" disabled>

    <label>Phone</label>
    <input type="text" name="phone"
           value="<?= htmlspecialchars($admin['Phone']) ?>">

    <label>Address</label>
    <input type="text" name="address"
           value="<?= htmlspecialchars($admin['Address']) ?>">

    <label>Area</label>
    <select name="area_id" required>
        <?php while ($area = mysqli_fetch_assoc($areas)) {
            $selected = ($area['Area_Id'] == $admin['Area_Id']) ? 'selected' : '';
            echo "<option value='{$area['Area_Id']}' $selected>
                    {$area['Area_Name']}
                  </option>";
        } ?>
    </select>

    <hr>

    <h3>Change Password</h3>

    <label>Current Password</label>
    <input type="password" name="current_password">

    <label>New Password</label>
    <input type="password" name="new_password">

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password">

    <button type="submit">Save Changes</button>
</form>

</div> <!-- account-content -->
</div> <!-- account-wrapper -->

<?php require_once '../home page/footer.php'; ?>
