<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $_title ?? '';?></title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Cache-Control" content="no-siteapp" />
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
const URI = "<?php echo config('env.APP_DOMAIN');?>";
</script>
<div class="cover">
	<div class="layer">
		<div class="left">

		</div>
		<div class="right"></div>
	</div>
</div>