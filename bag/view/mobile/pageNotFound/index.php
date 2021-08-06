<div class="layer">
	<div id="page-not-found">
		<p class="tips">We've searched high and low<br />
	but can't find what you are looking for?</p>
		<p class="home-tips">
			<span>Please try searching our site or return to our</span><br />
			<a href="<?php echo url();?>"><?php echo $siteName;?> HOME</a>
		</p>
		<a class="flex input-group searching" href="<?php echo url('search');?>">
			<button type="button" class="btn"><i class="iconfont icon-search"></i></button>
			<input class="input" type="text" name="search" placeholder="Search">
		</a>
	</div>
	<div class="mt32">
		<?php $this->load('common/recommend');?>
	</div>
</div>

