<?php
session_start();
include("../AdminPanel/db.php");

if (!isset($_SESSION['User_Id'])) {
    header("Location: ../login/login.php");
    exit;
}

$userId = $_SESSION['User_Id'];

/* FORM DATA */
$productIds = $_POST['product_id'] ?? [];
$ratings    = $_POST['rating'] ?? [];
$feedbacks  = $_POST['feedback'] ?? [];

if (empty($productIds)) {
    die("No products to review");
}

foreach ($productIds as $productId) {

    $productId = (int)$productId;

    /* Rating is REQUIRED */
    if (!isset($ratings[$productId])) {
        continue; // skip if no rating selected
    }

    $rating  = (int)$ratings[$productId];
    $comment = trim($feedbacks[$productId] ?? '');

    /* Insert review */
    $stmt = mysqli_prepare($connection, "
        INSERT INTO feedback_details (User_Id, Product_Id, Rating, Comment)
        VALUES (?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iiis",
        $userId,
        $productId,
        $rating,
        $comment
    );

    mysqli_stmt_execute($stmt);
}

/* Redirect after success */
header("Location: orders.php");
exit;
