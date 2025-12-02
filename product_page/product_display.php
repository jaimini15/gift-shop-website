<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $categoryName ?> | GiftShop</title>

  <link rel="stylesheet" href="../home page/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<?php include("../AdminPanel/db.php"); ?>

  <!-- Fabric.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
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

<!-- PAGE CONTENT -->
<div class="container container-box" style="padding:40px 0;">
    <div class="row">

        <!-- LEFT IMAGE (Preview Area) -->
        <div class="col-md-5">
            <div class="product-image" style="position:relative; width:100%; padding-left:40px; padding-right:40px;">
                <!-- BASE MUG IMAGE -->
                <img id="mugBase" src="product_mug_buynow1.jpg" style="width:100%; border-radius:8px;">
                
    
            </div>
        </div>

        <!-- RIGHT DETAILS -->
        <div class="col-md-7">

            <h2 class="fw-bold">Personalized Wooden Engraved Birthday Frame With Picture</h2>

            <p>
                <span class="price-new" style="color:#e40000;font-size:24px;font-weight:bold;">₹499</span>
            </p>

            <p>Elevate birthday gifting with our Personalized Wooden Engraved Frame.</p>

            <!-- Upload Photo -->
            <label class="label-title" style="font-weight:600;margin-top:20px;">Upload Photo for Customization*</label>
            <input type="file" id="uploadPhoto" class="form-control mb-2">

            <!-- PREVIEW BUTTON -->
            <button id="previewButton" class="btn btn-primary mb-3">Preview Photo</button>

            <!-- FABRIC.JS CANVAS -->
            <div id="fabricContainer" style="margin-bottom:20px; display:none;">
                <canvas id="mugCanvas" width="400" height="400" style="border:1px solid #ccc; border-radius:8px;"></canvas>
            </div>
<br>
            <!-- Text Options -->
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

            <button class="btn px-5 mt-3" style="background-color:#7e2626d5;color:#fff;font-weight:500px;font-size:20px;">BUY NOW</button>
        </div>

    </div>
</div>

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

<!-- ZOOM LOGIC -->
<script>
const img = document.querySelector('.product-image img');

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
</script>

<script src="../home page/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- PRICE LOGIC -->
<script>
let basePrice = 499;

function updateTotal() {
    let wrap = document.getElementById("giftWrap").checked;
    let card = document.getElementById("giftCard").checked;
    let giftWrapPrice = 39;
    let giftCardPrice = 50;
    let html = "";
    if (wrap) html += `<tr><td>Gift Wrap Price</td><td>₹${giftWrapPrice}</td></tr>`;
    if (card) html += `<tr><td>Personalized Card Price</td><td>₹${giftCardPrice}</td></tr>`;
    html += `<tr><td>Product Price</td><td>₹${basePrice}</td></tr>`;
    let total = basePrice + (wrap ? giftWrapPrice : 0) + (card ? giftCardPrice : 0);
    html += `<tr style="font-weight:bold;background:#f5f5f5;"><td>Total</td><td>₹${total}</td></tr>`;
    document.getElementById("priceTable").innerHTML = html;
}

document.getElementById("giftWrap").addEventListener("change", updateTotal);
document.getElementById("giftCard").addEventListener("change", function(){
    document.getElementById("giftCardMessageBox").style.display = this.checked ? "block" : "none";
    updateTotal();
});
document.getElementById("customText").addEventListener("change", function(){
    document.getElementById("customMessageBox").style.display = "block";
});
document.getElementById("defaultText").addEventListener("change", function(){
    document.getElementById("customMessageBox").style.display = "none";
});
updateTotal();
</script>


<!-- FABRIC.JS LOGIC -->
<script>
let canvas;
document.getElementById("previewButton").addEventListener("click", function() {
    const fileInput = document.getElementById("uploadPhoto");
    if (!fileInput.files[0]) { alert("Please upload an image first!"); return; }
    document.getElementById("fabricContainer").style.display = "block";

    if (!canvas) { canvas = new fabric.Canvas('mugCanvas', { preserveObjectStacking: true }); }
    else { canvas.clear(); }

    // Product background
    fabric.Image.fromURL('product_mug_buynow1.jpg', function(mugImg){
        mugImg.scaleToWidth(canvas.width);
        mugImg.scaleToHeight(canvas.height);
        canvas.setBackgroundImage(mugImg, canvas.renderAll.bind(canvas));
    });

    // Customer image
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
        });
    };
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
