<?php
if (!isset($_SESSION)) session_start();

include("../db.php");

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

// Fetch all areas
$areas = mysqli_query($connection, "SELECT * FROM area_details ORDER BY Area_Name");

if (isset($_POST['add'])) {

    $first    = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last     = mysqli_real_escape_string($connection, $_POST['last_name']);
    $dob      = mysqli_real_escape_string($connection, $_POST['dob']);
    $phone    = mysqli_real_escape_string($connection, $_POST['phone']);
    $address  = mysqli_real_escape_string($connection, $_POST['address']);
    $area_id  = (int)$_POST['area_id'];
    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $status   = mysqli_real_escape_string($connection, $_POST['status']);

    // Check email exists
    $check = mysqli_query($connection, "SELECT * FROM user_details WHERE Email='$email'");
    if (mysqli_num_rows($check) == 0) {

        mysqli_query($connection, "
            INSERT INTO user_details
            (First_Name, Last_Name, DOB, User_Role, Phone, Address, Area_Id, Email, Password, Status)
            VALUES
            ('$first','$last','$dob','DELIVERY_BOY','$phone','$address','$area_id','$email','$password','$status')
        ");

        header("Location: ../layout.php?view=delivery_boys&msg=added");
        exit;
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

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control"
                       required pattern="[A-Za-z]+"
                       title="Only alphabets allowed">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control"
                       required pattern="[A-Za-z]+"
                       title="Only alphabets allowed">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">DOB</label>
            <input type="date" name="dob" class="form-control"
                   required
                   max="<?= date('Y-m-d', strtotime('-17 years')) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control"
                   required pattern="[0-9]{10}"
                   maxlength="10"
                   title="Exactly 10 digits">
        </div>

        <div class="mb-3">
            <label class="form-label">Full Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>

        <!-- ✅ SELECT AREA (ONLY CHANGE) -->
        <div class="mb-3">
            <label class="form-label">Select Area</label>
            <select name="area_id" class="form-control" required>
                <option value="">-- Select Area --</option>
                <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                    <option value="<?= $row['Area_Id']; ?>">
                        <?= $row['Area_Name']; ?> (<?= $row['Pincode']; ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Email ID</label>
            <input type="email" name="email" class="form-control"
                   required
                   pattern="[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)"
                   title="Only Gmail or Yahoo email allowed">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control"
                   required maxlength="10">
        </div>

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
