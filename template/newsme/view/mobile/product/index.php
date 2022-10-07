<div id="product-page">
	<?php if (empty($info)){?><div class="empty-info">
        <img src="<?php echo siteUrl('image/common/oooops.png');?>">
        <p class="mt12 f14">No item matched.<br/>Please try with other options.</p>
    </div>
    <?php $this->load('product/recommend', ['title'=>true]);?>
    <?php } else {?>
	<div class="product-image">
		<ul>
			<?php foreach ($info['image_list'] as $value){?><li class="slider">
				<img src="<?php echo str_replace('/400', '/600', $value);?>" alt="<?php echo $info['name'];?>">
			</li>
			<?php } ?>
		</ul>
		<ol class="pop-content">
			<?php $count=0; foreach ($info['image_list'] as $key => $value){$count++;?><li <?php echo $count==1?'class="active"':'';?>></li>
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
		<p class="product-name"><?php echo $skuId ? $skuInfo['name'] : $info['name'];?></p>
		<div class="product-price mt8">
			<?php if ($skuId) {?><span class="price"><?php echo $skuInfo['price_format'];?></span>
			<?php if($info['show_price']){?><span class="original_price"><?php echo $skuInfo['original_price_format'];?></span>
			<?php }?>
			<?php } else { ?>
			<span class="price"><?php echo $info['min_price_format'];?><?php if($info['max_price']>$info['min_price']){ echo ' - '.$info['max_price_format'];}?></span>
			<?php if($info['show_price']){?><span class="original_price"><?php echo $info['original_price_format'];?></span>
			<?php }?>
			<?php } ?>
		</div>
		<p class="mt4 c9 tc">
			<span class="left"><?php if ($skuId){ echo distT('sku').': '.$skuId;}else{echo distT('spu').': '.$spuId;}?></span>
			<span><?php echo distT('stock');?>: <?php echo $stock;?></span>
			<?php if ($saleTotal){?>
			<span class="right"><?php echo distT('sold');?>: <?php echo $saleTotal;?></span>
			<?php } ?>
		</p>
	</div>
	<div class="mt10 layer attr-select bg-f">
		<ul>
			<li id="sku-select">
				<p class="title c40 f600"><?php echo distT('sku');?></p>
				<p class="text e1 f600">
					<span class="attr-text">
					<?php if (empty($skuAttv)) {?>
					<span>SELECT </span>
					<span class="attr-text"><?php echo implode(' ', $info['attr']);?></span>
					<?php } else {?>
					<span class="attr-text"><?php foreach ($skuAttv as $value){ echo $info['attv'][$value].' ';} ?></span>
					<?php } ?>
				</p>
				<span class="iconfont icon-xiangyou1"></span>
			</li>
			<li id="description">
				<p class="title"><?php echo distT('param');?></p>
				<p class="text e1"><?php echo implode(' ', array_column($info['description'], 'name'));?></p>
				<span class="iconfont icon-xiangyou1"></span>
			</li>
		</ul>
	</div>
	<?php if (!empty($info['introduce'])) {?>
	<div class="product-introduce mt10">
		<div class="list-title flex layer">
			<div class="tcell">
				<p class="line"></p>
			</div>
			<p class="title"><?php echo distT('introduce');?></p>
			<div class="tcell">
				<p class="line"></p>
			</div>
		</div>
		<div class="introduce-image tc mt10 layer bg-f">
			<?php foreach($info['introduce'] as $value){?><p>
				<img data-src="<?php echo str_replace('/400', '', $value);?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
			</p>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	<!-- add cart -->
	<div class="cart-bottom">
		<div class="left">
			<a class="tab relative" href="<?php echo url('cart');?>">
				<span class="iconfont icon-gouwuche"></span>
				<p class="text"><?php echo distT('cart');?></p>
			</a>
			<button class="tab like-block" href="javascript:;">
				<span class="iconfont icon-xihuan<?php echo $isLiked ? 'fill' : '';?>"></span>
				<p class="text"><?php echo distT('collect');?></p>
			</button>
		</div>
		<div class="right">
			<button class="btn btn-light-black add-to-cart"><?php echo distT('add_to_cart');?></button>
			<button class="btn btn-black checkout-btn"><?php echo distT('checkout');?></button>
		</div>
		<div class="clear"></div>
	</div>
	<?php }?>
</div>
<?php if (!empty($info)){?><div class="m-modal hide" id="description-modal">
	<div class="mask"></div>
	<div class="dialog layer">
		<span class="iconfont icon-guanbi2"></span>
		<p class="dialog-title"><?php echo distT('product_parameters');?></p>
		<div class="content">
			<table class="product-param-list f15">
				<tbody>
					<?php foreach ($info['description'] as $value){if(empty($value['name'])){continue;}?><tr class="item">
						<td width="33%" class="param-name"><?php echo $value['name'];?>:</td>
						<td width="67%" class="param-value"><?php echo $value['value'];?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="m-modal hide" id="sku-select-modal">
	<div class="mask"></div>
	<div class="dialog layer">
		<span class="iconfont icon-guanbi2"></span>
		<div class="contentfill">
			<div class="sku-image-block mt10 f0">
				<div class="sku-image tcell">
					<img src="<?php echo $info['image_list'][0];?>" alt="<?php echo $info['name'];?>">
				</div>
				<div class="sku-pro-info tcell">
					<p class="product-price">
						<span class="price"><?php echo $info['min_price_format'];?><?php if($info['max_price']>$info['min_price']){echo ' - '.$info['min_price_format'];}?></span>
						<?php if ($info['show_price']){?><span class="original_price"><?php echo $info['original_price'];?></span>
						<?php }?>
					</p>
					<p class="stock c6">
						<span><?php echo distT('stock');?>: </span>
						<span class="number"><?php echo $stock;?></span>
					</p>
					<p class="select-text c6">
						<span><?php echo distT('select');?>: </span>
						<span class="text"><?php echo implode(' ', $info['attr']);?></span>
					</p>
				</div>
			</div>
			<div class="sku-attr-list mt20">
				<?php foreach ($info['attrMap'] as $key => $value) { ?>
				<div class="item attr-item" data-id="<?php echo $key;?>">
					<p class="title"><?php echo $info['attr'][$key];?></p>
					<ul class="mt10">
						<?php foreach ($value as $vv){?>
						<?php if (empty($info['attvImage'][$vv])){ ?>
						<li class="item-text<?php echo count($value)==1||in_array($vv, $skuAttv)?' active':'';?>" data-id="<?php echo $vv;?>" title="<?php echo $info['attv'][$vv];?>"><?php echo $info['attv'][$vv];?></li>
						<?php } else { ?>
						<li class="item-image<?php echo count($value)==1||in_array($vv, $skuAttv)?' active':'';?>" data-id="<?php echo $vv;?>" title="<?php echo $info['attv'][$vv];?>">
							<div class="attv-image tcell">
								<img src="<?php echo $info['attvImage'][$vv];?>" alt="<?php echo $info['attv'][$vv];?>">
							</div>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<div class="clear"></div>
				</div>
				<?php } ?>
				<div class="item stock-content">
					<p class="title left"><?php echo distT('quantity');?></p>
					<div class="right quantity" data-stock="<?php echo $stock;?>">
						<button class="plus <?php echo $stock > 0 ? '' : 'disabled';?>" <?php echo $stock > 0 ? '' : 'disabled="disabled"';?>><span class="iconfont icon-jiahao1"></span></button>
						<input type="text" class="num" value="<?php echo $stock > 0 ? 1 : 0;?>" maxlength="4">
						<button class="minus disabled" disabled="disabled"><span class="iconfont icon-jianhao"></span></button>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="dialog-footer">
			<button class="btn btn-light-black add-to-cart left w50"><?php echo distT('add_to_cart');?></button>
			<button class="btn btn-black checkout-btn left w50"><?php echo distT('checkout');?></button>
		</div>
	</div>
</div>
<form action="<?php echo url('checkout');?>" id="checkout-form" method="post">
	<input type="hidden" name="id" value="0">
	<input type="hidden" name="quantity" value="1">
	<input type="hidden" name="shipping_address_id" value="0">
</form>
<script type="text/javascript">
$(function(){
	$('.product-image').slider();
	PRODUCT.init({
		spuId: <?php echo $spuId;?>,
		skuId: <?php echo $skuId;?>,
		sku: <?php echo json_encode($info['sku'], JSON_UNESCAPED_UNICODE);?>,
		skuMap: <?php echo json_encode($info['skuMap'], JSON_UNESCAPED_UNICODE);?>,
		filterMap: <?php echo json_encode($info['filterMap'], JSON_UNESCAPED_UNICODE);?>,
		name: '<?php echo addslashes($info['name']);?>',
		url: '<?php echo $info['url'];?>',
		image: '<?php echo $info['image_list'][0];?>',
		stock: <?php echo $stock;?>,
		price: '<?php echo $info['min_price_format'].($info['max_price']>$info['min_price']?' - '.$info['max_price_format']:'');?>',
		originalPrice: '<?php echo $info['original_price_format'];?>'
	});
});
</script>
<?php }?>