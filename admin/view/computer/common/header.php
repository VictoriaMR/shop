<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理后台<?php echo empty($_title) ? '' : '-'.$_title;?></title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo siteUrl(html()->getCss());?>">
    <script type="text/javascript" src="<?php echo siteUrl(html()->getJs());?>"></script>
</head>
<body>
<div id="progressing"></div>
<script type="text/javascript">
const URI = "<?php echo env('APP_DOMAIN');?>";
</script>
<?php if (!empty($_nav)) {?>
<div id="header-nav" class="container-fluid">
    <div class="nav">
        <span><?php echo $_nav['default'];?></span>
        <?php if (!empty($_nav[$_func])){?>
        <span>&gt; <?php echo $_nav[$_func];?></span>
        <?php } ?>
        <a href="<?php echo url();?>" class="glyphicon glyphicon-repeat ml12" title="重新加载"></a>
        <a href="<?php echo url();?>" target="_blank" class="glyphicon glyphicon-link ml12" title="新页面打开"></a>
    </div>
</div>
<?php } ?>
<?php if (!empty($_tagShow) && !empty($_tag)) {?>
<div class="container-fluid" style="margin-bottom: 15px;">
    <ul class="nav nav-tabs">
        <?php foreach ($_tag as $key => $value) {?>
        <li<?php if($_func == $key) echo ' class="active"';?>>
            <a href="<?php echo url($_path.'/'.$key);?>"><?php echo $value;?></a>
        </li>
        <?php } ?>
    </ul>
    <div class="clear"></div>
</div>
<?php } ?>