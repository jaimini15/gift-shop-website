<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../AdminPanel/db.php");

// ✅ BASE PATH (change if your folder name is different)
$BASE = "/GitHub/gift-shop-website/";
// CART COUNT 
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
<!-- Navbar Starts -->
<header>
    <div class="logo">GiftShop</div>

    <nav>
        <ul>
           <li><a href="<?= $BASE ?>home page/index.php" class="active">Home</a></li> |
<li><a href="<?= $BASE ?>home page/about.php">About us</a></li> |

            <li class="dropdown">
                <a href="#">Shop</a>
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

           <li><a href="<?= $BASE ?>home page/contact.php">Contact</a></li>
        </ul>
    </nav>
    <div class="icons">

        <!-- CART ICON -->
        <a href="javascript:void(0)" id="cartBtn" class="cart-wrapper">
            <div class="cart-box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-badge"><?= $cart_count ?></span>
            </div>
        </a>

       <!-- PROFILE -->
<!-- PROFILE -->
<?php if (!isset($_SESSION['User_Id'])): ?>

   <a href="<?= $BASE ?>login/login.php">
        <i class="fa-regular fa-user"></i> My Profile
    </a>

<?php else: ?>

    <?php
        // Fetch role from database
        $uid = $_SESSION['User_Id'];
        $role = "CUSTOMER"; // default role

        $roleQuery = mysqli_query($connection, "SELECT User_Role FROM user_details WHERE User_Id='$uid'");
        if ($roleRow = mysqli_fetch_assoc($roleQuery)) {
            $role = $roleRow['User_Role'];
        }
    ?>

    <div class="profile-dropdown">
        <a class="profile-btn">
            <i class="fa-regular fa-user"></i> My Profile
        </a>

        <ul class="profile-menu">
            <li>
                <a href="<?php
                    if ($role == 'ADMIN') {
                        echo '../AdminPanel/admin_profile_main.php';
                    } 
                    elseif ($role == 'DELIVERY_BOY') {
                        echo '../DeliveryBoyPanel/deliveryboy_profile_main.php';
                    } 
                    else {
                        echo '../customer_profile/profile.php';
                    }
                ?>">
                    Check Profile
                </a>
            </li>
            <li><a href="../login/logout.php">Logout</a></li>
        </ul>
    </div>

<?php endif; ?>


    </div>
</header>
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
<script>
const sidePanel = document.getElementById("sidePanel");
const panelContent = document.getElementById("panelContent");
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="/GitHub/gift-shop-website/product_page/cart.js"></script>



