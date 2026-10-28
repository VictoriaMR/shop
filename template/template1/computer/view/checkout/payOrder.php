<?php
$this->load('common/header_all');
$placeholderImg = siteUrl('image/common/noimg.svg');
$base = $orderInfo['base'] ?? [];
$shipAddr = $orderInfo['shipping_address'] ?? [];
?>

<main class="pay-page" id="main-content">
	<div class="container">
		<?php if (!empty($error) || empty($base)) { ?>
		<div class="empty-state">
			<h1 class="empty-title"><?php echo htmlspecialchars($error ?: 'Order not found', ENT_QUOTES, 'UTF-8'); ?></h1>
			<p class="empty-desc">Please check your order history or contact customer support for assistance.</p>
			<a href="<?php echo url('order'); ?>" class="btn btn-primary">View My Orders</a>
		</div>
		<?php } else { ?>
		<input type="hidden" id="pay-order-id" value="<?php echo (int)$base['order_id']; ?>">
		<input type="hidden" id="pay-selected-method" value="<?php echo (int)$method; ?>">

		<div class="pay-banner">
			<div>
				<h1 class="pay-banner-title">Order <?php echo htmlspecialchars($base['order_no'], ENT_QUOTES, 'UTF-8'); ?> Created</h1>
				<p class="pay-banner-sub">Select your preferred payment method below to complete your order.</p>
			</div>
			<div style="text-align:right;">
				<div style="font-size:12px;color:#cbd5e1;">Amount Due</div>
				<strong style="font-size:26px;font-weight:800;color:#ffffff;"><?php echo htmlspecialchars($base['order_total_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
			</div>
		</div>

		<div class="pay-layout">
			<div>
				<!-- Payment Methods -->
				<section class="pay-card" aria-labelledby="pay-methods-heading">
					<h2 class="pay-card-title" id="pay-methods-heading">Select Payment Method</h2>
					<div class="pay-method-list">
						<?php foreach ($methodList as $mKey => $mRow) {
							$isSelected = ((int)$mKey === (int)$method);
						?>
						<label class="pay-method-item<?php echo $isSelected ? ' selected' : ''; ?>" data-method-type="<?php echo (int)$mKey; ?>">
							<div class="flex-align" style="gap:14px;">
								<input type="radio" name="pay_method" value="<?php echo (int)$mKey; ?>" <?php echo $isSelected ? 'checked' : ''; ?> style="width:18px;height:18px;accent-color:var(--primary);">
								<div>
									<div class="pay-method-name"><?php echo htmlspecialchars($mRow['name'], ENT_QUOTES, 'UTF-8'); ?></div>
									<?php if (!empty($mRow['desc'])) { ?>
									<div class="pay-method-desc"><?php echo htmlspecialchars($mRow['desc'], ENT_QUOTES, 'UTF-8'); ?></div>
									<?php } ?>
								</div>
							</div>
							<?php if (!empty($mRow['badge'])) { ?>
							<span class="pay-method-badge"><?php echo htmlspecialchars($mRow['badge'], ENT_QUOTES, 'UTF-8'); ?></span>
							<?php } ?>
						</label>
						<?php } ?>
					</div>
				</section>

				<!-- Delivery Address Summary -->
				<?php if (!empty($shipAddr)) { ?>
				<section class="pay-card" aria-labelledby="pay-addr-heading">
					<h2 class="pay-card-title" id="pay-addr-heading">Delivery Destination</h2>
					<p style="font-size:14px;font-weight:700;color:var(--text-main);margin-bottom:4px;">
						<?php echo htmlspecialchars(trim(($shipAddr['first_name'] ?? '') . ' ' . ($shipAddr['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
						<span style="font-weight:500;color:var(--text-secondary);margin-left:8px;"><?php echo htmlspecialchars($shipAddr['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
					</p>
					<p style="font-size:13px;color:var(--text-secondary);">
						<?php echo htmlspecialchars(($shipAddr['address1'] ?? '') . ' ' . ($shipAddr['address2'] ?? '') . ', ' . ($shipAddr['city'] ?? '') . ', ' . ($shipAddr['state'] ?? '') . ' ' . ($shipAddr['postcode'] ?? '') . ', ' . ($shipAddr['country'] ?? $shipAddr['country_code2'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
					</p>
				</section>
				<?php } ?>
			</div>

			<!-- Right Column: Summary & Pay CTA -->
			<aside class="pay-card" style="position:sticky;top:96px;" aria-label="Payment Summary">
				<h2 class="pay-card-title">Payment Breakdown</h2>
				<?php foreach (($orderInfo['fee_list'] ?? []) as $fee) { ?>
				<div class="flex-between" style="font-size:14px;color:var(--text-secondary);margin-bottom:12px;">
					<span><?php echo htmlspecialchars($fee['name'], ENT_QUOTES, 'UTF-8'); ?></span>
					<span><?php echo htmlspecialchars($fee['value_format'], ENT_QUOTES, 'UTF-8'); ?></span>
				</div>
				<?php } ?>

				<div class="flex-between" style="padding-top:16px;margin-top:16px;border-top:1px solid var(--border-color);margin-bottom:20px;">
					<span style="font-size:15px;font-weight:800;color:var(--text-main);">Total Due</span>
					<strong style="font-size:24px;font-weight:800;color:var(--accent);"><?php echo htmlspecialchars($base['order_total_format'], ENT_QUOTES, 'UTF-8'); ?></strong>
				</div>

				<button type="button" id="btn-confirm-payment" class="btn btn-primary btn-block" style="height:48px;font-size:15px;margin-bottom:12px;">
					Complete Payment Now
				</button>
				<a href="<?php echo url('order/detail', ['id' => $base['order_id']]); ?>" class="btn btn-outline btn-block btn-sm">View Order Details</a>
			</aside>
		</div>
		<?php } ?>
	</div>
</main>
