<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../AdminPanel/db.php");
?>
<header>
    <div class="logo">GiftShop</div>

    <nav>
        <ul>
            <li><a href="../home page/index.php" class="active">Home</a></li> |
            <li><a href="../home page/about.php">About us</a></li> | 

            <li class="dropdown">
                <a href="#">Shop</a>
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

            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <div class="icons">

        <?php  
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

                $countQ = mysqli_query($connection, 
                    "SELECT SUM(Quantity) AS total FROM customize_cart_details 
                    WHERE Cart_Id IN ($cartIdList)"
                );

                $countRow = mysqli_fetch_assoc($countQ);
                $cart_count = $countRow['total'] ?? 0;
            }
        }
        ?>

        <a href="javascript:void(0)" id="cartBtn" class="cart-wrapper">
            <div class="cart-box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-badge"><?= $cart_count ?></span>
            </div>
        </a>

        <?php if (!isset($_SESSION['User_Id'])): ?>

            <a href="../login/login.php">
                <i class="fa-regular fa-user"></i> My Profile
            </a>

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

<div id="sidePanel">
    <span id="panelClose">&times;</span>
    <div id="panelContent"></div>
</div>

<script>
const panel = document.getElementById("sidePanel");
const content = document.getElementById("panelContent");

document.getElementById("panelClose").onclick = () => {
    panel.classList.remove("active");
};

document.getElementById("cartBtn").onclick = () => {
    fetch("../product_page/cart_panel.php")
        .then(res => res.text())
        .then(data => {
            content.innerHTML = data;
            panel.classList.add("active");
        });
};

document.getElementById("profileCheckBtn")?.addEventListener("click", () => {
    window.location.href = "../customer_profile/profile.php";
});
</script>
