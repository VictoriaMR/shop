<?php
$this->load('common/header_all');
$placeholderImg = siteUrl('image/common/noimg.svg');
$hasProduct = !empty($info) && !empty($info['sku']);
?>

<main class="pdp-page" id="main-content">
	<div class="container">
		<!-- 1. Breadcrumb -->
		<nav class="breadcrumb-nav" aria-label="Breadcrumb">
			<ol class="breadcrumb-list">
				<li class="breadcrumb-item">
					<a href="<?php echo url(); ?>"><?php echo distT('breadcrumb_home'); ?></a>
				</li>
				<?php if (!empty($crumbs)) {
					foreach ($crumbs as $crumb) { ?>
				<li class="breadcrumb-sep" aria-hidden="true">/</li>
				<li class="breadcrumb-item">
					<a href="<?php echo htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8'); ?></a>
				</li>
				<?php }
				} ?>
				<li class="breadcrumb-sep" aria-hidden="true">/</li>
				<li class="breadcrumb-item current" aria-current="page">
					<?php echo htmlspecialchars(!empty($info['name']) ? $info['name'] : 'Product Details', ENT_QUOTES, 'UTF-8'); ?>
				</li>
			</ol>
		</nav>

		<?php if ($hasProduct) {
			$curPriceFmt = $activeSku['price_format'] ?? $info['min_price_format'] ?? '$0.00';
			$origPriceFmt = $activeSku['original_price_format'] ?? $info['original_price_format'] ?? '';
			$curStock = (int)($activeSku['stock'] ?? $totalStock ?? 0);
			$mainImg = $galleryImages[0] ?? $placeholderImg;
		?>
		<!-- 2. Main Two-Column Product Stage -->
		<section class="pdp-main-grid" aria-label="Product Overview">
			<!-- Left Column: Gallery -->
			<div class="pdp-gallery">
				<div class="pdp-stage-wrap">
					<img
						id="pdp-main-image"
						class="pdp-stage-img"
						src="<?php echo htmlspecialchars($mainImg, ENT_QUOTES, 'UTF-8'); ?>"
						alt="<?php echo htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8'); ?>"
						width="520"
						height="520"
						fetchpriority="high"
						onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
					>
				</div>

				<?php if (count($galleryImages) > 1) { ?>
				<div class="pdp-thumb-list" role="list" aria-label="Product thumbnails">
					<?php foreach (array_slice($galleryImages, 0, 12) as $gIdx => $gUrl) { ?>
					<button
						type="button"
						class="pdp-thumb-btn<?php echo $gIdx === 0 ? ' active' : ''; ?>"
						data-full-src="<?php echo htmlspecialchars($gUrl, ENT_QUOTES, 'UTF-8'); ?>"
						aria-label="View product image <?php echo $gIdx + 1; ?>"
					>
						<img
							src="<?php echo htmlspecialchars($gUrl, ENT_QUOTES, 'UTF-8'); ?>"
							alt=""
							width="76"
							height="76"
							loading="lazy"
							onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
						>
					</button>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<!-- Right Column: Summary & Purchase Controls -->
			<div class="pdp-summary">
				<div class="pdp-meta-top">
					<?php if (!empty($cateName)) { ?>
					<span class="pdp-cate-pill"><?php echo htmlspecialchars($cateName, ENT_QUOTES, 'UTF-8'); ?></span>
					<?php } ?>
					<span class="pdp-sku-code">Item #<span id="pdp-sku-id-text"><?php echo (int)$skuId; ?></span></span>
					<span id="pdp-stock-badge" class="pdp-stock-pill<?php echo $curStock <= 0 ? ' out' : ''; ?>">
						<?php echo $curStock > 0 ? distT('in_stock') : distT('out_of_stock'); ?>
					</span>
				</div>

				<h1 class="pdp-title"><?php echo htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8'); ?></h1>

				<div class="pdp-price-card">
					<div class="pdp-price-main">
						<strong class="pdp-price-now" id="pdp-price-current"><?php echo htmlspecialchars($curPriceFmt, ENT_QUOTES, 'UTF-8'); ?></strong>
						<?php if (!empty($origPriceFmt)) { ?>
						<del class="pdp-price-was" id="pdp-price-old"><?php echo htmlspecialchars($origPriceFmt, ENT_QUOTES, 'UTF-8'); ?></del>
						<?php } ?>
						<?php if (!empty($discountPercent)) { ?>
						<span class="pdp-price-save" id="pdp-discount-badge">-<?php echo (int)$discountPercent; ?>%</span>
						<?php } ?>
					</div>
					<div class="pdp-price-perk">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
						<span><?php echo distT('free_shipping_tag'); ?> &bull; 30-Day Buyer Protection</span>
					</div>
				</div>

				<input type="hidden" id="pdp-selected-sku-id" value="<?php echo (int)$skuId; ?>">

				<!-- Variant / Attribute Groups -->
				<?php if (!empty($info['attrMap']) && !empty($info['attr'])) {
					foreach ($info['attrMap'] as $nid => $vidList) {
						$attrLabel = $info['attr'][$nid] ?? 'Option';
						$curVid = $selectedAttvByAttrn[$nid] ?? ($vidList[0] ?? 0);
						$curValLabel = $info['attv'][$curVid] ?? '';
				?>
				<div class="pdp-attr-group" data-attrn-id="<?php echo (int)$nid; ?>">
					<div class="pdp-attr-label">
						<span><?php echo htmlspecialchars($attrLabel, ENT_QUOTES, 'UTF-8'); ?>:</span>
						<span class="pdp-attr-selected"><?php echo htmlspecialchars($curValLabel, ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="pdp-attr-options" role="group" aria-label="<?php echo htmlspecialchars($attrLabel, ENT_QUOTES, 'UTF-8'); ?>">
						<?php foreach ($vidList as $vid) {
							$vLabel = $info['attv'][$vid] ?? ('#' . $vid);
							$vImg = $info['attvImage'][$vid] ?? '';
							$isActive = ((int)$vid === (int)$curVid);
						?>
						<button
							type="button"
							class="pdp-attr-btn<?php echo $isActive ? ' active' : ''; ?>"
							data-attrn-id="<?php echo (int)$nid; ?>"
							data-attrv-id="<?php echo (int)$vid; ?>"
							data-attrv-name="<?php echo htmlspecialchars($vLabel, ENT_QUOTES, 'UTF-8'); ?>"
							data-attrv-image="<?php echo htmlspecialchars($vImg, ENT_QUOTES, 'UTF-8'); ?>"
							aria-pressed="<?php echo $isActive ? 'true' : 'false'; ?>"
						>
							<?php if (!empty($vImg)) { ?>
							<img class="pdp-attr-swatch" src="<?php echo htmlspecialchars($vImg, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="24" height="24" onerror="this.style.display='none';">
							<?php } ?>
							<span><?php echo htmlspecialchars($vLabel, ENT_QUOTES, 'UTF-8'); ?></span>
						</button>
						<?php } ?>
					</div>
				</div>
				<?php }
				} ?>

				<!-- Quantity Stepper -->
				<div class="pdp-qty-row">
					<label class="pdp-attr-label" for="pdp-qty-input" style="margin-bottom:0;"><?php echo distT('quantity_label'); ?>:</label>
					<div class="pdp-qty-stepper">
						<button type="button" class="pdp-qty-btn" id="pdp-qty-minus" aria-label="Decrease quantity">&minus;</button>
						<input
							type="number"
							id="pdp-qty-input"
							class="pdp-qty-input"
							value="1"
							min="1"
							max="<?php echo max(1, $curStock); ?>"
							aria-label="Quantity"
						>
						<button type="button" class="pdp-qty-btn" id="pdp-qty-plus" aria-label="Increase quantity">&#43;</button>
					</div>
					<span class="pdp-stock-hint"><strong id="pdp-stock-count"><?php echo $curStock; ?></strong> pieces available</span>
				</div>

				<!-- Primary Actions -->
				<div class="pdp-actions-row">
					<button type="button" id="btn-add-to-bag" class="btn btn-primary pdp-btn-bag" <?php echo $curStock <= 0 ? 'disabled' : ''; ?>>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						<span><?php echo distT('add_to_bag'); ?></span>
					</button>
					<button type="button" id="btn-buy-now" class="btn pdp-btn-buy" <?php echo $curStock <= 0 ? 'disabled' : ''; ?>>
						<?php echo distT('buy_now'); ?>
					</button>
					<button
						type="button"
						class="wishlist-btn pdp-wishlist-trigger<?php echo !empty($isLiked) ? ' liked' : ''; ?>"
						data-spu-id="<?php echo (int)$spuId; ?>"
						aria-label="<?php echo distT('save_wishlist'); ?>"
						title="<?php echo distT('save_wishlist'); ?>"
					>
						<svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo !empty($isLiked) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
					</button>
				</div>

				<!-- Trust & Delivery Guarantees -->
				<div class="pdp-trust-box">
					<div class="pdp-trust-item">
						<svg class="pdp-trust-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
						<div>
							<div class="pdp-trust-title">Tracked Express Delivery</div>
							<div class="pdp-trust-sub">Estimated 5–9 business days</div>
						</div>
					</div>
					<div class="pdp-trust-item">
						<svg class="pdp-trust-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
						<div>
							<div class="pdp-trust-title">30-Day Easy Returns</div>
							<div class="pdp-trust-sub">Hassle-free refund or exchange</div>
						</div>
					</div>
					<div class="pdp-trust-item">
						<svg class="pdp-trust-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
						<div>
							<div class="pdp-trust-title">Secure SSL Checkout</div>
							<div class="pdp-trust-sub">PayPal &amp; major credit cards</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<script>
			window.PDP_SKU_MAP = <?php echo json_encode($skuFrontendMap ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
		</script>

		<!-- 3. Product Details, Specifications & Shipping Tabs -->
		<section class="pdp-details-section" aria-label="Product Information">
			<div class="pdp-tabs-nav" role="tablist">
				<button type="button" role="tab" class="pdp-tab-btn active" data-pdp-tab="details" aria-selected="true">
					<?php echo distT('tab_details'); ?>
				</button>
				<button type="button" role="tab" class="pdp-tab-btn" data-pdp-tab="specs" aria-selected="false">
					<?php echo distT('tab_specs'); ?> (<?php echo count($info['description'] ?? []); ?>)
				</button>
				<button type="button" role="tab" class="pdp-tab-btn" data-pdp-tab="shipping" aria-selected="false">
					<?php echo distT('tab_shipping'); ?>
				</button>
			</div>

			<!-- Tab 1: Overview & Showcase -->
			<div data-pdp-panel="details" role="tabpanel">
				<?php if (!empty($info['description'])) { ?>
				<div class="pdp-specs-grid">
					<?php foreach (array_slice($info['description'], 0, 8) as $spec) {
						if (empty($spec['name']) && empty($spec['value'])) continue;
					?>
					<div class="pdp-spec-item">
						<span class="pdp-spec-name"><?php echo htmlspecialchars(ucfirst($spec['name']), ENT_QUOTES, 'UTF-8'); ?></span>
						<span class="pdp-spec-val"><?php echo htmlspecialchars($spec['value'], ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if (!empty($info['introduce'])) { ?>
				<div class="pdp-showcase-images">
					<?php foreach ($info['introduce'] as $idx => $introImg) { ?>
					<img
						class="lazyload"
						src="<?php echo $placeholderImg; ?>"
						data-src="<?php echo htmlspecialchars($introImg, ENT_QUOTES, 'UTF-8'); ?>"
						alt="<?php echo htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8'); ?> detail <?php echo $idx + 1; ?>"
						loading="lazy"
						onerror="this.style.display='none';"
					>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<!-- Tab 2: Full Specifications -->
			<div data-pdp-panel="specs" role="tabpanel" class="hidden">
				<?php if (!empty($info['description'])) { ?>
				<div class="pdp-specs-grid">
					<?php foreach ($info['description'] as $spec) {
						if (empty($spec['name']) && empty($spec['value'])) continue;
					?>
					<div class="pdp-spec-item">
						<span class="pdp-spec-name"><?php echo htmlspecialchars(ucfirst($spec['name']), ENT_QUOTES, 'UTF-8'); ?></span>
						<span class="pdp-spec-val"><?php echo htmlspecialchars($spec['value'], ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<?php } ?>
				</div>
				<?php } else { ?>
				<p class="text-muted">Standard studio specifications apply to this item.</p>
				<?php } ?>
			</div>

			<!-- Tab 3: Shipping & Returns Policy -->
			<div data-pdp-panel="shipping" role="tabpanel" class="hidden">
				<div class="pdp-policy-card">
					<h3>Express Worldwide Shipping</h3>
					<p>Orders over $59 qualify for <strong>Free Express Shipping</strong>. Standard dispatch takes 1–2 business days from our fulfillment studio, with tracked doorstep delivery in 5–9 business days.</p>
					<h3>30-Day Buyer Protection &amp; Returns</h3>
					<p>If you are not 100% satisfied with your purchase, you can request a return or exchange within 30 days of delivery. Visit our <a href="<?php echo url('faq', ['f' => 1]); ?>" style="text-decoration:underline;font-weight:600;">Return &amp; Exchange Policy</a> for step-by-step guidance.</p>
				</div>
			</div>
		</section>

		<?php } else { ?>
		<!-- Fallback when product ID does not exist -->
		<div class="empty-state">
			<div class="empty-icon" aria-hidden="true">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
			</div>
			<h1 class="empty-title">Product Currently Unavailable</h1>
			<p class="empty-desc">The item you are looking for may have been moved or is temporarily out of stock. Explore our curated recommendations below.</p>
			<a href="<?php echo url('category'); ?>" class="btn btn-primary">Explore All Categories</a>
		</div>
		<?php } ?>

		<!-- 4. Related Products -->
		<?php if (!empty($relatedProducts)) { ?>
		<section class="pdp-related-section" aria-labelledby="pdp-related-heading">
			<div class="section-header flex-between">
				<div>
					<div class="section-eyebrow">CURATED RECOMMENDATIONS</div>
					<h2 class="section-title" id="pdp-related-heading"><?php echo distT('related_heading'); ?></h2>
					<p class="section-subtitle"><?php echo distT('related_sub'); ?></p>
				</div>
				<a href="<?php echo url('newIn'); ?>" class="btn btn-outline btn-sm">View New Arrivals</a>
			</div>

			<div class="product-grid grid-4">
				<?php foreach ($relatedProducts as $item) { ?>
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
						<?php if (!empty($item['cate_name'])) { ?>
						<span class="product-cate-tag text-ellipsis"><?php echo htmlspecialchars($item['cate_name'], ENT_QUOTES, 'UTF-8'); ?></span>
						<?php } ?>
						<h3 class="product-title">
							<a href="<?php echo $item['url']; ?>" class="line-clamp-2" title="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></a>
						</h3>
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
		</section>
		<?php } ?>
	</div>
</main>
