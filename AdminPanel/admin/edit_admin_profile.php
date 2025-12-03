<?php
if (!isset($_SESSION)) session_start();

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

include(__DIR__ . '/../db.php');

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$admin = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT * FROM user_details WHERE User_Id = $admin_id LIMIT 1"
));

// If form submitted
if (isset($_POST['update'])) {

    $fname   = mysqli_real_escape_string($connection, $_POST['fname']);
    $lname   = mysqli_real_escape_string($connection, $_POST['lname']);
    $dob     = mysqli_real_escape_string($connection, $_POST['dob']);
    $phone   = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $pincode = mysqli_real_escape_string($connection, $_POST['pincode']);
    $email   = mysqli_real_escape_string($connection, $_POST['email']);

    $update = "
        UPDATE user_details 
        SET 
            First_Name='$fname',
            Last_Name='$lname',
            DOB='$dob',
            Phone='$phone',
            Address='$address',
            Pincode='$pincode',
            Email='$email'
        WHERE User_Id=$admin_id
    ";

    if (mysqli_query($connection, $update)) {
        header("Location: ../layout.php?view=admin_profile&success=Profile updated successfully");
        exit;
    } else {
        $error = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Admin Profile</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-4">
    <div class="card shadow p-4" style="max-width: 650px; margin:auto;">
        <h3 class="text-center mb-3">Edit Profile</h3>

        <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <label>First Name</label>
            <input type="text" name="fname" class="form-control mb-2" value="<?php echo $admin['First_Name']; ?>" required>

            <label>Last Name</label>
            <input type="text" name="lname" class="form-control mb-2" value="<?php echo $admin['Last_Name']; ?>" required>

            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control mb-2" value="<?php echo $admin['DOB']; ?>">

            <label>Phone</label>
            <input type="text" name="phone" class="form-control mb-2" value="<?php echo $admin['Phone']; ?>">

            <label>Address</label>
            <input type="text" name="address" class="form-control mb-2" value="<?php echo $admin['Address']; ?>">

            <label>Pincode</label>
            <input type="text" name="pincode" class="form-control mb-2" value="<?php echo $admin['Pincode']; ?>">

            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" value="<?php echo $admin['Email']; ?>" required>

            <button type="submit" name="update" class="btn btn-primary w-100">
                Update Profile
            </button>

        </form>
    </div>
</div>

</body>
</html>
