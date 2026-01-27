<?php
/********************************************************
 * DELIVERY BOY PROFILE (SAFE + CLEAN)
 ********************************************************/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../AdminPanel/db.php';

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['User_Id'])) {
    echo "<p style='color:red'>Unauthorized access</p>";
    exit;
}

$deliveryBoyId = (int) $_SESSION['User_Id'];
$error = '';
$success = false;

/* ================= FETCH USER DATA ================= */
$result = mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $deliveryBoyId LIMIT 1"
);

$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<p style='color:red'>Delivery boy not found</p>";
    exit;
}

/* ================= FORM SUBMIT ================= */
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

    /* ===== PASSWORD UPDATE ===== */
    if ($current || $new || $confirm) {

        if ($current !== $user['Password']) {
            $error = "Current password is incorrect!";
        } elseif ($new !== $confirm) {
            $error = "New password and confirm password do not match!";
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

        /* REFRESH DATA */
        $result = mysqli_query(
            $connection,
            "SELECT * FROM user_details WHERE User_Id = $deliveryBoyId LIMIT 1"
        );
        $user = mysqli_fetch_assoc($result);

        $success = true;
    }
}

/* ================= FETCH AREAS ================= */
$areas = mysqli_query(
    $connection,
    "SELECT Area_Id, Area_Name FROM area_details"
);
?>
<style>
/* Labels */
.account-content label {
    display: block;
    margin-top: 15px;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 15px;
    color: #111827;
}

/* Inputs & Select */
.account-content input,
.account-content select,
.account-content textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}

/* Focus effect */
.account-content input:focus,
.account-content select:focus,
.account-content textarea:focus {
    border-color: #7e2626d5;
    box-shadow: 0 0 0 2px rgba(126, 38, 38, 0.15);
}

/* Disabled email */
.account-content input[disabled] {
    background: #f3f4f6;
    cursor: not-allowed;
}

/* Password section */
.account-content h3 {
    margin-top: 25px;
    font-size: 18px;
    color: #111827;
}

/* Button */
.account-content button {
    margin-top: 22px;
    padding: 12px 32px;
    border: none;
    background: #7e2626d5;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: 0.2s;
}

.account-content button:hover {
    background: black;
}

/* Horizontal line */
.account-content hr {
    margin: 25px 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}
</style>

<h3>Edit Delivery Boy Profile</h3>

<?php if ($error): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green">Profile updated successfully</p>
<?php endif; ?>

<form method="post">

    <label>First Name</label>
    <input type="text" name="fname" value="<?= htmlspecialchars($user['First_Name']) ?>" required>

    <label>Last Name</label>
    <input type="text" name="lname" value="<?= htmlspecialchars($user['Last_Name']) ?>" required>

    <label>Date of Birth</label>
    <input type="date" name="dob" value="<?= htmlspecialchars($user['DOB']) ?>">

    <label>Email</label>
    <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>">

    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($user['Address']) ?>">

    <label>Area</label>
    <select name="area_id" required>
        <?php while ($area = mysqli_fetch_assoc($areas)): ?>
            <option value="<?= $area['Area_Id'] ?>"
                <?= $area['Area_Id'] == $user['Area_Id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($area['Area_Name']) ?>
            </option>
        <?php endwhile; ?>
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
