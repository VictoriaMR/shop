<?php
$this->load('common/header_all');

$siteName = $siteName ?? \App::get('domain', 'name', 'Shop');
$cid = (int)($cid ?? 0);
$rootCateId = (int)($rootCateId ?? 0);
$activeCateName = $activeCateName ?? distT('all_categories_heading');
$cateMeta = $cateMeta ?? [
	'eyebrow' => 'CURATED DEPARTMENT CATALOG',
	'desc'    => '',
	'theme'   => 'theme-indigo',
	'icon'    => 'default',
];
$catalogTotal = (int)($catalogTotal ?? 0);
$total = (int)($total ?? 0);
$sort = $sort ?? 'featured';
$priceRange = $priceRange ?? 'all';
$hasActiveFilter = !empty($hasActiveFilter);
$resetUrl = $resetUrl ?? url('category');
$baseCateUrl = $baseCateUrl ?? url('category');
$crumbs = $crumbs ?? [];
$categoryPills = $categoryPills ?? [];
$subCategories = $subCategories ?? [];
$priceFilters = $priceFilters ?? [];
$sortOptions = $sortOptions ?? [];
$productList = $productList ?? [];
$recommendList = $recommendList ?? [];
$pagination = $pagination ?? [
	'total'         => 0,
	'size'          => 20,
	'current'       => 1,
	'total_pages'   => 0,
	'showing_start' => 0,
	'showing_end'   => 0,
	'prev_url'      => '',
	'next_url'      => '',
	'pages'         => [],
];
$placeholderImg = siteUrl('image/common/noimg.svg');

// Structured Data (BreadcrumbList + CollectionPage)
$breadcrumbElements = [];
foreach ($crumbs as $idx => $crumb) {
	$breadcrumbElements[] = [
		'@type'    => 'ListItem',
		'position' => $idx + 1,
		'name'     => $crumb['name'],
		'item'     => $crumb['url'],
	];
}
$itemListElements = [];
foreach ($productList as $idx => $prod) {
	$itemListElements[] = [
		'@type'    => 'ListItem',
		'position' => $idx + 1,
		'url'      => $prod['url'],
		'name'     => $prod['name'],
	];
}
?>
<main class="cate-main" id="main-content">
	<script type="application/ld+json">
	<?php echo json_encode([
		'@context' => 'https://schema.org',
		'@graph'   => [
			[
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $breadcrumbElements,
			],
			[
				'@type'       => 'CollectionPage',
				'name'        => $activeCateName,
				'description' => $_desc ?? $cateMeta['desc'],
				'url'         => $_canonical ?? $baseCateUrl,
				'mainEntity'  => [
					'@type'           => 'ItemList',
					'numberOfItems'   => $total,
					'itemListElement' => $itemListElements,
				],
			],
		],
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);?>
	</script>

	<div class="container">
		<!-- 1. Breadcrumb Navigation -->
		<?php if (!empty($crumbs)) { ?>
		<nav class="breadcrumb-nav" aria-label="Breadcrumb">
			<ol class="breadcrumb-list">
				<?php
				$lastIdx = count($crumbs) - 1;
				foreach ($crumbs as $idx => $crumb) {
					$isLast = ($idx === $lastIdx);
				?>
				<li class="breadcrumb-item <?php echo $isLast ? 'active' : '';?>" <?php echo $isLast ? 'aria-current="page"' : '';?>>
					<?php if (!$isLast) { ?>
					<a href="<?php echo htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8');?></a>
					<span class="breadcrumb-sep" aria-hidden="true">/</span>
					<?php } else { ?>
					<span><?php echo htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8');?></span>
					<?php } ?>
				</li>
				<?php } ?>
			</ol>
		</nav>
		<?php } ?>

		<!-- 2. Category Header Banner -->
		<section class="cate-banner" aria-labelledby="cate-page-title">
			<div class="cate-banner-inner <?php echo htmlspecialchars($cateMeta['theme'] ?? 'theme-indigo', ENT_QUOTES, 'UTF-8');?>">
				<div class="cate-banner-left">
					<div class="cate-emblem" aria-hidden="true">
						<?php switch ($cateMeta['icon'] ?? 'default') {
							case 'fashion': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
								<?php break;
							case 'furniture': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3"/><path d="M2 11v5a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H6v-2a2 2 0 0 0-4 0Z"/><path d="M4 18v2"/><path d="M20 18v2"/></svg>
								<?php break;
							case 'lighting': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
								<?php break;
							case 'outdoor': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m8 3 4 8 5-5 5 15H2L8 3z"/></svg>
								<?php break;
							default: ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
						<?php } ?>
					</div>
					<div>
						<div class="cate-banner-badges flex-align">
							<span class="badge badge-dark"><?php echo htmlspecialchars($cateMeta['eyebrow'], ENT_QUOTES, 'UTF-8');?></span>
							<span class="badge badge-accent">DIRECT STUDIO PRICING</span>
						</div>
						<h1 class="cate-banner-title" id="cate-page-title"><?php echo htmlspecialchars($activeCateName, ENT_QUOTES, 'UTF-8');?></h1>
						<p class="cate-banner-desc"><?php echo htmlspecialchars($cateMeta['desc'], ENT_QUOTES, 'UTF-8');?></p>
					</div>
				</div>

				<div class="cate-banner-right">
					<div class="cate-metric-box">
						<strong class="cate-metric-num"><?php echo $total > 0 ? $total : $catalogTotal;?>+</strong>
						<span class="cate-metric-label">Curated Styles</span>
					</div>
					<div class="cate-metric-box">
						<strong class="cate-metric-num accent">UP TO 35%</strong>
						<span class="cate-metric-label">Member Savings</span>
					</div>
				</div>
			</div>
		</section>

		<!-- 3. Primary Department Filter Pills (.filter-pills-wrap with Fixed Pseudo-Static URLs) -->
		<?php if (!empty($categoryPills)) { ?>
		<nav class="filter-pills-wrap" aria-label="Filter by department">
			<ul class="filter-pills">
				<?php foreach ($categoryPills as $pill) { ?>
				<li>
					<a
						href="<?php echo htmlspecialchars($pill['url'], ENT_QUOTES, 'UTF-8');?>"
						class="filter-pill <?php echo !empty($pill['active']) ? 'active' : '';?>"
						<?php echo !empty($pill['active']) ? 'aria-current="page"' : '';?>
					>
						<span><?php echo htmlspecialchars($pill['name'], ENT_QUOTES, 'UTF-8');?></span>
						<span class="pill-count"><?php echo (int)$pill['count'];?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</nav>
		<?php } ?>

		<!-- 4. Subcategory Quick-Filter Bar (Fixed Pseudo-Static URLs) -->
		<?php if (!empty($subCategories)) { ?>
		<nav class="subcate-panel" aria-label="Subcategories">
			<span class="subcate-label"><?php echo distT('subcategories_label');?></span>
			<ul class="subcate-list">
				<?php foreach ($subCategories as $sub) { ?>
				<li>
					<a
						href="<?php echo htmlspecialchars($sub['url'], ENT_QUOTES, 'UTF-8');?>"
						class="subcate-chip <?php echo !empty($sub['active']) ? 'active' : '';?>"
						<?php echo !empty($sub['active']) ? 'aria-current="page"' : '';?>
					>
						<span><?php echo htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8');?></span>
						<?php if (!empty($sub['count'])) { ?>
						<span class="subcate-count">(<?php echo (int)$sub['count'];?>)</span>
						<?php } ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</nav>
		<?php } ?>

		<!-- 5. Listing Toolbar + Product Grid -->
		<section class="cate-catalog-section" aria-labelledby="cate-catalog-heading">
			<h2 class="sr-only" id="cate-catalog-heading"><?php echo htmlspecialchars($activeCateName, ENT_QUOTES, 'UTF-8');?> Product Grid</h2>

			<div class="listing-toolbar flex-between">
				<div class="toolbar-left flex-align">
					<p class="result-count">
						<?php if ($total > 0) { ?>
						Showing <strong><?php echo (int)$pagination['showing_start'];?>–<?php echo (int)$pagination['showing_end'];?></strong> of <strong><?php echo $total;?></strong> products
						<?php } else { ?>
						<strong>0</strong> products found
						<?php } ?>
					</p>

					<?php if (!empty($priceFilters)) { ?>
					<span class="toolbar-divider" aria-hidden="true"></span>
					<div class="price-chips flex-align" role="group" aria-label="Filter by price">
						<?php foreach ($priceFilters as $pf) { ?>
						<a
							href="<?php echo htmlspecialchars($pf['url'], ENT_QUOTES, 'UTF-8');?>"
							class="price-chip <?php echo !empty($pf['active']) ? 'active' : '';?>"
							<?php echo !empty($pf['active']) ? 'aria-current="true"' : '';?>
						>
							<?php echo htmlspecialchars($pf['label'], ENT_QUOTES, 'UTF-8');?>
						</a>
						<?php } ?>
					</div>
					<?php } ?>

					<?php if ($hasActiveFilter) { ?>
					<a href="<?php echo htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');?>" class="reset-filter-link" title="<?php echo distT('clear_filters');?>">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
						<span><?php echo distT('clear_filters');?></span>
					</a>
					<?php } ?>
				</div>

				<?php if (!empty($sortOptions)) { ?>
				<div class="toolbar-right flex-align">
					<span class="sort-label"><?php echo distT('sort_label');?></span>
					<div class="sort-tabs" role="group" aria-label="Sort products">
						<?php foreach ($sortOptions as $so) { ?>
						<a
							href="<?php echo htmlspecialchars($so['url'], ENT_QUOTES, 'UTF-8');?>"
							class="sort-tab <?php echo !empty($so['active']) ? 'active' : '';?>"
							<?php echo !empty($so['active']) ? 'aria-current="true"' : '';?>
						>
							<?php echo htmlspecialchars($so['label'], ENT_QUOTES, 'UTF-8');?>
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
				<article class="product-card" data-spu-id="<?php echo (int)$item['spu_id'];?>">
					<div class="product-thumb-wrap">
						<a href="<?php echo $item['url'];?>" class="product-thumb-link" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>">
							<?php if ($isEager) { ?>
							<img
								class="product-thumb"
								src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8');?>"
								alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>"
								width="300"
								height="300"
								<?php echo $isFirst ? 'fetchpriority="high"' : '';?>
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg;?>';"
							>
							<?php } else { ?>
							<img
								class="product-thumb lazyload"
								src="<?php echo $placeholderImg;?>"
								data-src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8');?>"
								alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>"
								width="300"
								height="300"
								loading="lazy"
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg;?>';"
							>
							<?php } ?>
						</a>
						<div class="product-badges">
							<?php if (!empty($item['badge'])) { ?>
							<span class="badge badge-hot"><?php echo htmlspecialchars($item['badge'], ENT_QUOTES, 'UTF-8');?></span>
							<?php } ?>
							<?php if (!empty($item['discount_percent'])) { ?>
							<span class="badge badge-sale">-<?php echo (int)$item['discount_percent'];?>%</span>
							<?php } ?>
						</div>
						<button
							type="button"
							class="wishlist-btn <?php echo !empty($item['is_liked']) ? 'liked' : '';?>"
							data-spu-id="<?php echo (int)$item['spu_id'];?>"
							aria-label="Add <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?> to Wishlist"
							title="Add to Wishlist"
						>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo !empty($item['is_liked']) ? 'currentColor' : 'none';?>" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
						</button>
					</div>
					<div class="product-body">
						<?php if (!empty($item['cate_name'])) { ?>
						<span class="product-cate-tag text-ellipsis"><?php echo htmlspecialchars($item['cate_name'], ENT_QUOTES, 'UTF-8');?></span>
						<?php } ?>
						<h3 class="product-title">
							<a href="<?php echo $item['url'];?>" class="line-clamp-2" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?></a>
						</h3>
						<div class="product-price-row flex-between">
							<div class="price-group flex-align">
								<strong class="price-current"><?php echo htmlspecialchars($item['min_price_format'], ENT_QUOTES, 'UTF-8');?></strong>
								<?php if (!empty($item['original_price_format'])) { ?>
								<del class="price-old"><?php echo htmlspecialchars($item['original_price_format'], ENT_QUOTES, 'UTF-8');?></del>
								<?php } ?>
							</div>
							<a href="<?php echo $item['url'];?>" class="product-action-btn" aria-label="View <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
							</a>
						</div>
					</div>
				</article>
				<?php } ?>
			</div>

			<!-- 6. Pagination Bar -->
			<?php if ((int)$pagination['total_pages'] > 1) { ?>
			<nav class="pagination-wrap flex-between" aria-label="Pagination">
				<div class="pagination-summary">
					Page <strong><?php echo (int)$pagination['current'];?></strong> of <strong><?php echo (int)$pagination['total_pages'];?></strong>
					(<?php echo $total;?> items)
				</div>

				<ul class="pagination">
					<li>
						<?php if (!empty($pagination['prev_url'])) { ?>
						<a href="<?php echo htmlspecialchars($pagination['prev_url'], ENT_QUOTES, 'UTF-8');?>" class="page-link" aria-label="Previous page">
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
								href="<?php echo htmlspecialchars($pg['url'], ENT_QUOTES, 'UTF-8');?>"
								class="page-link <?php echo !empty($pg['current']) ? 'active' : '';?>"
								aria-label="Page <?php echo (int)$pg['num'];?>"
								<?php echo !empty($pg['current']) ? 'aria-current="page"' : '';?>
							>
								<?php echo (int)$pg['num'];?>
							</a>
						</li>
						<?php } ?>
					<?php } ?>

					<li>
						<?php if (!empty($pagination['next_url'])) { ?>
						<a href="<?php echo htmlspecialchars($pagination['next_url'], ENT_QUOTES, 'UTF-8');?>" class="page-link" aria-label="Next page">
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

				<form class="page-jump-form flex-align" action="<?php echo htmlspecialchars($baseCateUrl, ENT_QUOTES, 'UTF-8');?>" method="get">
					<?php if ($sort !== 'featured') { ?>
					<input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8');?>">
					<?php } ?>
					<?php if ($priceRange !== 'all') { ?>
					<input type="hidden" name="price" value="<?php echo htmlspecialchars($priceRange, ENT_QUOTES, 'UTF-8');?>">
					<?php } ?>
					<label for="cate-page-jump-input">Go to</label>
					<input
						type="number"
						id="cate-page-jump-input"
						class="page-jump-input"
						name="page"
						min="1"
						max="<?php echo (int)$pagination['total_pages'];?>"
						value="<?php echo (int)$pagination['current'];?>"
						aria-label="Page number"
					>
					<button type="submit" class="btn btn-outline btn-sm">Go</button>
				</form>
			</nav>
			<?php } ?>

			<?php } else { ?>
			<!-- Empty State + Recommended Products -->
			<div class="empty-state">
				<div class="empty-icon" aria-hidden="true">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
				</div>
				<h3 class="empty-title"><?php echo distT('empty_title');?></h3>
				<p class="empty-desc"><?php echo distT('empty_desc');?></p>
				<a href="<?php echo htmlspecialchars($hasActiveFilter ? $resetUrl : url('category'), ENT_QUOTES, 'UTF-8');?>" class="btn btn-primary">
					<?php echo $hasActiveFilter ? distT('clear_filters') : distT('all_categories_pill');?>
				</a>
			</div>

			<?php if (!empty($recommendList)) { ?>
			<div class="cate-recommend-section">
				<div class="section-header flex-between">
					<div>
						<div class="section-eyebrow">CURATED FOR YOU</div>
						<h3 class="section-title"><?php echo distT('recommend_heading');?></h3>
						<p class="section-subtitle"><?php echo distT('recommend_sub');?></p>
					</div>
				</div>
				<div class="product-grid grid-4">
					<?php foreach ($recommendList as $item) { ?>
					<article class="product-card" data-spu-id="<?php echo (int)$item['spu_id'];?>">
						<div class="product-thumb-wrap">
							<a href="<?php echo $item['url'];?>" class="product-thumb-link" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>">
								<img
									class="product-thumb lazyload"
									src="<?php echo $placeholderImg;?>"
									data-src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8');?>"
									alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>"
									width="300"
									height="300"
									loading="lazy"
									onerror="this.onerror=null;this.src='<?php echo $placeholderImg;?>';"
								>
							</a>
							<div class="product-badges">
								<span class="badge badge-sale">-<?php echo (int)$item['discount_percent'];?>%</span>
							</div>
						</div>
						<div class="product-body">
							<h4 class="product-title">
								<a href="<?php echo $item['url'];?>" class="line-clamp-2"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?></a>
							</h4>
							<div class="product-price-row flex-between">
								<div class="price-group flex-align">
									<strong class="price-current"><?php echo htmlspecialchars($item['min_price_format'], ENT_QUOTES, 'UTF-8');?></strong>
									<del class="price-old"><?php echo htmlspecialchars($item['original_price_format'], ENT_QUOTES, 'UTF-8');?></del>
								</div>
								<a href="<?php echo $item['url'];?>" class="product-action-btn" aria-label="View <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>">
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
