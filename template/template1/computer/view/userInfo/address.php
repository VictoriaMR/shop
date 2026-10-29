<?php $this->load('common/header_all'); ?>

<main class="member-shell container">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <span>Address Book</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Address Book</h1>
                        <p class="member-card-subtitle">Manage your shipping and billing addresses for faster checkout.</p>
                    </div>
                    <button type="button" class="btn-member-primary" id="js-add-new-address">
                        + Add New Address
                    </button>
                </div>

                <?php if (!empty($list)) { ?>
                <div class="address-book-grid">
                    <?php foreach ($list as $addr) { ?>
                    <article class="address-book-card <?php echo !empty($addr['is_default']) ? 'is-default' : ''; ?>">
                        <div class="address-card-top">
                            <div class="address-card-header">
                                <span class="address-recipient">
                                    <?php echo htmlspecialchars(trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <div class="address-badges">
                                    <?php if (!empty($addr['is_default'])) { ?>
                                    <span class="addr-badge shipping">Default Shipping</span>
                                    <?php } ?>
                                    <?php if (!empty($addr['is_bill'])) { ?>
                                    <span class="addr-badge billing">Default Billing</span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="address-lines">
                                <?php if (!empty($addr['company_name'])) { ?>
                                <p><?php echo htmlspecialchars($addr['company_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php } ?>
                                <p><?php echo htmlspecialchars($addr['address1'] ?? '', ENT_QUOTES, 'UTF-8'); ?><?php echo !empty($addr['address2']) ? ', ' . htmlspecialchars($addr['address2'], ENT_QUOTES, 'UTF-8') : ''; ?></p>
                                <p><?php echo htmlspecialchars(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postcode'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><?php echo htmlspecialchars($addr['country'] ?? ($addr['country_code2'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p>Phone: <?php echo htmlspecialchars($addr['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>

                        <div class="address-card-actions">
                            <div class="address-default-links">
                                <?php if (empty($addr['is_default'])) { ?>
                                <button type="button" class="addr-action-btn js-set-default-shipping" data-id="<?php echo (int)$addr['address_id']; ?>">Set Default Shipping</button>
                                <?php } ?>
                                <?php if (empty($addr['is_bill'])) { ?>
                                <button type="button" class="addr-action-btn js-set-default-billing" data-id="<?php echo (int)$addr['address_id']; ?>">Set Default Billing</button>
                                <?php } ?>
                            </div>
                            <div class="address-crud-links">
                                <button type="button" class="addr-action-btn js-edit-addr" data-id="<?php echo (int)$addr['address_id']; ?>">Edit</button>
                                <button type="button" class="addr-action-btn danger js-delete-addr" data-id="<?php echo (int)$addr['address_id']; ?>">Delete</button>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <h2>No saved addresses yet</h2>
                    <p>Add a shipping address now to speed up your future checkout experience.</p>
                    <button type="button" class="btn-member-primary" onclick="window.ShopAddressModal && window.ShopAddressModal.open()">+ Add Shipping Address</button>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>

<?php $this->load('common/address'); ?>
