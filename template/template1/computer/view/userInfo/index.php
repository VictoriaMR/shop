<?php $this->load('common/header_all'); ?>

<main class="member-shell container">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <span>My Account</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <?php if (!empty($info)) { ?>
            <!-- Profile Hero Banner -->
            <section class="profile-overview-card">
                <div class="profile-hero-left">
                    <div class="profile-avatar-wrap">
                        <?php if (!empty($info['avatar'])) { ?>
                        <img src="<?php echo htmlspecialchars($info['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" width="68" height="68">
                        <?php } else { ?>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?php } ?>
                    </div>
                    <div class="profile-hero-meta">
                        <h2><?php echo htmlspecialchars(!empty($info['name']) ? $info['name'] : ($info['email'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p>
                            <?php echo htmlspecialchars($info['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($info['mobile'])) { ?>
                            &middot; <?php echo htmlspecialchars($info['mobile'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php } ?>
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-edit-profile" id="js-toggle-edit-profile">Edit Profile</button>
            </section>

            <!-- Collapsible Edit Profile Form -->
            <section class="member-card profile-edit-panel" id="js-profile-edit-panel">
                <div class="member-card-header">
                    <div>
                        <h2 class="member-card-title">Personal Information</h2>
                        <p class="member-card-subtitle">Update your name and contact phone number.</p>
                    </div>
                </div>
                <form id="js-profile-form">
                    <div class="profile-form-grid">
                        <div class="profile-field">
                            <label for="pf-first-name">First Name *</label>
                            <input type="text" id="pf-first-name" name="first_name" value="<?php echo htmlspecialchars($info['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="profile-field">
                            <label for="pf-last-name">Last Name *</label>
                            <input type="text" id="pf-last-name" name="last_name" value="<?php echo htmlspecialchars($info['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="profile-field">
                            <label for="pf-email">Email Address</label>
                            <input type="email" id="pf-email" value="<?php echo htmlspecialchars($info['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>
                        <div class="profile-field">
                            <label for="pf-phone">Mobile Phone *</label>
                            <div class="profile-phone-group">
                                <select name="dialing_code" aria-label="Country Dialing Code">
                                    <?php
                                    $curDial = ltrim($info['dialing_code'] ?? '+1', '+');
                                    $seenCodes = [];
                                    if (!empty($countryList)) {
                                        foreach ($countryList as $c) {
                                            $dc = trim($c['dialing_code'] ?? '');
                                            if ($dc === '' || isset($seenCodes[$dc])) continue;
                                            $seenCodes[$dc] = true;
                                    ?>
                                    <option value="+<?php echo htmlspecialchars($dc, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $curDial === $dc ? 'selected' : ''; ?>>+<?php echo htmlspecialchars($dc, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php } } else { ?>
                                    <option value="+1">+1</option>
                                    <?php } ?>
                                </select>
                                <input type="tel" id="pf-phone" name="phone" value="<?php echo htmlspecialchars($info['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Phone number" required>
                            </div>
                        </div>
                    </div>
                    <div class="profile-form-actions">
                        <button type="button" class="btn-member-outline" id="js-cancel-edit-profile">Cancel</button>
                        <button type="submit" class="btn-member-primary" id="js-save-profile-btn">Save Changes</button>
                    </div>
                </form>
            </section>

            <!-- Account Statistics Grid -->
            <div class="member-stats-grid">
                <a href="<?php echo url('userInfo/wishList'); ?>" class="member-stat-box">
                    <span class="member-stat-label">Saved Wishlist</span>
                    <span class="member-stat-value"><?php echo (int)($collectionTotal ?? 0); ?></span>
                </a>
                <a href="<?php echo url('userInfo/address'); ?>" class="member-stat-box">
                    <span class="member-stat-label">Saved Addresses</span>
                    <span class="member-stat-value"><?php echo (int)($addressTotal ?? 0); ?></span>
                </a>
                <a href="<?php echo url('userInfo/history'); ?>" class="member-stat-box">
                    <span class="member-stat-label">Recently Viewed</span>
                    <span class="member-stat-value"><?php echo (int)($historyTotal ?? 0); ?></span>
                </a>
                <a href="<?php echo url('userInfo/coupon'); ?>" class="member-stat-box">
                    <span class="member-stat-label">Available Coupons</span>
                    <span class="member-stat-value">0</span>
                </a>
            </div>

            <!-- Order Status Breakdown -->
            <div class="order-status-strip">
                <a href="<?php echo url('order/index', ['status' => 1]); ?>" class="status-pill-card">
                    <div class="status-pill-info">
                        <span>Unpaid</span>
                        <strong><?php echo (int)($orderTotal[1] ?? 0); ?></strong>
                    </div>
                    <div class="status-pill-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                </a>
                <a href="<?php echo url('order/index', ['status' => 2]); ?>" class="status-pill-card">
                    <div class="status-pill-info">
                        <span>Processing</span>
                        <strong><?php echo (int)($orderTotal[2] ?? 0); ?></strong>
                    </div>
                    <div class="status-pill-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </a>
                <a href="<?php echo url('order/index', ['status' => 3]); ?>" class="status-pill-card">
                    <div class="status-pill-info">
                        <span>Shipped</span>
                        <strong><?php echo (int)($orderTotal[3] ?? 0); ?></strong>
                    </div>
                    <div class="status-pill-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                </a>
                <a href="<?php echo url('order/index', ['status' => 4]); ?>" class="status-pill-card">
                    <div class="status-pill-info">
                        <span>Completed</span>
                        <strong><?php echo (int)($orderTotal[4] ?? 0); ?></strong>
                    </div>
                    <div class="status-pill-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </a>
            </div>

            <!-- Recent Orders Section -->
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h2 class="member-card-title">Recent Orders</h2>
                        <p class="member-card-subtitle">Track your latest purchases and delivery status.</p>
                    </div>
                    <a href="<?php echo url('order/index'); ?>" class="btn-member-outline">View All Orders</a>
                </div>

                <?php if (!empty($recentOrders)) { ?>
                <div class="recent-orders-list">
                    <?php foreach ($recentOrders as $ord) { ?>
                    <div class="recent-order-row">
                        <div class="recent-order-meta">
                            <strong>Order #<?php echo htmlspecialchars($ord['order_no'] ?? $ord['order_id'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span>Placed on <?php echo htmlspecialchars($ord['add_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?> &middot; <?php echo htmlspecialchars($ord['status_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="recent-order-right">
                            <span class="recent-order-total"><?php echo htmlspecialchars(($ord['currency_symbol'] ?? '$') . number_format((float)($ord['order_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8'); ?></span>
                            <a href="<?php echo url('order/detail', ['id' => $ord['order_id']]); ?>" class="btn-member-outline">Details</a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <h3>No orders yet</h3>
                    <p>When you place an order, your order status and tracking information will appear here.</p>
                    <a href="<?php echo url('newIn'); ?>" class="btn-member-primary">Explore New Arrivals</a>
                </div>
                <?php } ?>
            </section>

            <?php } else { ?>
            <!-- Guest Sign-In Prompt -->
            <section class="member-card">
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h2>Sign in to your account</h2>
                    <p>Access your orders, manage shipping addresses, and view your saved wishlist items.</p>
                    <div style="display:flex;gap:12px;justify-content:center;">
                        <a href="<?php echo url('login'); ?>" class="btn-member-primary">Sign In</a>
                        <a href="<?php echo url('login'); ?>?tab=register" class="btn-member-outline">Create Account</a>
                    </div>
                </div>
            </section>
            <?php } ?>
        </div>
    </div>
</main>
