<?php
include(__DIR__ . '/../db.php');  
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock - Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .content { margin-left: 0px; padding: 0px;  }
        .card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .prod-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

<div class="content">
    <div class="card-box">
        <h2 class="fw-bold">Manage Stock</h2>

        <a href="stock/add_stock.php" class="btn btn-primary mt-3 mb-3">+ Add Stock</a>

        <?php
        $query = "
            SELECT s.Stock_Id, s.Product_Id, s.Stock_Available, s.Last_Update,
                   p.Product_Name, p.Product_Image
            FROM stock_details s
            INNER JOIN product_details p ON s.Product_Id = p.Product_Id
            ORDER BY p.Product_Name ASC
        ";

        $result = mysqli_query($connection, $query);

        if (!$result) {
            echo "<div class='alert alert-danger'>Query Failed: " . mysqli_error($connection) . "</div>";
        } elseif (mysqli_num_rows($result) == 0) {
            echo "<div class='alert alert-warning'>No stock records found.</div>";
        } else {
        ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Stock ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Available Stock</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['Stock_Id'] ?></td>
                    <td><?= $row['Product_Name'] ?></td>

                    <td>
                        <?php if (!empty($row['Product_Image'])) { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['Product_Image']); ?>" 
                                 class="prod-img">
                        <?php } else { ?>
                            <span class="text-muted">No Image</span>
                        <?php } ?>
                    </td>

                    <td><?= $row['Stock_Available'] ?></td>
                    <td><?= $row['Last_Update'] ?></td>

                    <td>
                        <a href="stock/edit_stock.php?id=<?= $row['Stock_Id'] ?>" class="btn btn-sm btn-warning">Update</a>
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
