<!DOCTYPE html>
<html lang="<?php echo strtolower(lanId('code'));?>" style="font-size:100px">
<head>
	<meta charset="utf-8">
	<title><?php echo $_title??\App::get('router', 'path').' - '.\App::get('base_info', 'name');?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
	<meta name="theme-color" content="#e5edef">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta name="apple-mobile-web-app-title" content="<?php echo \App::get('base_info', 'name');?>"/>
	<meta name="keywords" content="<?php echo $_keyword??\App::get('router', 'path').' - '.\App::get('base_info', 'name');?>"/>
	<meta name="description" content="<?php echo $_desc??\App::get('router', 'path').' - '.\App::get('base_info', 'name');?>"/>
	<meta name="App-Config" content="fullscreen=yes,useHistoryState=yes,transition=yes"/>
	<meta content="yes" name="apple-mobile-web-app-capable"/>
	<meta content="yes" name="apple-touch-fullscreen"/>
	<meta content="telephone=no,email=no" name="format-detection"/>
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo siteUrl('apple-touch-icon.png');?>"/>
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo siteUrl('favicon-32x32.png');?>"/>
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo siteUrl('favicon-16x16.png');?>"/>
	<link rel="icon" type="image/png" sizes="48x48" href="<?php echo siteUrl('favicon-48x48.png');?>"/>
	<?php if ($file = frame('Html')->getCommon('css')){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($file);?>"/>
	<?php }?><?php if ($file = frame('Html')->getCss()){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($file);?>">
	<?php }?><?php if ($file = frame('Html')->getCommon('js')){?><script type="text/javascript" src="<?php echo siteUrl($file);?>"></script><?php }?><?php if ($file = frame('Html')->getJs()){?><script type="text/javascript" src="<?php echo siteUrl($file);?>"></script>
	<?php }?><?php if ($file = frame('Html')->getLanguageJs()){?><script type="text/javascript" src="<?php echo siteUrl($file);?>"></script><?php }?></head>
<body>