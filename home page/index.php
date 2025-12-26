<?php
include("../AdminPanel/db.php");

/* Fetch first enabled category */
$firstCategoryId = null;

$catQ = mysqli_query(
    $connection,
    "SELECT Category_Id 
     FROM category_details 
     WHERE Status='Enabled' 
     ORDER BY Category_Id ASC 
     LIMIT 1"
);

if ($row = mysqli_fetch_assoc($catQ)) {
    $firstCategoryId = $row['Category_Id'];
}

/* Fallback URL if no category exists */
$shopNowUrl = $firstCategoryId
    ? "../product_page/product_list.php?category_id=" . $firstCategoryId
    : "#";
?>

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

  <?php require_once 'navbar.php'; ?>

  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" style="background-color: black;"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" style="background-color: black;"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" style="background-color: black;"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" style="background-color: black;"></button>
    </div>
    <div class="carousel-inner">

      <!-- Slide 1 -->
       <div class="carousel-item">
        <section class="hero">
          <div class="hero-left">
            <img src="home_category_mobile.jpg" class="hero-img">
          </div>
          <div class="hero-content">
            <p class="collection">Customize Everything</p>
            <h1>Give your mobile cover<br>a personal touch.</h1>
            <p class="subtitle">Premium quality • Trendy designs</p>
            <button class="shop-btn" onclick="location.href='#categories'">CUSTOMIZE NOW</button>
          </div>
          <div class="hero-right">
            <img src="homepagemobile_silde3.jpg" style="height: 450px;" class="hero-img">
          </div>
        </section>
      </div>
      <!-- Slide 2 -->
       <div class="carousel-item active">
        <section class="hero">
          <div class="hero-left">
            <img src="slidemug1.jpeg" class="hero-img">
          </div>
          <div class="hero-content">
            <p class="collection">Mugs That Speak Your Heart</p>
            <h1>Your ideas, your style<br>a mug that matches your vibe.</h1>
            <p class="subtitle">Premium quality • Perfect for gifts</p>
            <button class="shop-btn" onclick="location.href='#categories'">CUSTOMIZE NOW</button>
          </div>
          <div class="hero-right">
            <img src="sildemug.jpeg" style="height: 450px;" class="hero-img">
          </div>
        </section>
      </div>
      <!-- Slide 3 -->
      <div class="carousel-item">
        <section class="hero">
          <div class="hero-left">
            <img src="homepagephoto1_slide1.jpg " class="hero-img">
          </div>
          <div class="hero-content">
            <p class="collection">Frame Your Moments</p>
            <h1>Make your memories stand out<br> with a personalized frame.</h1>
            <p class="subtitle">High-quality build • Beautiful finishes</p>
            <button class="shop-btn" onclick="location.href='#categories'">CUSTOMIZE NOW</button>
          </div>
          <div class="hero-right">
            <img src="homephoto2.jpg" style="height: 450px;" class="hero-img">
          </div>
        </section>
      </div>

      <!-- Slide 4 -->
      <div class="carousel-item">
        <section class="hero">
          <div class="hero-left">
            <img src="slidebook1.jpeg" class="hero-img">
          </div>
          <div class="hero-content">
            <p class="collection">Personal Touch, Perfect Finish</p>
            <h1>Feature your own photo or <br>short message on the diary cover</h1>
            <p class="subtitle">Clean look • Superior craftsmanship</p>
            <button class="shop-btn" onclick="location.href='#categories'">SHOP COLLECTION</button>
          </div>
          <div class="hero-right">
            <img src="home_category_book.jpg" style="height: 450px;" class="hero-img">
          </div>
        </section>
      </div>

    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>

  <!-- FEATURES SECTION -->
  <section class="features">
    <h2>Our Amazing Feature</h2>
    <div class="feature-container">
      <div class="feature">
        <h3>Live Previews</h3>
        <p>See exactly how your custom gift will look before you buy.</p>
      </div>
      <div class="feature">
        <h3>Gift Wrapping & Personalized Cards</h3>
        <p>Make it extra special with beautiful wrapping and heartfelt cards.</p>
      </div>
      <div class="feature">
        <h3>Free Hampers</h3>
        <p>Add 3 or more products to your cart and unlock our curated hamper option all in one.</p>
      </div>
    </div>
  </section>

  <!-- ABOUT SECTION -->
  <section class="home-about">
    <div class="image">
      <img src="home_about_mobile1.jpg">
    </div>
    <div class="content">
      <h3>About Us</h3>
      <p>GiftShop specializes in creating personalized gifts like mugs, mobile covers, photo frames, and diaries. 
        We focus on premium quality products designed just the way you want.
      </p>
      <a href="about.php" class="btn" style="color:white;" >read more</a>
    </div>
  </section>

  <!-- CATEGORIES SECTION -->
  <?php include 'category_section.php'; ?>

  <!-- OFFER SECTION -->
  <section class="home-offer">
    <div class="content">
      <h3>Upto 50% off</h3>
      <p>Celebrate love, laughter, and memories with custom-made treasures.<br>
      Shop today and save up to 50% on your perfect gift!</p>
      <a href="<?= $shopNowUrl ?>" class="btn">Shop now</a>
    </div>
  </section>

  <?php require_once 'footer.php'; ?>

  <!-- JS -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
