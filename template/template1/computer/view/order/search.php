<?php $this->load('common/header_all'); ?>

<main class="member-shell">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <a href="<?php echo url('order/index'); ?>">My Orders</a>
        <span>/</span>
        <span>Search Results</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Search Orders</h1>
                        <p class="member-card-subtitle">
                            <?php if (!empty($keyword)) { ?>
                            Showing orders matching "<strong><?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?></strong>"
                            <?php } else { ?>
                            Search by order number or product name.
                            <?php } ?>
                        </p>
                    </div>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">Back to All Orders</a>
                </div>

                <div class="order-toolbar">
                    <form class="order-search-box" action="<?php echo url('order/search'); ?>" method="get">
                        <input type="search" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Order # or product name..." aria-label="Search orders">
                        <button type="submit" class="btn-member-primary" style="height:36px;">Search</button>
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
                        <div class="order-card-foot">
                            <div class="order-total-summary">
                                Total: <strong><?php echo htmlspecialchars(($order['currency_symbol'] ?? '$') . number_format((float)($order['order_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                            <div class="order-action-group">
                                <a href="<?php echo url('order/detail', ['id' => $order['order_id']]); ?>" class="btn-member-outline">Order Details</a>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <h3>No matching orders</h3>
                    <p>We couldn't find any orders matching your search query.</p>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">View All Orders</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
