<?php
session_start();
// redirect to login - if not logged
if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}

// SAFE role fetch (no warning)
if (!isset($_SESSION['User_Role'])) {
    session_destroy();
    header("Location: ../login/login.php");
    exit;
}

$role = $_SESSION['User_Role'];

// Admin → admin_profile_main
if ($role === "ADMIN") {
    header("Location: ../AdminPanel/admin_profile_main.php");
    exit;
}

// Delivery boy → deliveryboy_profile_main
if ($role === "DELIVERY_BOY") {
    header("Location: ../DeliveryBoyPanel/deliveryboy_profile_main.php");
    exit;
}

?>
<?php
include("../AdminPanel/db.php");
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo "<h2 style='text-align:center;color:red;'>Invalid Product!</h2>";
    exit;
}

$product_id = (int) $_GET['product_id'];

$prodStmt = mysqli_prepare($connection, "
    SELECT p.Product_Id, p.Category_Id, p.Product_Name, p.Product_Image,
           p.Product_Default_Text, p.Product_Photo, p.Product_Text,
           p.Description, p.Price, p.Status,
           IFNULL(s.Stock_Available, 0) AS Stock_Available
    FROM Product_Details p
    LEFT JOIN stock_details s ON p.Product_Id = s.Product_Id
    WHERE p.Product_Id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($prodStmt, 'i', $product_id);
mysqli_stmt_execute($prodStmt);
$res = mysqli_stmt_get_result($prodStmt);
$product = mysqli_fetch_assoc($res);
$stockAvailable = (int) $product['Stock_Available'];
$fiveStarRow = mysqli_fetch_assoc(mysqli_query($connection, "
    SELECT COUNT(*) AS five_star_count
    FROM feedback_details
    WHERE Product_Id = {$product['Product_Id']} AND Rating = 5
"));

$fiveStarCount = (int) ($fiveStarRow['five_star_count'] ?? 0);


mysqli_stmt_close($prodStmt);

if (!$product || strtolower($product['Status']) === 'disabled') {
    echo "<h2 style='text-align:center;color:red;'>Product Not Found!</h2>";
    exit;
}
function img_src_from_blob_single($blob, $placeholder = 'product_mug_buynow1.jpg')
{
    if ($blob === null || $blob === '' || strlen($blob) === 0) {
        return $placeholder;
    }
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}

// prepare values
$productName = htmlspecialchars($product['Product_Name'], ENT_QUOTES);
$price = number_format((float) $product['Price'], 2, '.', '');
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
    <?php
    $ACTIVE_PAGE = "shop";
    include("../home page/navbar.php");
    ?>
    <!-- PAGE CONTENT -->
    <div class="container container-box" style="padding:40px 0;">
        <div class="row">

            <!-- LEFT IMAGE (Preview Area) -->
            <div class="col-md-5">
                <div class="product-image"
                    style="position:relative; width:100%; padding-left:40px; padding-right:40px;">
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

                <p><?= nl2br($description) ?></p>

                <!-- Upload Photo (only show if product supports photo customization) -->
                <?php if (strtolower($productPhoto) === 'yes'): ?>
                    <label class="label-title" style="font-weight:600;margin-top:20px;">Upload Photo for
                        Customization</label>
                    <input type="file" id="uploadPhoto" class="form-control mb-2">
                    <script>
                        document.getElementById("uploadPhoto")?.addEventListener("change", function () {
                            // Cart
                            document.getElementById("realUpload").files = this.files;
                            //  Buy Now 
                            document.getElementById("bn_realUpload").files = this.files;
                        });
                    </script>

                    <!-- PREVIEW BUTTON -->
                    <button id="previewButton" class="btn btn-primary mb-3">Preview Photo</button>
                <?php endif; ?>

                <!-- FABRIC.JS CANVAS -->
                <div id="fabricContainer" style="margin-bottom:20px; display:none;">
                    <canvas id="mugCanvas" width="430" height="480"
                        style="border:1px solid #ccc; border-radius:8px;"></canvas>
                </div>
                <br>
                <!-- Text Options (only show if product supports text) -->
                <?php if (strtolower($productText) === 'yes'): ?>
                    <label class="label-title" style="font-weight:600;margin-top:20px;">Design & Text Options</label>
                    <div class="option-box"
                        style="background:#f9f9f9;padding:15px;border-radius:5px;border:1px solid #eee;margin-bottom:15px;">
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
                        <textarea id="customMessage" class="form-control" rows="4"
                            placeholder="Enter your custom message..."></textarea>
                    </div>
                <?php else: ?>
                    <?php if (!empty($defaultText)): ?>
                        <div style="margin-top:20px;">
                            <label class="label-title" style="font-weight:600;">Message</label>
                            <div class="option-box" style="padding:12px;border-radius:5px;border:1px solid #eee;">
                                <?= $defaultText ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Make gift special -->
                <label class="label-title" style="font-weight:600;margin-top:20px;">Make the Gift Special</label>
                <div class="option-box"
                    style="background:#f9f9f9;padding:15px;border-radius:5px;border:1px solid #eee;margin-bottom:15px;">
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

                        <input type="hidden" name="product_id" value="<?= (int) $product['Product_Id'] ?>">

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
                    <!-- BUY NOW FORM -->
                    <form id="buyNowForm" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= (int) $product['Product_Id'] ?>">
                        <input type="hidden" name="gift_wrap" id="bn_gift_wrap">
                        <input type="hidden" name="gift_card" id="bn_gift_card">
                        <input type="hidden" name="gift_card_msg" id="bn_gift_msg">
                        <input type="hidden" name="custom_text" id="bn_custom_text">

                        <input type="file" id="bn_realUpload" name="custom_image" style="display:none;">

                        <button type="button" onclick="buyNowPay()" class="product-btn">
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
    <div class="reviews-section" style="margin:50px auto; max-width:1300px;">
        <h4 style="font-size:26px; font-weight:600; margin-bottom:25px; color:#333;">
            <?= $reviewCount ?> Reviews for <?= $productName ?>
        </h4>
        <?php if ($reviewCount === 0): ?>
            <p style="font-size:16px; color:#555;">No reviews yet. Be the first to review!</p>
        <?php else: ?>
            <div class="reviews-list" style="display:flex; flex-direction:column; gap:20px;">
                <?php while ($rev = mysqli_fetch_assoc($reviewsQuery)): ?>
                    <div class="review-card" style="
                    background:#fff; 
                    padding:20px; 
                    border-radius:12px; 
                    box-shadow:0 5px 15px rgba(0,0,0,0.05);
                    display:flex; 
                    flex-direction:column;
                    gap:10px;
                    transition: transform 0.2s, box-shadow 0.2s;
                ">
                        <div class="review-header" style="display:flex; align-items:center; gap:12px;">
                            <div style="
                            width:50px; 
                            height:50px; 
                            border-radius:50%; 
                            background:#ddd;
                            display:flex; 
                            justify-content:center; 
                            align-items:center;
                            font-weight:bold;
                            color:#555;
                            font-size:18px;
                        ">
                                <?= strtoupper(substr($rev['First_Name'], 0, 1)) ?>
                            </div>
                            <div style="flex:1;">
                                <strong style="font-size:16px; color:#333;">
                                    <?= htmlspecialchars($rev['First_Name'] . ' ' . $rev['Last_Name']) ?>
                                </strong>
                                <div class="rating-stars" style="color:#f5a623; font-size:16px; margin-top:2px;">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rev['Rating']
                                            ? '<i class="fa-solid fa-star"></i>'
                                            : '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <span style="font-size:14px; color:#888;">
                                <?= date('d M Y') ?>
                            </span>
                        </div>
                        <div class="review-body" style="font-size:15px; color:#555; line-height:1.5;">
                            <?= htmlspecialchars($rev['Comment']) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>


    <?php require_once '../home page/footer.php' ?>


    <script src="../home page/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- stock avaiable -->
    <script>
        const STOCK_AVAILABLE = <?= $stockAvailable ?>;
    </script>

    <!-- PRICE UPDATE -->
    <script>
        let basePrice = <?= json_encode((float) $product['Price']) ?>;

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
        document.getElementById("giftCard").addEventListener("change", function () {
            document.getElementById("giftCardMessageBox").style.display = this.checked ? "block" : "none";
            updateTotal();
        });

        if (document.getElementById("customText")) {
            document.getElementById("customText").addEventListener("change", function () {
                document.getElementById("customMessageBox").style.display = "block";
            });
        }
        if (document.getElementById("defaultText")) {
            document.getElementById("defaultText").addEventListener("change", function () {
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
            previewBtn.addEventListener("click", function () {
                const fileInput = document.getElementById("uploadPhoto");
                if (!fileInput || !fileInput.files[0]) {
                    alert("Please upload an image first!");
                    return;
                }
                document.getElementById("fabricContainer").style.display = "block";

                if (!canvas) {
                    canvas = new fabric.Canvas('mugCanvas', {
                        preserveObjectStacking: true
                    });
                } else {
                    canvas.clear();
                }

                const baseSrc = document.getElementById("mugBase").getAttribute('src');
                fabric.Image.fromURL(baseSrc, function (mugImg) {
                    mugImg.scaleToWidth(canvas.width);
                    mugImg.scaleToHeight(canvas.height);
                    canvas.setBackgroundImage(mugImg, canvas.renderAll.bind(canvas));
                }, {
                    crossOrigin: 'anonymous'
                });

                const file = fileInput.files[0];
                const reader = new FileReader();
                reader.onload = function (f) {
                    fabric.Image.fromURL(f.target.result, function (img) {
                        img.set({
                            left: canvas.width / 4,
                            top: canvas.height / 4,
                            angle: 0,
                            padding: 10,
                            cornersize: 10
                        });
                        img.scaleToWidth(canvas.width / 2);
                        img.scaleToHeight(canvas.height / 2);
                        canvas.add(img);
                        canvas.setActiveObject(img);
                        canvas.renderAll();
                    }, {
                        crossOrigin: 'anonymous'
                    });
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
    <script>
        document.querySelector("form[action$='add_to_cart.php']").addEventListener("submit", function (e) {

            // Gift wrap
            document.getElementById("giftWrapVal").value =
                document.getElementById("giftWrap").checked ? 1 : 0;

            // Gift card
            document.getElementById("giftCardVal").value =
                document.getElementById("giftCard").checked ? 1 : 0;

            // Gift Card Message
            let visibleGiftMsg = document.querySelector("#giftCardMessageBox textarea");
            let hiddenGiftMsg = document.getElementById("giftCardMsgVal");

            if (visibleGiftMsg && document.getElementById("giftCard").checked) {
                hiddenGiftMsg.value = visibleGiftMsg.value.trim();
            } else {
                hiddenGiftMsg.value = "";
            }

            // Custom Text
            let customRadio = document.getElementById("customText");
            let defaultRadio = document.getElementById("defaultText");

            let customMsg = document.getElementById("customMessage") ?
                document.getElementById("customMessage").value.trim() :
                "";

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
        function validateCustomization(isBuyNow = false) {

            /*IMAGE REQUIRED CHECK */
            const uploadInput = isBuyNow ?
                document.getElementById("bn_realUpload") :
                document.getElementById("realUpload");
            const photoRequired = <?= json_encode(strtolower($productPhoto) === 'yes') ?>;
            if (photoRequired && (!uploadInput || uploadInput.files.length === 0)) {
                alert("Please upload an image for customization.");
                return false;
            }
            /* CUSTOM TEXT REQUIRED CHECK */
            const customRadio = document.getElementById("customText");
            const defaultRadio = document.getElementById("defaultText");
            const customMsg = document.getElementById("customMessage");

            if (customRadio && customRadio.checked) {
                if (!customMsg || customMsg.value.trim().length === 0) {
                    alert("Please enter your custom message.");
                    return false;
                }
            }

            return true;
        }
    </script>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        function buyNowPay() {
            // OUT OF STOCK CHECK
            if (STOCK_AVAILABLE <= 0) {
                alert("Sorry! This item is out of stock.");
                return;
            }

            if (!validateCustomization(true)) {
                return;
            }
            // SET BUY NOW VALUES
            document.getElementById("bn_gift_wrap").value =
                document.getElementById("giftWrap")?.checked ? 1 : 0;

            document.getElementById("bn_gift_card").value =
                document.getElementById("giftCard")?.checked ? 1 : 0;

            const giftMsg = document.querySelector("#giftCardMessageBox textarea");
            document.getElementById("bn_gift_msg").value =
                giftMsg ? giftMsg.value.trim() : "";

            const customText = document.getElementById("customMessage");
            document.getElementById("bn_custom_text").value =
                customText ? customText.value.trim() : "";



            let form = document.getElementById("buyNowForm");
            let formData = new FormData(form);

            fetch("../view_cart/buy_now_prepare.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(prep => {

                    console.log("BUY_NOW:", prep);

                    if (!prep.success) {
                        alert("Order creation failed");
                        return;
                    }

                    fetch("../view_cart/create_razorpay_order_buy_now.php", {
                        method: "POST"
                    })
                        .then(res => res.json())
                        .then(data => {

                            console.log("RAZORPAY:", data);

                            if (!data.success) {
                                alert(data.error);
                                return;
                            }

                            var options = {
                                key: data.key,
                                amount: data.amount,
                                currency: "INR",
                                name: "GiftShop Pvt Ltd",
                                description: "Buy Now Payment",
                                order_id: data.orderId,

                                handler: function (response) {

                                    fetch("../view_cart/confirm_payment_buy_now.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify({
                                            razorpay_payment_id: response
                                                .razorpay_payment_id,
                                            razorpay_order_id: response.razorpay_order_id,
                                            razorpay_signature: response.razorpay_signature
                                        })
                                    })
                                        .then(res => res.json())
                                        .then(result => {
                                            console.log("CONFIRM:", result);

                                            if (result.success) {
                                                window.location.href =
                                                    "../view_cart/order_summary.php?order_id=" + result
                                                        .order_id;

                                            } else {
                                                alert("Payment failed  " + result.error);
                                            }
                                        });
                                         
                                }
                            };

                            var rzp = new Razorpay(options);
                            rzp.open();
                        });
                });
        }
    </script>



</body>

</html>