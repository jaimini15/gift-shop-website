<?php
if (!isset($_SESSION)) session_start();
include(__DIR__ . '/../db.php');

/* ================= ADMIN CHECK ================= */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== "ADMIN") {
    header("Location: ../admin_login/login.php?error=Please login first");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = (int)$_GET['id'];

/* ================= FETCH DELIVERY BOY ================= */
$res = mysqli_query($connection, "
    SELECT * FROM user_details 
    WHERE User_Id=$id AND User_Role='DELIVERY_BOY'
");
$data = mysqli_fetch_assoc($res);

if (!$data) {
    die("Delivery Boy not found");
}

/* ================= FETCH AREAS + ASSIGN STATUS ================= */
$areas = mysqli_query($connection, "
    SELECT 
        ad.Area_Id,
        ad.Area_Name,
        ad.Pincode,
        CASE 
            WHEN dam.delivery_boy_id = $id THEN 'SELF'
            WHEN dam.delivery_boy_id IS NOT NULL THEN 'OTHER'
            ELSE 'NONE'
        END AS assign_status
    FROM area_details ad
    LEFT JOIN delivery_area_map dam 
        ON ad.Area_Id = dam.area_id
");

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $fname    = mysqli_real_escape_string($connection, $_POST['First_Name'] ?? '');
    $lname    = mysqli_real_escape_string($connection, $_POST['Last_Name'] ?? '');
    $dob      = !empty($_POST['DOB']) ? mysqli_real_escape_string($connection, $_POST['DOB']) : NULL;
    $phone    = mysqli_real_escape_string($connection, $_POST['Phone'] ?? '');
    $address  = mysqli_real_escape_string($connection, $_POST['Address'] ?? '');
    $email    = mysqli_real_escape_string($connection, $_POST['Email'] ?? '');
    $password = mysqli_real_escape_string($connection, $_POST['Password'] ?? '');
    $status   = mysqli_real_escape_string($connection, $_POST['Status'] ?? 'ENABLE');

    /* MAIN AREA (required column) */
    $main_area = 0;
    if (!empty($_POST['areas'])) {
        $main_area = (int)$_POST['areas'][0];
    }

    mysqli_query($connection, "
        UPDATE user_details SET
            First_Name='$fname',
            Last_Name='$lname',
            DOB=" . ($dob ? "'$dob'" : "NULL") . ",
            Phone='$phone',
            Address='$address',
            Area_Id='$main_area',
            Email='$email',
            Password='$password',
            Status='$status'
        WHERE User_Id=$id
    ");

    /* UPDATE AREA MAP */
    mysqli_query($connection, "
        DELETE FROM delivery_area_map 
        WHERE delivery_boy_id=$id
    ");

    if (!empty($_POST['areas'])) {
        foreach ($_POST['areas'] as $area_id) {
            mysqli_query($connection, "
                INSERT INTO delivery_area_map (delivery_boy_id, area_id)
                VALUES ($id, $area_id)
            ");
        }
    }

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

        <!-- AREA ASSIGNMENT -->
        <div class="mb-3">
            <label>Assign Delivery Areas</label>
            <div class="border rounded p-3">
                <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="areas[]"
                               value="<?= $row['Area_Id']; ?>"
                               <?= ($row['assign_status']=='SELF') ? 'checked' : '' ?>
                               <?= ($row['assign_status']=='OTHER') ? 'disabled' : '' ?>>
                        <label class="form-check-label text-muted">
                            <?= $row['Area_Name']; ?> (<?= $row['Pincode']; ?>)
                            <?= ($row['assign_status']=='OTHER') ? ' - Assigned to another delivery boy' : '' ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="Email" class="form-control"
                   required pattern="[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)"
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
