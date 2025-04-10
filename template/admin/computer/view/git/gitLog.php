<style type="text/css">
.green{color: green;}
.gray{color: gray;}
</style>
<div class="container-fluid">
    <form action="<?php echo adminUrl('git/gitLog');?>" class="form-inline">
        <div class="row-item">
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <div class="btn-group" role="group">
                <button type="button" data-id="" class="btn <?php echo $id ? 'btn-default' : 'btn-primary';?>">全部</button>
                <?php foreach ($git_library as $value){?>
                <button type="button" data-id="<?php echo $value;?>" class="btn <?php echo $id == $value ? 'btn-primary' : 'btn-default';?>"><?php echo $value;?></button>
                <?php }?>
            </div>
            <?php if ($id){?>
            <button id="update-git" type="button" class="btn btn-success right" data-id="<?php echo $id;?>"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;更新当前库</button>
            <?php }?>
        </div>
    </form>
    <table class="table table-hover mt20" id="data-list">
        <tbody>
            <tr>
                <th width="80">ID</th>
                <th width="150">版本库</th>
                <th width="100">状态</th>
                <th width="150">作者</th>
                <th width="400">版本号</th>
                <th width="150">版本/发布时间</th>
                <th width="400">发布信息</th>
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
            <tr data-id="<?php echo $value['git_id'];?>">
                <td><?php echo $value['git_id'];?></td>
                <td><?php echo $value['library'];?></td>
                <td><?php echo $value['status']?'<span class="green">已发布</span>':'<span class="gray">未发布</span>';?></td>
                <td><?php echo $value['author'];?></td>
                <td><?php echo $value['commit'];?></td>
                <td>
                    <span title="版本时间"><?php echo $value['commit_time'];?></span>
                    <br>
                    <span title="发布时间"><?php echo $value['release_time']??'--';?></span>
                </td>
                <td><?php echo $value['info'];?></td>
                <td>
                    <?php if(!$value['status']){?>
                    <button class="btn btn-primary btn-xs release-btn"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;发布</button>
                    <?php }?>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <?php echo page($size, $total);?>
</div>