<?php
// Expected: $currentStep = 1 | 2 | 3
?>

<header class="cart-header">
    <div class="header-inner">
        <div class="logo">GiftShop</div>

        <div class="steps-wrapper">

            <!-- CART -->
            <div class="step <?= ($currentStep == 1) ? 'active' : '' ?>">
                <span class="circle">1</span>
                <span class="label">Cart</span>
            </div>

            <div class="line <?= ($currentStep > 1) ? 'active-line' : '' ?>"></div>

            <!-- PAYMENT -->
            <div class="step <?= ($currentStep == 2) ? 'active' : '' ?>">
                <span class="circle">2</span>
                <span class="label">Payment</span>
            </div>

            <div class="line <?= ($currentStep > 2) ? 'active-line' : '' ?>"></div>

            <!-- SUMMARY -->
            <div class="step <?= ($currentStep == 3) ? 'active' : '' ?>">
                <span class="circle">3</span>
                <span class="label">Summary</span>
            </div>

        </div>

        <div></div>
    </div>
</header>
