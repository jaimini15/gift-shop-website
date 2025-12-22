<?php
session_start();
include("../AdminPanel/db.php");

$uid = $_SESSION["User_Id"];
$profileUser = mysqli_fetch_assoc(
    mysqli_query($connection,"SELECT First_Name FROM user_details WHERE User_Id='$uid'")
);

$activePage = "feedback";
include("account_layout.php");
?>

<h2>Feedback</h2>
<p>We’d love to hear from you 💬</p>

<textarea placeholder="Write your feedback..."></textarea>
<button>Submit</button>
</div>
</div>
<?php include("../home page/footer.php"); ?>