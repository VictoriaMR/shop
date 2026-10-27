<?php
$this->load('common/header_all');

$siteName = $siteName ?? \App::get('domain', 'name', 'Shop');
$bannerList = $bannerList ?? [];
$serviceList = $serviceList ?? [];
$popularCate = $popularCate ?? [];
$newArrivals = $newArrivals ?? [];
$bestSeller = $bestSeller ?? [];
$placeholderImg = siteUrl('image/common/noimg.svg');
?>
<main class="home-main" id="main-content">
	<!-- Structured Data (JSON-LD) for Search Engine Rich Snippets -->
	<script type="application/ld+json">
	<?php echo json_encode([
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => $siteName,
		'url'      => url(),
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => url('search') . '?keyword={search_term_string}',
			'query-input' => 'required name=search_term_string',
		],
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);?>
	</script>

	<!-- 1. Hero Promotional Carousel -->
	<?php if (!empty($bannerList)) { ?>
	<section class="hero-section" aria-label="Featured Promotions">
		<div class="container">
			<div class="hero-carousel" id="hero-carousel" data-autoplay="5500">
				<div class="hero-track">
					<?php foreach ($bannerList as $idx => $slide) {
						$isFirst = ($idx === 0);
						$headingTag = $isFirst ? 'h1' : 'h2';
						$showcaseItem = $newArrivals[$idx] ?? ($bestSeller[$idx] ?? null);
					?>
					<div class="hero-slide <?php echo htmlspecialchars($slide['theme'] ?? 'theme-slate', ENT_QUOTES, 'UTF-8');?> <?php echo $isFirst ? 'active' : '';?>" data-index="<?php echo $idx;?>" aria-hidden="<?php echo $isFirst ? 'false' : 'true';?>">
						<div class="hero-content">
							<div class="hero-badges flex-align">
								<span class="badge badge-dark"><?php echo htmlspecialchars($slide['tag'], ENT_QUOTES, 'UTF-8');?></span>
								<?php if (!empty($slide['discount'])) { ?>
								<span class="badge badge-accent"><?php echo htmlspecialchars($slide['discount'], ENT_QUOTES, 'UTF-8');?></span>
								<?php } ?>
							</div>
							<<?php echo $headingTag;?> class="hero-title"><?php echo htmlspecialchars($slide['title'], ENT_QUOTES, 'UTF-8');?></<?php echo $headingTag;?>>
							<p class="hero-subtitle"><?php echo htmlspecialchars($slide['subtitle'], ENT_QUOTES, 'UTF-8');?></p>
							<div class="hero-actions flex-align">
								<a href="<?php echo $slide['btn_url'];?>" class="btn btn-primary btn-lg">
									<span><?php echo htmlspecialchars($slide['btn_text'], ENT_QUOTES, 'UTF-8');?></span>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
								</a>
								<?php if (!empty($slide['sub_text'])) { ?>
								<a href="<?php echo $slide['sub_url'];?>" class="btn btn-outline btn-lg"><?php echo htmlspecialchars($slide['sub_text'], ENT_QUOTES, 'UTF-8');?></a>
								<?php } ?>
							</div>
						</div>

						<div class="hero-visual">
							<div class="hero-visual-card">
								<div class="visual-deco-circle" aria-hidden="true"></div>
								<?php if ($showcaseItem) { ?>
								<a href="<?php echo $showcaseItem['url'];?>" class="hero-featured-product" title="<?php echo htmlspecialchars($showcaseItem['name'], ENT_QUOTES, 'UTF-8');?>">
									<div class="hero-featured-img">
										<img
											src="<?php echo htmlspecialchars($showcaseItem['image'], ENT_QUOTES, 'UTF-8');?>"
											alt="<?php echo htmlspecialchars($showcaseItem['name'], ENT_QUOTES, 'UTF-8');?>"
											width="280"
											height="280"
											<?php echo $isFirst ? 'fetchpriority="high"' : 'loading="lazy"';?>
											onerror="this.onerror=null;this.src='<?php echo $placeholderImg;?>';"
										>
										<span class="badge badge-sale">-<?php echo (int)$showcaseItem['discount_percent'];?>%</span>
									</div>
									<div class="hero-featured-info">
										<p class="hero-featured-name line-clamp-1"><?php echo htmlspecialchars($showcaseItem['name'], ENT_QUOTES, 'UTF-8');?></p>
										<div class="hero-featured-price flex-align">
											<strong class="price-current"><?php echo htmlspecialchars($showcaseItem['min_price_format'], ENT_QUOTES, 'UTF-8');?></strong>
											<del class="price-old"><?php echo htmlspecialchars($showcaseItem['original_price_format'], ENT_QUOTES, 'UTF-8');?></del>
										</div>
									</div>
								</a>
								<?php } ?>
								<div class="hero-floating-pill">
									<strong><?php echo htmlspecialchars($slide['badge_title'] ?? 'Verified Quality', ENT_QUOTES, 'UTF-8');?></strong>
									<span><?php echo htmlspecialchars($slide['badge_sub'] ?? 'Direct From Studio', ENT_QUOTES, 'UTF-8');?></span>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>

				<?php if (count($bannerList) > 1) { ?>
				<button type="button" class="carousel-arrow carousel-prev" id="hero-prev" aria-label="Previous slide">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
				</button>
				<button type="button" class="carousel-arrow carousel-next" id="hero-next" aria-label="Next slide">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
				</button>
				<div class="carousel-dots" id="hero-dots" role="group" aria-label="Slide controls">
					<?php foreach ($bannerList as $idx => $slide) { ?>
					<button
						type="button"
						class="carousel-dot <?php echo $idx === 0 ? 'active' : '';?>"
						data-slide="<?php echo $idx;?>"
						aria-current="<?php echo $idx === 0 ? 'true' : 'false';?>"
						aria-label="Go to slide <?php echo $idx + 1;?>"
					></button>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<!-- 2. Brand Service Guarantee Bar -->
	<?php if (!empty($serviceList)) { ?>
	<section class="service-section" aria-label="Our Service Guarantees">
		<div class="container">
			<div class="service-bar">
				<?php foreach ($serviceList as $srv) { ?>
				<div class="service-item flex-align">
					<div class="service-icon" aria-hidden="true">
						<?php if ($srv['icon'] === 'truck') { ?>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
						<?php } elseif ($srv['icon'] === 'shield') { ?>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
						<?php } elseif ($srv['icon'] === 'refresh') { ?>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
						<?php } else { ?>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
						<?php } ?>
					</div>
					<div class="service-text">
						<h2 class="service-title"><?php echo htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8');?></h2>
						<p class="service-desc"><?php echo htmlspecialchars($srv['desc'], ENT_QUOTES, 'UTF-8');?></p>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<!-- 3. Popular Categories Section -->
	<?php if (!empty($popularCate)) { ?>
	<section class="section-block" id="section-categories" aria-labelledby="heading-categories">
		<div class="container">
			<div class="section-header flex-between">
				<div>
					<h2 class="section-title" id="heading-categories"><?php echo distT('popular_categories');?></h2>
					<p class="section-subtitle"><?php echo distT('popular_categories_sub');?></p>
				</div>
				<a href="<?php echo url('category');?>" class="section-more flex-align" title="Browse All Categories">
					<span><?php echo distT('view_all');?></span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
			</div>

			<div class="category-grid">
				<?php foreach ($popularCate as $cate) { ?>
				<a href="<?php echo $cate['url'];?>" class="category-card" title="<?php echo htmlspecialchars($cate['name'], ENT_QUOTES, 'UTF-8');?>">
					<div class="category-icon-box" style="background-color: <?php echo htmlspecialchars($cate['color'], ENT_QUOTES, 'UTF-8');?>; color: <?php echo htmlspecialchars($cate['accent'], ENT_QUOTES, 'UTF-8');?>;" aria-hidden="true">
						<?php switch ($cate['icon']) {
							case 'fashion': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
								<?php break;
							case 'furniture': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3"/><path d="M2 11v5a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H6v-2a2 2 0 0 0-4 0Z"/><path d="M4 18v2"/><path d="M20 18v2"/></svg>
								<?php break;
							case 'lighting': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
								<?php break;
							case 'shirt': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a4 4 0 0 1-4 4H4l2 5h2v9h8v-9h2l2-5h-4a4 4 0 0 1-4-4z"/></svg>
								<?php break;
							case 'dress': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m8 3 4 4 4-4"/><path d="m9 7-3 5 4 2-3 7h10l-3-7 4-2-3-5"/></svg>
								<?php break;
							case 'outdoor': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m8 3 4 8 5-5 5 15H2L8 3z"/></svg>
								<?php break;
							case 'decor': ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
								<?php break;
							default: ?>
								<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3Z"/></svg>
						<?php } ?>
					</div>
					<div class="category-info">
						<h3 class="category-name text-ellipsis"><?php echo htmlspecialchars($cate['name'], ENT_QUOTES, 'UTF-8');?></h3>
						<p class="category-sub text-ellipsis"><?php echo htmlspecialchars($cate['subtitle'], ENT_QUOTES, 'UTF-8');?></p>
					</div>
				</a>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>

	<!-- 4. New Arrivals Section -->
	<?php if (!empty($newArrivals)) { ?>
	<section class="section-block section-alt" id="section-new-arrivals" aria-labelledby="heading-new-arrivals">
		<div class="container">
			<div class="section-header flex-between">
				<div>
					<div class="section-eyebrow">JUST LANDED</div>
					<h2 class="section-title" id="heading-new-arrivals"><?php echo distT('new_arrivals');?></h2>
					<p class="section-subtitle"><?php echo distT('new_arrivals_sub');?></p>
				</div>
				<a href="<?php echo url('newIn');?>" class="section-more flex-align" title="View All New Arrivals">
					<span><?php echo distT('view_all');?></span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
			</div>

			<div class="product-grid grid-4">
				<?php foreach ($newArrivals as $item) { ?>
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
							<?php if (!empty($item['badge'])) { ?>
							<span class="badge badge-new"><?php echo htmlspecialchars($item['badge'], ENT_QUOTES, 'UTF-8');?></span>
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
		</div>
	</section>
	<?php } ?>

	<!-- 5. Best Sellers / Recommended Products Section (No Pagination) -->
	<?php if (!empty($bestSeller)) { ?>
	<section class="section-block" id="section-bestsellers" aria-labelledby="heading-bestsellers">
		<div class="container">
			<div class="section-header flex-between">
				<div>
					<div class="section-eyebrow">MOST POPULAR</div>
					<h2 class="section-title" id="heading-bestsellers"><?php echo distT('best_sellers');?></h2>
					<p class="section-subtitle"><?php echo distT('best_sellers_sub');?></p>
				</div>
				<a href="<?php echo url('search');?>" class="section-more flex-align" title="Explore Full Catalog">
					<span>Explore All</span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
				</a>
			</div>

			<div class="product-grid grid-5">
				<?php foreach ($bestSeller as $item) { ?>
				<article class="product-card" data-spu-id="<?php echo (int)$item['spu_id'];?>">
					<div class="product-thumb-wrap">
						<a href="<?php echo $item['url'];?>" class="product-thumb-link" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>">
							<img
								class="product-thumb lazyload"
								src="<?php echo $placeholderImg;?>"
								data-src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8');?>"
								alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');?>"
								width="260"
								height="260"
								loading="lazy"
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg;?>';"
							>
						</a>
						<div class="product-badges">
							<span class="badge badge-hot"><?php echo htmlspecialchars($item['badge'] ?: 'HOT', ENT_QUOTES, 'UTF-8');?></span>
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

			<div class="section-footer center">
				<a href="<?php echo url('newIn');?>" class="btn btn-outline btn-lg">Explore More Products</a>
			</div>
		</div>
	</section>
	<?php } ?>
</main>
