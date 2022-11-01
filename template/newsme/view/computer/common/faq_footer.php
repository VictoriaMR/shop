<div class="faq-footer">
	<div class="layer">
		<table width="100%" border="0">
			<tbody>
				<tr>
					<td width="33.3%">
						<p class="title item">About Us</p>
						<a class="item" href="<?php echo url('about-us', ['f'=>'18']);?>" title="About <?php echo \App::get('base_info', 'name');?>">About <?php echo \App::get('base_info', 'name');?></a>
						<a class="item" href="<?php echo url('contact-us', ['f'=>'22']);?>" title="Contact Us">Contact Us</a>
						<a class="item" href="<?php echo url('our-guarantees', ['f'=>'21']);?>" title="Our Guarantees">Our Guarantees</a>
						<a class="item" href="<?php echo url('return-policy', ['f'=>'1']);?>" title="Return Policy">Return Policy</a>
						<a class="block f14" href="<?php echo url('ordering-guidance', ['f'=>'7']);?>" title="Ordering Guidance">Ordering Guidance</a>
					</td>
					<td width="33.3%">
						<p class="title item">HELP & SUPPORT</p>
						<a class="item" href="<?php echo url('order');?>" title="My Orders">My Orders</a>
						<a class="item" href="<?php echo url('account-setting', ['f'=>'14']);?>" title="Account Setting">Account Setting</a>
						<a class="item" href="<?php echo url('track-my-order', ['f'=>'11']);?>" title="Track My Order">Track My Order</a>
						<a class="item" href="<?php echo url('shipping-info', ['f'=>'8']);?>" title="Shipping Info">Shipping Info</a>
						<a class="block f14" href="<?php echo url('faq');?>" title="Help Center">Help Center</a>
					</td>
					<td width="33.3%" class="contact">
						<p class="title item">Contact Us</p>
						<button type="button" class="item"><?php echo \App::get('base_info', 'email');?></button>
						<p class="f18 item">Customer Service</p>
						<p class="item">Open 24/7.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
