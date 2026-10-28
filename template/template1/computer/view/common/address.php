<?php
$countries = service('system/Country')->getList();
if (empty($countries)) {
	$countries = [
		['code2' => 'US', 'dialing_code' => 1, 'name' => 'United States'],
		['code2' => 'GB', 'dialing_code' => 44, 'name' => 'United Kingdom'],
		['code2' => 'CA', 'dialing_code' => 1, 'name' => 'Canada'],
		['code2' => 'AU', 'dialing_code' => 61, 'name' => 'Australia'],
		['code2' => 'DE', 'dialing_code' => 49, 'name' => 'Germany'],
		['code2' => 'FR', 'dialing_code' => 33, 'name' => 'France'],
	];
}
?>
<div id="pc-address-modal" class="addr-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="pc-addr-modal-title">
	<div class="addr-modal-dialog">
		<div class="addr-modal-head flex-between">
			<h2 class="addr-modal-title" id="pc-addr-modal-title">Add Shipping Address</h2>
			<button type="button" class="addr-modal-close" id="pc-addr-modal-close" aria-label="Close modal">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
			</button>
		</div>

		<form id="pc-address-form" class="addr-modal-body" novalidate>
			<input type="hidden" name="address_id" id="addr-field-id" value="0">

			<div class="addr-form-row grid-2">
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-first-name">First Name <span class="req">*</span></label>
					<input type="text" id="addr-field-first-name" name="first_name" class="addr-input" placeholder="e.g. Alex" maxlength="32" autocomplete="given-name" required>
				</div>
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-last-name">Last Name <span class="req">*</span></label>
					<input type="text" id="addr-field-last-name" name="last_name" class="addr-input" placeholder="e.g. Morgan" maxlength="32" autocomplete="family-name" required>
				</div>
			</div>

			<div class="addr-form-row grid-2">
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-country">Country / Region <span class="req">*</span></label>
					<select id="addr-field-country" name="country_code2" class="addr-input" autocomplete="country" required>
						<?php foreach ($countries as $cRow) { ?>
						<option value="<?php echo htmlspecialchars($cRow['code2'], ENT_QUOTES, 'UTF-8'); ?>" data-dialing="<?php echo (int)$cRow['dialing_code']; ?>" <?php echo $cRow['code2'] === 'US' ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($cRow['name'], ENT_QUOTES, 'UTF-8'); ?> (+<?php echo (int)$cRow['dialing_code']; ?>)
						</option>
						<?php } ?>
					</select>
				</div>
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-phone">Phone Number (for delivery) <span class="req">*</span></label>
					<input type="tel" id="addr-field-phone" name="phone" class="addr-input" placeholder="e.g. 2025550188" maxlength="20" autocomplete="tel-national" required>
				</div>
			</div>

			<div class="addr-field-group">
				<label class="addr-label" for="addr-field-address1">Street Address <span class="req">*</span></label>
				<input type="text" id="addr-field-address1" name="address1" class="addr-input" placeholder="House number and street name" maxlength="64" autocomplete="address-line1" required>
			</div>

			<div class="addr-form-row grid-2">
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-address2">Apartment, Suite, Unit (Optional)</label>
					<input type="text" id="addr-field-address2" name="address2" class="addr-input" placeholder="Apt, suite, floor, etc." maxlength="64" autocomplete="address-line2">
				</div>
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-company">Company Name (Optional)</label>
					<input type="text" id="addr-field-company" name="company_name" class="addr-input" placeholder="Company or studio" maxlength="64" autocomplete="organization">
				</div>
			</div>

			<div class="addr-form-row grid-3">
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-city">City <span class="req">*</span></label>
					<input type="text" id="addr-field-city" name="city" class="addr-input" placeholder="City" maxlength="32" autocomplete="address-level2" required>
				</div>
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-state">State / Province <span class="req">*</span></label>
					<input type="text" id="addr-field-state" name="state" class="addr-input" placeholder="State / Region" maxlength="32" autocomplete="address-level1" required>
				</div>
				<div class="addr-field-group">
					<label class="addr-label" for="addr-field-postcode">ZIP / Postal Code <span class="req">*</span></label>
					<input type="text" id="addr-field-postcode" name="postcode" class="addr-input" placeholder="e.g. 10001" maxlength="10" autocomplete="postal-code" required>
				</div>
			</div>

			<div class="addr-checks-row">
				<label class="addr-check-item" for="addr-field-default">
					<input type="checkbox" id="addr-field-default" name="is_default" value="1" checked>
					<span>Set as default shipping address</span>
				</label>
				<label class="addr-check-item" for="addr-field-bill">
					<input type="checkbox" id="addr-field-bill" name="is_bill" value="1" checked>
					<span>Set as default billing address</span>
				</label>
			</div>

			<div class="addr-modal-foot flex-between">
				<button type="button" class="btn btn-outline" id="pc-addr-modal-cancel">Cancel</button>
				<button type="submit" class="btn btn-primary" id="pc-addr-modal-submit">Save Address</button>
			</div>
		</form>
	</div>
</div>
