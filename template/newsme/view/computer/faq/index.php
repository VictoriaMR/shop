<?php $this->load('common/base_header');?>
<div class="faq-page mt12 mb20">
    <div class="layer bg-f">
        <?php if (empty($info) && empty($groupList)) {?><div class="empty-info">
            <img src="<?php echo siteUrl('image/common/oooops.png');?>">
            <p class="mt12 f16">No item matched. Please try with other options.</p>
        </div>
        <?php } else if (!empty($info)){?>
        <?php } else if (!empty($groupList)){?><div class="faq-search">
            <img src="<?php echo siteUrl('image/common/faq/faq-background.png');?>" alt="faq-search-img">
            <p class="faq-search-title">We’re Here to Help！</p>
            <form action="<?php echo url('faq');?>">
                <input name="search" placeholder="Popular Searches: Refund,Return,Shipping" autocomplete="off">
                <button type="submit"><i class="icon icon-search-white"></i></button>
            </form>
        </div>
        <div class="group-list">
            <ul class="f0">
                <?php foreach($groupList as $value){?><li<?php echo $value['group_id']==$gid?' class="active"':'';?> style="width: <?php echo 100/count($groupList);?>%">
                    <i class="icon-faq icon-<?php echo $value['icon'];?>"></i>
                    <p><?php echo $value['name'];?></p>
                </li>
                <?php }?>
            </ul>
        </div>
        <?php }?>
    </div>
</div>
<?php $this->load('common/base_footer');?>