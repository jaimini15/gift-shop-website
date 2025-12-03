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
   <?php include("../AdminPanel/db.php"); ?>
  
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
      <a href="#"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
      <a href="#"><i class="fa-regular fa-user"></i> My Profile</a>
    </div>
</header>
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
