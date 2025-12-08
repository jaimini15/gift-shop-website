<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GiftShop | Home</title>
  <link rel="stylesheet" href="style.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
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
            <li><a href="../home page/about.php" class="active">About us</a></li> |

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
   
<section class="about-section">
  <div class="about-container">

    <div class="about-image-left">
      <img src="about_page_frame.jpg" alt="Gift shop creative workspace">
    </div>

    <div class="about-content">
      <h2>About Our Gift Shop</h2>
      <p>
        Welcome to <b>GiftShop</b>, your one-stop destination for personalized and unique gifts made with love and creativity!
We believe every gift should tell a story — your story. 
That's why we bring you a wide range of customizable products including mugs, photo frames, and more, 
designed to make every moment special.
      </p>
      <p>
       At <b>GiftShop</b>, we turn your ideas into beautiful keepsakes that express emotions better than words. 
       Whether it's a birthday, anniversary, festival, or corporate event, our mission is to help you create 
       thoughtful and memorable gifts for your loved ones.
      </p>
      <a href="index.php#categories" class="btn" style="color:white;" >Explore Our Collection</a>

    </div>

  </div>
</section>

<!--services section strats here-->

<section class="about-services">

    <h1 class="heading-title">Our Services</h1>

    <div class="box-container">

        <div class="box">
            <div class="content">
                <h3>Live Previews</h3>
                <p>Our Live Preview tool shows your design exactly as it will appear on the product. 
                  Whether you add a photo, name, or message, you can see every update instantly. 
                  This allows you to adjust sizes and placements with ease. 
                  It ensures your personalized gift turns out just the way you imagined with no surprises when it arrives.</p>
            </div>
        </div>

        <div class="box">
            <div class="content">
                <h3>Gift Wrapping & Personalized Cards</h3>
                <p>Make your gift feel extra special with our premium wrapping and personalized message card. 
                  With just one click, you can add these thoughtful extras to your order for a small additional cost.  
                  We take care of the presentation for you. Neatly wrapped and paired with a heartfelt message card written by you. 
                   We ensure the wrapping is elegant and suitable for any occasion. </p>
            </div>
        </div>

        <div class="box">
            <div class="content">
                <h3>Hampers</h3>
               <p>Why settle for one gift when you can create an entire experience?
                When you add three or more products, our hamper option automatically unlocks.
You can mix and match items like mugs, diaries, and frames to build a themed bundle.
Each hamper is beautifully arranged and wrapped, ready to surprise your loved ones.
Perfect for celebrations, corporate gifting, or festive occasions</p>
            </div>
        </div>

</section>

<!--services section ends here-->

<!-- about our mission starts -->
  <section class="mission-section">
  <div class="mission-container">
    <div class="mission-icon">
      <img src="https://static.thenounproject.com/png/2191323-200.png" alt="Target Icon">
    </div>
    <div class="mission-content">
      <h2><span>OUR</span> MISSION</h2>
      <p>
        Our mission is to bring joy and connection through thoughtful gifting.  
        We aim to make every present personal — crafted with creativity,  
        wrapped with love, and delivered with care.  
        At our gift shop, we believe every gift tells a story worth sharing.
      </p>
    </div>
  </div>
</section>


<!-- about our mission ends -->
<!-- review section starts here -->

<section class="reviews">
  <h1 class="heading-title">Our Team</h1>

  <div class="review-container">
    <div class="slide">
      <p>
        "Traveling has never been this easy! The FlyAway team understood exactly what I was looking for and planned an itinerary full of incredible experiences. Every detail was handled professionally, and the price was unbeatable.
      </p>
      <h3>Riya Rathod</h3>
      <span>Traveler</span>
      <img src="about_our_team_riya.jpg" alt="Client Joe Root">
    </div>

    <div class="slide">
      <p>
        "I was skeptical at first, but FlyAway exceeded my expectations! Their recommendations were spot-on, and the booking process was smooth. I got great discounts on flights and hotels, and everything was hassle-free. A fantastic service!"
      </p>
      <h3>Jaimini Shah</h3>
      <span>Traveler</span>
      <img src="about_our_team_jaimini.jpg" alt="Client Smith Alice">
    </div>

    <div class="slide">
      <p>
        "FlyAway truly cares about its customers. From the moment I booked, I felt supported. They even helped me with last-minute travel changes without extra charges. It’s rare to find such a reliable and customer-friendly travel service."
      </p>
      <h3>Prerna Nankani</h3>
      <span>Traveler</span>
      <img src="about_our_team_prerna.jpg" alt="Client Smith Sofia">
    </div>
  </div>
</section>

<!-- review section ends here -->



<?php require_once 'footer.php' ?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
