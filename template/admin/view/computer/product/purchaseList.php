<div class="container-fluid" id="purchase-list-page">
    <form action="<?php echo adminUrl('product/purchaseList');?>" class="form-inline" method="get">
        <div class="form-group">
            <input type="text" class="form-control" name="item_id" value="<?php echo $item_id;?>" placeholder="产品源ID" autocomplete="off">
        </div>
        <div class="form-group">
            <select class="form-control" name="purchase_channel_id">
                <option value="-1">请选择渠道</option>
                <?php if (!empty($channelList)) {
                    foreach ($channelList as $key => $value) {?>
                <option <?php if ($purchase_channel_id==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php } }?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control" name="status">
                <option value="-1">请选择状态</option>
                <?php if (!empty($statusList)) {
                    foreach ($statusList as $key => $value) {?>
                <option <?php if ($status==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php } }?>
            </select>
        </div>
        <div class="form-group">
            <input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
            <input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
        </div>
        <div class="form-group">
            <button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
        </div>
        <div class="clear"></div>
    </form>
    <table class="table mt10" id="data-list">
        <tbody>
            <tr>
                <th width="50">ID</th>
                <th width="50">渠道</th>
                <th width="100">源ID</th>
                <th width="50">状态</th>
                <th width="120">店铺信息</th>
                <th width="60">添加人</th>
                <th width="80">添加时间</th>
                <th width="100">操作</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="8">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr>
                <td><?php echo $value['purchase_product_id'];?></td>
                <td><?php echo $channelList[$value['purchase_channel_id']]??'--';?></td>
                <td><?php echo $value['item_id'];?></td>
                <td class="status status-<?php echo $value['status'];?>"><?php echo $statusList[$value['status']] ?? $value['status'];?></td>
                <td><?php echo purchase()->shop()->shopInfo($value['shop_info']);?></td>
                <td><?php echo $value['user_info'];?></td>
                <td><?php echo $value['add_time'];?></td>
                <td>
                    <?php if (in_array($value['status'], [0, 1])){?>
                    <?php if ($value['status'] == 0) {?>
                    <button class="btn btn-primary btn-xs">设置</button>
                    <?php }?>
                    <a target="_blank" class="btn btn-info btn-xs" href="<?php echo purchase()->product()->url($value['purchase_channel_id'], $value['item_id']);?>"><?php echo $value['status']==0?'上传':'更新';?></a>
                    <?php if ($value['status'] == 1) {?>
                    <a class="btn btn-primary btn-xs" href="<?php echo adminUrl('product/operate', ['id'=>$value['purchase_product_id']]);?>">配置</a>
                    <?php }?>
                    <?php } else {?>
                    <?php }?>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <?php echo page($size, $total);?>
</div>