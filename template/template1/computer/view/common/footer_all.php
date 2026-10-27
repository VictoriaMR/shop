<?php
$siteName = $siteName ?? \App::get('domain', 'name', 'Shop');
$siteEmail = $siteEmail ?? 'support@shop.com';
?>
<footer class="site-footer">
	<div class="footer-main">
		<div class="container footer-grid">
			<!-- Col 1: Brand Info & Newsletter -->
			<div class="footer-col footer-col-brand">
				<a href="<?php echo url();?>" class="footer-logo flex-align" title="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?>">
					<span class="brand-mark" aria-hidden="true">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
					</span>
					<strong class="footer-brand-name"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?></strong>
				</a>
				<p class="footer-desc"><?php echo appT('newsletter_desc');?></p>
				<form class="newsletter-form" id="footer-newsletter-form" onsubmit="return false;">
					<label for="footer-newsletter-email" class="sr-only">Email address</label>
					<input type="email" id="footer-newsletter-email" class="newsletter-input" placeholder="Enter your email address..." required>
					<button type="submit" class="btn btn-primary newsletter-btn"><?php echo appT('subscribe');?></button>
				</form>
				<div class="footer-contact-meta">
					<span>Customer Care: <a href="mailto:<?php echo htmlspecialchars($siteEmail, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($siteEmail, ENT_QUOTES, 'UTF-8');?></a></span>
					<span>Service Hours: 24/7 Online Support</span>
				</div>
			</div>

			<!-- Col 2: Company & About -->
			<div class="footer-col">
				<h2 class="footer-heading"><?php echo appT('about_us');?></h2>
				<ul class="footer-links">
					<li><a href="<?php echo url('faq', ['f' => 18]);?>" title="About <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?>">About <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?></a></li>
					<li><a href="<?php echo url('faq', ['f' => 21]);?>" title="Our Guarantee">Our Guarantee</a></li>
					<li><a href="<?php echo url('contact');?>" title="Contact Us">Contact Us</a></li>
					<li><a href="<?php echo url('faq', ['f' => 19]);?>" title="Terms of Use">Terms of Use</a></li>
					<li><a href="<?php echo url('faq', ['f' => 20]);?>" title="Privacy Policy">Privacy Policy</a></li>
					<li><a href="<?php echo url('faq', ['f' => 23]);?>" title="Copyright Notice">Copyright Notice</a></li>
				</ul>
			</div>

			<!-- Col 3: Customer Care & Policies -->
			<div class="footer-col">
				<h2 class="footer-heading"><?php echo appT('customer_care');?></h2>
				<ul class="footer-links">
					<li><a href="<?php echo url('faq');?>" title="Help Center">Help Center &amp; FAQ</a></li>
					<li><a href="<?php echo url('faq', ['f' => 7]);?>" title="Ordering Guidance">Ordering Guidance</a></li>
					<li><a href="<?php echo url('faq', ['f' => 8]);?>" title="Shipping Info">Shipping Information</a></li>
					<li><a href="<?php echo url('faq', ['f' => 11]);?>" title="Track Order">Track My Order</a></li>
					<li><a href="<?php echo url('faq', ['f' => 1]);?>" title="Return Policy">Return &amp; Exchange Policy</a></li>
					<li><a href="<?php echo url('faq', ['f' => 16]);?>" title="Refunds">Refunds &amp; Billing</a></li>
				</ul>
			</div>

			<!-- Col 4: Quick Shop & Account -->
			<div class="footer-col">
				<h2 class="footer-heading">Quick Links</h2>
				<ul class="footer-links">
					<li><a href="<?php echo url('newIn');?>" title="New Arrivals">New Arrivals</a></li>
					<li><a href="<?php echo url('userInfo');?>" title="My Account">My Account</a></li>
					<li><a href="<?php echo url('order');?>" title="Order History">My Orders</a></li>
					<li><a href="<?php echo url('userInfo/wishList');?>" title="Wishlist">Saved Wishlist</a></li>
					<li><a href="<?php echo url('cart');?>" title="Shopping Bag">Shopping Bag</a></li>
				</ul>
			</div>
		</div>
	</div>

	<!-- Payment & Security Trust Bar -->
	<div class="footer-payment">
		<div class="container flex-between">
			<div class="payment-label flex-align">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
				<span>Guaranteed Safe &amp; Secure Checkout</span>
			</div>
			<div class="payment-badges flex-align" role="group" aria-label="Supported payment methods">
				<span class="pay-card">VISA</span>
				<span class="pay-card">MasterCard</span>
				<span class="pay-card">PayPal</span>
				<span class="pay-card">AMEX</span>
				<span class="pay-card">Discover</span>
				<span class="pay-card">Apple Pay</span>
			</div>
		</div>
	</div>

	<?php $this->load('common/footer_simple');?>
</footer>
