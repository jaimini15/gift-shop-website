<?php include("../db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - Admin Panel</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        /* HEADER */
        .header {
            height: 70px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            display: flex;
            align-items: center;
            padding: 0 30px;
            justify-content: space-between;
            z-index: 100;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 80px;
            color: white;
            overflow-y: auto;
        }

        .sidebar a {
            padding: 12px 20px;
            display: block;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #495057;
        }

        /* CONTENT */
        .content {
            margin-left: 260px;
            padding: 20px;
            margin-top: 90px;
        }

        .card-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>

</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo fw-bold fs-4">
        <i class="fa-solid fa-gift text-danger"></i> GiftShop Admin
    </div>
    <div>
        <i class="fa-solid fa-user-shield"></i> Admin
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../admin.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="#"><i class="fa-solid fa-users"></i> Users</a>
    <a href="categories.php" class="active"><i class="fa-solid fa-layer-group"></i> Categories</a>
    <a href="../products/products.php"><i class="fa-solid fa-box"></i> Products</a>
    <a href="#"><i class="fa-solid fa-boxes-packing"></i> Stock</a>
    <a href="#"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
    <a href="#"><i class="fa-solid fa-credit-card"></i> Payments</a>
    <a href="#"><i class="fa-solid fa-truck"></i> Delivery</a>
    <a href="#"><i class="fa-solid fa-comments"></i> Feedback</a>
    <a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- CONTENT AREA -->
<div class="content">

    <div class="card-box">
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
    </div>

</div>

</body>
</html>
