<?php include("../db.php"); ?>
<!DOCTYPE html>
<html>
<head>
<title>Products</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">
<h2 class="fw-bold">Manage Products</h2>

<a href="add_products.php" class="btn btn-primary mt-3 mb-3">+ Add Product</a>

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
    <th>Product Name</th>
    <th>Price</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $row['Product_Id'] ?></td>
    <td><?= $row['Category_Name'] ?></td>
    <td><?= $row['Product_Name'] ?></td>
    <td><?= $row['Price'] ?></td>
    <td><?= $row['Status'] ?></td>
    <td>
        <a href="edit_products.php?id=<?= $row['Product_Id'] ?>" class="btn btn-sm btn-primary">Edit</a>

<a href="delete_products.php?id=<?= $row['Product_Id'] ?>" 
   class="btn btn-sm btn-danger"
   onclick="return confirm('Delete this product?')">Delete</a>
    </td>
</tr>
<?php } ?>
</tbody>
</table>
<?php } ?>
</body>
</html>
