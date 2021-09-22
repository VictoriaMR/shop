<form class="layer8 cover" action="<?php echo url('order/search');?>">
	<a class="iconfont icon-xiangzuo1 f20 left" href="<?php echo url('order');?>"></a>
	<div class="order-search-content">
		<button class="btn btn-black"><?php echo appT('search');?></button>
		<div class="order-search-btn">
			<span class="iconfont icon-sousuo"></span>
			<input type="text" class="input" name="keyword" value="<?php echo $keyword;?>" placeholder="<?php echo appT('Product Name/Order NO.');?>">
		</div>
	</div>
</form>
<div class="cover-top-padding"></div>