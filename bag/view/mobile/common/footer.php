<?php $path = router()->getRoute('path'); if (in_array($path, ['index'])){ ?>
<div id="pb-footbar">
	<a class="tab<?php echo $path=='index'?' active':'';?>" href="<?php echo url('');?>">
		<span class="iconfont icon-home<?php echo $path=='index'?'fill':'';?>"></span>
		<p class="text">首页</p>
	</a>
	<a class="tab<?php echo $path=='cart'?' active':'';?>" href="<?php echo url('cart');?>">
		<span class="iconfont icon-cart<?php echo $path=='cart'?'fill':'';?>"></span>
		<p class="text">购物车</p>
	</a>
	<a class="tab<?php echo $path=='order'?' active':'';?>" href="<?php echo url('order');?>">
		<span class="iconfont icon-baby<?php echo $path=='order'?'fill':'';?>"></span>
		<p class="text">订单</p>
	</a>
	<a class="tab<?php echo $path=='message'?' active':'';?>" href="<?php echo url('contact');?>">
		<span class="iconfont icon-comment<?php echo $path=='message'?'fill':'';?>"></span>
		<p class="text">消息</p>
	</a>
	<a class="tab<?php echo $path=='userInfo'?' active':'';?>" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-my<?php echo $path=='userInfo'?'fill':'';?>"></span>
		<p class="text">我的</p>
	</a>
</div>
<?php } ?>
</body>
</html>