<?php $this->load('common/base_header');?>
<div class="search-page mt12 mb20">
    <?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
    <?php if (!empty($param['keyword'])){?><div class="layer bg-f5 search-info">
        <p class="f24 f600"><?php echo iget('keyword', '');?></p>
    </div>
    <?php }?>
    <div class="layer0 bg-f product-container">
        <?php $this->load('product/list', ['list'=>$list, 'total'=>$total, 'param'=>$param, 'size'=>$size, 'empty'=>true, 'recommend'=>true]);?>
        <div class="clear"></div>
    </div>
</div>
<?php $this->load('common/base_footer');?>