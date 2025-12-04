<?php
session_start();
include("../AdminPanel/db.php");


// Save relative URL only
$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];


// Validate category id
if (!isset($_GET['category_id'])) {
    echo "<h2 style='text-align:center;'>Invalid Category!</h2>";
    exit;
}

$category_id = $_GET['category_id'];

// Fetch category info
$catQuery  = "SELECT * FROM category_details WHERE Category_Id='$category_id'";
$catResult = mysqli_query($connection, $catQuery);
$category  = mysqli_fetch_assoc($catResult);

// If not found or disabled
if (!$category || $category['Status'] === 'Disabled') {
    echo "<h2 style='text-align:center;'>Category Not Available</h2>";
    exit;
}

$categoryName = $category['Category_Name'];
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

<!-- NAVBAR -->
<header>
    <div class="logo">GiftShop</div>

    <nav>
      <ul>
        <li><a href="../home page/index.php">Home</a></li> |
        <li><a href="../home page/about.php">About us</a></li> |
        
        <li class="dropdown">
            <a href="#" class="active">Shop</a>
            <ul class="dropdown-content">
                <?php  
                $catQuery = "SELECT * FROM category_details WHERE Status='Enabled'";
                $catResult = mysqli_query($connection, $catQuery);
                while ($cat = mysqli_fetch_assoc($catResult)) {
                ?>
                    <li>
                        <a href="../product_page/product_list.php?category_id=<?= $cat['Category_Id'] ?>">
                            <?= $cat['Category_Name'] ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li> |

        <li><a href="../home page/contact.php">Contact</a></li>
      </ul>
    </nav>

    <div class="icons">
        <a href="#"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
        <a href="#"><i class="fa-regular fa-user"></i> My Profile</a>
    </div>
</header>

<!-- PAGE BANNER -->
<section class="hero-title">
    <h1>Perfect Personalized <?= $categoryName ?></h1>
    <p>Thoughtful <?= strtolower($categoryName) ?> designed for every occasion.</p>
</section>

<!-- PRODUCT GRID -->
<section class="product-content">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
$productQuery = "
    SELECT * FROM product_details
    WHERE Category_Id='$category_id' AND Status='Enabled'
";

$productResult = mysqli_query($connection, $productQuery);

if (mysqli_num_rows($productResult) > 0) {
    while ($product = mysqli_fetch_assoc($productResult)) {
        $img = base64_encode($product['Product_Image']);
?>
    <div class="col">
        <div class="card shadow-sm">

            <img src="data:image/jpeg;base64,<?= $img ?>"
                 class="card-img-top"
                 style="width: 100%; height: 225px; object-fit: cover;">

            <div class="card-body">
                <p class="card-text"><?= $product['Description'] ?></p>
                <p class="card-price">₹ <?= $product['Price'] ?></p>

                <button class="product-btn"
                        onclick="showLogin()">
                    Buy now
                </button>
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
<section class="footer">
    <div class="box-container">

        <div class="box">
            <h3>Quick links</h3>
            <a href="../home page/index.php"><i class="fas fa-angle-right"></i>Home</a>
            <a href="../home page/about.php"><i class="fas fa-angle-right"></i>About Us</a>
            <a href="../home page/index.php#categories"><i class="fas fa-angle-right"></i>Shop</a>
            <a href="../home page/contact.php"><i class="fas fa-angle-right"></i>Contact us</a>
        </div>

        <div class="box">
            <h3>Extra links</h3>
            <a href="#"><i class="fas fa-angle-right"></i>Ask question</a>
            <a href="#"><i class="fas fa-angle-right"></i>Privacy policy</a>
            <a href="#"><i class="fas fa-angle-right"></i>Terms of use</a>
        </div>

        <div class="box">
            <h3>Contact info</h3>
            <a href="#"><i class="fas fa-phone"></i>+123-456-7890</a>
            <a href="#"><i class="fas fa-phone"></i>+222-333-4523</a>
            <a href="#"><i class="fas fa-envelope"></i>GiftShop@gmail.com</a>
            <a href="#"><i class="fas fa-map"></i>Maninagar, India - 380008</a>
        </div>

        <div class="box">
            <h3>Follow us</h3>
            <a href="#"><i class="fab fa-facebook-f"></i>Facebook</a>
            <a href="#"><i class="fab fa-twitter"></i>Twitter</a>
            <a href="#"><i class="fab fa-instagram"></i>Instagram</a>
            <a href="#"><i class="fab fa-linkedin"></i>Linkedin</a>
        </div>

    </div>
    <div class="credit">created by <span>GiftShop</span> | all right reserved!</div>
</section>

<!-- BLUR BACKGROUND -->
<div id="blur-overlay"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.35); backdrop-filter:blur(8px); z-index:999;">
</div>

<div id="login-popup" style="display:none; z-index:1000;">
    <?php $embedded = true; include("../login/login.php"); ?>
</div>

<!-- REGISTER POPUP -->
<div id="register-popup" style="display:none; z-index:1000;">
    <?php 
        $embedded = true;
        include("../registration/registration.php");
    ?>
</div>

<script>
function showLogin() {
    document.getElementById("blur-overlay").style.display = "block";
    document.getElementById("login-popup").style.display = "flex";
    document.getElementById("register-popup").style.display = "none";
}
</script>

<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
