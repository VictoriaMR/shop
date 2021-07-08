<!DOCTYPE html>
<html>
<head>
    <title><?php echo $_title ?? '';?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo siteUrl('static/c_common.css');?>">
    <script type="text/javascript" src="<?php echo siteUrl('static/c_common.js');?>"></script>
    <?php foreach (\frame\Html::getCss() as $value) {?>
    <link rel="stylesheet" type="text/css" href="<?php echo env('APP_DOMAIN').$value;?>">
    <?php }?>
    <?php foreach (\frame\Html::getJs() as $value) {?>
    <script type="text/javascript" src="<?php echo env('APP_DOMAIN').$value;?>"></script>
    <?php }?>
</head>
<body>
<script type="text/javascript">
var URI = "<?php echo env('APP_DOMAIN');?>";
</script>
<div class="cover">
    <div class="layer">
        <div class="left">
            
        </div>
        <div class="right"></div>
    </div>
</div>