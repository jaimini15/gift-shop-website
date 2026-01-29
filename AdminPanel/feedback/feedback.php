<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #f4f6f9;
        font-family: Arial, sans-serif;
    }
    .content {
        margin-left: 0px;
        padding: 0px;
    }
    .card-box {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>
</head>

<body>

<div class="content">
<div class="card-box">

<h3 class="fw-bold mb-4">Feedback Details</h3>

<?php
$feedbacks = mysqli_query($connection, "
    SELECT
        f.Feedback_Id,
        f.Rating,
        f.Comment,

        CONCAT(u.First_Name, ' ', u.Last_Name) AS User_Name,

        p.Product_Name,
        p.Product_Image

    FROM feedback_details f
    LEFT JOIN user_details u ON u.User_Id = f.User_Id
    LEFT JOIN product_details p ON p.Product_Id = f.Product_Id

    ORDER BY f.Feedback_Id DESC
");
?>

<table class="table table-bordered align-middle">
<thead class="table-dark">
<tr>
    <th>Feedback ID</th>
    <th>User Name</th>
    <th>Product</th>
    <th>Image</th>
    <th>Rating</th>
    <th>Comment</th>
</tr>
</thead>

<tbody>

<?php
if (mysqli_num_rows($feedbacks) == 0) {
    echo '
    <tr>
        <td colspan="6" class="text-center text-muted">
            No feedback records found
        </td>
    </tr>';
}

while ($row = mysqli_fetch_assoc($feedbacks)) {
?>
<tr>
    <td><?= $row['Feedback_Id'] ?></td>

    <td><?= htmlspecialchars($row['User_Name'] ?? 'Unknown User') ?></td>

    <td><?= htmlspecialchars($row['Product_Name'] ?? 'Unknown Product') ?></td>

    <td class="text-center">
        <?php if (!empty($row['Product_Image'])) { ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['Product_Image']) ?>"
                 class="product-img">
        <?php } else { ?>
            <span class="text-muted">No Image</span>
        <?php } ?>
    </td>

    <td>
        <?php
        $rating = (int) $row['Rating'];
        for ($i = 1; $i <= 5; $i++) {
            echo $i <= $rating ? '⭐' : '☆';
        }
        ?>
        (<?= $rating ?>/5)
    </td>

    <td style="max-width:300px; word-break:break-word;">
        <?= htmlspecialchars($row['Comment'] ?? '-') ?>
    </td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</body>
</html>
