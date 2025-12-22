<?php
if (!isset($profileUser) || !is_array($profileUser)) {
    $profileUser = [];
}
if (!isset($activePage)) {
    $activePage = "";
}
// Prevent undefined variable warning
if (!isset($activePage)) {
    $activePage = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Account | GiftShop</title>

    <!-- MAIN SITE CSS -->
    <link rel="stylesheet" href="../home page/style.css">

    <!-- ACCOUNT PANEL CSS (IMPORTANT) -->
    <link rel="stylesheet" href="account.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <?php include("../home page/navbar.php"); ?>

    <!-- ================= ACCOUNT WRAPPER ================= -->
    <div class="account-wrapper">

        <!-- ========== LEFT SIDEBAR ========== -->
<div class="account-sidebar">

    <?php if (!empty($profileUser['First_Name'])): ?>
        <div class="sidebar-hello">
            Hello, <?= htmlspecialchars($profileUser['First_Name']); ?> 👋
        </div>
    <?php else: ?>
        <div class="sidebar-hello">Hello 👋</div>
    <?php endif; ?>

    <!-- MY ACCOUNT (DASHBOARD) -->
    <a href="profile.php" class="<?= $activePage=='account' ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i> My Account
    </a>

    <a href="orders.php" class="<?= $activePage=='orders' ? 'active' : '' ?>">
        <i class="fa-solid fa-box"></i> My Orders
    </a>

    <a href="edit_profile.php" class="<?= $activePage=='profile' ? 'active' : '' ?>">
        <i class="fa-solid fa-user"></i> My Profile
    </a>

    <a href="change_password.php" class="<?= $activePage=='password' ? 'active' : '' ?>">
        <i class="fa-solid fa-lock"></i> Change Password
    </a>

    <a href="feedback.php" class="<?= $activePage=='feedback' ? 'active' : '' ?>">
        <i class="fa-solid fa-comment-dots"></i> Feedback
    </a>

    <a href="../login/logout.php">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>

</div>

        <!-- ========== RIGHT CONTENT AREA ========== -->
        <div class="account-content">