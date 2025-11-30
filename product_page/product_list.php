<?php
include("../AdminPanel/db.php");

// Get category id from URL
if (!isset($_GET['category_id'])) {
    echo "<h2 style='text-align:center;'>Invalid Category!</h2>";
    exit;
}

$category_id = $_GET['category_id'];

// Fetch category details
$catQuery = "SELECT * FROM category_details WHERE Category_ID='$category_id'";
$catResult = mysqli_query($connection, $catQuery);
$category = mysqli_fetch_assoc($catResult);

// If category not found OR disabled
if (!$category || $category['Status'] == 'Disabled') {
    echo "<h2 style='text-align:center;'>Category Not Available</h2>";
    exit;
}

$categoryName = $category['Category_Name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $categoryName ?> | GiftShop</title>

  <link rel="stylesheet" href="../home page/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <style>
      .hero-title {
          text-align:center;
          padding:40px 0;
      }
      .hero-title h1 {
          font-size:40px;
          font-weight:700;
      }
      .hero-title p {
          color:#666;
          font-size:18px;
          margin-top:8px;
      }
      .product-btn {
          background:#a75a55;
          color:white;
          border:none;
          padding:8px 18px;
          border-radius:6px;
      }
      .product-btn:hover {
          background:#8e4742;
      }
      .card-price {
          font-weight:bold;
          font-size:20px;
      }
  </style>
</head>

<body>

<!-- NAVBAR -->
<?php include '../home page/navbar.php'; ?>

<!-- PAGE TITLE -->
<section class="hero-title">
    <h1>Perfect Personalized <?= $categoryName ?></h1>
    <p>Thoughtful <?= strtolower($categoryName) ?> designed for every occasion.</p>
</section>

<!-- PRODUCT CONTENT -->
<section class="product-content">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
// Fetch products under this category
$productQuery = "SELECT * FROM product_details 
                 WHERE Category_ID='$category_id' 
                 AND Status='Enabled'";

$productResult = mysqli_query($connection, $productQuery);

if (mysqli_num_rows($productResult) > 0) {
    while ($product = mysqli_fetch_assoc($productResult)) {

        // Convert product image blob to display
        $img = base64_encode($product['Product_Image']);
?>
    <div class="col">
      <div class="card shadow-sm">

        <img class="card-img-top"
            src="data:image/jpeg;base64,<?= $img ?>"
            style="width:100%; height:225px; object-fit:cover;">

        <div class="card-body">
          <p class="card-text"><?= $product['Description'] ?></p>

          <p class="card-price">₹ <?= $product['Price'] ?></p>

          <button class="product-btn">Buy now</button>
        </div>
      </div>
    </div>
<?php 
    }
} else {
    echo "<h3 style='text-align:center;'>No products found.</h3>";
}
?>
</div>
</section>

<!-- FOOTER -->
<?php require_once '../home page/footer.php'; ?>

<!-- SCRIPTS -->
<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
