<?php
include(__DIR__ . '/../db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - Admin Panel</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .content { margin-left: 120px; padding: 20px; margin-top: 30px; }
        .card-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .order-header { background:#212529; color:#fff; padding:10px; border-radius:6px; }
    </style>
</head>

<body>

<div class="content">
    <div class="card-box">

        <h2 class="fw-bold mb-4">Manage Orders</h2>

        <?php
        $orderQuery = "SELECT * FROM `order` ORDER BY Order_Id DESC";
        $orders = mysqli_query($connection, $orderQuery);

        if (!$orders) {
            echo "<div class='alert alert-danger'>Query Failed: " . mysqli_error($connection) . "</div>";
        } elseif (mysqli_num_rows($orders) == 0) {
            echo "<div class='alert alert-warning'>No orders found.</div>";
        } else {
            while ($order = mysqli_fetch_assoc($orders)) {
        ?>

        <!-- Order Header -->
        <div class="order-header mb-2">
            <div class="row">
                <div class="col-md-3"><strong>Order ID:</strong> <?= $order['Order_Id'] ?></div>
                <div class="col-md-3"><strong>User ID:</strong> <?= $order['User_Id'] ?></div>
                <div class="col-md-3"><strong>Date:</strong> <?= $order['Order_Date'] ?></div>
                <div class="col-md-3"><strong>Status:</strong> <?= $order['Status'] ?></div>
            </div>
            <div class="mt-2"><strong>Total Amount:</strong> ₹<?= number_format($order['Total_Amount'], 2) ?></div>
        </div>

        <!-- Order Items -->
        <table class="table table-bordered table-striped mb-5">
            <thead class="table-light">
                <tr>
                    <th>Item ID</th>
                    <th>Product ID</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Custom Text</th>
                    <th>Custom Image</th>
                    <th>Hamper Suggested</th>
                </tr>
            </thead>
            <tbody>

            <?php
            $itemQuery = "
                SELECT oi.*
                FROM order_item oi
                WHERE oi.Order_Id = {$order['Order_Id']}
            ";
            $items = mysqli_query($connection, $itemQuery);

            if ($items && mysqli_num_rows($items) > 0) {
                while ($item = mysqli_fetch_assoc($items)) {
            ?>
                <tr>
                    <td><?= $item['Order_Item_Id'] ?></td>
                    <td><?= $item['Product_Id'] ?></td>
                    <td><?= $item['Quantity'] ?></td>
                    <td>₹<?= number_format($item['Price_Snapshot'], 2) ?></td>
                    <td><?= $item['Custom_Text'] ?? '<span class="text-muted">N/A</span>' ?></td>
                    <td>
                        <?php if (!empty($item['Custom_Image'])) { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($item['Custom_Image']); ?>" class="product-img">
                        <?php } else { ?>
                            <span class="text-muted">No Image</span>
                        <?php } ?>
                    </td>
                    <td><?= $item['Is_Hamper_Suggested'] ? 'Yes' : 'No' ?></td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7' class='text-center text-muted'>No items found</td></tr>";
            }
            ?>

            </tbody>
        </table>

        <?php
            }
        }
        ?>

    </div>
</div>

</body>
</html>
