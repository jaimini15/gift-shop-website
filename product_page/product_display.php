<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo "<h2 style='text-align:center;color:red;'>Invalid Product!</h2>";
    exit;
}

$product_id = (int)$_GET['product_id'];

$prodStmt = mysqli_prepare($connection, "
    SELECT Product_Id, Category_Id, Product_Name, Product_Image, Product_Default_Text,
           Product_Photo, Product_Text, Description, Price, Status
    FROM Product_Details
    WHERE Product_Id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($prodStmt, 'i', $product_id);
mysqli_stmt_execute($prodStmt);
$res = mysqli_stmt_get_result($prodStmt);
$product = mysqli_fetch_assoc($res);
$fiveStarRow = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(*) AS five_star_count
    FROM feedback_details
    WHERE Product_Id = {$product['Product_Id']} AND Rating = 5
"));

$fiveStarCount = (int)($fiveStarRow['five_star_count'] ?? 0);


mysqli_stmt_close($prodStmt);

if (!$product || strtolower($product['Status']) === 'disabled') {
    echo "<h2 style='text-align:center;color:red;'>Product Not Found!</h2>";
    exit;
}
function img_src_from_blob_single($blob, $placeholder = 'product_mug_buynow1.jpg') {
    if ($blob === null || $blob === '' || strlen($blob) === 0) {
        return $placeholder;
    }
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}

// prepare values
$productName = htmlspecialchars($product['Product_Name'], ENT_QUOTES);
$price = number_format((float)$product['Price'], 2, '.', '');
$description = htmlspecialchars($product['Description'], ENT_QUOTES);
$defaultText = htmlspecialchars($product['Product_Default_Text'] ?? '', ENT_QUOTES);
$productPhoto = $product['Product_Photo'] ?? 'No'; 
$productText = $product['Product_Text'] ?? 'No';
$imgSrc = img_src_from_blob_single($product['Product_Image'], 'product_mug_buynow1.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $productName ?> | GiftShop</title>

  <link rel="stylesheet" href="../home page/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
</head>

<body>

<!-- Navbar starts -->
  <?php

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
                    <li><a href="#" id="profileCheckBtn">Check Profile</a></li>
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
.reviews-section h4 {
    font-size: 20px;
    margin-bottom: 15px;

}

.single-review i {
    margin-right: 2px;
}

</style>

<div id="sidePanel">
    <span id="panelClose">&times;</span>
    <div id="panelContent" style="margin-top:40px;"></div>
</div>
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

function removeItem(id) {
    if (!confirm("Remove this item from cart?")) return;

    const itemDiv = document.getElementById("item-" + id);
    const hr = itemDiv?.nextElementSibling;

    fetch("../product_page/delete_from_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {

        if (data.status !== "success") {
            alert("Delete failed");
            return;
        }

        /* ✅ Remove item from UI */
        if (itemDiv) itemDiv.remove();
        if (hr && hr.tagName === "HR") hr.remove();

        /* ✅ UPDATE SUBTOTAL (THIS WAS MISSING) */
        const subtotalSpan = document.getElementById("cartSubtotal");
        if (subtotalSpan) {
            subtotalSpan.innerText = data.subtotal.toLocaleString("en-IN");
        }

        /* ✅ EMPTY CART MESSAGE */
        if (data.subtotal <= 0) {
            document.querySelector(".cart-container").innerHTML =
                "<p class='empty-msg'>Your cart is empty.</p>";
        }

        updateCartCount();
    })
    .catch(err => {
        console.error(err);
        alert("Network error");
    });
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
<!-- PAGE CONTENT -->
<div class="container container-box" style="padding:40px 0;">
    <div class="row">

        <!-- LEFT IMAGE (Preview Area) -->
        <div class="col-md-5">
            <div class="product-image" style="position:relative; width:100%; padding-left:40px; padding-right:40px;">
                <img id="mugBase" src="<?= $imgSrc ?>" 
     style="width:430px; height:480px; object-fit:cover; border-radius:8px;" 
     alt="<?= $productName ?>">

            </div>
        </div>

        <!-- RIGHT DETAILS -->
        <div class="col-md-7">
            <h2 class="fw-bold"><?= $productName ?></h2>
            <?php if ($fiveStarCount > 0): ?>
<div style="margin:8px 0; display:flex; align-items:center; gap:6px;">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <i class="fa-solid fa-star" style="color:#f5a623;"></i>
    <?php endfor; ?>
    <span style="color:#e40046;font-size:14px;">
        (<?= $fiveStarCount ?> customer review<?= $fiveStarCount > 1 ? 's' : '' ?>)
    </span>
</div>
<?php endif; ?>


            <p>
                <span class="price-new" style="color:#e40000;font-size:24px;font-weight:bold;">₹<?= $price ?></span>
            </p>

            <p><?= $description ?></p>

            <!-- Upload Photo (only show if product supports photo customization) -->
            <?php if (strtolower($productPhoto) === 'yes') : ?>
                <label class="label-title" style="font-weight:600;margin-top:20px;">Upload Photo for Customization*</label>
                <input type="file" id="uploadPhoto" class="form-control mb-2">
             <script>   document.getElementById("uploadPhoto")?.addEventListener("change", function(){
    document.getElementById("realUpload").files = this.files;
});</script>

                <!-- PREVIEW BUTTON -->
                <button id="previewButton" class="btn btn-primary mb-3">Preview Photo</button>
            <?php endif; ?>

            <!-- FABRIC.JS CANVAS -->
            <div id="fabricContainer" style="margin-bottom:20px; display:none;">
                <canvas id="mugCanvas" width="430" height="480" style="border:1px solid #ccc; border-radius:8px;"></canvas>
            </div>
<br>
            <!-- Text Options (only show if product supports text) -->
            <?php if (strtolower($productText) === 'yes') : ?>
                <label class="label-title" style="font-weight:600;margin-top:20px;">Design & Text Options*</label>
                <div class="option-box" style="background:#f9f9f9;padding:15px;border-radius:5px;border:1px solid #eee;margin-bottom:15px;">
                    <div>
                        <input type="radio" name="text_option" id="customText" value="custom" checked>
                        <label for="customText">Custom Text</label>
                    </div>
                    <div>
                        <input type="radio" name="text_option" id="defaultText" value="default">
                        <label for="defaultText">Default Text</label>
                    </div>
                </div>

                <!-- Custom message box -->
                <div id="customMessageBox">
                    <label class="label-title">Type your own message</label>
                    <textarea id="customMessage" class="form-control" rows="4" placeholder="Enter your custom message..."></textarea>
                </div>
            <?php else: ?>
                <!-- If text not supported, still keep a non-editable default text visible if available -->
                <?php if (!empty($defaultText)) : ?>
                    <div style="margin-top:20px;">
                        <label class="label-title" style="font-weight:600;">Message</label>
                        <div class="option-box" style="padding:12px;border-radius:5px;border:1px solid #eee;"><?= $defaultText ?></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Make gift special -->
            <label class="label-title" style="font-weight:600;margin-top:20px;">Make the Gift Special</label>
            <div class="option-box" style="background:#f9f9f9;padding:15px;border-radius:5px;border:1px solid #eee;margin-bottom:15px;">
                <div>
                    <input type="checkbox" id="giftWrap" value="39">
                    <label for="giftWrap">Gift Wrap (₹39)</label>
                </div>
                <div class="mt-2">
                    <input type="checkbox" id="giftCard" value="50">
                    <label for="giftCard">Personalized Gift Card (₹50)</label>
                </div>
            </div>

            <!-- Gift Card Message -->
            <div id="giftCardMessageBox" style="display:none;">
                <label class="label-title">Add a Gift Card (₹50.00)</label>
                <textarea class="form-control" rows="4" placeholder="Type your Gift Card message..."></textarea>
            </div>

            <!-- PRICE TABLE -->
            <div style="margin-top:25px;">
                <table class="table table-bordered" style="width:80%;font-size:18px;">
                    <tbody id="priceTable"></tbody>
                </table>
            </div>

            <div style="display:flex; gap:20px; margin-top:20px;">

    <!-- ADD TO CART -->
    <form method="POST" action="add_to_cart.php" enctype="multipart/form-data">

    <input type="hidden" name="product_id" value="<?= (int)$product['Product_Id'] ?>">

    <!-- Gift Wrap -->
    <input type="hidden" id="giftWrapVal" name="gift_wrap" value="0">

    <!-- Gift Card Message -->
<textarea id="giftCardMsgVal" name="gift_card_msg" style="display:none;"></textarea>

<!-- Custom Text -->
<input type="hidden" id="customTextVal" name="custom_text">


    <!-- Default Text -->
    <input type="hidden" id="defaultTextVal" name="default_text" value="<?= $defaultText ?>">

    
    <input type="file" id="realUpload" name="custom_image" style="display:none;">

    <input type="hidden" id="giftCardVal" name="gift_card" value="0">

    <button type="submit" class="product-btn" style="padding:0.5rem 2rem;">Add to Cart</button>

</form>
    <!-- BUY NOW -->
    <form method="POST" action="../view_cart/buy_now_prepare.php">

    <input type="hidden" name="product_id"
           value="<?= (int)$product['Product_Id'] ?>">

    <input type="hidden" name="gift_wrap" id="bn_gift_wrap">
    <input type="hidden" name="gift_card" id="bn_gift_card">
    <input type="hidden" name="gift_card_msg" id="bn_gift_msg">
    <input type="hidden" name="custom_text" id="bn_custom_text">

    <button type="submit" class="product-btn">
        Buy Now
    </button>

</form>

<?php
// Fetch reviews for this product
$reviewsQuery = mysqli_query($connection, "
    SELECT fd.Comment, fd.Rating, u.First_Name, u.Last_Name
    FROM feedback_details fd
    JOIN user_details u ON fd.User_Id = u.User_Id
    WHERE fd.Product_Id = {$product['Product_Id']}
    ORDER BY fd.Feedback_Id DESC  -- latest first, assuming you have a PK like Feedback_Id
");

$reviewCount = mysqli_num_rows($reviewsQuery);
?>


</div>

        </div>

    </div>
</div>
<div class="reviews-section" style="margin-top:40px;margin-left:20px;margin-right:40px;">
    <h4><?= $reviewCount ?> Reviews for <?= $productName ?></h4>

    <?php if ($reviewCount === 0): ?>
        <p>No reviews yet. Be the first to review!</p>
    <?php else: ?>
        <?php while ($rev = mysqli_fetch_assoc($reviewsQuery)): ?>
            <div class="single-review" style="border-bottom:1px solid #eee; padding:15px 0;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                    <div style="width:40px; height:40px; background:#ccc; border-radius:50%;"></div>
                    <strong><?= htmlspecialchars($rev['First_Name'] . ' ' . $rev['Last_Name']) ?></strong>
                    <span style="margin-left:auto; color:#f5a623;">
                        <?php
                        for ($i=1; $i<=5; $i++) {
                            echo $i <= $rev['Rating']
                                ? '<i class="fa-solid fa-star"></i>'
                                : '<i class="fa-regular fa-star"></i>';
                        }
                        ?>
                    </span>
                </div>
                <p style="margin-top:5px;"><?= htmlspecialchars($rev['Comment']) ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- FOOTER Starts -->
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
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=giftshopmaninagar@gmail.com" target="_blank">
                    <i class="fas fa-envelope"></i> giftshopmaninagar@gmail.com</a>
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

<!-- ZOOM EFFECT ON IMAGE -->
<script>
const img = document.querySelector('.product-image img');

if (img) {
    img.addEventListener('mousemove', (e) => {
        const rect = img.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        img.style.transformOrigin = `${x}% ${y}%`;
        img.style.transform = 'scale(2)';
    });

    img.addEventListener('mouseleave', () => {
        img.style.transform = 'scale(1)';
        img.style.transformOrigin = 'center center';
    });
}
</script>

<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- PRICE UPDATE -->
<script>
let basePrice = <?= json_encode((float)$product['Price']) ?>;

function updateTotal() {
    let wrap = document.getElementById("giftWrap").checked;
    let card = document.getElementById("giftCard").checked;
    let giftWrapPrice = 39;
    let giftCardPrice = 50;
    let html = "";
    if (wrap) html += `<tr><td>Gift Wrap Price</td><td>₹${giftWrapPrice}</td></tr>`;
    if (card) html += `<tr><td>Personalized Card Price</td><td>₹${giftCardPrice}</td></tr>`;
    html += `<tr><td>Product Price</td><td>₹${basePrice.toFixed(2)}</td></tr>`;
    let total = basePrice + (wrap ? giftWrapPrice : 0) + (card ? giftCardPrice : 0);
    html += `<tr style="font-weight:bold;background:#f5f5f5;"><td>Total</td><td>₹${total.toFixed(2)}</td></tr>`;
    document.getElementById("priceTable").innerHTML = html;
}

document.getElementById("giftWrap").addEventListener("change", updateTotal);
document.getElementById("giftCard").addEventListener("change", function(){
    document.getElementById("giftCardMessageBox").style.display = this.checked ? "block" : "none";
    updateTotal();
});

if (document.getElementById("customText")) {
    document.getElementById("customText").addEventListener("change", function(){
        document.getElementById("customMessageBox").style.display = "block";
    });
}
if (document.getElementById("defaultText")) {
    document.getElementById("defaultText").addEventListener("change", function(){
        document.getElementById("customMessageBox").style.display = "none";
    });
}
updateTotal();
</script>

<!-- PREVIEW USING FABRIC.js -->
<script>
let canvas;
const previewBtn = document.getElementById("previewButton");
if (previewBtn) {
    previewBtn.addEventListener("click", function() {
        const fileInput = document.getElementById("uploadPhoto");
        if (!fileInput || !fileInput.files[0]) { alert("Please upload an image first!"); return; }
        document.getElementById("fabricContainer").style.display = "block";

        if (!canvas) { canvas = new fabric.Canvas('mugCanvas', { preserveObjectStacking: true }); }
        else { canvas.clear(); }

        const baseSrc = document.getElementById("mugBase").getAttribute('src');
        fabric.Image.fromURL(baseSrc, function(mugImg){
            mugImg.scaleToWidth(canvas.width);
            mugImg.scaleToHeight(canvas.height);
            canvas.setBackgroundImage(mugImg, canvas.renderAll.bind(canvas));
        }, { crossOrigin: 'anonymous' });

        const file = fileInput.files[0];
        const reader = new FileReader();
        reader.onload = function(f){
            fabric.Image.fromURL(f.target.result, function(img){
                img.set({ left: canvas.width/4, top: canvas.height/4, angle: 0, padding:10, cornersize:10 });
                img.scaleToWidth(canvas.width/2);
                img.scaleToHeight(canvas.height/2);
                canvas.add(img);
                canvas.setActiveObject(img);
                canvas.renderAll();
            }, { crossOrigin: 'anonymous' });
        };
        reader.readAsDataURL(file);
    });
}
</script>
<script>
document.querySelector("form[action$='add_to_cart.php']").addEventListener("submit", function(e) {

    // Gift wrap
    document.getElementById("giftWrapVal").value =
        document.getElementById("giftWrap").checked ? 1 : 0;

    // Gift card
    document.getElementById("giftCardVal").value =
        document.getElementById("giftCard").checked ? 1 : 0;

    // Gift Card Message (VISIBLE → HIDDEN)
    let visibleGiftMsg = document.querySelector("#giftCardMessageBox textarea");
    let hiddenGiftMsg  = document.getElementById("giftCardMsgVal");

    if (visibleGiftMsg && document.getElementById("giftCard").checked) {
        hiddenGiftMsg.value = visibleGiftMsg.value.trim();
    } else {
        hiddenGiftMsg.value = "";
    }

    // Custom Text
    let customRadio  = document.getElementById("customText");
    let defaultRadio = document.getElementById("defaultText");

    let customMsg = document.getElementById("customMessage")
        ? document.getElementById("customMessage").value.trim()
        : "";

    if (customRadio && customRadio.checked) {
        if (customMsg.length === 0) {
            alert("Please enter your custom message.");
            e.preventDefault();
            return;
        }
        document.getElementById("customTextVal").value = customMsg;
    } else if (defaultRadio && defaultRadio.checked) {
        document.getElementById("customTextVal").value =
            document.getElementById("defaultTextVal").value;
    }

});
</script>
<script>
const buyNowForm =
    document.querySelector("form[action$='buy_now_prepare.php']");

if (buyNowForm) {
    buyNowForm.addEventListener("submit", function () {

        document.getElementById("bn_gift_wrap").value =
            document.getElementById("giftWrap")?.checked ? 1 : 0;

        document.getElementById("bn_gift_card").value =
            document.getElementById("giftCard")?.checked ? 1 : 0;

        const giftMsg =
            document.querySelector("#giftCardMessageBox textarea");

        document.getElementById("bn_gift_msg").value =
            giftMsg ? giftMsg.value.trim() : "";

        const customText =
            document.getElementById("customMessage");

        document.getElementById("bn_custom_text").value =
            customText ? customText.value.trim() : "";
    });
}
</script>


</body>
</html>
