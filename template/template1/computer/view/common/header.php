<!DOCTYPE html>
<html lang="<?php echo strtolower(lanId('code'));?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="cache-control" content="no-cache">
	<title><?php echo $_title??\App::get('router', 'path').' - '.\App::get('domain', 'name');?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#E5EDEF">
	<meta name="format-detection" content="telephone=no,email=no">
	<meta name="apple-mobile-web-app-title" content="<?php echo \App::get('domain', 'name');?>">
	<meta name="keywords" content="<?php echo $_keyword??\App::get('router', 'path').' - '.\App::get('domain', 'name');?>">
	<meta name="description" content="<?php echo $_desc??\App::get('router', 'path').' - '.\App::get('domain', 'name');?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo siteUrl('apple-touch-icon.png');?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo siteUrl('favicon-32x32.png');?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo siteUrl('favicon-16x16.png');?>">
	<link rel="icon" type="image/png" sizes="48x48" href="<?php echo siteUrl('favicon-48x48.png');?>">
	<link rel="stylesheet" type="text/css" href="<?php echo siteUrl(frame('Html')->getCommon('css'));?>">
	<?php if (!empty($file = frame('Html')->getCss())){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($file);?>">
	<?php }?><script type="text/javascript" src="<?php echo siteUrl(frame('Html')->getCommon('js'));?>"></script>
	<?php if (!empty($file = frame('Html')->getJs())){?><script type="text/javascript" src="<?php echo siteUrl($file);?>"></script>
<?php }?></head>
<body>
