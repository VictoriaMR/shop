<div class="container-fluid" id="category-list">
    <table class="table mt10" id="data-list">
        <tbody>
            <tr>
                <th width="50">ID</th>
                <th width="200">名称</th>
                <th width="100">状态</th>
                <th width="250">操作</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="8">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr class="item" data-id="<?php echo $value['cate_id'];?>">
                <td><?php echo $value['cate_id'];?></td>
                <td>
                    <span class="cate_name"><?php echo $value['name'];?></span>
                </td>
                <td>
                    <div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status">
                        <div class="switch_status <?php echo $value['status']?'on':'off';?>"></div>
                    </div>
                </td>
                <td>
                    <a class="btn btn-info btn-xs ml4" href="<?php echo adminUrl('category/attrUsed', ['cid'=>$value['cate_id']]);?>"><span class="glyphicon glyphicon-forward"></span>&nbsp;属性</a>
                    <a class="btn btn-default btn-xs ml4 modify" href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>"><span class="glyphicon glyphicon-align-left"></span>&nbsp;子类目</a>
                    <button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
                    <button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <p class="mb10 mt10">合计: <?php echo count($list);?>个类目</p>
</div>