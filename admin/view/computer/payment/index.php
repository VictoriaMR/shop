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
					<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status">
						<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td class="col-md-1">
					<div class="switch_botton" data-status="<?php echo $value['is_sandbox'];?>" data-type="is_sandbox">
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
				<form class="form-horizontal" role="form" method="post" action="#">
					<div class="form-group">
						<label for="secret_key" class="col-sm-3 control-label">App Key:</label>
						<div class="col-sm-9">
							<textarea class="form-control" rows="3" name="app_key" autocomplete="off" readonly="readonly"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="secret_key" class="col-sm-3 control-label">Secret Key:</label>
						<div class="col-sm-9">
							<textarea class="form-control" rows="3" name="secret_key" autocomplete="off" readonly="readonly"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="secret_key" class="col-sm-3 control-label">WebHook Key:</label>
						<div class="col-sm-9">
							<textarea class="form-control" rows="3" name="webhook_key" autocomplete="off" readonly="readonly"></textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="partEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">添加账号</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form" method="post" action="#">
					<input type="hidden" name="opn" value='editInfo'>
					<input type="hidden" name="payment_id" value="0">
					<div class="form-group">
						<label for="account" class="col-sm-2 control-label">账号:</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="2" name="name" autocomplete="off" placeholder="账户名称" required="required" maxlength="150"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="secret" class="col-sm-2 control-label">账号类型:</label>
						<div class="col-sm-8">
							<select class="form-control" name="type">
								<option>请选择账号类型</option>
								<?php foreach($typeList as $key => $value) {?>
								<option value="<?php echo $key;?>"><?php echo $value;?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="client_id" class="col-sm-2 control-label">App Key:</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="3" name="app_key" autocomplete="off" placeholder="公钥" required="required" maxlength="150"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="secret" class="col-sm-2 control-label">Secret Key:</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="3" name="secret_key" autocomplete="off" placeholder="私钥" required="required" maxlength="150"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="secret" class="col-sm-2 control-label">Webhook Key:</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="3" name="webhook_key" autocomplete="off" placeholder="网络钩子密钥" maxlength="150"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="is_sandbox" class="col-sm-2 control-label">沙盒:</label>
						<div class="col-sm-8">
							<label class="radio-inline">
								<input type="radio" name="is_sandbox" value="0"> 正式
							</label>
							<label class="radio-inline">
								<input type="radio" name="is_sandbox" value="1"> 测试
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="is_sandbox" class="col-sm-2 control-label">状态:</label>
						<div class="col-sm-8">
							<label class="radio-inline">
								<input type="radio" name="status" class="status" value="0"> 停用
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" class="status"  value="1"> 启用
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="remark" class="col-sm-2 control-label">备注:</label>
						<div class="col-sm-8">
							<textarea class="form-control" rows="2" name="remark" autocomplete="off" placeholder="内部使用备注" maxlength="64"></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary save">保存</button>
			</div>
		</div>
	</div>
</div>