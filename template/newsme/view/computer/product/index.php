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
                <div class="relative img">
                    <img data-src="<?php echo str_replace('/400', '/600', $info['image'][0]['url']);?>" src="<?php echo siteUrl('image/common/noimg.svg');?>" class="lazyload">
                </div>
            </div>
            <div class="left attr-content w60">
                <p class="f24 f600"><?php echo $info['name'];?></p>
                <div class="price-content">
                    <span class="price"><?php echo $info['min_price_format'];?></span>
                    <?php if ($info['original_price'] > $info['min_price']){?>
                    <span class="original-price"><?php echo $info['original_price_format'];?></span>
                    <span class="discount"><?php echo sprintf('%.2f', ($info['original_price'] - $info['min_price'])/$info['original_price']*100);?> off</span>
                    <?php }?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php }?>
    </div>
</div>
<?php $this->load('common/base_footer');?>