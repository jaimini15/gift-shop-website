<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../AdminPanel/db.php"); ?>
<header>
    <div class="logo">GiftShop</div>

    <nav>
      <ul>
        <li><a href="../home page/index.php" class="active">Home</a></li> |
        <li><a href="../home page/about.php">About us</a></li> | 
        
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

    <?php if (!isset($_SESSION['User_Id'])): ?>

    <!-- NOT LOGGED IN -->
    <a href="../login/login.php">
        <i class="fa-regular fa-user"></i> My Profile
    </a>

<?php else: ?>

   <!-- LOGGED IN -->
<div class="profile-dropdown">
    <a class="profile-btn">
        <i class="fa-regular fa-user"></i> My Profile
    </a>

    <ul class="profile-menu">
        <li><a href="#">Check Profile</a></li>
        <li><a href="../login/logout.php">Logout</a></li>
    </ul>
</div>


<?php endif; ?>


</div>


</header>
