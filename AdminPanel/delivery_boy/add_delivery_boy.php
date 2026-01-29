<?php
if (!isset($_SESSION)) session_start();
include(__DIR__ . '/../../AdminPanel/db.php');

$msg = "";

/* ================= FETCH AREAS ================= */
$areas = mysqli_query($connection, "
    SELECT 
        ad.Area_Id,
        ad.Area_Name,
        ad.Pincode,
        IF(dam.area_id IS NULL, 0, 1) AS assigned_area
    FROM area_details ad
    LEFT JOIN delivery_area_map dam 
        ON ad.Area_Id = dam.area_id
");

/* ================= ADD DELIVERY BOY ================= */
if (isset($_POST['add'])) {

    $first   = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last    = mysqli_real_escape_string($connection, $_POST['last_name']);
    $dob     = $_POST['dob'];
    $phone   = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $email   = mysqli_real_escape_string($connection, $_POST['email']);
    $pass    = mysqli_real_escape_string($connection, $_POST['password']);
    $status  = "ENABLE";
    $area_id_main = 0;
    if (!empty($_POST['areas'])) {
        $area_id_main = (int)$_POST['areas'][0]; // first selected area
    }

    /* INSERT DELIVERY BOY */
    $insertUser = mysqli_query($connection, "
        INSERT INTO user_details
        (First_Name, Last_Name, DOB, User_Role, Status, Phone, Address, Area_Id, Email, Password)
        VALUES
        ('$first','$last','$dob','DELIVERY_BOY','$status','$phone','$address','$area_id_main','$email','$pass')
    ");

    if ($insertUser) {

        $delivery_boy_id = mysqli_insert_id($connection);

        /* MAP MULTIPLE AREAS */
        if (!empty($_POST['areas'])) {
            foreach ($_POST['areas'] as $area_id) {
                mysqli_query($connection, "
                    INSERT INTO delivery_area_map (delivery_boy_id, area_id)
                    VALUES ($delivery_boy_id, $area_id)
                ");
            }
        }

        header("Location: ../layout.php?view=delivery_boys&msg=updated");
    exit;
    } else {
        $msg = "Error while registering delivery boy";
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

    <?php if ($msg) { ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php } ?>

    <form method="POST">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required pattern="[A-Za-z]+">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required pattern="[A-Za-z]+">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">DOB</label>
            <input type="date" name="dob" class="form-control"
                   required max="<?= date('Y-m-d', strtotime('-17 years')) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control"
                   required pattern="[0-9]{10}" maxlength="10">
        </div>

        <div class="mb-3">
            <label class="form-label">Full Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>

        <!-- AREA ASSIGNMENT -->
        <div class="mb-3">
            <label class="form-label">Assign Delivery Areas</label>
            <div class="border rounded p-3">
                <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="areas[]"
                               value="<?= $row['Area_Id']; ?>"
                               <?= $row['assigned_area'] ? 'disabled' : ''; ?>>
                        <label class="form-check-label text-muted">
                            <?= $row['Area_Name']; ?> (<?= $row['Pincode']; ?>)
                            <?= $row['assigned_area'] ? ' - Already Assigned' : ''; ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email ID</label>
            <input type="email" name="email" class="form-control"
                   required pattern="[a-zA-Z0-9]+@(gmail|yahoo)\.(com|in)">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control"
                   required maxlength="10">
        </div>

        <button type="submit" name="add" class="btn btn-success">Add Delivery Boy</button>
        <a href="delivery_boys.php" class="btn btn-secondary">Back</a>

    </form>

</div>
</body>
</html>
