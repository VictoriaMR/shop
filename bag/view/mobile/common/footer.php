<?php $path = router()->getRoute('path'); if (in_array($path, ['index'])){ ?>
<div id="pb-footbar">
	<a class="tab<?php echo $path=='index'?' active':'';?>" href="<?php echo url('');?>">
		<span class="iconfont icon-shouye<?php echo $path=='index'?'fill':'';?>"></span>
		<p class="text">home</p>
	</a>
	<a class="tab<?php echo $path=='cart'?' active':'';?>" href="<?php echo url('cart');?>">
		<span class="iconfont icon-gouwuche<?php echo $path=='cart'?'fill':'';?>"></span>
		<p class="text">cart</p>
	</a>
	<a class="tab<?php echo $path=='category'?' active':'';?>" href="<?php echo url('category');?>">
		<span class="iconfont icon-sousuoleimu<?php echo $path=='category'?'fill':'';?>"></span>
		<p class="text">category</p>
	</a>
	<a class="tab<?php echo $path=='message'?' active':'';?>" href="<?php echo url('contact');?>">
		<span class="iconfont icon-liuyan<?php echo $path=='message'?'fill':'';?>"></span>
		<p class="text">chat</p>
	</a>
	<a class="tab<?php echo $path=='userInfo'?' active':'';?>" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-wode<?php echo $path=='userInfo'?'fill':'';?>"></span>
		<p class="text">mine</p>
	</a>
</div>
<?php } ?>
</body>
</html>