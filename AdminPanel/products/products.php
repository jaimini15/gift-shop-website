
<?php
include "../session_protect.php"  // Correct path to db.php
?> 
<?php
include(__DIR__ . '/../db.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Products</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
    .card-box {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-img {
        height: 60px;
        width: 60px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>

</head>

<body style="margin-left:0px;margin-top:2">

<div class="card-box">

    <h2 class="fw-bold">Manage Products</h2>

    <a href="products/add_products.php" class="btn btn-primary mt-3 mb-3">+ Add Product</a>

    <?php
    $query = "
    SELECT p.*, c.Category_Name
    FROM Product_Details p
    LEFT JOIN Category_Details c 
    ON p.Category_Id = c.Category_Id";
    
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) == 0){
        echo "<div class='alert alert-warning'>No products found.</div>";
    } else {
    ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)){ ?>
            <tr>
                <td><?= $row['Product_Id'] ?></td>
                <td><?= $row['Category_Name'] ?></td>

                <!-- IMAGE COLUMN -->
               <td>
                        <?php if (!empty($row['Product_Image'])) { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['Product_Image']); ?>"
                                 class="product-img">
                        <?php } else { ?>
                            <span class="text-muted">No Image</span>
                        <?php } ?>
                </td>

                <td><?= $row['Product_Name'] ?></td>
                <td><?= $row['Price'] ?></td>
                <td><?= $row['Status'] ?></td>

                <td>
                    <a href="products/edit_products.php?id=<?= $row['Product_Id'] ?>" 
                       class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php } ?>
</div>

</body>
</html>
