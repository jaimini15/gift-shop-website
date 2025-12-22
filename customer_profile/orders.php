<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];
$profileUser = mysqli_fetch_assoc(
    mysqli_query($connection,"SELECT First_Name FROM user_details WHERE User_Id='$uid'")
);

$activePage = "orders";
include("account_layout.php");
?>

<h2>My Orders</h2>

<div class="empty-box">
    <p>No orders found!</p>
    <a href="../home page/index.php" class="btn">START GIFTING</a>
</div>

</div> <!-- account-content -->
</div> <!-- account-wrapper -->

<?php include("../home page/footer.php"); ?>
