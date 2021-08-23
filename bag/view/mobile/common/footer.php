<?php $router = router()->getRoute();
if (!in_array($router['path'], ['checkout']) && !in_array($router['path'].'_'.$router['func'], ['product_index'])){ ?>
<div class="p22"></div>
<div id="pb-footbar">
	<a class="tab<?php echo $router['path']=='index'?' active':'';?>" href="<?php echo url('');?>">
		<span class="iconfont icon-shouye<?php echo $router['path']=='index'?'fill':'';?>"></span>
		<p class="text">home</p>
	</a>
	<a class="tab<?php echo $router['path']=='cart'?' active':'';?>" href="<?php echo url('cart');?>">
		<span class="iconfont footer icon-gouwuche<?php echo $router['path']=='cart'?'fill':'';?>"></span>
		<p class="text">cart</p>
	</a>
	<a class="tab<?php echo $router['path']=='category'?' active':'';?>" href="<?php echo url('category');?>">
		<span class="iconfont icon-sousuoleimu<?php echo $router['path']=='category'?'fill':'';?>"></span>
		<p class="text">category</p>
	</a>
	<a class="tab<?php echo $router['path']=='message'?' active':'';?>" href="<?php echo url('contact');?>">
		<span class="iconfont icon-liuyan<?php echo $router['path']=='message'?'fill':'';?>"></span>
		<p class="text">chat</p>
	</a>
	<a class="tab<?php echo $router['path']=='userInfo'?' active':'';?>" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-wode<?php echo $router['path']=='userInfo'?'fill':'';?>"></span>
		<p class="text">mine</p>
	</a>
</div>
<?php } ?>
</body>
</html>