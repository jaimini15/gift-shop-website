<?php
if (!isset($_SESSION)) session_start();

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

include(__DIR__ . '/../db.php');

// Fetch admin details with area
$admin_id = $_SESSION['admin_id'];
$admin = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $admin_id LIMIT 1"
));

// Fetch all areas
$areas = mysqli_query($connection, "SELECT * FROM area_details ORDER BY Area_Name");

$error = "";

// Update profile and password
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['fname']);
    $lname    = mysqli_real_escape_string($connection, $_POST['lname']);
    $dob      = $_POST['dob'];
    $phone    = $_POST['phone'];
    $address  = mysqli_real_escape_string($connection, $_POST['address']);
    $area_id  = (int)$_POST['area_id'];
    $email    = $_POST['email'];

    // Password fields
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    /* ================= PASSWORD LOGIC (UNCHANGED) ================= */

    $db_pass = $admin['Password'];

    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {

        if ($current_password !== $db_pass) {
            $error = "Current password is incorrect!";
        }
        elseif ($new_password !== $confirm_password) {
            $error = "New Password and Confirm Password do not match!";
        }
        else {
            $plain_pass = mysqli_real_escape_string($connection, $new_password);
            mysqli_query(
                $connection,
                "UPDATE user_details SET Password='$plain_pass' WHERE User_Id=$admin_id"
            );
        }
    }

    /* ================= UPDATE PROFILE ================= */

    if (empty($error)) {

        mysqli_query(
            $connection,
            "UPDATE user_details SET 
                First_Name='$fname',
                Last_Name='$lname',
                DOB='$dob',
                Phone='$phone',
                Address='$address',
                Area_Id='$area_id',
                Email='$email'
            WHERE User_Id=$admin_id"
        );

        header("Location: ../layout.php?view=admin_profile&success=Profile updated successfully");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
.edit-card {
    max-width: 850px;
    margin: auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
.form-label { font-weight: 600; }
.form-control { height: 45px; border-radius: 8px; }
.btn-primary { padding: 10px 20px; font-size: 16px; border-radius: 8px; }
</style>
</head>

<body>

<div class="container mt-4">
<div class="edit-card">

<h3 class="text-center mb-4">
    <i class="fa-solid fa-user-pen"></i> Edit Profile
</h3>

<?php if (!empty($error)) { ?>
    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <input type="text" name="fname" class="form-control"
               required pattern="[A-Za-z]+"
               value="<?= $admin['First_Name']; ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" name="lname" class="form-control"
               required pattern="[A-Za-z]+"
               value="<?= $admin['Last_Name']; ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" class="form-control"
               max="<?= date('Y-m-d', strtotime('-17 years')) ?>"
               value="<?= $admin['DOB']; ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
               pattern="[0-9]{10}" maxlength="10"
               value="<?= $admin['Phone']; ?>">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control"
               value="<?= $admin['Address']; ?>">
    </div>

    <!-- ✅ SELECT AREA (ONLY CHANGE) -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Select Area</label>
        <select name="area_id" class="form-control" required>
            <option value="">-- Select Area --</option>
            <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                <option value="<?= $row['Area_Id']; ?>"
                    <?= ($row['Area_Id'] == $admin['Area_Id']) ? 'selected' : ''; ?>>
                    <?= $row['Area_Name']; ?> (<?= $row['Pincode']; ?>)
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               required
               pattern="[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)"
               value="<?= $admin['Email']; ?>">
    </div>
</div>

<hr>
<h5 class="mt-3">Change Password</h5>

<div class="mb-3">
    <label class="form-label">Current Password</label>
    <input type="password" name="current_password" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">New Password</label>
    <input type="password" name="new_password" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Confirm New Password</label>
    <input type="password" name="confirm_password" class="form-control">
</div>

<button type="submit" name="update" class="btn btn-primary">
    <i class="fa-solid fa-check"></i> Save Changes
</button>

</form>
</div>
</div>

</body>
</html>
