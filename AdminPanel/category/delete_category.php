<?php
include("../db.php");

$id = $_GET['id'];

mysqli_query($connection, "DELETE FROM Category_Details WHERE Category_Id=$id");

header("Location: categories.php?msg=deleted");
?>
