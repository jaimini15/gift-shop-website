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
  <!-- MAIN CSS -->
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

//CART COUNT
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
<?php include("../home page/navbar.php"); ?>
   
<section class="about-section" >
  <div class="about-container" style="margin-top:40px;margin-bottom: 40px;max-height: 400px;">

    <div class="about-image-left">
      <img src="about_page_frame.jpg" alt="Gift shop creative workspace" style="max-height:400px;">
    </div>

    <div class="about-content">
      <h2>About Our Gift Shop</h2>
      <p>
        Welcome to <b>GiftShop</b>, your one-stop destination for personalized and unique gifts made with love and creativity!
We believe every gift should tell a story thats your story. 
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

<!--services section starts here-->

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
<!-- Team section starts here -->

<section class="reviews">
  <h1 class="heading-title">Our Team</h1>

  <div class="review-container">
    <div class="slide">
      <p>
      Focused on creating a visually attractive, simple, and responsive website so customers can easily browse products and enjoy a smooth shopping experience on any device.
      </p>
      <h3>Riya Rathod</h3>
      <span>Developer</span>
      <img src="about_our_team_riya.jpg" alt="Client Joe Root">
    </div>

    <div class="slide">
       <p>
       Handles all behind-the-scenes operations, including order processing, payments, and data security, ensuring everything works accurately and reliably for customers.
      </p>
      <h3>Jaimini Shah</h3>
      <span>Developer</span>
      <img src="about_our_team_jaimini.jpg" alt="Client Smith Alice">
    </div>

    <div class="slide">
       <p>
       Connects design and functionality to make sure every feature works seamlessly, providing customers with a smooth journey from selecting a gift to completing their purchase.
      </p>
      <h3>Prerna Nankani</h3>
      <span>Developer</span>
      <img src="about_our_team_prerna.jpg" alt="Client Smith Sofia">
    </div>
  </div>
</section>

<!-- Team section ends here -->



<?php require_once 'footer.php' ?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
