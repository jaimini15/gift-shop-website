<?php
if (!isset($_SESSION)) session_start();

include(__DIR__ . '/../db.php');

// Only Admin allowed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);

// Fetch delivery boy
$query = "
    SELECT u.*, a.Pincode 
    FROM user_details u
    LEFT JOIN area_details a ON u.Area_Id = a.Area_Id
    WHERE u.User_Id=$id AND u.User_Role='DELIVERY_BOY'
";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Delivery Boy not found");
}

// Fetch all areas
$areas = mysqli_query($connection, "SELECT * FROM area_details ORDER BY Area_Name");

// UPDATE PROCESS
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['First_Name']);
    $lname    = mysqli_real_escape_string($connection, $_POST['Last_Name']);
    $dob      = mysqli_real_escape_string($connection, $_POST['DOB']);
    $phone    = mysqli_real_escape_string($connection, $_POST['Phone']);
    $address  = mysqli_real_escape_string($connection, $_POST['Address']);
    $area_id  = (int)$_POST['Area_Id'];
    $email    = mysqli_real_escape_string($connection, $_POST['Email']);
    $password = mysqli_real_escape_string($connection, $_POST['Password']);
    $status   = mysqli_real_escape_string($connection, $_POST['Status']);

    mysqli_query($connection, "
        UPDATE user_details SET
            First_Name='$fname',
            Last_Name='$lname',
            DOB='$dob',
            Phone='$phone',
            Address='$address',
            Area_Id='$area_id',
            Email='$email',
            Password='$password',
            Status='$status'
        WHERE User_Id=$id
    ");

    header("Location: ../layout.php?view=delivery_boys&msg=updated");
    exit;
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

    <h2 class="fw-bold mb-3">Edit Delivery Boy</h2>

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>First Name</label>
                <input type="text" name="First_Name" class="form-control"
                       required pattern="[A-Za-z]+"
                       value="<?= $data['First_Name'] ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Last Name</label>
                <input type="text" name="Last_Name" class="form-control"
                       required pattern="[A-Za-z]+"
                       value="<?= $data['Last_Name'] ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>DOB</label>
                <input type="date" name="DOB" class="form-control"
                       required
                       max="<?= date('Y-m-d', strtotime('-17 years')) ?>"
                       value="<?= $data['DOB'] ?>">
            </div>

            <div class="col-md-6 mb-3">
                <label>Phone</label>
                <input type="text" name="Phone" class="form-control"
                       required pattern="[0-9]{10}" maxlength="10"
                       value="<?= $data['Phone'] ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="Address" class="form-control" required
                   value="<?= $data['Address'] ?>">
        </div>

        <!-- ✅ SELECT AREA (ONLY CHANGE) -->
        <div class="mb-3">
            <label>Select Area</label>
            <select name="Area_Id" class="form-control" required>
                <option value="">-- Select Area --</option>
                <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                    <option value="<?= $row['Area_Id']; ?>"
                        <?= ($row['Area_Id'] == $data['Area_Id']) ? 'selected' : ''; ?>>
                        <?= $row['Area_Name']; ?> (<?= $row['Pincode']; ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="Email" class="form-control"
                   required
                   pattern="[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)"
                   value="<?= $data['Email'] ?>">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="text" name="Password" class="form-control"
                   required maxlength="10"
                   value="<?= $data['Password'] ?>">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="Status" class="form-control" required>
                <option value="ENABLE"  <?= ($data['Status']=='ENABLE')?'selected':'' ?>>ENABLE</option>
                <option value="DISABLE" <?= ($data['Status']=='DISABLE')?'selected':'' ?>>DISABLE</option>
            </select>
        </div>

        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="../layout.php?view=delivery_boys" class="btn btn-secondary">Back</a>

    </form>

</div>

</body>
</html>
