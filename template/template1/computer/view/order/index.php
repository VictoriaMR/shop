<?php $this->load('common/header_all'); ?>

<main class="member-shell">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <span>My Orders</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">My Orders</h1>
                        <p class="member-card-subtitle">Track, manage, or buy items again from your order history.</p>
                    </div>
                    <a href="<?php echo url('order/trash'); ?>" class="order-trash-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        Deleted Orders
                    </a>
                </div>

                <?php $curStatus = isset($status) && $status !== '' ? (int)$status : -1; ?>
                <div class="order-toolbar">
                    <div class="order-filter-tabs">
                        <a href="<?php echo url('order/index'); ?>" class="order-tab-link <?php echo $curStatus === -1 ? 'is-active' : ''; ?>">All Orders</a>
                        <a href="<?php echo url('order/index', ['status' => 1]); ?>" class="order-tab-link <?php echo $curStatus === 1 ? 'is-active' : ''; ?>">Unpaid</a>
                        <a href="<?php echo url('order/index', ['status' => 2]); ?>" class="order-tab-link <?php echo $curStatus === 2 ? 'is-active' : ''; ?>">Processing</a>
                        <a href="<?php echo url('order/index', ['status' => 3]); ?>" class="order-tab-link <?php echo $curStatus === 3 ? 'is-active' : ''; ?>">Shipped</a>
                        <a href="<?php echo url('order/index', ['status' => 4]); ?>" class="order-tab-link <?php echo $curStatus === 4 ? 'is-active' : ''; ?>">Completed</a>
                    </div>

                    <form class="order-search-box" action="<?php echo url('order/search'); ?>" method="get">
                        <input type="search" name="keyword" placeholder="Order # or product name..." aria-label="Search orders">
                        <button type="submit" class="btn-member-outline" style="height:36px;">Search</button>
                    </form>
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
                            <span class="order-status-badge status-<?php echo (int)($order['status'] ?? 0); ?>">
                                <?php echo htmlspecialchars($order['status_text'] ?? 'Pending', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>

                        <div class="order-card-body">
                            <?php if (!empty($order['product'])) { foreach ($order['product'] as $prod) { ?>
                            <div class="order-product-row">
                                <div class="order-product-left">
                                    <a href="<?php echo htmlspecialchars($prod['url'] ?? url('product', ['pid' => $prod['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>" class="order-product-thumb">
                                        <img src="<?php echo htmlspecialchars($prod['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($prod['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                                    </a>
                                    <div class="order-product-info">
                                        <h4><a href="<?php echo htmlspecialchars($prod['url'] ?? url('product', ['pid' => $prod['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($prod['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a></h4>
                                        <?php if (!empty($prod['attr'])) { ?>
                                        <div class="order-product-attrs">
                                            <?php
                                            $attrParts = [];
                                            foreach ($prod['attr'] as $at) {
                                                $attrParts[] = ($at['attr_name'] ?? '') . ': ' . ($at['attr_value'] ?? '');
                                            }
                                            echo htmlspecialchars(implode(' / ', $attrParts), ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </div>
                                        <?php } ?>
                                        <div class="order-product-qty">Qty: <?php echo (int)($prod['quantity'] ?? 1); ?></div>
                                    </div>
                                </div>
                                <div class="order-product-price">
                                    <?php echo htmlspecialchars(($order['currency_symbol'] ?? '$') . number_format((float)($prod['price'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                            <?php } } ?>
                        </div>

                        <div class="order-card-foot">
                            <div class="order-total-summary">
                                Total: <strong><?php echo htmlspecialchars(($order['currency_symbol'] ?? '$') . number_format((float)($order['order_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                            <div class="order-action-group">
                                <a href="<?php echo url('order/detail', ['id' => $order['order_id']]); ?>" class="btn-member-outline">Order Details</a>
                                <?php if (($order['status'] ?? 0) == 1) { ?>
                                <a href="<?php echo url('checkout/payOrder', ['order_id' => $order['order_id']]); ?>" class="btn-member-primary">Pay Now</a>
                                <button type="button" class="btn-member-outline js-order-cancel" data-id="<?php echo (int)$order['order_id']; ?>">Cancel</button>
                                <?php } elseif (($order['status'] ?? 0) == 3) { ?>
                                <button type="button" class="btn-member-primary js-order-complete" data-id="<?php echo (int)$order['order_id']; ?>">Confirm Receipt</button>
                                <?php } ?>
                                <button type="button" class="btn-member-outline js-order-repurchase" data-id="<?php echo (int)$order['order_id']; ?>">Buy Again</button>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <h3>No orders found</h3>
                    <p>You don't have any orders matching the selected filter yet.</p>
                    <a href="<?php echo url('newIn'); ?>" class="btn-member-primary">Start Shopping</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
