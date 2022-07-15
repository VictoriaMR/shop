<div class="container-fluid" id="git-list">
    <table class="table table-hover mt20" id="data-list">
        <tbody>
            <tr>
                <th width="200">版本库</th>
                <th width="150">发布状态</th>
                <th width="350">发布时间<br>版本时间</th>
                <th width="300">版本号</th>
                <th width="300">版本信息</th>
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
            <tr>
                <td><?php echo $value['name'];?></td>
                <td><?php echo $value['status_text'];?></td>
                <td>
                    <span title="发布时间"><?php echo $value['release_time'] ?? '--';?></span>
                    <br>
                    <span title="版本时间"><?php echo $value['commit_time'] ?? '--';?></span>
                </td>
                <td><?php echo $value['commit'] ?? '--';?></td>
                <td><?php echo $value['info'] ?? '--';?></td>
                <td>
                    <a href="<?php echo url('git/gitLog', ['id'=>$value['name']]);?>" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;日志</a>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
</div>