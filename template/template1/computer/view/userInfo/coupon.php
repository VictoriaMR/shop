<?php $this->load('common/header_all'); ?>

<main class="member-shell container">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <span>Coupons &amp; Vouchers</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Coupons &amp; Vouchers</h1>
                        <p class="member-card-subtitle">Redeem promotional codes and manage your active discounts.</p>
                    </div>
                </div>

                <form class="coupon-redeem-bar" id="js-coupon-redeem-form">
                    <input type="text" id="js-coupon-code" placeholder="Enter coupon or promo code" aria-label="Promo code">
                    <button type="submit" class="btn-member-primary">Redeem</button>
                </form>

                <div class="coupon-tabs">
                    <button type="button" class="coupon-tab is-active js-coupon-tab">Available (0)</button>
                    <button type="button" class="coupon-tab js-coupon-tab">Used (0)</button>
                    <button type="button" class="coupon-tab js-coupon-tab">Expired (0)</button>
                </div>

                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <h2>No active coupons available</h2>
                    <p>Subscribe to our newsletter or check seasonal promotions for exclusive voucher codes.</p>
                    <a href="<?php echo url('newIn'); ?>" class="btn-member-outline">Shop New Arrivals</a>
                </div>
            </section>
        </div>
    </div>
</main>
