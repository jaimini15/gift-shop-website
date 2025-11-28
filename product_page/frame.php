<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GiftShop | Home</title>
  <link rel="stylesheet" href="../home page/style.css" />
  <link rel="stylesheet" href="product_page.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<header>
    <div class="logo">GiftShop</div>
    <nav>
      <ul>
       <li><a href="../home page/index.php" >Home</a></li> |
        <li><a href="../home page/about.php" >About us</a></li> 
        <li class="dropdown">
        <a href="#" class="active">Shop</a>
        <ul class="dropdown-content">
         <li><a href="mug.php">Mug</a></li>
          <li><a href="frame.php">Frame</a></li>
          <li><a href="mobilecover.php">Mobile Cover</a></li>
          <li><a href="diaries.php">Dairies</a></li>
        </ul>
      </li> |
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
    <div class="icons">
      <a href="#"><i class="fa-solid fa-cart-shopping"></i>Cart</a>
      <a href="#"><i class="fa-regular fa-user"></i> My Profile</a>
    </div>
  </header>


    <section class="hero-title">
    <h1>Frame Your Memories</h1>
    <p>Turn special moments into timeless decor.</p>
</section>

<?php require_once '../home page/footer.php' ?>
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <!-- JavaScript link -->
   <script src="script.js"></script>
   <!--Javascript bootstrp -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>