<?php $this->load('common/header_all'); ?>

<main class="faq-shell">
    <section class="faq-hero">
        <h1>Help Center &amp; Policies</h1>
        <p>Find answers about shipping, returns, sizing, orders, and store policies.</p>
        <form class="faq-search-form" action="<?php echo url('faq'); ?>" method="get">
            <input type="search" name="q" value="<?php echo htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search help articles (e.g., return policy, shipping, privacy)..." aria-label="Search FAQ">
            <button type="submit">Search</button>
        </form>
    </section>

    <div class="faq-layout">
        <!-- Sidebar Topics -->
        <aside class="faq-sidebar" aria-label="Help Topics">
            <h2 class="faq-sidebar-title">Browse Topics</h2>
            <ul class="faq-topic-nav">
                <li>
                    <a href="<?php echo url('faq'); ?>" class="faq-topic-link <?php echo (empty($activeGroupId) && empty($activeArticle)) ? 'is-active' : ''; ?>">
                        <span>All Help Topics</span>
                    </a>
                </li>
                <?php if (!empty($groups)) { foreach ($groups as $g) { ?>
                <li>
                    <a href="<?php echo url('faq', ['g' => $g['group_id']]); ?>" class="faq-topic-link <?php echo ((int)($activeGroupId ?? 0) === (int)$g['group_id']) ? 'is-active' : ''; ?>">
                        <span><?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                </li>
                <?php } } ?>
            </ul>

            <div style="margin-top:24px;padding-top:18px;border-top:1px solid #f1f5f9;">
                <p style="font-size:12px;color:#64748b;margin:0 0 10px;">Need personalized support?</p>
                <a href="<?php echo url('contact'); ?>" class="btn-member-outline" style="width:100%;display:inline-flex;align-items:center;justify-content:center;height:38px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-weight:600;color:#0f172a;text-decoration:none;">Contact Customer Care</a>
            </div>
        </aside>

        <!-- Main FAQ Content -->
        <div class="faq-content-card">
            <?php if (!empty($activeArticle)) { ?>
                <article>
                    <header class="faq-article-header">
                        <span class="faq-article-badge"><?php echo htmlspecialchars($activeArticle['group_name'] ?? 'Policy', ENT_QUOTES, 'UTF-8'); ?></span>
                        <h2><?php echo htmlspecialchars($activeArticle['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    </header>
                    <div class="faq-article-prose">
                        <?php echo $activeArticle['content']; ?>
                    </div>
                    <div style="margin-top:32px;padding-top:20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                        <a href="<?php echo url('faq', !empty($activeArticle['group_id']) ? ['g' => $activeArticle['group_id']] : []); ?>" style="font-size:13px;font-weight:600;color:#0f172a;text-decoration:none;">&larr; Back to Topic Articles</a>
                        <a href="<?php echo url('contact'); ?>" style="font-size:13px;font-weight:600;color:#475569;text-decoration:none;">Still have questions? Contact Us</a>
                    </div>
                </article>
            <?php } else { ?>
                <?php
                $groupedArticles = [];
                foreach (($articles ?? []) as $art) {
                    if (!empty($activeGroupId) && (int)$art['group_id'] !== (int)$activeGroupId) {
                        continue;
                    }
                    $groupedArticles[$art['group_name']][] = $art;
                }
                ?>
                <?php if (!empty($groupedArticles)) { ?>
                    <?php foreach ($groupedArticles as $groupName => $items) { ?>
                    <section class="faq-group-section">
                        <h2 class="faq-group-heading"><?php echo htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <?php foreach ($items as $idx => $item) { ?>
                        <div class="faq-accordion-item <?php echo $idx === 0 ? 'is-open' : ''; ?>">
                            <button type="button" class="faq-accordion-trigger js-faq-trigger">
                                <span><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="faq-accordion-panel">
                                <?php echo $item['content']; ?>
                                <div style="margin-top:12px;">
                                    <a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" style="font-size:12px;font-weight:600;color:#0f172a;">Permalink &rarr;</a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </section>
                    <?php } ?>
                <?php } else { ?>
                    <div style="text-align:center;padding:40px 20px;">
                        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 8px;">No matching articles found</h3>
                        <p style="font-size:14px;color:#64748b;margin:0 0 20px;">Try searching with different keywords or browse all help topics.</p>
                        <a href="<?php echo url('faq'); ?>" style="display:inline-flex;align-items:center;height:40px;padding:0 20px;background:#0f172a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">View All Articles</a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</main>
