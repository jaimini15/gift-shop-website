<?php
include(__DIR__ . '/../db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .content { margin-left: 120px; padding: 20px; margin-top: 30px; }
        .card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .product-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
    </style>
</head>

<body>

<div class="content">
    <div class="card-box">

        <h2 class="fw-bold">Manage Products</h2>

        <a href="products/add_products.php" class="btn btn-primary mt-3 mb-3">+ Add Product</a>

        <?php
        $query = "
        SELECT p.*, c.Category_Name
        FROM Product_Details p
        LEFT JOIN Category_Details c 
        ON p.Category_Id = c.Category_Id
        ";
        
        $result = mysqli_query($connection, $query);

        if (!$result) {
            echo "<div class='alert alert-danger'>Query Failed: " . mysqli_error($connection) . "</div>";
        } elseif (mysqli_num_rows($result) == 0) {
            echo "<div class='alert alert-warning'>No products found.</div>";
        } else {
        ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Default Text</th>
                    <th>Product Photo</th>
                    <th>Product Text</th>
                    <th>Description</th>
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

                    <td>
                        <?php if (!empty($row['Product_Image'])) { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['Product_Image']); ?>" 
                                 class="product-img">
                        <?php } else { ?>
                            <span class="text-muted">No Image</span>
                        <?php } ?>
                    </td>

                    <td><?= $row['Product_Default_Text'] ?></td>
                    <td><?= $row['Product_Photo'] ?></td>
                    <td><?= $row['Product_Text'] ?></td>
                    <td><?= $row['Description'] ?></td>
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
</div>

</body>
</html>
