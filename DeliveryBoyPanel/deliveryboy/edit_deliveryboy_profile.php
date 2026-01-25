<?php
/* ================= DB ================= */
require_once __DIR__ . '/../../AdminPanel/db.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['User_Id']) || $_SESSION['Role'] !== 'DELIVERY') {
    echo "<div class='alert alert-danger'>Unauthorized access</div>";
    return;
}

$deliveryBoyId = (int) $_SESSION['User_Id'];
$error = "";
$success = "";

/* ================= FETCH PROFILE ================= */
$res = mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $deliveryBoyId LIMIT 1"
);
$delivery_boy = mysqli_fetch_assoc($res);

if (!$delivery_boy) {
    echo "<div class='alert alert-danger'>Profile not found</div>";
    return;
}

/* ================= FETCH AREAS ================= */
$areas = mysqli_query(
    $connection,
    "SELECT Area_Id, Area_Name, Pincode FROM area_details ORDER BY Area_Name"
);

/* ================= UPDATE PROFILE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fname   = mysqli_real_escape_string($connection, $_POST['fname']);
    $lname   = mysqli_real_escape_string($connection, $_POST['lname']);
    $dob     = $_POST['dob'];
    $phone   = $_POST['phone'];
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $area_id = (int) $_POST['area_id'];

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    /* ===== PASSWORD CHANGE (OPTIONAL) ===== */
    if ($current || $new || $confirm) {

        if ($current !== $delivery_boy['Password']) {
            $error = "Current password is incorrect.";
        } elseif ($new !== $confirm) {
            $error = "New password and confirm password do not match.";
        } else {
            mysqli_query(
                $connection,
                "UPDATE user_details 
                 SET Password='$new' 
                 WHERE User_Id=$deliveryBoyId"
            );
        }
    }

    /* ===== PROFILE UPDATE ===== */
    if (empty($error)) {

        mysqli_query(
            $connection,
            "UPDATE user_details SET
                First_Name='$fname',
                Last_Name='$lname',
                DOB='$dob',
                Phone='$phone',
                Address='$address',
                Area_Id=$area_id
             WHERE User_Id=$deliveryBoyId"
        );

        $success = "Profile updated successfully.";

        // Refresh data
        $res = mysqli_query(
            $connection,
            "SELECT * FROM user_details WHERE User_Id = $deliveryBoyId LIMIT 1"
        );
        $delivery_boy = mysqli_fetch_assoc($res);
    }
}
?>

<!-- ================= CONTENT ONLY ================= -->

<h3 class="fw-bold mb-4">
    <i class="fa-solid fa-user"></i> My Profile
</h3>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="fname" class="form-control"
                   value="<?= htmlspecialchars($delivery_boy['First_Name']) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lname" class="form-control"
                   value="<?= htmlspecialchars($delivery_boy['Last_Name']) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control"
                   value="<?= $delivery_boy['DOB'] ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($delivery_boy['Phone']) ?>">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control"
                   value="<?= htmlspecialchars($delivery_boy['Address']) ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Area</label>
            <select name="area_id" class="form-control" required>
                <?php while ($area = mysqli_fetch_assoc($areas)): ?>
                    <option value="<?= $area['Area_Id'] ?>"
                        <?= ($area['Area_Id'] == $delivery_boy['Area_Id']) ? 'selected' : '' ?>>
                        <?= $area['Area_Name'] ?> (<?= $area['Pincode'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control"
                   value="<?= htmlspecialchars($delivery_boy['Email']) ?>" disabled>
        </div>

    </div>

    <hr>

    <h6 class="fw-bold">Change Password (Optional)</h6>

    <input type="password" name="current_password" class="form-control mb-2"
           placeholder="Current Password">

    <input type="password" name="new_password" class="form-control mb-2"
           placeholder="New Password">

    <input type="password" name="confirm_password" class="form-control mb-3"
           placeholder="Confirm New Password">

    <button class="btn btn-primary">
        <i class="fa-solid fa-check"></i> Save Changes
    </button>

</form>
