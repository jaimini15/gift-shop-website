<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GiftShop | Home</title>
  <link rel="stylesheet" href="style.css" />
  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <!-- HERO SLIDER SECTION -->
<section class="hero-slider">

  <div class="slide active">
    <div class="hero-left">
      <img src="homephoto2.jpg" alt="Decor Image" class="hero-img">
    </div>
    <div class="hero-content">
      <p class="collection">The Perfect Gift, Designed by You</p>
      <h1>From frames to mugs make it personal, make it special.</h1>
      <p class="subtitle">Supporting local makers since 2025</p>
      <button class="shop-btn">SHOP NOW</button>
    </div>
    <div class="hero-right">
      <img src="homephoto1.jpg" alt="Decor Image" class="hero-img">
    </div>
  </div>

  <div class="slide">
    <div class="hero-left">
      <img src="slide2_left.jpg" alt="Decor Image" class="hero-img">
    </div>
    <div class="hero-content">
      <p class="collection">Make Memories Last Forever</p>
      <h1>Customize photo frames that tell your story.</h1>
      <p class="subtitle">Crafted with love and care</p>
      <button class="shop-btn">SHOP NOW</button>
    </div>
    <div class="hero-right">
      <img src="slide2_right.jpg" alt="Decor Image" class="hero-img">
    </div>
  </div>

  <div class="slide">
    <div class="hero-left">
      <img src="slide3_left.jpg" alt="Decor Image" class="hero-img">
    </div>
    <div class="hero-content">
      <p class="collection">A Gift for Every Occasion</p>
      <h1>From birthdays to anniversaries, make every moment special.</h1>
      <p class="subtitle">Find your perfect personalized surprise</p>
      <button class="shop-btn">SHOP NOW</button>
    </div>
    <div class="hero-right">
      <img src="slide3_right.jpg" alt="Decor Image" class="hero-img">
    </div>
  </div>

</section>


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

  <!--home about section starts here-->
<section class="home-about">

    <div class="image">
        <img src="home_about_mobile1.jpg">
    </div>

    <div class="content">
        <h3>About Us</h3>
        <p>FlyAway makes travel planning effortless by offering seamless itineraries tailored to your preferences. Whether you seek adventure, culture, or luxury, we customize every trip to suit your style...</p>
        <a href="about.php" class="btn">read more</a>
    </div>
</section>
<!--home about section ends here-->


<!--home package section strats here-->

<section class="home-package">

    <h1 class="heading-title">Our Categories</h1>

    <div class="box-container">

        <div class="box">
            <div class="image">
                <img src="home_category_mug.jpg">
            </div>
            <div class="content">
                <h3>Mug</h3>
                <a href="contact_us.html" class="btn">Explore</a>
            </div>
        </div>

        <div class="box">
            <div class="image">
                <img src="home_category_mobile.jpg">
            </div>
            <div class="content">
                <h3>Mobile Cover</h3>
                <a href="contact_us.html" class="btn">Explore</a>
            </div>
        </div>

        <div class="box">
            <div class="image">
                <img src="home_category_frame.jpg">
            </div>
            <div class="content">
                <h3>Photo Frame</h3>
                <a href="contact_us.html" class="btn">Explore</a>
            </div>
        </div>

         <div class="box">
            <div class="image">
                <img src="home_category_book.jpg">
            </div>
            <div class="content">
                <h3>Dairies</h3>
                <a href="contact_us.html" class="btn">Explore</a>
            </div>
        </div>

    </div>
</section>

<!--home package section ends here-->

<!--home offer section starts here-->

<section class="home-offer">
    <div class="content">
        <h3>Upto 50% off</h3>
        <p>Celebrate love, laughter, and memories with custom-made treasures.<br>
Shop today and save up to 50% on your perfect gift!</p>
        <a href="contact_us.html" class="btn">Shop now</a>
    </div>
</section>


<!--home offer section ends here-->

<?php require_once 'footer.php' ?>
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <!-- JavaScript link -->
   <script src="script.js"></script>
</body>
</html>
