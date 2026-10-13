<div class="container-fluid" id="payment-page">
	<form action="<?php echo adminUrl('payment/index');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="type">
						<option value="">全部支付类型</option>
						<?php foreach ($typeList as $key => $value){?>
						<option <?php echo (string)$key === (string)$type ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
						<?php }?>
					</select>
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="is_sandbox">
						<option value="-1">全部运行环境</option>
						<?php foreach ($sandBoxList as $key => $value){?>
						<option <?php echo (string)$isSandbox === (string)$key ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?>环境</option>
						<?php }?>
					</select>
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name ?? '');?>" placeholder="账号名称 / 标识关键字" autocomplete="off">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
				<div class="form-group right" style="margin-bottom: 10px;">
					<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增支付账号</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="110">账号类型</th>
				<th width="240">账号名称 / 备注</th>
				<th width="110">运行环境</th>
				<th width="90">状态</th>
				<th width="90">沙盒切换</th>
				<th width="110">配置秘钥</th>
				<th width="170">添加时间</th>
				<th width="130">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="9">
					<div class="tc orange">
						<i class="glyphicon glyphicon-info-sign"></i> 暂无支付账号数据
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $key => $value) { 
				$rowInfo = [
					'payment_id' => $value['payment_id'],
					'name'       => $value['name'],
					'type'       => $value['type'],
					'is_sandbox' => $value['is_sandbox'],
					'status'     => $value['status'],
					'remark'     => $value['remark'],
				];
			?>
			<tr data-id="<?php echo $value['payment_id'];?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<td><strong><?php echo $value['payment_id'];?></strong></td>
				<td><?php echo htmlspecialchars($value['type_name'] ?? '-');?></td>
				<td>
					<div><strong><?php echo htmlspecialchars($value['name']);?></strong></div>
					<?php if (!empty($value['remark'])){ ?>
					<div class="text-muted f12"><?php echo htmlspecialchars($value['remark']);?></div>
					<?php } ?>
				</td>
				<td>
					<?php if ($value['is_sandbox'] == 0){ ?>
					<span class="label label-success" style="font-weight: normal; padding: 3px 8px;">正式生产</span>
					<?php } else { ?>
					<span class="label label-warning" style="font-weight: normal; padding: 3px 8px;">沙盒测试</span>
					<?php } ?>
				</td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status" title="点击切换启用/停用">
						<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['is_sandbox'];?>" data-type="is_sandbox" title="点击切换正式/测试环境">
						<div class="switch_status <?php echo $value['is_sandbox'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td>
					<button class="btn btn-default btn-xs view-keys" type="button"><i class="glyphicon glyphicon-eye-open"></i> 查看秘钥</button>
				</td>
				<td>
					<span class="text-muted"><i class="glyphicon glyphicon-time f12"></i> <?php echo !empty($value['add_time']) ? $value['add_time'] : '-';?></span>
				</td>
				<td>
					<button class="btn btn-primary btn-xs modify" type="button"><i class="glyphicon glyphicon-edit"></i> 编辑</button>
					<button class="btn btn-danger btn-xs delete" type="button" style="margin-left: 4px;"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12">
			<?php echo page($size, $total);?>
		</div>
	</div>
</div>

<!-- 添加/编辑支付账号弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 480px;">
		<form class="form-horizontal">
			<input type="hidden" name="opn" value="editInfo">
			<input type="hidden" name="payment_id" class="no_show" value="0">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">添加支付账号</div>
			
			<div class="input-group">
				<span class="input-group-addon"><span>账号名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="账户标识 (如邮箱账号或标识)" autocomplete="off">
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>账号类型</span></span>
				<select class="form-control" name="type" required="required">
					<option value="">请选择账号类型</option>
					<?php foreach($typeList as $key => $value) {?>
					<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>App Key</span></span>
				<input type="text" class="form-control" name="app_key" placeholder="公钥 / Client ID / Publishable Key" required="required" maxlength="150" autocomplete="off">
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>Secret Key</span></span>
				<input type="text" class="form-control" name="secret_key" placeholder="私钥 / Secret Key" required="required" maxlength="150" autocomplete="off">
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>Webhook</span></span>
				<input type="text" class="form-control" name="webhook_key" placeholder="Webhook 签名秘钥 (可选)" maxlength="150" autocomplete="off">
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>运行环境</span></span>
				<select class="form-control" name="is_sandbox">
					<option value="0">正式生产环境</option>
					<option value="1">沙盒测试环境</option>
				</select>
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>账号状态</span></span>
				<select class="form-control" name="status">
					<option value="1">启用</option>
					<option value="0">停用</option>
				</select>
			</div>

			<div class="input-group">
				<span class="input-group-addon"><span>内部备注</span></span>
				<input type="text" class="form-control" name="remark" maxlength="64" placeholder="内部管理备注信息 (可选)" autocomplete="off">
			</div>

			<div style="margin-top: 20px;">
				<button type="button" class="btn btn-primary btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>

<!-- 查看配置秘钥弹窗 -->
<div id="dealbox-view" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 500px;">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">配置信息秘钥</div>
		
		<!-- 头部账号信息单独一行 -->
		<div class="account-header-info" style="margin-bottom: 16px; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
			<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-right: 10px;">
				<span class="text-muted" style="font-size: 13px;">账号名称：</span>
				<strong class="view-account-name" style="color: #0f172a; font-size: 14px;">-</strong>
			</div>
			<div style="flex-shrink: 0;">
				<span class="label label-info view-account-type" style="font-size: 11px; padding: 3px 8px; margin-right: 4px;">-</span>
				<span class="label view-account-env" style="font-size: 11px; padding: 3px 8px;">-</span>
			</div>
		</div>
		
		<div class="form-group" style="margin-bottom: 14px;">
			<label style="font-weight: 600; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
				<span>App Key / Client ID:</span>
				<button type="button" class="btn btn-default btn-xs copy-btn" data-target="app_key"><i class="glyphicon glyphicon-copy"></i> 复制</button>
			</label>
			<textarea class="form-control" rows="2" name="app_key" readonly="readonly" style="background: #f8fafc; font-family: monospace; font-size: 12px; resize: none;"></textarea>
		</div>

		<div class="form-group" style="margin-bottom: 14px;">
			<label style="font-weight: 600; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
				<span>Secret Key / 私钥:</span>
				<button type="button" class="btn btn-default btn-xs copy-btn" data-target="secret_key"><i class="glyphicon glyphicon-copy"></i> 复制</button>
			</label>
			<textarea class="form-control" rows="2" name="secret_key" readonly="readonly" style="background: #f8fafc; font-family: monospace; font-size: 12px; resize: none;"></textarea>
		</div>

		<div class="form-group" style="margin-bottom: 14px;">
			<label style="font-weight: 600; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
				<span>Webhook Key:</span>
				<button type="button" class="btn btn-default btn-xs copy-btn" data-target="webhook_key"><i class="glyphicon glyphicon-copy"></i> 复制</button>
			</label>
			<textarea class="form-control" rows="2" name="webhook_key" readonly="readonly" style="background: #f8fafc; font-family: monospace; font-size: 12px; resize: none;"></textarea>
		</div>

		<div style="margin-top: 20px; text-align: right;">
			<button type="button" class="btn btn-default close-btn">关闭</button>
		</div>
	</div>
</div>