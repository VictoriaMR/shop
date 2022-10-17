<div class="container-fluid" id="faq-page">
    <div class="form-group right">
        <button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增分组</button>
    </div>
    <table class="table table-hover mt20" id="data-list">
        <tbody>
            <tr>
                <th width="100">ID</th>
                <th width="500">名称</th>
                <th width="120">状态</th>
                <th>操作</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="10">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr data-id="<?php echo $value['group_id'];?>">
                <td><?php echo $value['group_id'];?></td>
                <td>
                    <span class="glyphicon glyphicon-globe" title="点击编辑多语言"></span>
                    <span><?php echo $value['name'];?></span>
                </td>
                <td>
                    <div class="switch_botton" data-status="<?php echo $value['status'];?>">
                        <div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
                    </div>
                </td>
                <td>
                    <button class="btn btn-primary btn-xs modify mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
                    <button class="btn btn-danger btn-xs delete mt2" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <?php echo page($size, $total);?>
</div>
<!-- 新增/编辑弹窗 -->
<div id="dealbox-info" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
    <form class="form-horizontal">
        <button type="button" class="close" aria-hidden="true">&times;</button>
        <div class="f24 dealbox-title">编辑分组</div>
        <input type="hidden" name="group_id" value="0">
        <input type="hidden" name="opn" value="editGroupInfo">
        <div class="input-group">
            <div class="input-group-addon"><span>名称</span>：</div>
            <input class="form-control" name="name" required="required" autocomplete="off" />
        </div>
        <div class="input-group">
            <div class="input-group-addon"><span>状态</span>：</div>
            <select class="form-control" name="status">
                <option value="">请选择状态</option>
                <option value="0">关闭</option>
                <option value="1">开启</option>
            </select>
        </div>
        <button type="botton" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
    </form>
    </div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
        <form class="form-horizontal">
            <button type="button" class="close" aria-hidden="true">&times;</button>
            <div class="f24 dealbox-title">多语言配置</div>
            <input type="hidden" name="group_id" value="0">
            <input type="hidden" name="name" value="">
            <input type="hidden" name="opn" value="editGroupLanguage">
            <table class="table table-bordered table-hover">
                <tbody></tbody>
            </table>
            <button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
        </form>
    </div>
</div>