<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="col-md-12 pt10">
			<div class="mr20 form-group mt10">
				<label for="contact">名称:</label>
				<input type="text" class="form-control" name="name" value="<?php echo $name;?>" placeholder="名称关键字" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">类型:</label>
				<select class="form-control" name="type">
					<option value="">请选择类型</option>
					<?php foreach ($typeList as $key => $value){?>
					<option <?php echo $key == $type ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">沙盒:</label>
				<select class="form-control" name="is_sandbox">
					<option value="">请选择沙盒</option>
					<?php foreach ($sandBoxList as $key => $value){?>
					<option <?php echo $isSandbox == $key ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
			<div class="form-group mt10 right">
				<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增支付账号</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">账号</th>
				<th class="col-md-1">账号类型</th>
				<th class="col-md-1">配置信息</th>
				<th class="col-md-1">状态</th>
				<th class="col-md-1">沙盒</th>
				<th class="col-md-1">备注</th>
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
			<tr data-id="<?php echo $value['payment_id'];?>">
				<td class="col-md-1"><?php echo $value['payment_id'];?></td>
				<td class="col-md-1"><?php echo $value['name'];?></td>
				<td class="col-md-1"><?php echo $value['type_name'];?></td>
				<td class="col-md-1">
					<span>*****</span>
					<span class="glyphicon glyphicon-eye-open"></span>
				</td>
				<td class="col-md-1">
					<div class="switch_botton" data-status="<?php echo $value['status'];?>">
						<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td class="col-md-1">
					<div class="switch_botton" data-status="<?php echo $value['is_sandbox'];?>">
						<div class="switch_status <?php echo $value['is_sandbox'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td class="col-md-1"><?php echo $value['remark'];?></td>
				<td class="col-md-1"><?php echo $value['add_time'];?></td>
				<td class="col-md-2">
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
<div class="modal fade" id="partView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				&times;
				</button>
				<h4 class="modal-title">配置信息</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="account" class="col-sm-2 control-label">账号名称:</label>
					<div class="col-sm-9">
						<input class="form-control" rows="3" name="account" autocomplete="off" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label for="merchant_id" class="col-sm-2 control-label">商户号:</label>
					<div class="col-sm-9">
						<input class="form-control" rows="3" name="merchant_id" autocomplete="off" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label for="secret_key" class="col-sm-2 control-label">Secret Key:</label>
					<div class="col-sm-9">
						<textarea class="form-control" rows="3" name="secret_key" autocomplete="off" readonly="readonly"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>