<?php
session_start();

// Not logged in → go to login
if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}

$role = $_SESSION['User_Role']; // ✅ correct session key

// Admin → admin dashboard
if ($role === "ADMIN") {
    header("Location: ../AdminPanel/admin_profile_main.php");
    exit;
}

// Delivery boy → delivery dashboard
if ($role === "DELIVERY_BOY") {
    header("Location: ../DeliveryBoyPanel/deliveryboy_profile_main.php");
    exit;
}  


// Only customer can stay on this page
include("../AdminPanel/db.php");
$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo "<h2 style='text-align:center;'>Invalid Category!</h2>";
    exit;
}
$category_id = $_GET['category_id'] + 0; 
$catStmt = mysqli_prepare($connection, "SELECT Category_Name, Status FROM category_details WHERE Category_Id = ?");
mysqli_stmt_bind_param($catStmt, 'i', $category_id);
mysqli_stmt_execute($catStmt);
$catResult = mysqli_stmt_get_result($catStmt);
$category = mysqli_fetch_assoc($catResult);
mysqli_stmt_close($catStmt);

if (!$category || strtolower($category['Status']) === 'disabled') {
    echo "<h2 style='text-align:center;'>Category Not Available</h2>";
    exit;
}

$categoryName = htmlspecialchars($category['Category_Name'], ENT_QUOTES);

function img_src_from_blob($blob, $placeholder = '../product_page/product_mug_buynow1.jpg') {
    if ($blob === null || $blob === '' || strlen($blob) === 0) {
        return $placeholder;
    }
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $categoryName ?> | GiftShop</title>

<link rel="stylesheet" href="../home page/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

<?php include("../home page/navbar.php"); ?>
<!-- PAGE BANNER -->
<section class="hero-title">
    <h1>Perfect Personalized <?= $categoryName ?></h1>
    <p>Thoughtful <?= strtolower($categoryName) ?> designed for every occasion.</p>
</section>

<!-- PRODUCT GRID -->
<section class="product-content">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
// Fetch products for category
$prodStmt = mysqli_prepare($connection, "
    SELECT 
        pd.Product_Id,
        pd.Product_Name,
        pd.Product_Image,
        pd.Description,
        pd.Price,
        COALESCE(sd.Stock_Available, 0) AS Stock_Available
    FROM product_details pd
    LEFT JOIN stock_details sd ON sd.Product_Id = pd.Product_Id
    WHERE pd.Category_Id = ? AND pd.Status = 'Enabled'
    ORDER BY pd.Product_Id DESC
");

mysqli_stmt_bind_param($prodStmt, 'i', $category_id);
mysqli_stmt_execute($prodStmt);
$productResult = mysqli_stmt_get_result($prodStmt);

if ($productResult && mysqli_num_rows($productResult) > 0) {
    while ($product = mysqli_fetch_assoc($productResult)) {
        $imgSrc = img_src_from_blob($product['Product_Image']);
        $description = htmlspecialchars($product['Description'], ENT_QUOTES);
        $price = number_format((float)$product['Price'], 2, '.', '');
        $pid = (int)$product['Product_Id'];
        $pname = htmlspecialchars($product['Product_Name'], ENT_QUOTES);
?>
    <div class="col">
        <div class="card shadow-sm">

            <img src="<?= $imgSrc ?>"
                 class="card-img-top"
                 style="width: 100%; height: 225px; object-fit: cover;"
                 alt="<?= $pname ?>">

            <div class="card-body">
                <?php if ($product['Stock_Available'] <= 0): ?>
    <p style="color:red; font-weight:bold; margin-bottom:5px;">
        OUT OF STOCK
    </p>
<?php endif; ?>

                <p class="card-text"><?= $description ?></p>
                <p class="card-price">₹ <?= $price ?></p>

               <?php 
if (!isset($_SESSION['User_Id'])): 
    $currentURL = "product_list.php?category_id=" . $category_id;
    $redirectURL = "../login/login.php?redirect=" . urlencode($currentURL);
?>
<a href="<?= $redirectURL ?>" class="product-btn">Buy Now</a>
<?php else: ?>
<a href="product_display.php?product_id=<?= $product['Product_Id'] ?>" class="product-btn">Buy Now</a>
<?php endif; ?>

            </div>
        </div>
    </div>

<?php
    }
} else {
    echo "<h3 style='text-align:center;'>No products found.</h3>";
}
mysqli_stmt_close($prodStmt);
?>
</div>
</section>
<?php require_once '../home page/footer.php' ?>

<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
