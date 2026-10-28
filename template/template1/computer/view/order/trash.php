<?php $this->load('common/header_all'); ?>

<main class="member-shell">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <a href="<?php echo url('order/index'); ?>">My Orders</a>
        <span>/</span>
        <span>Deleted Orders</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Deleted Orders</h1>
                        <p class="member-card-subtitle">View or restore orders you previously removed from your main order list.</p>
                    </div>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">Back to Active Orders</a>
                </div>

                <?php if (!empty($list)) { ?>
                <div class="order-list-stack">
                    <?php foreach ($list as $order) { ?>
                    <article class="order-card">
                        <div class="order-card-head">
                            <div class="order-head-meta">
                                <span>Order <strong>#<?php echo htmlspecialchars($order['order_no'] ?? $order['order_id'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
                                <span>Placed: <?php echo htmlspecialchars($order['add_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <span class="order-status-badge"><?php echo htmlspecialchars($order['status_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="order-card-foot">
                            <div class="order-total-summary">
                                Total: <strong><?php echo htmlspecialchars(($order['currency_symbol'] ?? '$') . number_format((float)($order['order_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                            <div class="order-action-group">
                                <button type="button" class="btn-member-outline js-order-restore" data-id="<?php echo (int)$order['order_id']; ?>">Restore Order</button>
                                <a href="<?php echo url('order/detail', ['id' => $order['order_id']]); ?>" class="btn-member-outline">Details</a>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <h3>Order trash is empty</h3>
                    <p>You have no deleted orders in your trash bin.</p>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">Back to My Orders</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
