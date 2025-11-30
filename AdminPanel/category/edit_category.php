<?php 
include("../db.php");

$id = $_GET['id'];

// Fetch current category data
$data = mysqli_fetch_assoc(mysqli_query(
    $connection, 
    "SELECT * FROM Category_Details WHERE Category_Id = $id"
));

if (isset($_POST['update'])) {

    $name   = $_POST['name'];
    $desc   = $_POST['description'];
    $status = $_POST['status'];

    // Check if new image uploaded
    if (!empty($_FILES['image']['name'])) {

        $img = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        // Upload new image
        move_uploaded_file($tmp, "uploads/" . $img);

        // Update with new image
        $query = "
            UPDATE Category_Details SET
                Category_Name  = '$name',
                Description    = '$desc',
                Status         = '$status',
                Category_Image = '$img'
            WHERE Category_Id = $id
        ";

    } else {

        // No new image → keep old one
        $old_img = $data['Category_Image'];

        $query = "
            UPDATE Category_Details SET
                Category_Name  = '$name',
                Description    = '$desc',
                Status         = '$status',
                Category_Image = '$old_img'
            WHERE Category_Id = $id
        ";
    }

    mysqli_query($connection, $query);
    header("Location: ../layout.php?view=categories&msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .preview-img {
            width:120px;
            height:120px;
            object-fit:cover;
            border-radius:10px;
            border:1px solid #ccc;
            margin-top:10px;
        }
    </style>
</head>

<body style="margin-left:260px; padding:20px;">

<h2 class="fw-bold mb-3">Edit Category</h2>

<form method="POST" enctype="multipart/form-data">

    <!-- Name -->
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" 
               name="name" 
               value="<?= $data['Category_Name'] ?>" 
               required 
               class="form-control">
    </div>

    <!-- Description -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= $data['Description'] ?></textarea>
    </div>

    <!-- Status -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="Enabled"  <?= $data['Status'] == "Enabled" ? "selected" : "" ?>>Enabled</option>
            <option value="Disabled" <?= $data['Status'] == "Disabled" ? "selected" : "" ?>>Disabled</option>
        </select>
    </div>

    <!-- Current Image -->
    <div class="mb-3">
        <label class="form-label">Current Image</label><br>

        <?php if (!empty($data['Category_Image'])) { ?>
            <img src="uploads/<?= $data['Category_Image'] ?>" class="preview-img">
        <?php } else { ?>
            <p class="text-muted">No image uploaded</p>
        <?php } ?>
    </div>

    <!-- Upload New Image -->
    <div class="mb-3">
        <label class="form-label">Upload New Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update</button>
</form>

</body>
</html>
