<?php
$this->load('common/header_all');
$placeholderImg = siteUrl('image/common/noimg.svg');
$hasItems = !empty($list);
?>

<main class="cart-page" id="main-content">
	<div class="container">
		<!-- 1. Breadcrumb -->
		<nav class="breadcrumb-nav" aria-label="Breadcrumb">
			<ol class="breadcrumb-list">
				<li class="breadcrumb-item">
					<a href="<?php echo url(); ?>"><?php echo distT('breadcrumb_home'); ?></a>
				</li>
				<li class="breadcrumb-sep" aria-hidden="true">/</li>
				<li class="breadcrumb-item current" aria-current="page"><?php echo distT('shopping_bag'); ?></li>
			</ol>
		</nav>

		<div class="cart-page-head flex-between">
			<h1 class="cart-page-title">
				<?php echo distT('shopping_bag'); ?>
				<?php if ($hasItems) { ?>
				<span class="cart-page-count">(<?php echo (int)$summary['total_items_count']; ?> items)</span>
				<?php } ?>
			</h1>
			<a href="<?php echo url('newIn'); ?>" class="btn btn-outline btn-sm">Continue Shopping</a>
		</div>

		<?php if ($hasItems) { ?>
		<!-- 2. Free Shipping Progress Bar -->
		<div class="cart-ship-banner">
			<div class="cart-ship-text">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
				<?php if ((float)$summary['free_shipping_diff'] > 0) { ?>
				<span>Add <strong><?php echo htmlspecialchars($summary['symbol'] . $summary['free_shipping_diff'], ENT_QUOTES, 'UTF-8'); ?></strong> more to unlock <strong>FREE Express Shipping</strong>!</span>
				<?php } else { ?>
				<span>Congratulations! Your order qualifies for <strong>FREE Express Shipping</strong>!</span>
				<?php } ?>
			</div>
			<div class="cart-ship-bar" role="progressbar" aria-valuenow="<?php echo (int)$summary['free_shipping_percent']; ?>" aria-valuemin="0" aria-valuemax="100">
				<div class="cart-ship-fill" style="width: <?php echo (int)$summary['free_shipping_percent']; ?>%;"></div>
			</div>
		</div>

		<!-- 3. Two-Column Cart Layout -->
		<div class="cart-layout">
			<!-- Left Column: Bag Items -->
			<section class="cart-items-panel" aria-label="Shopping Bag Items">
				<div class="cart-list-head flex-between">
					<label class="cart-check-label" for="cart-check-all">
						<input type="checkbox" id="cart-check-all" class="cart-checkbox" <?php echo !empty($summary['all_checked']) ? 'checked' : ''; ?>>
						<span>Select All (<?php echo count($list); ?>)</span>
					</label>
					<span>Item Details &amp; Quantity</span>
				</div>

				<?php foreach ($list as $item) {
					$cartId = (int)$item['cart_id'];
					$skuId = (int)$item['sku_id'];
					$spuId = (int)($item['spu_id'] ?? 0);
					$itemTitle = !empty($item['spu_name']) ? $item['spu_name'] : ($item['name'] ?? 'Product');
					$itemUrl = $item['url'] ?? ($spuId ? url('product', ['pid' => $spuId, 'sid' => $skuId]) : 'javascript:;');
				?>
				<article class="cart-item-row" data-cart-id="<?php echo $cartId; ?>" data-sku-id="<?php echo $skuId; ?>">
					<div>
						<input
							type="checkbox"
							class="cart-checkbox cart-item-check"
							data-cart-id="<?php echo $cartId; ?>"
							aria-label="Select <?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>"
							<?php echo !empty($item['checked']) ? 'checked' : ''; ?>
						>
					</div>

					<a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8'); ?>" class="cart-item-thumb">
						<img
							src="<?php echo htmlspecialchars($item['image'] ?? $placeholderImg, ENT_QUOTES, 'UTF-8'); ?>"
							alt="<?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>"
							width="96"
							height="96"
							onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
						>
					</a>

					<div>
						<h2 class="cart-item-title">
							<a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?></a>
						</h2>

						<?php if (!empty($item['attr']) && is_array($item['attr'])) { ?>
						<div class="cart-item-specs">
							<?php foreach ($item['attr'] as $aRow) { ?>
							<span class="cart-spec-chip">
								<?php echo htmlspecialchars(($aRow['attrn_name'] ?? 'Option') . ': ' . ($aRow['attrv_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
							</span>
							<?php } ?>
						</div>
						<?php } ?>

						<div class="cart-item-prices">
							<span class="cart-unit-price"><?php echo htmlspecialchars($item['price_format'], ENT_QUOTES, 'UTF-8'); ?></span>
							<?php if (!empty($item['original_price_format'])) { ?>
							<del class="cart-unit-old"><?php echo htmlspecialchars($item['original_price_format'], ENT_QUOTES, 'UTF-8'); ?></del>
							<?php } ?>
						</div>
					</div>

					<div class="cart-item-right">
						<strong class="cart-item-subtotal"><?php echo htmlspecialchars($item['price_total_format'], ENT_QUOTES, 'UTF-8'); ?></strong>

						<div class="cart-qty-box">
							<button type="button" class="cart-qty-btn" data-cart-qty-dir="minus" aria-label="Decrease quantity">&minus;</button>
							<input
								type="number"
								class="cart-qty-input"
								value="<?php echo (int)$item['quantity']; ?>"
								min="1"
								max="<?php echo max(1, (int)($item['stock'] ?? 99)); ?>"
								aria-label="Quantity"
								readonly
							>
							<button type="button" class="cart-qty-btn" data-cart-qty-dir="plus" aria-label="Increase quantity">&#43;</button>
						</div>

						<div class="cart-item-actions">
							<button type="button" class="cart-action-btn" data-cart-remove="<?php echo $cartId; ?>" aria-label="Remove item">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
								<span>Remove</span>
							</button>
						</div>
					</div>
				</article>
				<?php } ?>
			</section>

			<!-- Right Column: Sticky Order Summary -->
			<aside class="cart-summary-card" aria-label="Order Summary">
				<h2 class="cart-summary-title">Order Summary</h2>

				<div class="cart-summary-row">
					<span>Original Retail Price</span>
					<span><?php echo htmlspecialchars($summary['original_total_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>

				<?php if ((float)$summary['discount'] > 0) { ?>
				<div class="cart-summary-row discount">
					<span>Member Savings</span>
					<span><?php echo htmlspecialchars($summary['discount_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>
				<?php } ?>

				<div class="cart-summary-row">
					<span>Items Subtotal (<?php echo (int)$summary['checked_count']; ?>)</span>
					<span><?php echo htmlspecialchars($summary['subtotal_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>

				<div class="cart-summary-row">
					<span>Estimated Shipping</span>
					<strong><?php echo htmlspecialchars($summary['shipping_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
				</div>

				<div class="cart-summary-total">
					<span>Estimated Total</span>
					<strong><?php echo htmlspecialchars($summary['grand_total_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
				</div>

				<button type="button" id="btn-cart-checkout" class="btn btn-primary btn-block cart-checkout-btn" <?php echo (int)$summary['checked_count'] <= 0 ? 'disabled' : ''; ?>>
					Proceed to Checkout (<?php echo (int)$summary['checked_count']; ?>)
				</button>

				<div class="cart-trust-note">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
					<span>256-Bit SSL Encrypted Checkout &amp; 30-Day Returns</span>
				</div>
			</aside>
		</div>
		<?php } else { ?>
		<!-- Empty Bag Fallback -->
		<div class="empty-state">
			<div class="empty-icon" aria-hidden="true">
				<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
			</div>
			<h2 class="empty-title"><?php echo distT('empty_title'); ?></h2>
			<p class="empty-desc"><?php echo distT('empty_desc'); ?></p>
			<a href="<?php echo url('newIn'); ?>" class="btn btn-primary">Shop New Arrivals</a>
		</div>
		<?php } ?>

		<!-- 4. Recommended Products -->
		<?php if (!empty($recommendList)) { ?>
		<section class="cart-recommend-section" aria-labelledby="cart-rec-heading">
			<div class="section-header flex-between">
				<div>
					<div class="section-eyebrow">CURATED FOR YOU</div>
					<h2 class="section-title" id="cart-rec-heading"><?php echo distT('recommend_heading'); ?></h2>
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
						<h3 class="product-title">
							<a href="<?php echo $item['url']; ?>" class="line-clamp-2"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></a>
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
