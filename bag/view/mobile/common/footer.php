<?php $router = \Router::$_route;?>
<?php if ($router['path'] != 'Search'){ ?>
<div id="pb-footbar">
	<a class="tab<?php echo $router['path']=='Index'&&$router['func']=='index'?' active':'';?>" href="<?php echo url('');?>">
		<span class="iconfont icon-home<?php echo $router['path']=='Index'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">首页</p>
	</a>
	<a class="tab<?php echo $router['path']=='Cart'&&$router['func']=='index'?' active':'';?>" href="<?php echo url('cart');?>">
		<span class="iconfont icon-cart<?php echo $router['path']=='Cart'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">购物车</p>
	</a>
	<a class="tab<?php echo $router['path']=='Order'&&$router['func']=='index'?' active':'';?>" href="<?php echo url('order');?>">
		<span class="iconfont icon-baby<?php echo $router['path']=='Order'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">订单</p>
	</a>
	<a class="tab<?php echo $router['path']=='Message'&&$router['func']=='index'?' active':'';?>" href="<?php echo url('message');?>">
		<span class="iconfont icon-comment<?php echo $router['path']=='Message'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">消息</p>
	</a>
	<a class="tab<?php echo $router['path']=='UserInfo'&&$router['func']=='index'?' active':'';?>" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-my<?php echo $router['path']=='UserInfo'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">我的</p>
	</a>
</div>
<?php } ?>
</body>
</html>