<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $_title;?></title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<link rel="icon" href="/computer/favicon.ico" type="image/x-icon">
	<?php if ($temp=frame('Html')->getCommon('css')){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($temp);?>">
	<?php }?>
	<?php if ($temp=frame('Html')->getCss()){?><link rel="stylesheet" type="text/css" href="<?php echo siteUrl($temp);?>">
	<?php } ?>
	<?php if($temp=frame('Html')->getCommon('js')){?><script type="text/javascript" src="<?php echo siteUrl($temp);?>"></script>
	<?php }?>
	<?php if ($temp=frame('Html')->getJs()){?><script type="text/javascript" src="<?php echo siteUrl($temp);?>"></script>
	<?php } ?>
</head>
<body>
<?php if (!($_path == 'Index' && $_func == 'index') && $_path != 'Login'){?><div id="progressing"></div><?php }?>
<?php if (!empty($_nav)) {?>
<div id="header-nav" class="container-fluid">
	<div class="nav">
		<span><?php echo $_nav['default'];?></span>
		<?php if (!empty($_nav[$_func])){?>
		<span>&gt; <?php echo $_nav[$_func];?></span>
		<?php } ?>
		<a href="<?php echo frame('Router')->adminUrl($_path.'/'.$_func, iget());?>" class="glyphicon glyphicon-repeat ml12" title="重新加载"></a>
		<a href="<?php echo frame('Router')->adminUrl($_path.'/'.$_func, iget());?>" target="_blank" class="glyphicon glyphicon-link ml12" title="新页面打开"></a>
	</div>
</div>
<?php } ?>
<?php if (!empty(\App::$error)){?>
<div class="container-fluid">
	<div class="alert alert-danger"><?php echo implode(PHP_EOL, \App::$error);?></div>
</div>
<?php }?>
<?php if (!empty(\App::$seccess)){?>
<div class="container-fluid">
	<div class="alert alert-success"><?php echo implode(PHP_EOL, \App::$seccess);?></div>
</div>
<?php }?>
<?php if (!empty($_tag)) {?>
<div class="container-fluid" style="margin-bottom: 15px;">
	<ul class="nav nav-tabs">
		<?php foreach ($_tag as $key => $value) { if ($_func != $key && in_array($key, $_ignore)){continue;}?>
		<li<?php if($_func == $key) echo ' class="active"';?>>
			<a href="<?php echo frame('Router')->adminUrl($_path.'/'.$key, iget());?>"><?php echo $value;?></a>
		</li>
		<?php } ?>
	</ul>
	<div class="clear"></div>
</div>
<?php }?>