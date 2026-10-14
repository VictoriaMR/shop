<div id="payment-used-page" class="payment-used-page" data-url="<?php echo adminUrl('payment/paymentUsed');?>">
	<!-- 顶部标题栏 + 新增按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-link"></i>
			<span>支付账号使用</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 条</span>
		</div>
		<button class="btn btn-success btn-sm" type="button" id="add-data-btn">
			<i class="fa fa-plus"></i> 新增配置
		</button>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form method="get" action="<?php echo adminUrl('payment/paymentUsed');?>" class="search-form">
			<?php
				$curSiteId = iget('site_id', -1);
				$curType = iget('type', -1);
				$curPaymentId = iget('payment_id', -1);
			?>
			<div class="filter-row">
				<div class="filter-item">
					<select class="form-control" name="site_id">
						<option value="-1">请选择站点</option>
						<?php if (!empty($siteList)) { foreach ($siteList as $key => $value) { ?>
						<option <?php if ($curSiteId == $key) { echo 'selected'; } ?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="filter-item">
					<select class="form-control" name="type">
						<option value="-1">支付类型</option>
						<?php if (!empty($typeList)) { foreach ($typeList as $key => $value) { ?>
						<option <?php if ($curType == $key) { echo 'selected'; } ?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item flex-1">
					<select class="form-control" name="payment_id">
						<option value="-1">请选择支付账号</option>
						<?php if (!empty($paymentList)) { foreach ($paymentList as $key => $value) { ?>
						<option <?php if ($curPaymentId == $key) { echo 'selected'; } ?> value="<?php echo $key;?>"><?php echo (int)$key . ' - ' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
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
				<p>暂无站点支付账号使用配置</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) { ?>
			<div class="used-card" data-id="<?php echo (int)$value['item_id'];?>">
				<div class="card-header">
					<div class="site-main">
						<i class="fa fa-globe site-icon"></i>
						<span class="site-name"><?php echo htmlspecialchars($siteList[$value['site_id']] ?? ('站点#' . $value['site_id']), ENT_QUOTES, 'UTF-8');?></span>
						<span class="type-tag"><?php echo htmlspecialchars($typeList[$value['type']] ?? ('类型#' . $value['type']), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<span class="id-badge">#<?php echo (int)$value['item_id'];?></span>
				</div>

				<div class="card-body">
					<div class="info-row">
						<span class="info-label">关联支付账号：</span>
						<span class="info-val account-highlight">
							<?php echo htmlspecialchars($paymentList[$value['payment_id']] ?? '未知账号', ENT_QUOTES, 'UTF-8');?>
							<small class="text-muted">(ID: <?php echo (int)$value['payment_id'];?>)</small>
						</span>
					</div>
					<div class="info-row">
						<span class="info-label">配置添加时间：</span>
						<span class="info-val"><?php echo $value['add_time'] ?: '-';?></span>
					</div>
				</div>

				<div class="card-footer">
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
				<h4 class="dealbox-title">新增账号使用</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form method="post" id="used-form">
					<input type="hidden" name="item_id" value="0">
					<input type="hidden" name="opn" value="editUsedInfo">
					<div class="form-group">
						<label class="form-label">选择站点 <span class="required">*</span></label>
						<select class="form-control" name="site_id">
							<option value="0">请选择站点</option>
							<?php if (!empty($siteList)) { foreach ($siteList as $key => $value) { ?>
							<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">支付类型 <span class="required">*</span></label>
						<select class="form-control" name="type">
							<option value="0">请选择支付类型</option>
							<?php if (!empty($typeList)) { foreach ($typeList as $key => $value) { ?>
							<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">支付账号 <span class="required">*</span></label>
						<select class="form-control" name="payment_id">
							<option value="0">请选择支付账号</option>
							<?php if (!empty($paymentList)) { foreach ($paymentList as $key => $value) { ?>
							<option value="<?php echo $key;?>"><?php echo (int)$key . ' - ' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save">保 存</button>
				</form>
			</div>
		</div>
	</div>
</div>
