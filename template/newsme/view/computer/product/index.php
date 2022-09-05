<?php $this->load('common/base_header');?>
<div class="spu-page mt12 mb20">
    <?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
    <div class="layer bg-f">
        <?php if (empty($info)){?><div class="empty-info">
            <img src="<?php echo siteUrl('image/common/oooops.png');?>">
            <p class="mt12 f16">No item matched. Please try with other options.</p>
        </div>
        <?php $this->load('product/recommend');?>
        <?php } else {?><div class="info-content">
            <div class="left img-content w40">
                <div class="relative big-image img">
                    <img data-src="<?php echo str_replace('/400', '/600', $info['image'][$info['attach_id']]);?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
                </div>
                <?php if (!empty($info['image'])){?><div class="small-image relative">
                    <ul class="image-list">
                    <?php $count=0; foreach ($info['image'] as $key=>$value){$count++;?><li<?php echo $count==1?' class="selected"':'';?>>
                            <div class="image-comtent">
                                <img data-src="<?php echo str_replace('/400', '/200', $value);?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
                            </div>
                        </li><?php }?>
                    </ul>
                    <?php if (count($info['image'])>5){?><div class="movement left-movement disabled">
                        <span class="iconfont icon-xiangzuo1"></span>
                    </div>
                    <div class="movement right-movement">
                        <span class="iconfont icon-xiangyou1"></span>
                    </div>
                    <?php }?>
                </div>
                <?php }?>
            </div>
            <div class="left attr-content w60">
                <p class="f24 f600 name mb20"><?php echo $info['name'];?></p>
                <div class="info mb10 f14">
                    <span class="stock">In Stock</span>
                    <span class="num"><?php echo $skuId?'SKU: '.$skuId:'SPU: '.$spuId;?></span>
                </div>
                <div class="price-content mb20">
                    <span class="price"><?php echo $info['min_price_format'];?></span>
                    <?php if ($info['show_price'] && $info['original_price'] > $info['min_price']){?>
                    <span class="original-price"><?php echo $info['original_price_format'];?></span>
                    <span class="discount"><?php echo sprintf('%.2f', ($info['original_price'] - $info['min_price'])/$info['original_price'])*100;?> OFF</span>
                    <?php }?>
                </div>
                <div class="attr-content mb20">
                    <?php $attvImageId = array_keys(array_filter($info['attvImage'])); foreach ($info['attrMap'] as $key=>$value){?><div class="attr-item" data-id="<?php echo $key;?>">
                        <div class="attr-name-content">
                            <span class="attr-name"><?php echo $info['attr'][$key];?></span>
                        </div><?php $attrImage = !empty(array_intersect($value, $attvImageId));?>
                        <ul class="attv-list<?php echo $attrImage?' attv-img':'';?>">
                            <?php foreach ($value as $attv){?><li title="<?php echo $info['attv'][$attv];?>" data-id="<?php echo $attv;?>">
                                <?php if (!empty($info['attvImage'][$attv])){?><img data-src="<?php echo str_replace('/400', '/200', $info['attvImage'][$attv]['url']);?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload"><?php }else{?><span><?php echo $info['attv'][$attv];?></span><?php }?>
                            </li><?php }?>
                        </ul>
                    </div><?php }?>
                </div>
                <div class="relative noselect">
                    <?php $stock=$skuId?$info['sku'][$skuId]['stock']:'99';?> 
                    <div class="qty-content left" data-stock="<?php echo $stock;?>">
                        <span class="f16 f600">Qty</span>
                        <span class="ml20 iconfont icon-jianhao disabled"></span>
                        <input type="text" name="qty" value="1">
                        <span class="iconfont icon-jiahao1<?php echo $stock<=1?' disabled':'';?>"></span>
                    </div>
                    <button class="btn left btn-like"><span class="iconfont icon-xihuan"></span></button>
                    <div class="btn-content right">
                        <button class="btn btn-black btn-add-cart">ADD TO BAG</button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php }?>
    </div>
</div>
<script type="text/javascript">
PRODUCT_INFO.init(<?php echo json_encode($info, JSON_UNESCAPED_UNICODE);?>,<?php echo $spuId;?>,<?php echo $skuId;?>);
</script>
<?php $this->load('common/base_footer');?>