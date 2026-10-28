<?php $this->load('common/header_all'); ?>

<main class="member-shell">
    <nav class="member-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo url('index'); ?>">Home</a>
        <span>/</span>
        <a href="<?php echo url('userInfo'); ?>">My Account</a>
        <span>/</span>
        <span>Recently Viewed</span>
    </nav>

    <div class="member-layout">
        <?php $this->load('common/member_sidebar'); ?>

        <div class="member-main">
            <section class="member-card">
                <div class="member-card-header">
                    <div>
                        <h1 class="member-card-title">Recently Viewed</h1>
                        <p class="member-card-subtitle">Products you have browsed recently, organized by date.</p>
                    </div>
                </div>

                <?php if (!empty($list)) { ?>
                    <?php foreach ($list as $date => $items) { ?>
                    <div class="history-date-group">
                        <h2 class="history-date-heading"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <div class="history-grid">
                            <?php foreach ($items as $item) { ?>
                            <article class="history-card">
                                <div class="history-img-wrap">
                                    <a href="<?php echo htmlspecialchars($item['url'] ?? url('product', ['pid' => $item['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>">
                                        <img src="<?php echo htmlspecialchars($item['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                                    </a>
                                    <button type="button" class="btn-remove-history js-remove-history" data-id="<?php echo (int)($item['his_id'] ?? 0); ?>" title="Remove from History" aria-label="Remove from History">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    </button>
                                </div>
                                <div class="history-body">
                                    <a href="<?php echo htmlspecialchars($item['url'] ?? url('product', ['pid' => $item['spu_id']]), ENT_QUOTES, 'UTF-8'); ?>" class="history-title">
                                        <?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                    <span class="history-price"><?php echo htmlspecialchars(($item['currency_symbol'] ?? '$') . ($item['min_price'] ?? '0.00'), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </article>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                <?php } else { ?>
                <div class="member-empty-state">
                    <div class="member-empty-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3>No browsing history yet</h3>
                    <p>Products you view while signed in will automatically appear here for easy access.</p>
                    <a href="<?php echo url('newIn'); ?>" class="btn-member-primary">Browse Catalog</a>
                </div>
                <?php } ?>
            </section>
        </div>
    </div>
</main>
