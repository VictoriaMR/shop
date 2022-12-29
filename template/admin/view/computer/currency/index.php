<div class="container-fluid">
    <div style="text-align: right;">
        <button class="btn btn-success add"><span class="glyphicon glyphicon-plus"></span>&nbsp;新增货币</button>
        <button class="btn btn-primary update"><span class="glyphicon glyphicon-refresh"></span>&nbsp;更新汇率</button>
    </div>
    <table class="table table-hover mt10">
        <tbody>
            <tr>
                <th>货币代码</th>
                <th>名称</th>
                <th>符号</th>
                <th>汇率</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="8">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr class="item" data-id="<?php echo $value['code'];?>">
                <td><?php echo $value['code'];?></td>
                <td><?php echo $value['name'];?></td>
                <td><?php echo $value['symbol'];?></td>
                <td><?php echo $value['rate'];?></td>
                <td><?php echo $value['update_time'];?></td>
                <td>
                    <button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
                    <button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <p>合计<?php echo count($list);?>个货币</p>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
        <form class="form-horizontal">
            <button type="button" class="close" aria-hidden="true">&times;</button>
            <div class="f24 dealbox-title">货币管理</div>
            <input type="hidden" name="id" value="">
            <input type="hidden" name="opn" value="editCurrencyInfo">
            <div class="input-group">
                <div class="input-group-addon"><span>货币：</span></div>
                <input type="text" class="form-control" name="code" autocomplete="off">
            </div>
            <div class="input-group">
                <div class="input-group-addon"><span>名称：</span></div>
                <input type="text" class="form-control" name="name" autocomplete="off">
            </div>
            <div class="input-group">
                <div class="input-group-addon"><span>符号：</span></div>
                <input type="text" class="form-control" name="symbol" autocomplete="off">
            </div>
            <div class="input-group">
                <div class="input-group-addon"><span>汇率：</span></div>
                <input type="text" class="form-control" name="rate" autocomplete="off">
            </div>
            <button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
        </form>
    </div>
</div>