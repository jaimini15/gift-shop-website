
<?php include("../db.php"); ?>

<?php
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM Product_Details WHERE Product_Id=$id"));

$categories = mysqli_query($connection, "SELECT * FROM Category_Details");

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    // If user uploads a new image
    if (!empty($_FILES['image']['tmp_name'])) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $query = "UPDATE Product_Details SET
                    Product_Name='$name',
                    Description='$desc',
                    Price='$price',
                    Category_Id='$category',
                    Status='$status',
                    Product_Image='$image'
                  WHERE Product_Id=$id";
    } else {
        // No new image → keep old one
        $query = "UPDATE Product_Details SET
                    Product_Name='$name',
                    Description='$desc',
                    Price='$price',
                    Category_Id='$category',
                    Status='$status'
                  WHERE Product_Id=$id";
    }

    mysqli_query($connection, $query);
    header("Location: ../layout.php?view=products&msg=updated");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .preview-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
    </style>

</head>

<body style="margin-left:260px; padding:20px;">

<h2 class="fw-bold mb-3">Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?= $cat['Category_Id'] ?>" 
                    <?= $cat['Category_Id'] == $data['Category_Id'] ? "selected" : "" ?>>
                    <?= $cat['Category_Name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" value="<?= $data['Product_Name'] ?>" required class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required><?= $data['Description'] ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Price (₹)</label>
        <input type="number" name="price" value="<?= $data['Price'] ?>" required class="form-control">
    </div>

    

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="Enabled" <?= $data['Status'] == "Enabled" ? "selected" : "" ?>>Enabled</option>
            <option value="Disabled" <?= $data['Status'] == "Disabled" ? "selected" : "" ?>>Disabled</option>
        </select>
    </div>

    <!-- IMAGE PREVIEW (Same Style as edit_category.php) -->
    <div class="mb-3">
        <label class="form-label">Current Product Image</label><br>

        <?php if (!empty($data['Product_Image'])) { ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($data['Product_Image']); ?>" class="preview-img">
        <?php } else { ?>
            <p class="text-muted">No image uploaded</p>
        <?php } ?>
    </div>

    <!-- UPLOAD NEW IMAGE -->
    <div class="mb-3">
        <label class="form-label">Upload New Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Product</button>

</form>

</body>
</html>
