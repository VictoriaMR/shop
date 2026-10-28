<?php
$this->load('common/header_all');
$placeholderImg = siteUrl('image/common/noimg.svg');
$activeShipId = (int)($shipAddress['address_id'] ?? 0);
$activeBillId = (int)($billAddress['address_id'] ?? $activeShipId);
?>

<main class="checkout-page" id="main-content">
	<div class="container">
		<!-- 1. Checkout Progress Bar -->
		<div class="checkout-steps" aria-label="Checkout progress">
			<div class="checkout-step done">
				<span class="step-num">&#10003;</span>
				<span>1. Shopping Bag</span>
			</div>
			<span class="step-divider" aria-hidden="true"></span>
			<div class="checkout-step active" aria-current="step">
				<span class="step-num">2</span>
				<span>2. Shipping &amp; Order Review</span>
			</div>
			<span class="step-divider" aria-hidden="true"></span>
			<div class="checkout-step">
				<span class="step-num">3</span>
				<span>3. Payment &amp; Confirmation</span>
			</div>
		</div>

		<?php if (!empty($error) || empty($skuList)) { ?>
		<div class="empty-state">
			<div class="empty-icon" aria-hidden="true">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
			</div>
			<h1 class="empty-title"><?php echo htmlspecialchars($error ?: distT('checkout_error'), ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="empty-desc">Select items in your shopping bag or browse our catalog to place an order.</p>
			<a href="<?php echo url('cart'); ?>" class="btn btn-primary">Return to Shopping Bag</a>
		</div>
		<?php } else { ?>
		<input type="hidden" id="checkout-direct-sku-id" value="<?php echo (int)$skuId; ?>">
		<input type="hidden" id="checkout-direct-qty" value="<?php echo (int)$quantity; ?>">
		<input type="hidden" id="checkout-shipping-addr-id" value="<?php echo $activeShipId; ?>">
		<input type="hidden" id="checkout-billing-addr-id" value="<?php echo $activeBillId; ?>">

		<div class="checkout-grid">
			<!-- Left Column: Contact, Address, Shipping & Items -->
			<div>
				<!-- Guest Contact Email (if not logged in) -->
				<?php if (empty($isLogin)) { ?>
				<section class="checkout-card" aria-labelledby="checkout-contact-heading">
					<div class="checkout-card-head">
						<h2 class="checkout-card-title" id="checkout-contact-heading">Contact Information</h2>
						<a href="<?php echo url('login'); ?>" class="addr-edit-link">Already have an account? Sign In</a>
					</div>
					<div>
						<label class="addr-label" for="checkout-guest-email">Email Address (for order confirmation &amp; tracking) <span class="req">*</span></label>
						<input
							type="email"
							id="checkout-guest-email"
							class="addr-input"
							value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
							placeholder="name@example.com"
							autocomplete="email"
							required
						>
					</div>
				</section>
				<?php } ?>

				<!-- Shipping Address -->
				<section class="checkout-card" aria-labelledby="checkout-addr-heading">
					<div class="checkout-card-head">
						<h2 class="checkout-card-title" id="checkout-addr-heading">Shipping &amp; Billing Address</h2>
						<button type="button" class="btn btn-outline btn-sm" data-open-address-modal data-address-id="0">+ Add New Address</button>
					</div>

					<?php if (!empty($addressList)) { ?>
					<div class="checkout-addr-grid">
						<?php foreach ($addressList as $addr) {
							$aId = (int)($addr['address_id'] ?? 0);
							$isSelected = ($aId === $activeShipId);
						?>
						<div class="checkout-addr-item<?php echo $isSelected ? ' selected' : ''; ?>" data-addr-id="<?php echo $aId; ?>">
							<div class="addr-card-name">
								<span><?php echo htmlspecialchars(trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></span>
								<?php if (!empty($addr['is_default'])) { ?>
								<span class="addr-tag">Default</span>
								<?php } ?>
							</div>
							<div class="addr-card-lines">
								<?php echo htmlspecialchars($addr['address1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
								<?php if (!empty($addr['address2'])) { ?>, <?php echo htmlspecialchars($addr['address2'], ENT_QUOTES, 'UTF-8'); ?><?php } ?><br>
								<?php echo htmlspecialchars(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postcode'] ?? ''), ENT_QUOTES, 'UTF-8'); ?><br>
								<?php echo htmlspecialchars($addr['country'] ?? ($addr['country_code2'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
							</div>
							<div class="addr-card-foot">
								<span><?php echo htmlspecialchars($addr['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
								<button type="button" class="addr-edit-link" data-open-address-modal data-address-id="<?php echo $aId; ?>">Edit</button>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php } else { ?>
					<div style="text-align:center;padding:24px 0;">
						<p class="text-muted" style="margin-bottom:14px;">You have not added a delivery address yet.</p>
						<button type="button" class="btn btn-primary" data-open-address-modal data-address-id="0">Add Delivery Address</button>
					</div>
					<?php } ?>
				</section>

				<!-- Delivery Method & Shipping Protection -->
				<section class="checkout-card" aria-labelledby="checkout-ship-heading">
					<div class="checkout-card-head">
						<h2 class="checkout-card-title" id="checkout-ship-heading">Delivery &amp; Protection</h2>
					</div>

					<div class="ship-option-box">
						<div>
							<strong style="font-size:14px;color:var(--text-main);">Tracked Express Shipping</strong>
							<p style="font-size:12px;color:var(--text-secondary);">Estimated delivery in 5–9 business days with real-time tracking</p>
						</div>
						<strong style="font-size:14px;color:var(--primary);"><?php echo htmlspecialchars($summary['shipping_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>

					<label class="insurance-option-box" for="checkout-insurance-check">
						<div class="flex-align" style="gap:10px;">
							<input type="checkbox" id="checkout-insurance-check" style="width:18px;height:18px;accent-color:var(--primary);">
							<div>
								<strong style="font-size:14px;color:var(--text-main);">Add Shipping Protection Guarantee</strong>
								<p style="font-size:12px;color:var(--text-secondary);">Instant replacement for lost, stolen, or damaged packages in transit</p>
							</div>
						</div>
						<strong style="font-size:14px;color:var(--text-main);">+<?php echo htmlspecialchars($summary['insurance_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
					</label>
				</section>

				<!-- Order Items Review -->
				<section class="checkout-card" aria-labelledby="checkout-items-heading">
					<div class="checkout-card-head">
						<h2 class="checkout-card-title" id="checkout-items-heading">Order Items (<?php echo count($skuList); ?>)</h2>
						<a href="<?php echo url('cart'); ?>" class="addr-edit-link">Edit Bag</a>
					</div>

					<?php foreach ($skuList as $item) { ?>
					<div class="checkout-item-row">
						<div class="checkout-item-thumb">
							<img
								src="<?php echo htmlspecialchars($item['image'] ?? $placeholderImg, ENT_QUOTES, 'UTF-8'); ?>"
								alt="<?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
								width="64"
								height="64"
								onerror="this.onerror=null;this.src='<?php echo $placeholderImg; ?>';"
							>
						</div>
						<div>
							<h3 style="font-size:14px;font-weight:700;color:var(--text-main);margin-bottom:4px;">
								<?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
							</h3>
							<p style="font-size:12px;color:var(--text-secondary);">
								<?php echo htmlspecialchars($item['price_format'], ENT_QUOTES, 'UTF-8'); ?> &times; <?php echo (int)$item['quantity']; ?>
							</p>
						</div>
						<strong style="font-size:15px;font-weight:800;color:var(--text-main);">
							<?php echo htmlspecialchars($item['subtotal_format'], ENT_QUOTES, 'UTF-8'); ?>
						</strong>
					</div>
					<?php } ?>
				</section>
			</div>

			<!-- Right Column: Sticky Order Summary -->
			<aside class="checkout-summary-panel" aria-label="Order Summary">
				<h2 class="checkout-card-title" style="padding-bottom:14px;border-bottom:1px solid var(--border-color);margin-bottom:16px;">Order Summary</h2>

				<div class="checkout-fee-row">
					<span>Original Retail Price</span>
					<span><?php echo htmlspecialchars($summary['original_total_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>

				<?php if ((float)$summary['discount'] > 0) { ?>
				<div class="checkout-fee-row" style="color:#16a34a;font-weight:600;">
					<span>Member Savings</span>
					<span><?php echo htmlspecialchars($summary['discount_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>
				<?php } ?>

				<div class="checkout-fee-row">
					<span>Items Subtotal</span>
					<span><?php echo htmlspecialchars($summary['subtotal_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>

				<div class="checkout-fee-row">
					<span>Express Shipping</span>
					<strong><?php echo htmlspecialchars($summary['shipping_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
				</div>

				<div class="checkout-fee-row hidden" id="checkout-insurance-row">
					<span>Shipping Guarantee</span>
					<span><?php echo htmlspecialchars($summary['insurance_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>

				<div class="checkout-total-row">
					<span style="font-size:15px;font-weight:800;color:var(--text-main);">Order Total</span>
					<strong id="checkout-grand-total"><?php echo htmlspecialchars($summary['grand_total_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
				</div>

				<button type="button" id="btn-place-order" class="btn btn-primary btn-block" style="height:48px;font-size:15px;margin-bottom:12px;">
					Place Order &amp; Continue to Payment
				</button>

				<p style="font-size:12px;color:var(--text-muted);text-align:center;">
					Protected by 256-Bit SSL Encryption &amp; 30-Day Buyer Guarantee
				</p>
			</aside>
		</div>
		<?php } ?>
	</div>
</main>

<?php $this->load('common/address'); ?>
