<div class="mask"></div>
<div id="address-container" class="modal">
	<div class="content">
		<div class="header">
			<div class="title f14">Edit Address</div>
			<div class="close-btn f0"><img src="<?php echo siteUrl('/img/icon/close.svg');?>" alt="close"></div>
		</div>
		<div class="middle">
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
				</div>
				<div class="item-right">
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
				<div class="input-group">
					<div class="input-title">Phone Number*</div>
					<div class="input-content">
						<span class="dialing-code">+1</span><input class="phone-number" type="text" name="phone" autocomplete="off" id="phone" placeholder="For Delivery" maxlength="32" required="required"><i class="remove"></i>
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
						<span class="c5">Apartment / Suite / Other</span><span class="cr">*</span>
					</div>
					<div class="input-content">
						<input type="text" name="address_line2" autocomplete="off" placeholder="(Optional) House No, Apartment No" maxlength="64" required="required"><i class="remove"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			<button class="btn btn-black">SAVE</button>
		</div>
	</div>
</div>