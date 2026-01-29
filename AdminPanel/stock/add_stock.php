<?php
include(__DIR__ . '/../db.php');

if (isset($_POST['add'])) {
    $product_id = intval($_POST['product_id']);
    $stock      = intval($_POST['stock']);

    // CHECK PRODUCT IS IT HAS STOCK
    $check = mysqli_query($connection, 
        "SELECT * FROM stock_details WHERE Product_Id = $product_id"
    );

    if (mysqli_num_rows($check) > 0) {
        echo "<script>window.location='../layout.php?view=stock';</script>";
        exit;
    }

    // Insert Stock
    $query = "INSERT INTO stock_details (Product_Id, Stock_Available, Last_Update) 
              VALUES ($product_id, $stock, NOW())";

    if (mysqli_query($connection, $query)) {
        echo "<script>window.location='../layout.php?view=stock&msg=added';</script>";
    } else {
        echo "<script>alert('Failed to add stock: " . mysqli_error($connection) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

<div class="container">
    <div class="row">
        <div class="col-lg-8 ms-5"> 

            <h3 class="mb-4">Add Stock</h3>

            <form method="POST">

                <label>Product</label>
                <select name="product_id" class="form-control" required>
                    <option value="">Select Product</option>

                    <?php
                    // Status is Enabled
                    $productQuery = mysqli_query($connection, 
                        "SELECT Product_Id, Product_Name 
                         FROM product_details 
                         WHERE Status = 'Enabled'"
                    );

                    while ($p = mysqli_fetch_assoc($productQuery)) {
                        echo "<option value='{$p['Product_Id']}'>{$p['Product_Name']}</option>";
                    }
                    ?>
                </select>

                <br>

                <label>Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required>

                <br>

                <button type="submit" name="add" class="btn btn-success">Add Stock</button>
                <a href="../layout.php?view=stock" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>
