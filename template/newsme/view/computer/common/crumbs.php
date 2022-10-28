<?php if (!empty($crumbs)){?><div class="layer bg-f">
    <ul class="crumbs">
        <li>
            <a href="<?php echo url();?>" title="Home">Home</a>
        </li>
        <?php foreach($crumbs as $value){?><li>
            <?php if (empty($value['url'])){?>
            <span title="<?php echo $value['name'];?>"><?php echo $value['name'];?></span>
            <?php }else{?>
            <a href="<?php echo $value['url'];?>" title="<?php echo $value['name'];?>"><?php echo $value['name'];?></a>
            <?php }?>
        </li>
    <?php }?></ul>
</div>

<?php }?>