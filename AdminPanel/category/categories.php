<?php include("../db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body style="margin-left:260px; padding:20px;">
<h2 class="fw-bold">Manage Categories</h2>

<a href="add_category.php" class="btn btn-primary mt-3 mb-3">+ Add Category</a>

<?php
$query = "SELECT * FROM Category_Details";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) == 0){
    echo "<div class='alert alert-warning'>No categories found.</div>";
} else {
?>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?= $row['Category_Id'] ?></td>
            <td><?= $row['Category_Name'] ?></td>
            <td><?= $row['Description'] ?></td>
            <td><?= $row['Status'] ?></td>

            <td>
                <a href="edit_category.php?id=<?= $row['Category_Id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_category.php?id=<?= $row['Category_Id'] ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php } ?>
</body>
</html>
