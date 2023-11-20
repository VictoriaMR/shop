<?php if (!empty($list)){?>
<div class="f0 product-list type-<?php echo $type??0;?>">
	<?php for($i=0; $i<100; $i++){?>
	<a class="item" href="">
		<div class="product-image">
			<img src="<?php echo siteUrl('img/placeholder.svg');?>" data-src="https://res.litfad.com/site/img/item/2023/10/13/10526945/375x375.jpg" class="lazyload">
		</div>
		<div class="footer-content">
			<p class="product-name">Black Metal Chandelier with Modern Globe Design and Adjustable Hanging Length</p>
		</div>
	</a>
	<?php }?>
</div>
<?php }?>