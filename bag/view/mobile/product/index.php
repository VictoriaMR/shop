<div id="product-page">
	<div class="product-image">
		<ul>
		<?php foreach ($info['image'] as $value){?>
			<li class="slider">
				<img data-src="<?php echo $value['url'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
			</li>
			<?php } ?>
		</ul>
		<ol class="pop-content">
			<?php foreach ($info['image'] as $key => $value){?>
			<li <?php echo $key==0?'class="active"':'';?>></li>
			<?php } ?>
		</ol>
		<div class="navi-bar layer">
			<a href="javascript:window.history.back();" class="btn-back">
				<span class="iconfont icon-xiangzuo1"></span>
			</a>
			<a class="btn-cart right" href="<?php echo url('cart');?>">
				<span class="iconfont icon-gouwuche"></span>
			</a>
		</div>
		<button class="like-block">
			<span class="iconfont icon-xihuan<?php echo $isLiked ? 'fill' : '';?>"></span>
		</button>
	</div>
	<div class="name-and-price">
		<p class="product-name"><?php echo $info['name'];?></p>
		<div class="product-price mt8">
			<span class="price"><?php echo $info['min_price'];?> - <?php echo $info['max_price'];?></span>
			<span class="original_price"><?php echo $info['original_price'];?></span>
		</div>
		<p class="mt4 c9">
			<span>SKU: <?php echo $skuNo;?></span>
			<?php if ($info['sale_total']){?>
			<span class="right">Sold: <?php echo $info['sale_total'];?></span>
			<?php } ?>
		</p>
	</div>
	<div class="mt10 layer attr-select bg-f">
		<ul>
			<li id="sku-select">
				<p class="title c40 f600">SKU</p>
				<p class="text e1">SELECT <?php echo implode(' ', $info['attr']);?></p>
				<span class="iconfont icon-right"></span>
			</li>
			<li id="description">
				<p class="title">PARAM</p>
				<p class="text e1"><?php echo implode(' ', array_column($info['description'], 'name'));?></p>
				<span class="iconfont icon-right"></span>
			</li>
		</ul>
	</div>
	<!-- add cart -->
	<div class="cart-bottom">
		<div class="left">
			<a class="tab" href="<?php echo url('contact');?>">
				<span class="iconfont icon-kefu"></span>
				<p class="text">chat</p>
			</a>
			<button class="tab like-block" href="javascript:;">
				<span class="iconfont icon-xihuan<?php echo $isLiked ? 'fill' : '';?>"></span>
				<p class="text">collect</p>
			</button>
		</div>
		<div class="right">
			<button class="btn btn-light-black add-to-cart">ADD TO CART</button>
			<button class="btn btn-black checkout">CHECKOUT</button>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="m-modal hide" id="description-modal">
	<div class="mask"></div>
	<div class="dialog layer">
		<span class="iconfont icon-guanbi2"></span>
		<p class="dialog-title">Product parameters</p>
		<div class="content">
			<ul class="product-param-list f15">
				<?php foreach ($info['description'] as $value){?>
				<li class="item">
					<p class="param-name"><?php echo $value['name'];?></p>
					<p class="param-value"><?php echo $value['value'];?></p>
					<div class="clear"></div>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<div class="m-modal" id="sku-select-modal">
	<div class="mask"></div>
	<div class="dialog layer popup">
		<span class="iconfont icon-guanbi2"></span>
		<div class="contentfill">
			<div class="sku-image-block mt10">
				<div class="sku-image tcell">
					<img data-src="<?php echo $info['image'][0]['url'];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
				</div>
				<div class="sku-pro-info tcell">
					<p class="product-price">
						<span class="price"><?php echo $info['min_price'];?> - <?php echo $info['max_price'];?></span>
						<span class="original_price"><?php echo $info['original_price'];?></span>
					</p>
					<p class="stock c6">
						<span>STOCK: </span>
						<span class="number"><?php echo max(array_column($info['sku'], 'stock'));?></span>
					</p>
					<p class="c6">
						<span>SELECT: </span>
						<span class="text"><?php echo implode(' ', $info['attr']);?></span>
					</p>
				</div>
			</div>
			<div class="sku-attr-list mt20">
				<?php foreach ($info['attrMap'] as $key => $value) { ?>
				<div class="item attr-item">
					<p class="title"><?php echo $info['attr'][$key];?></p>
					<ul class="mt10">
						<?php foreach ($value as $vv){?>
						<?php if (empty($info['attvImage'][$vv])){ ?>
						<li class="item-text<?php echo count($value)>1?'':' active';?>" data-id="<?php echo $vv;?>" title="<?php echo $info['attv'][$vv];?>"><?php echo $info['attv'][$vv];?></li>
						<?php } else { ?>
						<li class="item-image<?php echo count($value)>1?'':' active';?>" data-id="<?php echo $vv;?>" title="<?php echo $info['attv'][$vv];?>">
							<div class="attv-image tcell">
								<img data-src="<?php echo $info['attvImage'][$vv];?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
							</div>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<div class="clear"></div>
				</div>
				<?php } ?>
				<div class="item stock-content">
					<p class="title left">Quantity</p>
					<div class="right quantity" data-stock="9999">
						<button class="plus"><span class="iconfont icon-jiahao1"></span></button>
						<input type="text" class="num" value="1" maxlength="4">
						<button class="minus disabled" disabled="disabled"><span class="iconfont icon-jianhao"></span></button>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="dialog-footer">
			<button class="btn btn-light-black add-to-cart left w50">ADD TO CART</button>
			<button class="btn btn-black buy-now left w50">BUY NOW</button>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.product-image').slider();
	PRODUCT.init({
		spuId: <?php echo $spuId;?>,
		skuId: <?php echo $skuId;?>,
		sku: <?php echo json_encode($info['sku'], JSON_UNESCAPED_UNICODE);?>,
		skuMap: <?php echo json_encode($info['skuMap'], JSON_UNESCAPED_UNICODE);?>,
		attrMap: <?php echo json_encode($info['attrMap'], JSON_UNESCAPED_UNICODE);?>,
		stock: <?php echo max(array_column($info['sku'], 'stock'));?>,
		price: '<?php echo $info['min_price'];?> - <?php echo $info['max_price'];?>',
	});
});
</script>