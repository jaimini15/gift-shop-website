<?php
include(__DIR__ . '/../db.php');

// GET STOCK ID 
if (!isset($_GET['id'])) {
    header("Location: ../layout.php?view=stock");
    exit;
}

$stock_id = intval($_GET['id']);

// FETCH STOCK DATA 
$stockQuery = mysqli_query($connection,
    "SELECT s.*, p.Product_Name 
     FROM stock_details s
     JOIN product_details p ON s.Product_Id = p.Product_Id
     WHERE s.Stock_Id = $stock_id"
);

if (mysqli_num_rows($stockQuery) == 0) {
    header("Location: ../layout.php?view=stock");
    exit;
}

$stock = mysqli_fetch_assoc($stockQuery);

//UPDATE STOCK 
if (isset($_POST['update'])) {
    $new_stock = intval($_POST['stock']);

    mysqli_query($connection,
        "UPDATE stock_details 
         SET Stock_Available = $new_stock, 
             Last_Update = NOW()
         WHERE Stock_Id = $stock_id"
    );

    
    header("Location: ../layout.php?view=stock&msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

<div class="container">
    <div class="row">
        <div class="col-lg-8 ms-5"> 

            <h3 class="mb-4">Update Stock</h3>

            <form method="POST">

                <label>Product Name</label>
                <input type="text" class="form-control" value="<?= $stock['Product_Name']; ?>" disabled>
                <br>

                <label>Current Stock</label>
                <input type="number" name="stock" class="form-control" 
                       value="<?= $stock['Stock_Available']; ?>" required>
                <br>

                <button type="submit" name="update" class="btn btn-success">Update Stock</button>
                <a href="../layout.php?view=stock" class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>
</div>

</body>
</html>
