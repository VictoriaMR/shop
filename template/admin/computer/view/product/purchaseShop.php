<div class="container-fluid" id="purchase-list-page">
    <form action="<?php echo adminUrl('product/purchaseShop');?>" class="form-inline" method="get">
        <div class="form-group">
            <input type="text" class="form-control" name="unique_id" value="<?php echo $unique_id;?>" placeholder="店铺源ID" autocomplete="off">
        </div>
        <div class="form-group">
            <select class="form-control" name="channel_id">
                <option value="-1">请选择渠道</option>
                <?php if (!empty($channelList)) {
                    foreach ($channelList as $key => $value) {?>
                <option <?php if ($channel_id==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php } }?>
            </select>
        </div>
        <div class="form-group">
            <button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
        </div>
        <div class="clear"></div>
    </form>
    <table class="table mt6" id="data-list">
        <tbody>
            <tr>
                <th width="30">ID</th>
                <th width="30">渠道</th>
                <th width="60">源ID</th>
                <th width="150">名称</th>
                <th width="50">产品数量</th>
                <th width="100">店铺信息</th>
                <th width="80">添加时间</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="7">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr>
                <td><?php echo $value['purchase_shop_id'];?></td>
                <td><?php echo $channelList[$value['channel_id']]??'--';?></td>
                <td><?php echo $value['unique_id'];?></td>
                <td><span class="glyphicon glyphicon-link"></span> <a href="https://<?php echo $value['url'];?>" target="_blank"><?php echo $value['name']?:'--';?></a></td>
                <td><?php echo $value['product_count']?:'--';?></td>
                <td><?php echo purchase()->shop()->shopInfo($value, false);?></td>
                <td>
                    <span title="添加时间"><?php echo $value['add_time'];?></span>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <?php echo page($size, $total);?>
</div>