<?php
include("../db.php");

$id = $_GET['id'];

// Fetch product data
$product = mysqli_fetch_assoc(mysqli_query(
    $connection,
    "SELECT * FROM Product_Details WHERE Product_Id=$id"
));

// Fetch categories FROM dropdown
$categories = mysqli_query(
    $connection,
    "SELECT * FROM Category_Details WHERE Status='Enabled'"
);

if(isset($_POST['update'])){

    $cat_id  = mysqli_real_escape_string($connection, $_POST['category_id']);
    $name    = mysqli_real_escape_string($connection, $_POST['product_name']);
    $desc    = mysqli_real_escape_string($connection, $_POST['description']);
    $price   = mysqli_real_escape_string($connection, $_POST['price']);
    $status  = mysqli_real_escape_string($connection, $_POST['status']);
    $default_text = mysqli_real_escape_string($connection, $_POST['product_default_text']);
    $product_photo = mysqli_real_escape_string($connection, $_POST['product_photo']);
    $product_text  = mysqli_real_escape_string($connection, $_POST['product_text']);

    // IMAGE UPLOAD
    if (!empty($_FILES['product_image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['product_image']['tmp_name']);
        $imageData = mysqli_real_escape_string($connection, $imageData);
        $update_image = "Product_Image='$imageData',";
    } else {
        $update_image = "";
    }
    // UPDATE PRODUCT
    $query = "
        UPDATE Product_Details SET
            Category_Id='$cat_id',
            Product_Name='$name',
            $update_image
            Product_Default_Text='$default_text',
            Product_Photo='$product_photo',
            Product_Text='$product_text',
            Description='$desc',
            Price='$price',
            Status='$status'
        WHERE Product_Id=$id
    ";

    mysqli_query($connection, $query);

    header("Location: ../layout.php?view=products&msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">

<h2 class="fw-bold mb-4">Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

    <!-- CATEGORY -->
    <div class="mb-3">
        <label class="form-label">Select Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Select Category --</option>

            <?php while($cat = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?= $cat['Category_Id'] ?>"
                    <?= ($product['Category_Id'] == $cat['Category_Id']) ? 'selected' : '' ?>>
                    <?= $cat['Category_Name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <!-- PRODUCT NAME -->
    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text"
               name="product_name"
               value="<?= $product['Product_Name'] ?>"
               required
               class="form-control">
    </div>

    <!-- PRODUCT IMAGE -->
    <div class="mb-3">
        <label class="form-label">Product Image</label>
        <input type="file" name="product_image" class="form-control">

        <?php if(!empty($product['Product_Image'])) { ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($product['Product_Image']); ?>"
                 class="mt-2"
                 width="120"
                 style="border:1px solid #ccc; padding:3px;">
        <?php } ?>
    </div>

    <!-- DEFAULT TEXT -->
    <div class="mb-3">
        <label class="form-label">Default Text</label>
        <textarea name="product_default_text" class="form-control" rows="3"><?= $product['Product_Default_Text'] ?></textarea>
    </div>

    <!-- PRODUCT PHOTO -->
    <div class="mb-3">
        <label class="form-label">Product Photo</label>
        <select name="product_photo" class="form-select">
            <option value="Yes" <?= ($product['Product_Photo']=="Yes") ? "selected" : "" ?>>Yes</option>
            <option value="No"  <?= ($product['Product_Photo']=="No")  ? "selected" : "" ?>>No</option>
        </select>
    </div>

    <!-- PRODUCT TEXT -->
    <div class="mb-3">
        <label class="form-label">Product Text</label>
        <select name="product_text" class="form-select">
            <option value="Yes" <?= ($product['Product_Text']=="Yes") ? "selected" : "" ?>>Yes</option>
            <option value="No"  <?= ($product['Product_Text']=="No")  ? "selected" : "" ?>>No</option>
        </select>
    </div>

    <!-- DESCRIPTION -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= $product['Description'] ?></textarea>
    </div>

    <!-- PRICE -->
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number"
               name="price"
               value="<?= $product['Price'] ?>"
               required
               class="form-control">
    </div>

    <!-- STATUS -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Enabled" <?= ($product['Status']=="Enabled") ? "selected" : "" ?>>Enabled</option>
            <option value="Disabled" <?= ($product['Status']=="Disabled") ? "selected" : "" ?>>Disabled</option>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="../layout.php?view=products" class="btn btn-secondary">Back</a>

</form>

</body>
</html>
