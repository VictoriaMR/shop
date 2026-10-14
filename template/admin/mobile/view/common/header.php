<!DOCTYPE html>
<html lang="zh-CN" style="font-size:100px">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlspecialchars($_title ?? '管理后台', ENT_QUOTES, 'UTF-8');?></title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,viewport-fit=cover">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Cache-Control" content="no-cache, no-siteapp">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="format-detection" content="telephone=no,email=no">
	<link rel="icon" href="/mobile/favicon.ico" type="image/x-icon">
	<script>
	(function(doc, win) {
		var docEl = doc.documentElement;
		var recalc = function() {
			var width = docEl.clientWidth || win.innerWidth;
			if (!width) return;
			if (width < 320) width = 320;
			if (width > 750) width = 750;
			docEl.style.fontSize = (width / 3.75) + 'px';
		};
		recalc();
		win.addEventListener('orientationchange' in win ? 'orientationchange' : 'resize', recalc, false);
	})(document, window);
	</script>
	<?php if ($temp = frame('Html')->getCommon('css')){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($temp);?>">
	<?php }?>
	<?php if ($temp = frame('Html')->getCommon('js')){?><script type="text/javascript" src="<?php echo siteUrl($temp);?>"></script>
	<?php }?>
	<?php if ($temp = frame('Html')->getCss()){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($temp);?>">
	<?php }?>
	<?php if ($temp = frame('Html')->getJs()){?><script type="text/javascript" src="<?php echo siteUrl($temp);?>"></script>
	<?php }?>
</head>
<body>
<?php if (!isset($_loading)){?><div id="progressing"></div><?php }?>
<?php if (!empty(\App::error())){?>
<div class="container-fluid">
	<div class="alert alert-danger"><?php echo implode(PHP_EOL, \App::error());?></div>
</div>
<?php }?>
<?php if (!empty(\App::$seccess)){?>
<div class="container-fluid">
	<div class="alert alert-success"><?php echo implode(PHP_EOL, \App::$seccess);?></div>
</div>
<?php }?>
<?php if (!empty($_tag)) {?>
<div class="mobile-tabs-wrap">
	<ul class="nav-tabs">
		<?php foreach ($_tag as $key => $value) { if ($_func != $key && in_array($key, $_ignore ?? [])){continue;}?>
		<li<?php if ($_func == $key) echo ' class="active"';?>>
			<a href="<?php echo frame('Router')->adminUrl($_path.'/'.$key);?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php }?>