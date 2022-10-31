<?php $this->load('common/base_header');?>
<div class="faq-page mt12 mb20">
    <div class="layer bg-f pb20">
        <?php if (empty($info) && empty($groupList) && empty($keyword)) {?><div class="empty-info">
            <img src="<?php echo siteUrl('image/common/oooops.png');?>">
            <p class="mt12 f16">No item matched. Please try with other options.</p>
        </div>
        <?php } else if (!empty($info)){?><div class="faq-info">
            <?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
            <div class="left info-left w70">
                <p class="title"><?php echo $info['title'];?></p>
                <?php echo $info['content'];?>
            </div>
            <div class="left list-right w30">
                <form action="<?php echo url('faq');?>" class="search-form">
                    <input class="input" name="search" placeholder="Popular Searches: Refund,Return,Shipping" autocomplete="off">
                    <i class="icon-search"></i>
                </form>
                <div class="list-content">
                    <p class="f16 f500 mb4">Related Articles</p>
                    <ul class="f14">
                        <?php foreach($faqList as $value){?><li<?php echo $info['faq_id']==$value['faq_id']?' class="active"':'';?>>
                            <a href="<?php echo url($value['title'], ['f'=>$value['faq_id']]);?>" title="<?php echo $value['title'];?>"><span><?php echo $value['title'];?></span></a>
                        </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
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
                <?php foreach($groupList as $value){?><li<?php echo $value['group_id']==$gid?' class="active"':'';?> data-gid="<?php echo $value['group_id'];?>" style="width: <?php echo 100/count($groupList);?>%">
                    <i class="icon-faq icon-<?php echo $value['icon'];?>"></i>
                    <p><?php echo $value['name'];?></p>
                </li>
                <?php }?>
            </ul>
        </div>
        <div class="faq-list">
            <?php foreach ($faqList as $key=>$value){?><div class="item<?php echo $key==$gid?' active':'';?>" data-gid="<?php echo $key??'';?>">
                <ul>
                    <?php foreach($value as $item){?><li>
                        <a href="<?php echo url($item['title'], ['f'=>$item['faq_id']]);?>" title="<?php echo $item['title'];?>"><span><?php echo $item['title'];?></span></a>
                    </li>
                    <?php }?>
                </ul>
            </div>
            <?php }?>
        </div>
        <?php } elseif (!empty($keyword)){?><div class="search-result">
            <?php $this->load('common/crumbs', ['crumbs'=>$crumbs]);?>
            <div class="search-result-content relative">
                <form action="<?php echo url('faq');?>" class="search-form">
                    <input class="input" name="search" value="<?php echo $keyword;?>" placeholder="Popular Searches: Refund,Return,Shipping" autocomplete="off">
                    <i class="icon-search"></i>
                </form>
                <div class="tc mb20">
                    <p class="f26 f400 tc">Search Result</p>
                    <p class="f14">
                        <span><?php echo count($faqList);?></span>
                        <span>results for</span>
                        <span class="keyword">"<?php echo $keyword;?>"</span>
                    </p>
                </div>
                <div class="search-list">
                    <?php foreach($faqList as $value){?><a class="item" href="<?php echo url($value['title'], ['f'=>$value['faq_id']]);?>">
                        <p class="title"><?php echo $value['title_format'];?></p>
                        <div class="faq-ext">
                            <span class="icon-agree">
                                <img src="<?php echo siteUrl('image/common/faq/agree.png');?>">
                            </span>
                            <span class="visit"><?php echo $value['visit_total'];?></span>
                        </div>
                        <p class="content"><?php echo $value['content'];?></p>
                    </a>
                    <?php }?>
                </div>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<?php $this->load('common/base_footer');?>