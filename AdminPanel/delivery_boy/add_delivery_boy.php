<?php
if (!isset($_SESSION)) session_start();

include("../db.php");

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

$message = "";

if (isset($_POST['add'])) {

    $first = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last  = mysqli_real_escape_string($connection, $_POST['last_name']);
    $dob   = mysqli_real_escape_string($connection, $_POST['dob']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $pincode = mysqli_real_escape_string($connection, $_POST['pincode']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']); // TEXT storage
    $status = mysqli_real_escape_string($connection, $_POST['status']); // NEW

    // Check if email already exists
    $check = mysqli_query($connection, "SELECT * FROM user_details WHERE Email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Email already exists!</div>";
    } else {

        // Insert Delivery Boy
        $query = "INSERT INTO user_details
        (First_Name, Last_Name, DOB, User_Role, Phone, Address, Pincode, Email, Password, Status)
        VALUES
        ('$first', '$last', '$dob', 'DELIVERY_BOY', '$phone', '$address', '$pincode', '$email', '$password', '$status')";

        if (mysqli_query($connection, $query)) {
            header("Location: ../layout.php?view=delivery_boys&msg=added");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Failed to add delivery boy!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Delivery Boy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">
<div class="container col-md-7">

    <h3 class="mb-4">Add Delivery Boy</h3>

    <?= $message ?>

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">DOB</label>
            <input type="date" name="dob" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" maxlength="13" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" maxlength="6" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email ID</label>
            <input type="email" name="email" maxlength="40" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" maxlength="10" class="form-control" required>
        </div>

        <!--NEW STATUS FIELD -->
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="ENABLE">ENABLE</option>
                <option value="DISABLE">DISABLE</option>
            </select>
        </div>

        <button type="submit" name="add" class="btn btn-success">Add Delivery Boy</button>
        <a href="delivery_boys.php" class="btn btn-secondary">Back</a>

    </form>

</div>
</body>
</html>
