<?php $router = \App::get('router');?>
<div class="layer cover">
	<div class="tc">
		<a class="<?php if ($router['path'] == 'NewIn' && $router['func'] == 'index'){ echo 'f18 f600';}else{echo 'f14 c9';}?>" href="<?php echo url('newIn');?>"><?php echo appT('new_in');?></a>
		<a class="ml20 <?php if ($router['path'] == 'Index' && $router['func'] == 'index'){ echo 'f18 f600';}else{echo 'f14 c9';}?>" href="<?php echo url('');?>"><?php echo appT('refer');?></a>
	</div>
	<a class="f18 top-search-icon" href="<?php echo url('search');?>">
		<span class="iconfont icon-sousuo"></span>
	</a>
</div>
<div class="cover-top-padding"></div>