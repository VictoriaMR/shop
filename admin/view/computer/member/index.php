<?php $this->load('common/header');?>
<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="row-item">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<div class="btn-group" role="group">
				<button type="button" data-id="-1" class="btn <?php echo ($status == 1 || $status == 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
				<button type="button" data-id="0" class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default';?>">未启用</button>
				<button type="button" data-id="1" class="btn <?php echo $status == 1 ? 'btn-primary' : 'btn-default';?>">已启用</button>
			</div>
		</div>
		<div class="col-md-12 pt10">
			<div class="form-group mt10 mr20">
				<label for="short_name">手机号:</label>
				<input type="text" class="form-control" name="phone" value="<?php echo $phone;?>" placeholder="手机号码" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">名称:</label>
				<input type="text" class="form-control" name="name" value="<?php echo $name;?>" placeholder="名称关键字" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">日期:</label>
				<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
				<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
			<div class="form-group mt10 right">
				<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增管理员</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20" id="data-list">
        <tbody>
	        <tr>
	            <th class="col-md-1">ID</th>
	            <th class="col-md-1">头像</th>
	            <th class="col-md-1">名称</th>
	            <th class="col-md-1">昵称</th>
	            <th class="col-md-1">手机</th>
	            <th class="col-md-1">状态</th>
	            <th class="col-md-1">邮箱</th>
	            <th class="col-md-1">盐值</th>
	            <th class="col-md-1">添加时间</th>
	            <th class="col-md-2">操作</th>
	        </tr>
        	<?php if (empty($list)){ ?>
        	<tr>
        		<td colspan="10">
        			<div class="tc orange">暂无数据</div>
        		</td>
        	</tr>
        	<?php } else {?>
        	<?php foreach ($list as $key => $value) { ?>
        	<tr data-id="<?php echo $value['mem_id'];?>">
        		<td class="col-md-1"><?php echo $value['mem_id'];?></td>
        		<td class="col-md-1">
        			<div class="avatar-hover">
        				<img src="<?php echo $value['avatar'];?>">
        			</div>
        		</td>
        		<td class="col-md-1"><?php echo $value['name'];?></td>
        		<td class="col-md-1"><?php echo $value['nickname'];?></td>
        		<td class="col-md-1"><?php echo $value['mobile'];?></td>
        		<td class="col-md-1">
        			<div class="switch_botton" data-status="<?php echo $value['status'];?>">
                        <div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
                    </div>
        		</td>
        		<td class="col-md-1"><?php echo $value['email'];?></td>
        		<td class="col-md-1"><?php echo $value['salt'];?></td>
        		<td class="col-md-1"><?php echo $value['create_at'];?></td>
        		<td class="col-md-2">
        			<button class="btn btn-primary btn-sm modify mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
                    <button class="btn btn-danger btn-sm delete mt2" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
        		</td>
        	</tr>
        	<?php } ?>
        	<?php }?>
        </tbody>
    </table>
	<?php echo page($size, $total);?>
</div>
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	    	<input type="hidden" name="mem_id" class="no_show" value="0">
	    	<input type="hidden" class="no_replace" name="opn" value="editInfo">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">新增管理员</div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>名称</span></div>
	            <input type="text" class="form-control" name="name" required="required" maxlength="30" autocomplete="off">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>昵称</span></div>
	            <input type="text" class="form-control" name="nickname" required="required" maxlength="30" autocomplete="off">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>手机号码</span></div>
	            <input type="text" class="form-control" name="mobile" required="required" maxlength="11" autocomplete="off">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>邮箱</span></div>
	            <input type="text" class="form-control" name="email" required="required" maxlength="30" autocomplete="off">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>状态</span></div>
	            <div class="switch_botton ml20" data-status="0">
                    <div class="switch_status off"></div>
                </div>
	            <input type="hidden" name="status" value="0">
	        </div>
	        <div class="input-group hidden">
	            <div class="input-group-addon"><span>盐值</span></div>
	            <input type="text" class="form-control" readonly="readonly" name="salt">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>密码</span></div>
	            <input type="password" class="form-control" name="password" maxlength="30" autocomplete="off">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>重复密码</span></div>
	            <input type="password" class="form-control" name="repassword" maxlength="30" autocomplete="off">
	        </div>
	        <button type="button" class="btn btn-primary btn-lg btn-block save">确认</button>
	    </form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	MEMBERLIST.init();
});
</script>
<?php $this->load('common/footer');?>