<?php $this->load('common/header');?>
<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<?php if (!empty($typeArr)) { ?>
		<div class="row-item">
			<input type="hidden" name="type_id" value="<?php echo $typeId;?>">
			<div class="btn-group" role="group">
				<button type="button" data-id="-1" class="btn <?php echo ($typeId >= 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
				<?php foreach ($typeArr as $key => $value) { ?>
				<button type="button" data-id="<?php echo $key;?>" class="btn <?php echo $typeId == $key ? 'btn-primary' : 'btn-default';?>"><?php echo $value;?></button>
				<?php } ?>
			</div>
		</div>
		<?php }?>
		<div class="col-md-12 pt10">
			<div class="form-group mt10 mr20">
				<label for="short_name">手机号:</label>
				<input type="text" class="form-control" name="mobile" value="<?php echo $mobile;?>" placeholder="手机号码">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">名称:</label>
				<input type="text" class="form-control" name="name" value="<?php echo $name;?>" placeholder="名称关键字">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">日期:</label>
				<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
				<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20">
        <tbody>
	        <tr>
	            <th class="col-md-1">ID</th>
	            <th class="col-md-1">头像</th>
	            <th class="col-md-1">名称</th>
	            <th class="col-md-1">浏览器</th>
	            <th class="col-md-1">操作系统</th>
	            <th class="col-md-1">备注</th>
	            <th class="col-md-1">IP</th>
	            <th class="col-md-1">时间</th>
	            <th class="col-md-4">设备详细</th>
	        </tr>
        	<?php if (empty($list)){ ?>
        	<tr>
        		<td colspan="9">
        			<div class="tc orange">暂无数据</div>
        		</td>
        	</tr>
        	<?php } else {?>
        	<?php foreach ($list as $key => $value) { ?>
        	<tr>
        		<td class="col-md-1"><?php echo $value['log_id'];?></td>
        		<td class="col-md-1">
        			<div class="avatar-hover">
        				<img src="<?php echo $value['avatar'];?>">
        			</div>
        		</td>
        		<td class="col-md-1"><?php echo $value['name'];?></td>
        		<td class="col-md-1"><?php echo $value['browser'];?></td>
        		<td class="col-md-1"><?php echo $value['system'];?></td>
        		<td class="col-md-1"><?php echo empty($value['remark']) ? $value['type_text'] : $value['remark'];?></td>
        		<td class="col-md-1"><?php echo $value['ip'];?></td>
        		<td class="col-md-1"><?php echo $value['create_at'];?></td>
        		<td class="col-md-4"><?php echo $value['agent'];?></td>
        	</tr>
        	<?php } ?>
        	<?php }?>
        </tbody>
    </table>
	<?php echo page($size, $total);?>
</div>
<?php $this->load('common/footer');?>