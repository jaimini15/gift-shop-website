<?php include("../db.php"); ?>
<?php
// Fetch all categories for dropdown
$cat_query = mysqli_query($connection, "SELECT * FROM Category_Details WHERE Status='Enabled'");

if(isset($_POST['submit'])){
    $cat_id = $_POST['category_id'];
    $pname = $_POST['product_name'];
    $pdesc = $_POST['description'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Image upload
    $image_name = $_FILES['product_image']['name'];
    $tmp = $_FILES['product_image']['tmp_name'];
    $path = "uploads/".$image_name;
    move_uploaded_file($tmp, $path);

    $query = "INSERT INTO Product_Details 
    (Category_Id, Product_Name, Product_Image, Description, Price, Status)
    VALUES ('$cat_id', '$pname', '$image_name', '$pdesc', '$price', '$status')";

    mysqli_query($connection, $query);

    header("Location: products.php?msg=added");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">

<h2 class="fw-bold mb-4">Add Product</h2>

<form method="POST" enctype="multipart/form-data">

    <!-- CATEGORY DROPDOWN -->
    <div class="mb-3">
        <label class="form-label">Select Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Select Category --</option>

            <?php while($cat = mysqli_fetch_assoc($cat_query)) { ?>
                <option value="<?= $cat['Category_Id'] ?>">
                    <?= $cat['Category_Name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <!-- PRODUCT NAME -->
    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="product_name" required class="form-control">
    </div>

    <!-- PRODUCT IMAGE -->
    <div class="mb-3">
        <label class="form-label">Product Image</label>
        <input type="file" name="product_image" required class="form-control">
    </div>

    <!-- DESCRIPTION -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <!-- PRICE -->
    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" required class="form-control">
    </div>

    <!-- STATUS -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Enabled">Enabled</option>
            <option value="Disabled">Disabled</option>
        </select>
    </div>

    <button type="submit" name="submit" class="btn btn-success">Add Product</button>
</form>

</body>
</html>
