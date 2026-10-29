<?php $this->load('common/header_all'); ?>

<main class="member-shell container">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <span>My Wishlist</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Saved Wishlist</h1>
                        <p class="member-card-subtitle">Keep track of items you love and move them to your shopping bag anytime.</p>
                    </div>
                </div>

                <?php if (!empty($list)) { ?>
                <div class="wishlist-grid">
                    <?php foreach ($list as $item) { ?>
                    <article class="wishlist-card">
                        <div class="wishlist-img-wrap">
                            <a href="<?php echo htmlspecialchars($item['url'] ?? url('product', ['pid' => $item['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="<?php echo htmlspecialchars($item['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                            </a>
                            <button type="button" class="btn-remove-wish js-remove-wishlist" data-spu-id="<?php echo (int)$item['spu_id']; ?>" title="Remove from Wishlist" aria-label="Remove from Wishlist">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="wishlist-body">
                            <a href="<?php echo htmlspecialchars($item['url'] ?? url('product', ['pid' => $item['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>" class="wishlist-title">
                                <?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <div class="wishlist-footer">
                                <span class="wishlist-price"><?php echo htmlspecialchars(($item['currency_symbol'] ?? '$') . ($item['min_price'] ?? '0.00'), ENT_QUOTES, 'UTF-8'); ?></span>
                                <a href="<?php echo htmlspecialchars($item['url'] ?? url('product', ['pid' => $item['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>" class="btn-member-outline">View Item</a>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </div>
                    <h2>Your wishlist is empty</h2>
                    <p>Tap the heart icon on any product to save your favorite styles here for later.</p>
                    <a href="<?php echo url('newIn'); ?>" class="btn-member-primary">Discover New Arrivals</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
