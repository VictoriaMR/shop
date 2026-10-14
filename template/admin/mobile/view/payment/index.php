<div id="payment-index-page" class="payment-page" data-url="<?php echo adminUrl('payment/index');?>">
	<!-- 顶部标题栏 + 新增按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-credit-card"></i>
			<span>支付账号管理</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 个</span>
		</div>
		<button class="btn btn-success btn-sm" type="button" id="add-data-btn">
			<i class="fa fa-plus"></i> 新增账号
		</button>
	</div>

	<!-- 筛选栏 -->
	<div class="filter-box">
		<form method="get" action="<?php echo adminUrl('payment/index');?>" class="search-form">
			<div class="filter-row">
				<div class="filter-item">
					<select class="form-control" name="type">
						<option value="0">所有类型</option>
						<?php if (!empty($typeList)) { foreach ($typeList as $key => $value) { ?>
						<option <?php if ($type == $key) { echo 'selected'; } ?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="filter-item">
					<select class="form-control" name="is_sandbox">
						<option value="-1">所有环境</option>
						<?php if (!empty($sandBoxList)) { foreach ($sandBoxList as $key => $value) { ?>
						<option <?php if ($isSandbox == $key && $isSandbox !== '') { echo 'selected'; } ?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');?>" placeholder="输入账号名称搜索..." autocomplete="off">
				</div>
				<button type="submit" class="btn btn-info search-submit-btn"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 列表区 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无匹配的支付账号数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) { ?>
			<div class="payment-card" data-id="<?php echo (int)$value['payment_id'];?>" data-info="<?php echo htmlspecialchars(json_encode($value, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<div class="card-header">
					<div class="account-main">
						<span class="account-name"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></span>
						<span class="type-tag"><?php echo htmlspecialchars($value['type_name'] ?? ($typeList[$value['type']] ?? '-'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<span class="id-badge">#<?php echo (int)$value['payment_id'];?></span>
				</div>

				<div class="card-body">
					<div class="switch-row">
						<div class="switch-group">
							<span class="switch-label">启用状态</span>
							<div class="switch_botton" data-status="<?php echo (int)$value['status'];?>" data-type="status">
								<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
							</div>
						</div>
						<div class="switch-group">
							<span class="switch-label">沙盒环境</span>
							<div class="switch_botton" data-status="<?php echo (int)$value['is_sandbox'];?>" data-type="is_sandbox">
								<div class="switch_status <?php echo $value['is_sandbox'] == 1 ? 'on' : 'off';?>"></div>
							</div>
						</div>
					</div>
					<div class="meta-list">
						<div class="meta-item">
							<span class="meta-label">备注说明：</span>
							<span class="meta-val"><?php echo $value['remark'] !== '' ? htmlspecialchars($value['remark'], ENT_QUOTES, 'UTF-8') : '-';?></span>
						</div>
						<div class="meta-item">
							<span class="meta-label">添加时间：</span>
							<span class="meta-val"><?php echo $value['add_time'] ?: '-';?></span>
						</div>
					</div>
				</div>

				<div class="card-footer">
					<button class="btn btn-info btn-xs view-keys" type="button"><i class="fa fa-key"></i> 查看秘钥</button>
					<button class="btn btn-primary btn-xs modify" type="button"><i class="fa fa-pen-to-square"></i> 编辑</button>
					<button class="btn btn-danger btn-xs delete" type="button"><i class="fa fa-trash-can"></i> 删除</button>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size, $total);?>

	<!-- 新增/编辑弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">编辑支付账号</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form method="post" id="payment-form">
					<input type="hidden" name="payment_id" value="0">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group">
						<label class="form-label">账户名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" placeholder="请输入账户名称" maxlength="32" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">支付类型 <span class="required">*</span></label>
						<select name="type" class="form-control">
							<?php if (!empty($typeList)) { foreach ($typeList as $key => $value) { ?>
							<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">AppKey <span class="required">*</span></label>
						<textarea class="form-control" name="app_key" rows="2" placeholder="请输入 AppKey"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">SecretKey <span class="required">*</span></label>
						<textarea class="form-control" name="secret_key" rows="3" placeholder="请输入 SecretKey"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">WebhookKey</label>
						<textarea class="form-control" name="webhook_key" rows="2" placeholder="选填，请输入 WebhookKey"></textarea>
					</div>
					<div class="form-row-inline">
						<div class="form-group half">
							<label class="form-label">是否沙盒</label>
							<div class="radio-group">
								<label class="radio-item"><input type="radio" name="is_sandbox" value="0" checked> 正式</label>
								<label class="radio-item"><input type="radio" name="is_sandbox" value="1"> 沙盒</label>
							</div>
						</div>
						<div class="form-group half">
							<label class="form-label">启用状态</label>
							<div class="radio-group">
								<label class="radio-item"><input type="radio" name="status" value="0"> 停用</label>
								<label class="radio-item"><input type="radio" name="status" value="1" checked> 启用</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="form-label">备注说明</label>
						<input type="text" class="form-control" name="remark" placeholder="选填，备注信息" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save">保 存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 查看秘钥弹窗 -->
	<div id="dealbox-view" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-key"></i> 查看账号秘钥</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="key-meta-bar">
					<div class="key-meta-item">
						<span class="meta-label">账户名称：</span>
						<strong class="view-account-name">-</strong>
					</div>
					<div class="key-meta-tags">
						<span class="type-tag view-account-type">-</span>
						<span class="env-tag view-account-env">-</span>
					</div>
				</div>
				<div class="key-field-item">
					<div class="key-field-header">
						<span class="key-name">AppKey</span>
						<button type="button" class="btn btn-default btn-xs copy-btn" data-target="app_key"><i class="fa fa-copy"></i> 复制</button>
					</div>
					<textarea class="form-control key-textarea" name="app_key" rows="2" readonly></textarea>
				</div>
				<div class="key-field-item">
					<div class="key-field-header">
						<span class="key-name">SecretKey</span>
						<button type="button" class="btn btn-default btn-xs copy-btn" data-target="secret_key"><i class="fa fa-copy"></i> 复制</button>
					</div>
					<textarea class="form-control key-textarea" name="secret_key" rows="3" readonly></textarea>
				</div>
				<div class="key-field-item">
					<div class="key-field-header">
						<span class="key-name">WebhookKey</span>
						<button type="button" class="btn btn-default btn-xs copy-btn" data-target="webhook_key"><i class="fa fa-copy"></i> 复制</button>
					</div>
					<textarea class="form-control key-textarea" name="webhook_key" rows="2" readonly></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
