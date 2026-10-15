<div class="purchase-list-page" id="purchase-list-page" data-url="<?php echo adminUrl('product/purchaseList');?>">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-cart-flatbed"></i>
			<span class="cur-page-title">采购产品列表</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 条</span>
		</div>
		<div class="header-actions">
			<a href="<?php echo adminUrl('product/purchaseShop');?>" class="btn btn-default btn-xs" title="供应商店铺">
				<i class="fa fa-store"></i> 店铺
			</a>
		</div>
	</div>

	<!-- 筛选表单（无重置按钮） -->
	<form action="<?php echo adminUrl('product/purchaseList');?>" method="get" class="search-card">
		<div class="filter-grid">
			<div class="filter-item">
				<input type="text" class="form-control" name="id" value="<?php echo $id > 0 ? (int)$id : '';?>" placeholder="采购ID" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="text" class="form-control" name="item_id" value="<?php echo htmlspecialchars((string)$item_id, ENT_QUOTES, 'UTF-8');?>" placeholder="产品源ID" autocomplete="off">
			</div>
			<div class="filter-item">
				<select class="form-control" name="channel_id">
					<option value="-1">全部渠道</option>
					<?php if (!empty($channelList)) {
						foreach ($channelList as $key => $value) { ?>
					<option value="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>" <?php echo ((string)$channel_id === (string)$key) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="filter-item">
				<select class="form-control" name="status">
					<option value="-1">全部状态</option>
					<?php if (!empty($statusList)) {
						foreach ($statusList as $key => $value) { ?>
					<option value="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>" <?php echo ((string)$status === (string)$key) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr((string)$stime, 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="开始日期">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr((string)$etime, 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="结束日期">
			</div>
		</div>
		<div class="filter-actions">
			<button class="btn btn-info btn-block" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 采购产品卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-inbox"></i>
				<p>暂无采购产品数据</p>
			</div>
		<?php } else {
			$statusNormal = purchase()->spu()->getConst('STATUS_NORMAL');
			$statusSet = purchase()->spu()->getConst('STATUS_SET');
			$statusSpu = purchase()->spu()->getConst('STATUS_SPU');
			foreach ($list as $value) {
				$pId = (int)$value['purchase_spu_id'];
				$curStatus = (int)$value['status'];
				$channelName = $channelList[$value['channel_id']] ?? '--';
				$statusLabel = $statusList[$curStatus] ?? $curStatus;
				$canUpload = in_array($curStatus, [$statusNormal, $statusSet]);
				$canConfig = in_array($curStatus, [$statusSet, $statusSpu]);
				$uploadTitle = ($curStatus == $statusSet) ? '上传源数据' : '更新源数据';
		?>
			<div class="purchase-card item" data-id="<?php echo $pId;?>" data-status="<?php echo $curStatus;?>">
				<div class="card-top">
					<div class="card-badges">
						<span class="id-badge">#<?php echo $pId;?></span>
						<span class="channel-badge"><?php echo htmlspecialchars((string)$channelName, ENT_QUOTES, 'UTF-8');?></span>
						<span class="status-badge status status-<?php echo $curStatus;?>"><?php echo htmlspecialchars((string)$statusLabel, ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="card-actions">
						<?php if ($canUpload) { ?>
						<a target="_blank" class="btn btn-info btn-xs icon-btn" href="<?php echo htmlspecialchars((string)$value['url'], ENT_QUOTES, 'UTF-8');?>" title="<?php echo $uploadTitle;?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-cloud-arrow-up"></i>
						</a>
						<?php } ?>
						<?php if ($canConfig) { ?>
						<a class="btn btn-primary btn-xs icon-btn" href="<?php echo adminUrl('product/operate', ['id' => $pId]);?>" title="配置产品" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-sliders"></i>
						</a>
						<?php } ?>
						<button type="button" class="btn btn-default btn-xs icon-btn btn-edit" title="修改状态" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-pen-to-square"></i>
						</button>
					</div>
				</div>

				<div class="card-title-row">
					<a href="<?php echo htmlspecialchars((string)$value['url'], ENT_QUOTES, 'UTF-8');?>" target="_blank" class="item-title e2">
						<?php echo htmlspecialchars((string)($value['name'] ?: '未获取标题 (源ID: ' . $value['item_id'] . ')'), ENT_QUOTES, 'UTF-8');?>
					</a>
				</div>

				<div class="card-meta-grid">
					<div class="meta-cell">
						<span class="m-label">源ID：</span>
						<span class="m-val"><?php echo htmlspecialchars((string)$value['item_id'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="meta-cell">
						<span class="m-label">价格 / 销量：</span>
						<span class="m-val">
							<strong class="price-val"><?php echo $value['price'] > 0 ? '￥' . htmlspecialchars((string)$value['price'], ENT_QUOTES, 'UTF-8') : '--';?></strong>
							<em>/</em>
							<span><?php echo htmlspecialchars((string)($value['sale_count'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
						</span>
					</div>
				</div>

				<?php if (!empty($value['shop_info'])) { ?>
				<div class="shop-info-bar">
					<i class="fa fa-store"></i>
					<div class="shop-info-html"><?php echo purchase()->shop()->shopInfo($value['shop_info']);?></div>
				</div>
				<?php } ?>

				<div class="card-bottom">
					<div class="creator-wrap">
						<?php echo $value['user_info'] ?: '<span class="text-muted">--</span>';?>
					</div>
					<div class="time-wrap">
						<div><i class="fa fa-clock"></i> <?php echo htmlspecialchars((string)$value['add_time'], ENT_QUOTES, 'UTF-8');?></div>
						<?php if (!empty($value['update_time'])) { ?>
						<div><i class="fa fa-rotate"></i> <?php echo htmlspecialchars((string)$value['update_time'], ENT_QUOTES, 'UTF-8');?></div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } } ?>
	</div>

	<?php if (!empty($list)) { ?>
	<div class="mt12">
		<?php echo page($size, $total);?>
	</div>
	<?php } ?>

	<!-- 状态修改弹窗 -->
	<div class="modal-mask edit-status-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">状态修改</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form class="edit-status-modal" onsubmit="return false;">
					<input type="hidden" name="id" value="0">
					<input type="hidden" name="opn" value="edit">
					<div class="form-group">
						<label class="form-label">操作状态</label>
						<select class="form-control" name="status">
							<option value="-1">请选择状态</option>
							<?php foreach ($statusList as $k => $v) { ?>
							<option value="<?php echo htmlspecialchars((string)$k, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block btn-save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">保存修改</button>
				</form>
			</div>
		</div>
	</div>
</div>
