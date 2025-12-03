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

// Update profile
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

.section-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #333;
}

.form-label {
    font-weight: 600;
}

.form-control {
    height: 45px;
    border-radius: 8px;
}

.btn-primary {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
}

.btn-secondary {
    padding: 10px 20px;
    border-radius: 8px;
}

input[type="date"] {
    padding-left: 10px;
}
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
                           value="<?php echo $admin['First_Name']; ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lname" class="form-control" 
                           value="<?php echo $admin['Last_Name']; ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" 
                           value="<?php echo $admin['DOB']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?php echo $admin['Phone']; ?>">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control"
                           value="<?php echo $admin['Address']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control"
                           value="<?php echo $admin['Pincode']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo $admin['Email']; ?>" required>
                </div>
            </div>

                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Save Changes
                </button>
            </div>

        </form>
    </div>

</div>

</body>
</html>
