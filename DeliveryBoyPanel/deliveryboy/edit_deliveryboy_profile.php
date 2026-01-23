<?php
/* ================= SESSION ================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= DB CONNECTION ================= */
include(__DIR__ . '/../../AdminPanel/db.php');

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['User_Id'])) {
    echo "<div class='alert alert-danger m-3'>Unauthorized access</div>";
    exit;
}

$deliveryBoyId = (int) $_SESSION['User_Id'];

// Fetch delivery boy
$res = mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $delivery_boy_id LIMIT 1"
);
$delivery_boy = mysqli_fetch_assoc($res);

// Fetch areas for dropdown
$areas = mysqli_query(
    $connection,
    "SELECT Area_Id, Area_Name, Pincode FROM area_details ORDER BY Area_Name"
);

$error = "";

// Update profile and password (UNCHANGED LOGIC)
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['fname']);
    $lname    = mysqli_real_escape_string($connection, $_POST['lname']);
    $dob      = mysqli_real_escape_string($connection, $_POST['dob']);
    $phone    = mysqli_real_escape_string($connection, $_POST['phone']);
    $address  = mysqli_real_escape_string($connection, $_POST['address']);
    $area_id  = (int)$_POST['area_id'];
    $email    = mysqli_real_escape_string($connection, $_POST['email']);

    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $db_pass = $delivery_boy['Password'];

    if ($current_password !== '' || $new_password !== '' || $confirm_password !== '') {

        if ($current_password !== $db_pass) {
            $error = "Current password is incorrect!";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirm password do not match!";
        } else {
            $plain_pass = mysqli_real_escape_string($connection, $new_password);
            mysqli_query(
                $connection,
                "UPDATE user_details SET Password='$plain_pass' WHERE User_Id=$delivery_boy_id"
            );
        }
    }

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
            WHERE User_Id=$delivery_boy_id"
        );

        header("Location: ../layout.php?view=profile&success=Profile updated successfully");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Delivery Boy Profile</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
.edit-card { max-width:850px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.form-label { font-weight:600; }
.form-control { height:45px; border-radius:8px; }
.btn-primary { padding:10px 20px; font-size:16px; border-radius:8px; }
</style>
</head>

<body>
<div class="container mt-4">
<div class="edit-card">

<h3 class="text-center mb-4">
    <i class="fa-solid fa-user-pen"></i> Edit Profile
</h3>

<?php if (!empty($error)) { ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
<?php } ?>

<form method="POST">

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <input type="text" name="fname" class="form-control"
               required pattern="[A-Za-z]+"
               value="<?= htmlspecialchars($delivery_boy['First_Name']); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" name="lname" class="form-control"
               required pattern="[A-Za-z]+"
               value="<?= htmlspecialchars($delivery_boy['Last_Name']); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" class="form-control"
               required
               max="<?= date('Y-m-d', strtotime('-17 years')) ?>"
               value="<?= htmlspecialchars($delivery_boy['DOB']); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
               required pattern="[0-9]{10}" maxlength="10"
               value="<?= htmlspecialchars($delivery_boy['Phone']); ?>">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control"
               required
               value="<?= htmlspecialchars($delivery_boy['Address']); ?>">
    </div>

    <!-- ✅ AREA DROPDOWN (Area Name + Pincode) -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Select Area</label>
        <select name="area_id" class="form-control" required>
            <option value="">-- Select Area --</option>
            <?php while ($area = mysqli_fetch_assoc($areas)) { ?>
                <option value="<?= $area['Area_Id']; ?>"
                    <?= ($delivery_boy['Area_Id'] == $area['Area_Id']) ? 'selected' : ''; ?>>
                    <?= $area['Area_Name'] . " (" . $area['Pincode'].")"; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               required
               pattern="[a-zA-Z0-9._%+-]+@(gmail|yahoo)\.(com|in)"
               value="<?= htmlspecialchars($delivery_boy['Email']); ?>">
    </div>
</div>

<hr>
<h5 class="mt-3">Change Password (Optional)</h5>

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
