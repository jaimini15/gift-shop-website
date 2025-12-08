<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | GiftShop</title>
    <link rel="stylesheet" href="../home page/style.css" />
    <link rel="stylesheet" href="contact.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

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

            <li><a href="../home page/contact.php" class="active">Contact</a></li>
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

    <!-- Main Contact Section -->
    <div class="contact-main">

        <div class="contact-left">
            <h1>CONTACT US</h1>
            <p class="contact-subtitle">We are here to meet any business need and to help you with your gifting solutions.</p>

            <p><i class="fa-solid fa-phone contact-icon"></i> <strong>Phone:</strong> +91 9876543210</p>
            <p><i class="fa-solid fa-location-dot contact-icon"></i> <strong>Location:</strong> Ahmedabad, Gujarat, India</p>
            <p><i class="fa-solid fa-envelope contact-icon"></i> <strong>Email:</strong> support@giftshop.com</p>

        </div>

        <div class="contact-image">
            <img src="contactimage2.webp" alt="Office Desk">
        </div>

    </div>


    <!-- Info Boxes -->
    <div class="contact-info-boxes">

        <div class="gift-extra-box">
            <i class="fa-solid fa-phone"></i>
            <h3>CALL US</h3>
            <p>+91 98765 43210<br>+91 91234 56789</p>
        </div>

        <div class="gift-extra-box">
            <i class="fa-solid fa-location-dot"></i>
            <h3>LOCATION</h3>
            <p>GiftShop, CG Road<br>Ahmedabad, Gujarat</p>
        </div>

        <div class="gift-extra-box">
            <i class="fa-solid fa-clock"></i>
            <h3>HOURS</h3>
            <p>Mon–Fri: 10 AM – 8 PM<br>Sat–Sun: 10 AM – 6 PM</p>
        </div>

    </div>


    <!-- Google Map -->
    <h2 class="contact-map-title">Find Us on Google Maps</h2>
    <div class="contact-map-container">
        <iframe
            width="100%"
            height="350"
            loading="lazy"
            allowfullscreen
            style="border:0;"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3670.0905310933363!2d72.55968227508943!3d23.09246737913215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84f0b9972b8f%3A0x52ed9b4ac95e2990!2sCG%20Road%2C%20Navrangpura%2C%20Ahmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000">
        </iframe>
    </div>
<?php require_once '../home page/footer.php' ?>
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <!-- JavaScript link -->
   <script src="script.js"></script>
   <!--Javascript bootstrp -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
