<div id="address-container" class="modal open">
	<form class="content">
		<div class="header">
			<div class="title f14">Edit Address</div>
			<div class="close-btn f0"><img src="<?php echo siteUrl('/img/icon/close.svg');?>" alt="close"></div>
		</div>
		<div class="middle">
			<div class="item" style="border-radius: 0.06rem;overflow: hidden;">
				<div class="item-left" style="padding-right: 0;">
					<div class="half-block active" data-type="0">Personal</div>
				</div><div class="item-right" style="padding-left: 0;">
					<div class="half-block" data-type="1">Company</div>
				</div>
			</div>
			<div class="item company-name-item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">Company Name</span><span class="cr">*</span>
					</div>
					<div class="input-content">
						<input type="text" name="company_name" autocomplete="off" placeholder="1-64 characters" maxlength="64" required="required"><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">Country</span><span class="cr">*</span>
					</div>
					<div class="select-group">
						<div class="title">Unite State</div>
						<i class="icon16 icon-down"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="item-left">
					<div class="input-group">
						<div class="input-title">
							<span class="c5">First Name</span><span class="cr">*</span>
						</div>
						<div class="input-content">
							<input type="text" name="first_name" autocomplete="off" placeholder="1-32 characters" maxlength="32" required="required"><i class="remove"></i>
						</div>
					</div>
				</div><div class="item-right">
					<div class="input-group">
						<div class="input-title">
							<span class="c5">Last Name</span><span class="cr">*</span>
						</div>
						<div class="input-content">
							<input type="text" name="last_name" autocomplete="off" placeholder="1-32 characters" maxlength="32" required="required"><i class="remove"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">Zip/Post Code</span><span class="cr">*</span>
					</div>
					<div class="input-content">
						<input type="text" name="postcode" autocomplete="off" placeholder="e.g. 20001 / 20001-0000" maxlength="32" required="required"><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group" id="phone-input-group">
					<div class="input-title"><span class="c5">Phone Number</span><span class="cr">*</span></div>
					<div class="input-content">
						<input class="phone-number" type="text" name="phone" autocomplete="off" id="phone" placeholder="For Delivery" maxlength="32" required="required"><span class="dialing-code">+1</span><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">City</span><span class="cr">*</span>
					</div>
					<div class="input-content">
						<input type="text" name="city" autocomplete="off" placeholder="1-32 characters" maxlength="32" required="required"><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">Street Address</span><span class="cr">*</span>
					</div>
					<div class="input-content">
						<input type="text" name="address_line1" autocomplete="off" placeholder="Street, Address" maxlength="64" required="required"><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="input-group">
					<div class="input-title">
						<span class="c5">Apartment / Suite / Other</span>
					</div>
					<div class="input-content">
						<input type="text" name="address_line2" autocomplete="off" placeholder="(Optional) House No, Apartment No" maxlength="64"><i class="remove"></i>
					</div>
				</div>
			</div>
			<div class="item default-item">
				<div class="switch-item">
					<span class="c5">Make Default Shipping Address</span>
					<div class="switch">
						<span class="slider"></span>
						<input type="hidden" name="default_shipping_address" value="0">
					</div>
				</div>
				<div class="switch-item">
					<span class="c5">Make Default Billing Address</span>
					<div class="switch">
						<span class="slider"></span>
						<input type="hidden" name="default_billing_address" value="0">
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			<button type="button" class="btn btn-black btn-save">SAVE</button>
		</div>
	</form>
</div>
<script>
	const local_country = "<?php echo site()->getCountryCode();?>";
	const country_list = "<?php echo json_encode(sys()->country()->getList());?>";

</script>
<?php 