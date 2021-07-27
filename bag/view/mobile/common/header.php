<!DOCTYPE html>
<html>
<head>
	<title><?php echo $_title ?? '';?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=3.0,minimum-scale=1.0,user-scalable=yes,viewport-fit=cover">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta name="apple-mobile-web-app-title" content="<?php echo $_sitename ?? '';?>"/>
	<meta name="App-Config" content="fullscreen=yes,useHistoryState=yes,transition=yes"/>
	<meta content="yes" name="apple-mobile-web-app-capable"/>
	<meta content="yes" name="apple-touch-fullscreen"/>
	<meta content="telephone=no,email=no" name="format-detection"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="<?php echo siteUrl(html()->getCommonCss());?>">
	<?php if (!empty($file = html()->getCss())){?>
	<link rel="stylesheet" type="text/css" href="<?php echo siteUrl($file);?>">
	<?php } ?>
	<script type="text/javascript" src="<?php echo siteUrl(html()->getCommonJs());?>"></script>
	<?php if (!empty($file = html()->getJs())){?>
	<script type="text/javascript" src="<?php echo siteUrl($file);?>"></script>
	<?php } ?>
</head>
<body>
<script type="text/javascript">
const URI = "<?php echo env('APP_DOMAIN');?>";
</script>
<div class="layer cover">
</div>