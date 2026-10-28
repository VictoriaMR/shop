<?php
$this->load('common/header_all');
$placeholderImg = siteUrl('image/common/noimg.svg');
$keywordSafe = htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8');
?>

<main class="search-page" id="main-content">
	<div class="container">
		<!-- 1. Breadcrumb -->
		<nav class="breadcrumb-nav" aria-label="Breadcrumb">
			<ol class="breadcrumb-list">
				<li class="breadcrumb-item">
					<a href="<?php echo url(); ?>"><?php echo distT('breadcrumb_home'); ?></a>
				</li>
				<li class="breadcrumb-sep" aria-hidden="true">/</li>
				<li class="breadcrumb-item current" aria-current="page">
					<?php echo $keyword !== '' ? (distT('breadcrumb_search') . ': "' . $keywordSafe . '"') : distT('breadcrumb_search'); ?>
				</li>
			</ol>
		</nav>

		<!-- 2. Search Hero Banner -->
		<section class="search-hero-banner" aria-labelledby="search-hero-heading">
			<div>
				<span class="search-hero-eyebrow"><?php echo distT('hero_eyebrow'); ?></span>
				<h1 class="search-hero-title" id="search-hero-heading">
					<?php echo $keyword !== '' ? distT('hero_heading_kw', ['{keyword}' => $keywordSafe]) : distT('hero_heading_all'); ?>
				</h1>
				<p class="search-hero-sub">
					<?php echo $keyword !== '' ? distT('hero_sub_kw', ['{total}' => (int)$total]) : distT('hero_sub_all'); ?>
				</p>
			</div>

			<form class="search-refine-form" action="<?php echo url('search'); ?>" method="get" role="search">
				<label for="search-refine-input" class="sr-only">Search keyword</label>
				<input
					type="search"
					id="search-refine-input"
					name="keyword"
					class="search-refine-input"
					value="<?php echo $keywordSafe; ?>"
					placeholder="Refine keyword (e.g. dress, lamp, sofa)..."
				>
				<button type="submit" class="search-refine-btn">Search</button>
			</form>
		</section>

		<!-- 3. Filter & Sort Toolbar + Product Grid -->
		<section class="search-catalog-section" aria-labelledby="search-catalog-heading">
			<h2 class="sr-only" id="search-catalog-heading">Search Results Product List</h2>

			<div class="listing-toolbar flex-between">
				<div class="toolbar-left flex-align">
					<p class="result-count">
						<?php if ($total > 0) { ?>
						Showing <strong><?php echo (int)$pagination['showing_start']; ?>–<?php echo (int)$pagination['showing_end']; ?></strong> of <strong><?php echo (int)$total; ?></strong> results
						<?php } else { ?>
						<strong>0</strong> products found
						<?php } ?>
					</p>

					<?php if (!empty($priceFilters)) { ?>
					<span class="toolbar-divider" aria-hidden="true"></span>
					<div class="price-chips flex-align" role="group" aria-label="Filter by price">
						<?php foreach ($priceFilters as $pf) { ?>
						<a
							href="<?php echo htmlspecialchars($pf['url'], ENT_QUOTES, 'UTF-8'); ?>"
							class="price-chip <?php echo !empty($pf['active']) ? 'active' : ''; ?>"
							<?php echo !empty($pf['active']) ? 'aria-current="true"' : ''; ?>
						>
							<?php echo htmlspecialchars($pf['label'], ENT_QUOTES, 'UTF-8'); ?>
						</a>
						<?php } ?>
					</div>
					<?php } ?>

					<?php if (!empty($hasActiveFilter)) { ?>
					<a href="<?php echo htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8'); ?>" class="reset-filter-link">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
						<span><?php echo distT('clear_filters'); ?></span>
					</a>
					<?php } ?>
				</div>

				<?php if (!empty($sortOptions)) { ?>
				<div class="toolbar-right flex-align">
					<span class="sort-label"><?php echo distT('sort_label'); ?></span>
					<div class="sort-tabs" role="group" aria-label="Sort products">
						<?php foreach ($sortOptions as $so) { ?>
						<a
							href="<?php echo htmlspecialchars($so['url'], ENT_QUOTES, 'UTF-8'); ?>"
							class="sort-tab <?php echo !empty($so['active']) ? 'active' : ''; ?>"
							<?php echo !empty($so['active']) ? 'aria-current="true"' : ''; ?>
						>
							<?php echo htmlspecialchars($so['label'], ENT_QUOTES, 'UTF-8'); ?>
						</a>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php if (!empty($productList)) { ?>
			<div class="product-grid grid-4">
				<?php foreach ($productList as $idx => $item) {
					$isEager = !empty($item['is_eager']);
					$isFirst = ($idx === 0);
				?>
				<article class="product-card" data-spu-id="<?php echo (int)$item['spu_id']; ?>">
					<div class="product-thumb-wrap">
						<a href="<?php echo $item['url']; ?>" class="product-thumb-link" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
							<?php if ($isEager) { ?>
							<img
								class="product-thumb"
								src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
								alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
								width="300"
								height="300"
								<?php echo $isFirst ? 'fetchpriority="high"' : ''; ?>
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
							>
							<?php } else { ?>
							<img
								class="product-thumb lazyload"
								src="<?php echo $placeholderImg; ?>"
								data-src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
								alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
								width="300"
								height="300"
								loading="lazy"
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
							>
							<?php } ?>
						</a>
						<div class="product-badges">
							<?php if (!empty($item['badge'])) { ?>
							<span class="badge badge-hot"><?php echo htmlspecialchars($item['badge'], ENT_QUOTES, 'UTF-8'); ?></span>
							<?php } ?>
							<?php if (!empty($item['discount_percent'])) { ?>
							<span class="badge badge-sale">-<?php echo (int)$item['discount_percent']; ?>%</span>
							<?php } ?>
						</div>
						<button
							type="button"
							class="wishlist-btn <?php echo !empty($item['is_liked']) ? 'liked' : ''; ?>"
							data-spu-id="<?php echo (int)$item['spu_id']; ?>"
							aria-label="Add <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?> to Wishlist"
						>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo !empty($item['is_liked']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
						</button>
					</div>
					<div class="product-body">
						<?php if (!empty($item['cate_name'])) { ?>
						<span class="product-cate-tag text-ellipsis"><?php echo htmlspecialchars($item['cate_name'], ENT_QUOTES, 'UTF-8'); ?></span>
						<?php } ?>
						<h3 class="product-title">
							<a href="<?php echo $item['url']; ?>" class="line-clamp-2" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></a>
						</h3>
						<div class="product-price-row flex-between">
							<div class="price-group flex-align">
								<strong class="price-current"><?php echo htmlspecialchars($item['min_price_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
								<?php if (!empty($item['original_price_format'])) { ?>
								<del class="price-old"><?php echo htmlspecialchars($item['original_price_format'], ENT_QUOTES, 'UTF-8'); ?></del>
								<?php } ?>
							</div>
							<a href="<?php echo $item['url']; ?>" class="product-action-btn" aria-label="View <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
							</a>
						</div>
					</div>
				</article>
				<?php } ?>
			</div>

			<?php if ((int)$pagination['total_pages'] > 1) { ?>
			<nav class="pagination-wrap flex-between" aria-label="Pagination">
				<div class="pagination-summary">
					Page <strong><?php echo (int)$pagination['current']; ?></strong> of <strong><?php echo (int)$pagination['total_pages']; ?></strong>
					(<?php echo (int)$total; ?> items)
				</div>

				<ul class="pagination">
					<li>
						<?php if (!empty($pagination['prev_url'])) { ?>
						<a href="<?php echo htmlspecialchars($pagination['prev_url'], ENT_QUOTES, 'UTF-8'); ?>" class="page-link" aria-label="Previous page">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
							<span>Prev</span>
						</a>
						<?php } else { ?>
						<span class="page-link disabled" aria-disabled="true">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
							<span>Prev</span>
						</span>
						<?php } ?>
					</li>

					<?php foreach ($pagination['pages'] as $pg) { ?>
						<?php if ($pg['type'] === 'ellipsis') { ?>
						<li><span class="page-ellipsis" aria-hidden="true">&hellip;</span></li>
						<?php } else { ?>
						<li>
							<a
								href="<?php echo htmlspecialchars($pg['url'], ENT_QUOTES, 'UTF-8'); ?>"
								class="page-link <?php echo !empty($pg['current']) ? 'active' : ''; ?>"
								aria-label="Page <?php echo (int)$pg['num']; ?>"
								<?php echo !empty($pg['current']) ? 'aria-current="page"' : ''; ?>
							>
								<?php echo (int)$pg['num']; ?>
							</a>
						</li>
						<?php } ?>
					<?php } ?>

					<li>
						<?php if (!empty($pagination['next_url'])) { ?>
						<a href="<?php echo htmlspecialchars($pagination['next_url'], ENT_QUOTES, 'UTF-8'); ?>" class="page-link" aria-label="Next page">
							<span>Next</span>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
						</a>
						<?php } else { ?>
						<span class="page-link disabled" aria-disabled="true">
							<span>Next</span>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
						</span>
						<?php } ?>
					</li>
				</ul>

				<form class="page-jump-form flex-align" id="search-page-jump-form" action="<?php echo url('search'); ?>" method="get">
					<?php if ($keyword !== '') { ?>
					<input type="hidden" name="keyword" value="<?php echo $keywordSafe; ?>">
					<?php } ?>
					<?php if ($sort !== 'featured') { ?>
					<input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
					<?php } ?>
					<?php if ($priceRange !== 'all') { ?>
					<input type="hidden" name="price" value="<?php echo htmlspecialchars($priceRange, ENT_QUOTES, 'UTF-8'); ?>">
					<?php } ?>
					<label for="search-page-jump-input">Go to</label>
					<input
						type="number"
						id="search-page-jump-input"
						class="page-jump-input"
						name="page"
						min="1"
						max="<?php echo (int)$pagination['total_pages']; ?>"
						value="<?php echo (int)$pagination['current']; ?>"
						aria-label="Page number"
					>
					<button type="submit" class="btn btn-outline btn-sm">Go</button>
				</form>
			</nav>
			<?php } ?>

			<?php } else { ?>
			<div class="empty-state">
				<div class="empty-icon" aria-hidden="true">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
				</div>
				<h3 class="empty-title"><?php echo distT('empty_title', ['{keyword}' => $keywordSafe ?: 'your filter']); ?></h3>
				<p class="empty-desc"><?php echo distT('empty_desc'); ?></p>
				<a href="<?php echo url('category'); ?>" class="btn btn-primary">Browse All Categories</a>
			</div>

			<?php if (!empty($recommendList)) { ?>
			<div class="search-recommend-section">
				<div class="section-header flex-between">
					<div>
						<div class="section-eyebrow">CURATED FOR YOU</div>
						<h3 class="section-title"><?php echo distT('recommend_heading'); ?></h3>
						<p class="section-subtitle"><?php echo distT('recommend_sub'); ?></p>
					</div>
				</div>
				<div class="product-grid grid-4">
					<?php foreach ($recommendList as $item) { ?>
					<article class="product-card" data-spu-id="<?php echo (int)$item['spu_id']; ?>">
						<div class="product-thumb-wrap">
							<a href="<?php echo $item['url']; ?>" class="product-thumb-link" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
								<img
									class="product-thumb lazyload"
									src="<?php echo $placeholderImg; ?>"
									data-src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
									alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
									width="300"
									height="300"
									loading="lazy"
									onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
								>
							</a>
							<div class="product-badges">
								<span class="badge badge-sale">-<?php echo (int)$item['discount_percent']; ?>%</span>
							</div>
						</div>
						<div class="product-body">
							<h4 class="product-title">
								<a href="<?php echo $item['url']; ?>" class="line-clamp-2"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></a>
							</h4>
							<div class="product-price-row flex-between">
								<div class="price-group flex-align">
									<strong class="price-current"><?php echo htmlspecialchars($item['min_price_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
									<del class="price-old"><?php echo htmlspecialchars($item['original_price_format'], ENT_QUOTES, 'UTF-8'); ?></del>
								</div>
								<a href="<?php echo $item['url']; ?>" class="product-action-btn" aria-label="View <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
								</a>
							</div>
						</div>
					</article>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
		</section>
	</div>
</main>
