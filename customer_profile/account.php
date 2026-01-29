<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION["User_Id"])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION["User_Id"];

// FETCH USER 
$result = mysqli_query(
    $connection,
    "SELECT First_Name FROM user_details WHERE User_Id='$uid' LIMIT 1"
);

$profileUser = mysqli_fetch_assoc($result);

if (!$profileUser || !is_array($profileUser)) {
    die("User data not found");
}

$activePage = "profile";
include("account_layout.php");
?>

<h2>My Profile</h2>
<p>Welcome, <?= htmlspecialchars($profileUser['First_Name']) ?></p>

</div> 
</div> 

<?php include("../home page/footer.php"); ?>
