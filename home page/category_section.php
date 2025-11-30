<?php include("../AdminPanel/db.php"); ?>

<section class="home-package" id="categories">
    <h1 class="heading-title">Our Categories</h1>

    <div class="box-container">

        <?php
        // Fetch only enabled categories
        $query = "SELECT * FROM category_details WHERE Status='Enabled'";
        $result = mysqli_query($connection, $query);

        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){

                $img = base64_encode($row['Category_Image']);
        ?>

        <!-- BOX START -->
        <div class="box">
            <div class="image">
                <img src="data:image/jpeg;base64,<?= $img ?>" alt="<?= $row['Category_Name'] ?>">
            </div>

            <div class="content">
                <h3><?= $row['Category_Name'] ?></h3>
                <a href="product_list.php?category_id=<?= $row['Category_Id'] ?>" class="btn">Explore</a>

            </div>
        </div>
        <!-- BOX END -->

        <?php
            }
        } else {
        ?>

        <p style="text-align:center; width:100%; font-size:1.2rem; color:#333;">
            No categories available
        </p>

        <?php } ?>

    </div>
</section>
