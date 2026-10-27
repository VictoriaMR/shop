<!DOCTYPE html>
<html lang="<?php echo strtolower(lanId('code'));?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
		$__siteName = \App::get('domain', 'name', 'Shop');
		$__pageTitle = !empty($_title) ? $_title : (appT('_title') . ' - ' . $__siteName);
		$__pageDesc = !empty($_desc) ? $_desc : appT('_desc');
		$__pageKeyword = !empty($_keyword) ? $_keyword : appT('_keyword');
		$__routePath = lcfirst(\App::get('router', 'path', 'Index'));
		$__canonicalUrl = !empty($_canonical) ? $_canonical : ($__routePath === 'index' ? url() : url($__routePath));
		$__commonCssFile = frame('Html')->getCommon('css');
		$__commonJsFile = frame('Html')->getCommon('js');
		$__commonCssUrl = $__commonCssFile ? siteUrl($__commonCssFile) : '';
		$__commonJsUrl = $__commonJsFile ? siteUrl($__commonJsFile) : '';
		$__pageCssFile = frame('Html')->getCss();
		$__pageJsFile = frame('Html')->getJs();
	?>
	<?php if ($__commonCssUrl) { ?><link rel="preload" href="<?php echo $__commonCssUrl;?>" as="style">
	<?php } ?><?php if ($__commonJsUrl) { ?><link rel="preload" href="<?php echo $__commonJsUrl;?>" as="script">
	<?php } ?><title><?php echo htmlspecialchars($__pageTitle, ENT_QUOTES, 'UTF-8');?></title>
	<meta name="keywords" content="<?php echo htmlspecialchars($__pageKeyword, ENT_QUOTES, 'UTF-8');?>">
	<meta name="description" content="<?php echo htmlspecialchars($__pageDesc, ENT_QUOTES, 'UTF-8');?>">
	<meta name="robots" content="index, follow">
	<meta name="theme-color" content="#111827">
	<meta name="format-detection" content="telephone=no,email=no">
	<meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($__siteName, ENT_QUOTES, 'UTF-8');?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php echo htmlspecialchars($__siteName, ENT_QUOTES, 'UTF-8');?>">
	<meta property="og:title" content="<?php echo htmlspecialchars($__pageTitle, ENT_QUOTES, 'UTF-8');?>">
	<meta property="og:description" content="<?php echo htmlspecialchars($__pageDesc, ENT_QUOTES, 'UTF-8');?>">
	<link rel="canonical" href="<?php echo htmlspecialchars($__canonicalUrl, ENT_QUOTES, 'UTF-8');?>">
	<?php if (!empty($_prevUrl)) { ?><link rel="prev" href="<?php echo htmlspecialchars($_prevUrl, ENT_QUOTES, 'UTF-8');?>">
	<?php } ?><?php if (!empty($_nextUrl)) { ?><link rel="next" href="<?php echo htmlspecialchars($_nextUrl, ENT_QUOTES, 'UTF-8');?>">
	<?php } ?><link rel="apple-touch-icon" sizes="180x180" href="<?php echo siteUrl('apple-touch-icon.png');?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo siteUrl('favicon-32x32.png');?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo siteUrl('favicon-16x16.png');?>">
	<link rel="icon" type="image/png" sizes="48x48" href="<?php echo siteUrl('favicon-48x48.png');?>">
	<?php if ($__commonCssUrl) { ?><link rel="stylesheet" type="text/css" href="<?php echo $__commonCssUrl;?>">
	<?php } ?><?php if (!empty($__pageCssFile)) { ?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($__pageCssFile);?>">
	<?php } ?><script type="text/javascript">
		window.REQUEST_PARAM = <?php echo json_encode([
			'class' => \App::get('router', 'class', 'home'),
			'path'  => \App::get('router', 'path', 'Index'),
			'func'  => \App::get('router', 'func', 'index'),
		], JSON_UNESCAPED_UNICODE);?>;
	</script>
	<?php if ($__commonJsUrl) { ?><script type="text/javascript" src="<?php echo $__commonJsUrl;?>" defer></script>
	<?php } ?><?php if (!empty($__pageJsFile)) { ?><script type="text/javascript" src="<?php echo siteUrl($__pageJsFile);?>" defer></script>
<?php } ?></head>
<body>
