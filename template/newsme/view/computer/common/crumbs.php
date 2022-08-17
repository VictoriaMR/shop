<?php if (!empty($crumbs)){?>
<ul class="crumbs">
    <?php foreach($crumbs as $value){?>
    <li>
        <a href="<?php echo empty($value['url'])?'javascript:;':$value['url'];?>"><?php echo $value['name'];?></a>
    </li>
    <?php }?>
</ul>

<?php }?>