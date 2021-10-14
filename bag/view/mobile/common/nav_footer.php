<?php $router = \App::get('router');?>
<div class="cover-footer-padding"></div>
<div id="pb-footbar">
	<a class="tab<?php echo $router['path']=='index'?' active':'';?>" href="<?php echo url('');?>">
		<span class="iconfont icon-shouye<?php echo $router['path']=='index'?'fill':'';?>"></span>
		<p class="text"><?php echo appT('home');?></p>
	</a>
	<a class="tab<?php echo $router['path']=='cart'?' active':'';?>" href="<?php echo url('cart');?>">
		<span class="iconfont footer icon-gouwuche<?php echo $router['path']=='cart'?'fill':'';?>"></span>
		<p class="text"><?php echo appT('bag');?></p>
	</a>
	<a class="tab<?php echo $router['path']=='category'?' active':'';?>" href="<?php echo url('category');?>">
		<span class="iconfont icon-sousuoleimu<?php echo $router['path']=='category'?'fill':'';?>"></span>
		<p class="text"><?php echo appT('category');?></p>
	</a>
	<a class="tab<?php echo $router['path']=='message'?' active':'';?>" href="<?php echo url('contact');?>">
		<span class="iconfont icon-liuyan<?php echo $router['path']=='message'?'fill':'';?>"></span>
		<p class="text"><?php echo appT('chat');?></p>
	</a>
	<a class="tab<?php echo $router['path']=='userInfo'?' active':'';?>" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-wode<?php echo $router['path']=='userInfo'?'fill':'';?>"></span>
		<p class="text"><?php echo appT('me');?></p>
	</a>
</div>