<!DOCTYPE html>
<html>
<head>
    <title><?php echo $_title ?? '';?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=3.0,minimum-scale=1.0,user-scalable=yes,viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta name="apple-mobile-web-app-title" content="LitFad"/>
    <meta name="App-Config" content="fullscreen=yes,useHistoryState=yes,transition=yes"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="yes" name="apple-touch-fullscreen"/>
    <meta content="telephone=no,email=no" name="format-detection"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo siteUrl('static/m_common.css');?>">
    <script type="text/javascript" src="<?php echo siteUrl('static/m_common.js');?>"></script>
    <?php foreach (\frame\Html::getCss() as $value) {?>
    <link rel="stylesheet" type="text/css" href="<?php echo env('APP_DOMAIN').$value;?>"><?php }?>
    <?php foreach (\frame\Html::getJs() as $value) {?>
    <script type="text/javascript" src="<?php echo env('APP_DOMAIN').$value;?>"></script>
    <?php }?>
</head>
<body>
<script type="text/javascript">
var URI = "<?php echo env('APP_DOMAIN');?>";
</script>
<div class="cover">
    <?php if (\Router::$_route['path'] == 'Search'){ ?>
    <a class="table left" href="<?php echo url('');?>">
        <div class="tcell">
            <span class="iconfont icon-back"></span>
        </div>
    </a>
    <div class="search-box">
        <div class="table">
            <div class="tcell">
                <img class="block center" src="<?php echo siteUrl('images/logo.png');?>">
            </div>
            <div class="tcell input-tcell">
                <input type="text" class="input" name="keyword" placeholder="what are you looking for?" autocomplete="off">
            </div>
            <div class="tcell">
                <span class="iconfont icon-search"></span>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <?php } else { ?>
    <a class="table left" href="<?php echo url('');?>">
        <div class="tcell">
            <img class="block" src="<?php echo siteUrl('images/logo.png');?>">
        </div>
    </a>
    <a class="table left ml10" href="<?php echo url('userInfo/wish');?>">
        <div class="tcell">
            <span class="iconfont icon-like"></span>
        </div>
    </a>
    <a class="table right ml10" href="javascript:;">
        <div class="tcell">
            <span class="iconfont icon-global"></span>
        </div>
    </a>
    <a class="table right" href="<?php echo url('search');?>">
        <div class="tcell">
            <span class="iconfont icon-search"></span>
        </div>
    </a>
    <?php } ?>
</div>