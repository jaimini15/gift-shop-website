<?php include("../db.php"); ?>

<?php
$id = $_GET['id'];

$product = mysqli_fetch_assoc(mysqli_query($connection, 
    "SELECT * FROM Product_Details WHERE Product_Id=$id"
));

// Fetch categories
$categories = mysqli_query($connection, 
    "SELECT * FROM Category_Details WHERE Status='Enabled'"
);

if(isset($_POST['update'])){
    $cat_id = $_POST['category_id'];
    $name   = $_POST['product_name'];
    $desc   = $_POST['description'];
    $price  = $_POST['price'];
    $status = $_POST['status'];

    // Image update
    if($_FILES['product_image']['name'] != ""){
        $img = $_FILES['product_image']['name'];
        $tmp = $_FILES['product_image']['tmp_name'];

        move_uploaded_file($tmp, "uploads/".$img);
    } else {
        $img = $product['Product_Image'];
    }

    mysqli_query($connection,
        "UPDATE Product_Details SET
            Category_Id='$cat_id',
            Product_Name='$name',
            Product_Image='$img',
            Description='$desc',
            Price='$price',
            Status='$status'
        WHERE Product_Id=$id"
    );

    header("Location: products.php?msg=updated");
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

    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select">
            <?php while($row = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?= $row['Category_Id'] ?>"
                    <?= $product['Category_Id'] == $row['Category_Id'] ? 'selected':'' ?>>
                    <?= $row['Category_Name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="product_name" value="<?= $product['Product_Name'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <img src="uploads/<?= $product['Product_Image'] ?>" width="100">
    </div>

    <div class="mb-3">
        <label class="form-label">Change Image</label>
        <input type="file" name="product_image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= $product['Description'] ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" value="<?= $product['Price'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="Enabled" <?= $product['Status']=='Enabled'?'selected':'' ?>>Enabled</option>
            <option value="Disabled" <?= $product['Status']=='Disabled'?'selected':'' ?>>Disabled</option>
        </select>
    </div>

    <button class="btn btn-primary" name="update">Update</button>
</form>

</body>
</html>
