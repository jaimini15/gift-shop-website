<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GiftShop | Home</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <!-- HERO SLIDER SECTION
    <section class="hero"> <div class="hero-left"> <img src="homephoto2.jpg" alt="Decor Image" class="hero-img"> </div> 
    <div class="hero-content"> <p class="collection">The Perfect Gift, Designed by You</p> 
    <h1 >From frames to mugs make it personal, 
<br>
make it special.</h1> <p class="subtitle">Supporting local makers since 2025</p> <button class="shop-btn">SHOP NOW</button> </div> <div class="hero-right"> <img src="homephoto1.jpg" alt="Decor Image" class="hero-img"> </div> </section> -->
<!-- HERO SLIDER SECTION -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">

  <!-- ========== INDICATORS (DOTS) ========== -->
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
  </div>

  <div class="carousel-inner">

    <!-- ========== SLIDE 1 ========== -->
    <div class="carousel-item active">
      <div class="hero-slide">
        <section class="hero">
          <div class="hero-left">
            <img src="homephoto2.jpg" class="hero-img">
          </div>

          <div class="hero-content">
            <p class="collection">The Perfect Gift, Designed by You</p>
            <h1>From frames to mugs make it personal,<br>make it special.</h1>
            <p class="subtitle">Supporting local makers since 2025</p>
            <button class="shop-btn">SHOP NOW</button>
          </div>

          <div class="hero-right">
            <img src="homephoto1.jpg" class="hero-img">
          </div>
        </section>
      </div>
    </div>

    <!-- ========== SLIDE 2 ========== -->
    <div class="carousel-item">
      <div class="hero-slide">
        <section class="hero">
          <div class="hero-left">
            <img src="home_category_mug.jpg" class="hero-img">
          </div>

          <div class="hero-content">
            <p class="collection">Create Memories</p>
            <h1>Beautiful custom mugs made<br>just for you.</h1>
            <p class="subtitle">Your design • Your style • Your gift</p>
            <button class="shop-btn">EXPLORE</button>
          </div>

          <div class="hero-right">
            <img src="home_category_frame.jpg" class="hero-img">
          </div>
        </section>
      </div>
    </div>

    <!-- ========== SLIDE 3 ========== -->
    <div class="carousel-item">
      <div class="hero-slide">
        <section class="hero">
          <div class="hero-left">
            <img src="home_category_mobile.jpg" class="hero-img">
          </div>

          <div class="hero-content">
            <p class="collection">Customize Everything</p>
            <h1>Give your mobile cover<br>a personal touch.</h1>
            <p class="subtitle">Premium quality • Trendy designs</p>
            <button class="shop-btn">CUSTOMIZE NOW</button>
          </div>

          <div class="hero-right">
            <img src="home_category_book.jpg" class="hero-img">
          </div>
        </section>
      </div>
    </div>

    <!-- ========== SLIDE 4 ========== -->
    <div class="carousel-item">
      <div class="hero-slide">
        <section class="hero">
          <div class="hero-left">
            <img src="home_about_mobile1.jpg" class="hero-img">
          </div>

          <div class="hero-content">
            <p class="collection">Perfect Gifts</p>
            <h1>Photo frames, diaries, and<br>unique handmade gifts.</h1>
            <p class="subtitle">Perfect for every occasion</p>
            <button class="shop-btn">SHOP COLLECTION</button>
          </div>

          <div class="hero-right">
            <img src="homephoto2.jpg" class="hero-img">
          </div>
        </section>
      </div>
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
   <!--Javascript bootstrp -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
