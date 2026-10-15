<div class="purchase-shop-page" id="purchase-shop-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-store"></i>
			<span class="cur-page-title">供应商店铺</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 家</span>
		</div>
		<div class="header-actions">
			<a href="<?php echo adminUrl('product/purchaseList');?>" class="btn btn-default btn-xs" title="采购产品列表">
				<i class="fa fa-cart-flatbed"></i> 采购池
			</a>
		</div>
	</div>

	<!-- 筛选表单（无重置按钮） -->
	<form action="<?php echo adminUrl('product/purchaseShop');?>" method="get" class="search-card">
		<div class="filter-grid">
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
				<input type="text" class="form-control" name="unique_id" value="<?php echo htmlspecialchars((string)$unique_id, ENT_QUOTES, 'UTF-8');?>" placeholder="店铺源ID" autocomplete="off">
			</div>
			<div class="filter-item filter-full">
				<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');?>" placeholder="店铺名称关键字" autocomplete="off">
			</div>
		</div>
		<div class="filter-actions">
			<button class="btn btn-info btn-block" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 供应商店铺卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-store-slash"></i>
				<p>暂无供应商店铺数据</p>
			</div>
		<?php } else {
			foreach ($list as $value) {
				$shopId = (int)$value['purchase_shop_id'];
				$channelName = $channelList[$value['channel_id']] ?? '--';
				$shopName = $value['name'] ?: '--';
				$shopUrl = !empty($value['url']) ? 'https://' . ltrim($value['url'], '/') : '';
				$pCount = (int)($value['product_count'] ?? 0);
				$shopMetricsHtml = purchase()->shop()->shopInfo($value, false);
		?>
			<div class="shop-card item"
				data-id="<?php echo $shopId;?>"
				data-name="<?php echo htmlspecialchars((string)$shopName, ENT_QUOTES, 'UTF-8');?>"
				data-channel="<?php echo htmlspecialchars((string)$channelName, ENT_QUOTES, 'UTF-8');?>"
				data-unique="<?php echo htmlspecialchars((string)$value['unique_id'], ENT_QUOTES, 'UTF-8');?>"
				data-count="<?php echo $pCount;?>"
				data-url="<?php echo htmlspecialchars((string)$shopUrl, ENT_QUOTES, 'UTF-8');?>"
				data-time="<?php echo htmlspecialchars((string)$value['add_time'], ENT_QUOTES, 'UTF-8');?>">
				<div class="card-top">
					<div class="shop-left">
						<div class="shop-icon-box">
							<i class="fa fa-store"></i>
						</div>
						<div class="shop-titles">
							<div class="shop-badge-line">
								<span class="id-badge">#<?php echo $shopId;?></span>
								<span class="channel-badge"><?php echo htmlspecialchars((string)$channelName, ENT_QUOTES, 'UTF-8');?></span>
								<span class="count-badge"><i class="fa fa-box"></i> <?php echo $pCount;?> 件</span>
							</div>
							<div class="shop-name-row">
								<?php if ($shopUrl) { ?>
									<a href="<?php echo htmlspecialchars((string)$shopUrl, ENT_QUOTES, 'UTF-8');?>" target="_blank" class="shop-name e1"><?php echo htmlspecialchars((string)$shopName, ENT_QUOTES, 'UTF-8');?></a>
								<?php } else { ?>
									<span class="shop-name e1"><?php echo htmlspecialchars((string)$shopName, ENT_QUOTES, 'UTF-8');?></span>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-default btn-xs icon-btn view-shop-btn" title="查看详情" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-eye"></i>
						</button>
						<?php if ($shopUrl) { ?>
						<a href="<?php echo htmlspecialchars((string)$shopUrl, ENT_QUOTES, 'UTF-8');?>" target="_blank" class="btn btn-primary btn-xs icon-btn" title="访问源店铺" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
							<i class="fa fa-arrow-up-right-from-square"></i>
						</a>
						<?php } ?>
					</div>
				</div>

				<div class="card-body">
					<div class="meta-row">
						<span class="meta-label">源ID：</span>
						<span class="meta-val"><?php echo htmlspecialchars((string)($value['unique_id'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<?php if ($shopMetricsHtml && $shopMetricsHtml !== '--') { ?>
					<div class="metrics-box">
						<?php echo $shopMetricsHtml;?>
					</div>
					<?php } ?>
				</div>

				<div class="card-bottom">
					<span class="time-text"><i class="fa fa-clock"></i> 添加时间：<?php echo htmlspecialchars((string)$value['add_time'], ENT_QUOTES, 'UTF-8');?></span>
				</div>
			</div>
		<?php } } ?>
	</div>

	<?php if (!empty($list)) { ?>
	<div class="mt12">
		<?php echo page($size, $total);?>
	</div>
	<?php } ?>

	<!-- 店铺只读详情弹窗（无底部关闭按钮） -->
	<div id="shop-detail-modal" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-store"></i> 店铺详情</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="detail-row">
					<span class="d-label">店铺ID</span>
					<span class="d-val j-detail-id">--</span>
				</div>
				<div class="detail-row">
					<span class="d-label">所属渠道</span>
					<span class="d-val j-detail-channel">--</span>
				</div>
				<div class="detail-row">
					<span class="d-label">店铺源ID</span>
					<span class="d-val j-detail-unique">--</span>
				</div>
				<div class="detail-row">
					<span class="d-label">店铺名称</span>
					<span class="d-val j-detail-name">--</span>
				</div>
				<div class="detail-row">
					<span class="d-label">产品数量</span>
					<span class="d-val j-detail-count">--</span>
				</div>
				<div class="detail-row">
					<span class="d-label">评分指标</span>
					<div class="d-val j-detail-metrics">--</div>
				</div>
				<div class="detail-row">
					<span class="d-label">添加时间</span>
					<span class="d-val j-detail-time">--</span>
				</div>
			</div>
		</div>
	</div>
</div>
