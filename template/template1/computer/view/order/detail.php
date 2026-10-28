<?php $this->load('common/header_all'); ?>
<?php
$base = $orderInfo['base'] ?? [];
$products = $orderInfo['product'] ?? [];
$addresses = $orderInfo['address'] ?? [];
$sym = $base['currency_symbol'] ?? '$';
?>

<main class="member-shell">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <a href="<?php echo url('order/index'); ?>">My Orders</a>
        <span>/</span>
        <span>Order Details</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <?php if (!empty($base)) { ?>
                <div class="order-detail-banner">
                    <div class="order-detail-meta">
                        <h2>Order #<?php echo htmlspecialchars($base['order_no'] ?? $base['order_id'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p>Placed on <?php echo htmlspecialchars($base['add_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?> &middot; Status: <strong><?php echo htmlspecialchars($base['status_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <?php if (($base['status'] ?? 0) == 1) { ?>
                        <a href="<?php echo url('checkout/payOrder', ['order_id' => $base['order_id']]); ?>" class="btn-member-primary">Complete Payment</a>
                        <?php } ?>
                        <button type="button" class="btn-member-outline js-detail-repurchase" data-id="<?php echo (int)$base['order_id']; ?>">Buy Again</button>
                    </div>
                </div>

                <?php if (!empty($addresses)) { ?>
                <div class="order-detail-addresses">
                    <?php foreach ($addresses as $addr) { ?>
                    <div class="detail-addr-box">
                        <h3><?php echo ($addr['type'] ?? 0) == 1 ? 'Billing Address' : 'Shipping Address'; ?></h3>
                        <p><strong><?php echo htmlspecialchars(trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p><?php echo htmlspecialchars(($addr['address1'] ?? '') . (!empty($addr['address2']) ? ', ' . $addr['address2'] : ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php echo htmlspecialchars(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postcode'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php echo htmlspecialchars($addr['country'] ?? ($addr['country_code2'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($addr['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

                <h3 style="font-size:15px;font-weight:700;margin:0 0 12px;">Order Items</h3>
                <div style="border-top:1px solid #f1f5f9;">
                    <?php foreach ($products as $prod) { ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #f1f5f9;gap:16px;">
                        <div style="display:flex;align-items:center;gap:14px;">
                            <img src="<?php echo htmlspecialchars($prod['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($prod['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="width:60px;height:75px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:4px;"><?php echo htmlspecialchars($prod['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div style="font-size:12px;color:#64748b;">Qty: <?php echo (int)($prod['quantity'] ?? 1); ?> &times; <?php echo htmlspecialchars($sym . number_format((float)($prod['price'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:#0f172a;">
                            <?php echo htmlspecialchars($sym . number_format((float)($prod['price'] ?? 0) * (int)($prod['quantity'] ?? 1), 2), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="detail-totals-panel">
                    <div class="detail-total-row">
                        <span>Items Subtotal</span>
                        <span><?php echo htmlspecialchars($sym . number_format((float)($base['product_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-total-row">
                        <span>Shipping Fee</span>
                        <span><?php echo htmlspecialchars($sym . number_format((float)($base['shipping_fee'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-total-row grand">
                        <span>Order Total</span>
                        <span><?php echo htmlspecialchars($sym . number_format((float)($base['order_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <h3>Order not found</h3>
                    <p><?php echo htmlspecialchars($error ?: 'We could not find the requested order details.', ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">Back to My Orders</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
