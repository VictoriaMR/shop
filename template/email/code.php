<style type="text/css">
html, body {
	margin: 0;
    padding: 0;
    width: 100%;
}
</style>
<div style="margin:0;font-family:'Open Sans';box-sizing:border-box;width:650px;margin:0 auto;background-color: #FFF; font-size: 14px;color: #000000;text-align: center;">
	<div style="font-weight: bold;">
		<div style="height: 100px;line-height: 100px;background-color: #F4F4F4;width: 100%;">
			<span style="font-size: 24px;">verification code</span>
		</div>
		<div style="margin-top: 40px; font-size: 24px;">Hi there,</div>
		<div style="margin-top: 24px; font-size: 16px;padding: 20px">Welcome to <?php echo $siteName; ?>! Please click the link to log in <?php echo $siteName; ?> or copy the verification code and enter the form to log in</div>
		<div style="margin-top: 24px;margin-bottom: 80px; padding: 20px;">
			<div style="height: 64px;line-height: 64px;width: 195px;margin:0 auto;background-color: #3F9C35;">
				<a href="<?php echo $link;?>" target="_blank" style="font-size: 32px;color: #FFFFFF;"><?php echo $code;?></a>
			</div>
			<p style="margin-top: 20px;"><?php echo htmlentities($link);?></p>
		</div>
	</div>
	<div style="background-color: #F5F5F5;padding: 20px;">
		<div style="color: #999999;">If you have any question, please feel free to contact us, we’re glad to hearing from you.</div>

		<div style="margin-top:40px;">
			<ul style="list-style: none;font-size:12px;line-height: 18px;color:#999999;font-weight:600;">
				<li style="display:inline-block;margin:0;"><a style="color: #999999;text-decoration: none;" href="#">Terms & Conditions</a></li>
				<li style="display:inline-block;padding:0 8px;margin:0;">|</li>
				<li style="display:inline-block;margin:0;"><a style="color: #999999;text-decoration: none;" href="#">Privacy Policy</a></li>
				<li style="display:inline-block;padding:0 8px;margin:0;">|</li>
				<li style="display:inline-block;margin:0;"><a style="color: #999999;text-decoration: none;" href="#">Intellectual Property Rights</a></li>
			</ul>
			<div style="box-sizing:border-box;font-size:12px;line-height:18px;font-weight:600;color:#333;margin-top:16px;">© 2019-<?php echo date('Y');?> <?php echo $siteName; ?> All Rights Reserved</div>
		</div>
	</div>
</div>