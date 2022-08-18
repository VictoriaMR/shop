<!DOCTYPE html>
<html lang="<?php echo strtolower(lanId('code'));?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="cache-control" content="no-cache">
	<title><?php echo $_title??appT('_title');?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="telephone=no,email=no" name="format-detection">
	<meta name="apple-mobile-web-app-title" content="<?php echo \App::get('base_info', 'name');?>">
	<meta name="description" content="<?php echo $_desc??appT('_desc');?>">
	<meta name="keywords" content="<?php echo $_keyword??appT('_keyword');?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo siteUrl('apple-touch-icon.png');?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo siteUrl('favicon-32x32.png');?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo siteUrl('favicon-16x16.png');?>">
	<link rel="icon" type="image/png" sizes="48x48" href="<?php echo siteUrl('favicon-48x48.png');?>">
	<link rel="stylesheet" type="text/css" href="<?php echo siteUrl(html()->getCommonCss());?>">
	<?php if (!empty($file = html()->getCss())){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($file);?>">
	<?php }?><script type="text/javascript" src="<?php echo siteUrl(html()->getCommonJs());?>"></script>
	<?php if (!empty($file = html()->getJs())){?><script type="text/javascript" src="<?php echo siteUrl($file);?>"></script>
<?php }?></head>
<body>
<script type="text/javascript">var URI='<?php echo APP_DOMAIN;?>';</script>
