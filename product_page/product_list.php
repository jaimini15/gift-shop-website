<?php
session_start();
include("../AdminPanel/db.php");

// Save relative URL only
$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';

// Validate category id
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo "<h2 style='text-align:center;'>Invalid Category!</h2>";
    exit;
}

$category_id = $_GET['category_id'] + 0; // simple cast to int for safety

// Fetch category info (prepared)
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
    // detect if blob is already binary - encode to base64
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

<!-- Navbar starts -->
   <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../AdminPanel/db.php");

// ------- CART COUNT ---------
$cart_count = 0;
if (isset($_SESSION['User_Id'])) {
    $uid = $_SESSION['User_Id'];

    $cartQ = mysqli_query($connection, "SELECT Cart_Id FROM cart WHERE User_Id='$uid'");
    if (mysqli_num_rows($cartQ) > 0) {
        $cartIds = [];
        while ($c = mysqli_fetch_assoc($cartQ)) {
            $cartIds[] = $c['Cart_Id'];
        }
        $cartIdList = implode(",", $cartIds);

        $countQ = mysqli_query(
            $connection,
            "SELECT SUM(Quantity) AS total FROM customize_cart_details WHERE Cart_Id IN ($cartIdList)"
        );
        $countRow = mysqli_fetch_assoc($countQ);
        $cart_count = $countRow['total'] ?? 0;
    }
}
?>
<!-- =================== NAVBAR =================== -->
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

                    while ($cat = mysqli_fetch_assoc($catResult)) { ?>
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

    <!-- =================== ICONS =================== -->
    <div class="icons">

        <!-- CART ICON -->
        <a href="javascript:void(0)" id="cartBtn" class="cart-wrapper">
            <div class="cart-box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-badge"><?= $cart_count ?></span>
            </div>
        </a>

        <!-- PROFILE -->
        <?php if (!isset($_SESSION['User_Id'])): ?>
            <a href="../login/login.php"><i class="fa-regular fa-user"></i> My Profile</a>

        <?php else: ?>
            <div class="profile-dropdown">
                <a class="profile-btn" id="profileBtn">
                    <i class="fa-regular fa-user"></i> My Profile
                </a>

                <ul class="profile-menu">
                    <li><a href="javascript:void(0)" id="profileCheckBtn">Check Profile</a></li>
                    <li><a href="../login/logout.php">Logout</a></li>
                </ul>
            </div>
        <?php endif; ?>

    </div>
</header>

<!-- =================== CART SLIDE PANEL =================== -->
<style>
#sidePanel {
    position: fixed;
    top: 0;
    right: -400px;
    width: 350px;
    height: 100%;
    background: white;
    box-shadow: -3px 0 10px rgba(0,0,0,0.3);
    padding: 20px;
    z-index: 9999;
    transition: 0.3s ease;
}

#sidePanel.active {
    right: 0;
}

#panelClose {
    font-size: 28px;
    cursor: pointer;
    float: right;
}
</style>

<div id="sidePanel">
    <span id="panelClose">&times;</span>
    <div id="panelContent" style="margin-top:40px;"></div>
</div>

<!-- =================== JAVASCRIPT =================== -->
<script>
const sidePanel = document.getElementById("sidePanel");
const panelContent = document.getElementById("panelContent");

// Open panel and attach handlers after HTML inject
document.getElementById("cartBtn").onclick = () => {
    sidePanel.classList.add("active");

    fetch("../product_page/cart_panel.php")
        .then(res => res.text())
        .then(html => {
            panelContent.innerHTML = html;

            // Attach remove handlers to all remove buttons and images
            document.querySelectorAll(".remove-btn").forEach(btn => {
                const id = btn.getAttribute("data-id");
                btn.addEventListener("click", () => removeItem(id));
            });

            // If you want image click to also delete:
            document.querySelectorAll(".cart-img[data-id]").forEach(img => {
                img.addEventListener("click", () => removeItem(img.getAttribute("data-id")));
            });
        })
        .catch(err => {
            console.error("Failed loading panel:", err);
        });
};

// Close panel
document.getElementById("panelClose").onclick = () => {
    sidePanel.classList.remove("active");
};

// PROFILE REDIRECT
document.getElementById("profileCheckBtn")?.addEventListener("click", () => {
    window.location.href = "../customer_profile/profile.php";
});
// -------------------
// GLOBAL FUNCTIONS
// -------------------
function removeItem(id) {
    if (!confirm("Remove this item from cart?")) return;

    let itemDiv = document.getElementById("item-" + id);
    let hr = itemDiv ? itemDiv.nextElementSibling : null;

    fetch("../product_page/delete_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(text => {
        const response = text.trim();
        if (response === "success") {

            if (itemDiv) {
                itemDiv.style.transition = "opacity 0.25s";
                itemDiv.style.opacity = "0";
                setTimeout(() => {
                    if (itemDiv) itemDiv.remove();
                    if (hr && hr.tagName === "HR") hr.remove();
                    updateSubtotal();
                    updateCartCount();
                }, 260);
            } else {
                // Just update counts if DOM element missing
                updateSubtotal();
                updateCartCount();
            }

        } else {
            // show full response for debugging
            alert("Delete failed:\n" + response);
            console.error("Delete failed response:", response);
        }
    })
    .catch(err => {
        alert("Network error while deleting. See console.");
        console.error(err);
    });
}


function updateSubtotal() {
    const items = document.querySelectorAll(".item-price");
    let subtotal = 0;
    items.forEach(item => {
        const txt = item.innerText; // ex: "1 × ₹589"
        const qty = parseInt(txt.split("×")[0]) || 0;
        const price = parseInt((txt.split("₹")[1] || "0").replace(/,/g,"")) || 0;
        subtotal += qty * price;
    });

    const el = document.getElementById("subtotal-box");
    if (el) el.innerText = "₹" + subtotal.toLocaleString();
}


function updateCartCount() {
    fetch("../product_page/get_cart_count.php")
        .then(r => r.text())
        .then(text => {
            const num = text.trim();
            const badge = document.querySelector(".cart-badge");
            if (badge) badge.innerText = num;
        })
        .catch(err => console.error("Failed to update cart count:", err));
}
</script>
<!-- Navbar ends -->

<!-- PAGE BANNER -->
<section class="hero-title">
    <h1>Perfect Personalized <?= $categoryName ?></h1>
    <p>Thoughtful <?= strtolower($categoryName) ?> designed for every occasion.</p>
</section>

<!-- PRODUCT GRID -->
<section class="product-content">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

<?php
// Fetch products for category (prepared)
$prodStmt = mysqli_prepare($connection, "
    SELECT Product_Id, Product_Name, Product_Image, Description, Price
    FROM product_details
    WHERE Category_Id = ? AND Status = 'Enabled'
    ORDER BY Product_Id DESC
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
                <p class="card-text"><?= $description ?></p>
                <p class="card-price">₹ <?= $price ?></p>

               <?php 
if (!isset($_SESSION['User_Id'])): 
    // Build correct redirect URL
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

<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
