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

$message = "";

// UPDATE PROCESS
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['First_Name']);
    $lname    = mysqli_real_escape_string($connection, $_POST['Last_Name']);
    $dob      = mysqli_real_escape_string($connection, $_POST['DOB']);
    $phone    = mysqli_real_escape_string($connection, $_POST['Phone']);
    $address  = mysqli_real_escape_string($connection, $_POST['Address']);
    $pincode  = mysqli_real_escape_string($connection, $_POST['Pincode']);
    $email    = mysqli_real_escape_string($connection, $_POST['Email']);
    $password = mysqli_real_escape_string($connection, $_POST['Password']);
    $status   = mysqli_real_escape_string($connection, $_POST['Status']);

    // ---------------- VALIDATIONS ---------------- //

    // Name validations
    if (!preg_match("/^[A-Za-z]+$/", $fname)) {
        $message = "<div class='alert alert-danger'>First Name must contain only letters.</div>";
    } else if (!preg_match("/^[A-Za-z]+$/", $lname)) {
        $message = "<div class='alert alert-danger'>Last Name must contain only letters.</div>";
    }
    // Address
    else if (!preg_match("/^[A-Za-z ]+$/", $address)) {
        $message = "<div class='alert alert-danger'>Address must contain only letters and spaces.</div>";
    }
    // Age 18+
    else {
        $age = (int)((time() - strtotime($dob)) / (365*24*60*60));
        if ($age < 18) {
            $message = "<div class='alert alert-danger'>Delivery boy must be 18+ years old.</div>";
        }
    }

    // Phone
    if ($message === "" && !preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "<div class='alert alert-danger'>Phone must be exactly 10 digits.</div>";
    }

    // Pincode
    if ($message === "" && !preg_match("/^[0-9]{6}$/", $pincode)) {
        $message = "<div class='alert alert-danger'>Pincode must be 6 digits.</div>";
    }

    // Email validation (same as add_delivery_boy)
    if ($message === "" &&
        !preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]{5,}\.[A-Za-z]{2,3}$/", $email)
    ) {
        $message = "<div class='alert alert-danger'>Invalid Email Format!</div>";
    }

    // ---------------- END VALIDATIONS ---------------- //

    if ($message === "") {

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
            $message = "<div class='alert alert-danger'>Update Failed: " . mysqli_error($connection) . "</div>";
        }
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

    <?= $message ?>

    <h2 class="fw-bold mb-3">
        <i class="fa-solid fa-motorcycle"></i> Edit Delivery Boy
    </h2>

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>First Name</label>
                <input type="text" name="First_Name" class="form-control" required
                       value="<?= $data['First_Name'] ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Last Name</label>
                <input type="text" name="Last_Name" class="form-control" required
                       value="<?= $data['Last_Name'] ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>DOB</label>
                <input type="date" name="DOB" class="form-control" required
                       value="<?= $data['DOB'] ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Phone</label>
                <input type="text" name="Phone" maxlength="10" class="form-control" required
                       value="<?= $data['Phone'] ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="Address" class="form-control" required
                   value="<?= $data['Address'] ?>">
        </div>

        <div class="mb-3">
            <label>Pincode</label>
            <input type="text" name="Pincode" maxlength="6" class="form-control" required
                   value="<?= $data['Pincode'] ?>">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="Email" class="form-control" required
                   value="<?= $data['Email'] ?>">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="text" name="Password" class="form-control" required
                   value="<?= $data['Password'] ?>">
        </div>

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
