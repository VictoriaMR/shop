<?php
$siteName = $siteName ?? \App::get('domain', 'name', 'Shop');
?>
<div class="footer-bottom">
	<div class="container flex-between">
		<p class="copyright-text">Copyright &copy; 2020-<?php echo date('Y');?> <strong><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?></strong>. <?php echo appT('copyright');?></p>
		<div class="footer-legal-links flex-align">
			<a href="<?php echo url('faq', ['f' => 19]);?>">Terms &amp; Conditions</a>
			<span class="sep">|</span>
			<a href="<?php echo url('faq', ['f' => 20]);?>">Privacy Policy</a>
			<span class="sep">|</span>
			<a href="<?php echo url('faq', ['f' => 1]);?>">Return Policy</a>
			<span class="sep">|</span>
			<a href="<?php echo url('contact');?>">Support</a>
		</div>
	</div>
</div>
<button type="button" id="back-to-top" class="back-to-top" aria-label="Back to top" title="Back to top">
	<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
</button>
