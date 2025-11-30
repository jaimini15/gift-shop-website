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
  <?php include("../AdminPanel/db.php"); ?>
<header>
    <div class="logo">GiftShop</div>

    <nav>
      <ul>
        <li><a href="../home page/index.php">Home</a></li> |
        <li><a href="about.php">About us</a></li> | 
        
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

        <li><a href="contact.php" class="active">Contact</a></li>
      </ul>
    </nav>

    <div class="icons">
      <a href="#"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
      <a href="#"><i class="fa-regular fa-user"></i> My Profile</a>
    </div>
</header>

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
