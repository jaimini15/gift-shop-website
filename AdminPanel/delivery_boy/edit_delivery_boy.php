<?php
include(__DIR__ . '/../db.php');

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);

// Fetch data
$query = "SELECT * FROM user_details WHERE User_Id = $id AND User_Role='DELIVERY_BOY'";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Delivery Boy not found");
}

// Update Logic
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['First_Name']);
    $lname    = mysqli_real_escape_string($connection, $_POST['Last_Name']);
    $dob      = mysqli_real_escape_string($connection, $_POST['DOB']);
    $phone    = mysqli_real_escape_string($connection, $_POST['Phone']);
    $address  = mysqli_real_escape_string($connection, $_POST['Address']);
    $pincode  = mysqli_real_escape_string($connection, $_POST['Pincode']);
    $email    = mysqli_real_escape_string($connection, $_POST['Email']);
    $password = mysqli_real_escape_string($connection, $_POST['Password']);
    $status   = mysqli_real_escape_string($connection, $_POST['Status']); // ADDED

    $update = "
        UPDATE user_details 
        SET First_Name='$fname',
            Last_Name='$lname',
            DOB='$dob',
            Phone='$phone',
            Address='$address',
            Pincode='$pincode',
            Email='$email',
            Password='$password',
            Status='$status'
        WHERE User_Id=$id
    ";

    if (mysqli_query($connection, $update)) {
        header("Location: ../layout.php?view=delivery_boys&msg=updated");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Update Failed: " . mysqli_error($connection) . "</div>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Delivery Boy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body { background: #f4f6f9; }
        .card-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 60%;
            margin: 30px auto;
        }
    </style>
</head>

<body>

<div class="card-box">
    <h2 class="fw-bold mb-3">
        <i class="fa-solid fa-motorcycle"></i> Edit Delivery Boy
    </h2>

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>First Name</label>
                <input type="text" name="First_Name" class="form-control" value="<?= $data['First_Name'] ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Last Name</label>
                <input type="text" name="Last_Name" class="form-control" value="<?= $data['Last_Name'] ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>DOB</label>
                <input type="date" name="DOB" class="form-control" value="<?= $data['DOB'] ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Phone</label>
                <input type="text" name="Phone" class="form-control" value="<?= $data['Phone'] ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="Address" class="form-control" value="<?= $data['Address'] ?>">
        </div>

        <div class="mb-3">
            <label>Pincode</label>
            <input type="text" name="Pincode" class="form-control" value="<?= $data['Pincode'] ?>">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="Email" class="form-control" value="<?= $data['Email'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="text" name="Password" class="form-control" value="<?= $data['Password'] ?>" required>
        </div>

        <!-- 🔥 ONLY NEW FIELD ADDED -->
        <div class="mb-3">
            <label>Status</label>
            <select name="Status" class="form-control">
                <option value="ENABLE"  <?= ($data['Status'] == 'ENABLE') ? 'selected' : '' ?>>ENABLE</option>
                <option value="DISABLE" <?= ($data['Status'] == 'DISABLE') ? 'selected' : '' ?>>DISABLE</option>
            </select>
        </div>

        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="../../layout.php?view=delivery_boys" class="btn btn-secondary">Back</a>

    </form>
</div>

</body>
</html>
