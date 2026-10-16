<div class="product-index-page" id="product-index-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-boxes-stacked"></i>
			<span class="cur-page-title">SPU产品列表</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 件</span>
		</div>
		<div class="header-actions">
			<a href="<?php echo adminUrl('product/purchaseList');?>" class="btn btn-default btn-xs" title="采购产品列表">
				<i class="fa fa-cart-flatbed"></i> 采购池
			</a>
		</div>
	</div>

	<!-- 筛选表单（无重置按钮） -->
	<form action="<?php echo adminUrl('product');?>" method="get" class="search-card">
		<div class="filter-grid">
			<div class="filter-item">
				<input type="text" class="form-control" name="spu_id" value="<?php echo !empty($spuId) ? htmlspecialchars((string)$spuId, ENT_QUOTES, 'UTF-8') : '';?>" placeholder="SPU ID" autocomplete="off">
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
			<div class="filter-item filter-full">
				<select class="form-control" name="cate">
					<option value="0">全部分类</option>
					<?php
					$allCates = isset($cateList[0]) ? $cateList : ($cateList[-1] ?? []);
					if (!empty($allCates)) {
						foreach ($allCates as $cIdx => $cRow) {
							$cId = (int)$cRow['cate_id'];
							$cLev = max(0, (int)($cRow['level'] ?? 0));
							$nextRow = $allCates[$cIdx + 1] ?? null;
							$hasSub = $nextRow && (int)($nextRow['parent_id'] ?? 0) === $cId;
							$pad = str_repeat('　', $cLev);
					?>
					<option value="<?php echo $cId;?>" <?php echo $hasSub ? 'disabled="disabled"' : '';?> <?php echo ((int)$cate === $cId && $cId > 0) ? 'selected' : '';?>><?php echo $pad . htmlspecialchars((string)$cRow['name'], ENT_QUOTES, 'UTF-8');?></option>
					<?php }
					} elseif (!empty($cateArr)) {
						foreach ($cateArr as $cId => $cName) { ?>
					<option value="<?php echo (int)$cId;?>" <?php echo ((int)$cate === (int)$cId && (int)$cId > 0) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$cName, ENT_QUOTES, 'UTF-8');?></option>
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

	<!-- 产品卡片列表 -->
	<div id="spu-list" class="card-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-box-open"></i>
				<p>没有找到产品数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$spuIdVal = (int)$value['spu_id'];
				$rawImg = trim((string)($value['image'] ?? ''));
				$hasImg = $rawImg !== '' && strpos($rawImg, 'image/common/') === false;
				$nameStr = htmlspecialchars((string)($value['name'] ?? '--'), ENT_QUOTES, 'UTF-8');
				$cateName = htmlspecialchars((string)($cateArr[$value['cate_id']] ?? '--'), ENT_QUOTES, 'UTF-8');
				$minPrice = $value['min_price'] ?? 0;
				$maxPrice = $value['max_price'] ?? 0;
				$priceStr = ($minPrice < $maxPrice) ? ('￥' . $minPrice . ' - ￥' . $maxPrice) : ('￥' . $minPrice);
				$isOpen = (int)($value['status'] ?? 0) === 1;
				$statusText = htmlspecialchars((string)($value['status_text'] ?? ''), ENT_QUOTES, 'UTF-8');
				$detailUrl = adminUrl('product/detail', ['id' => $spuIdVal]);
			?>
			<div class="spu-card spu-item" data-id="<?php echo $spuIdVal;?>">
				<div class="card-main">
					<a href="<?php echo $detailUrl;?>" class="spu-thumb">
						<?php if ($hasImg) { ?>
							<img src="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" alt="SPU">
						<?php } else { ?>
							<i class="fa fa-image"></i>
						<?php } ?>
					</a>
					<div class="spu-info">
						<div class="spu-top-row">
							<div class="spu-badges">
								<span class="id-badge">#<?php echo $spuIdVal;?></span>
								<span class="status-badge <?php echo $isOpen ? 'status-on' : 'status-off';?>"><?php echo $statusText;?></span>
							</div>
							<div class="card-actions">
								<a href="<?php echo $detailUrl;?>" class="btn btn-primary btn-xs icon-btn" title="产品详情" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
									<i class="fa fa-pen-to-square"></i>
								</a>
							</div>
						</div>
						<a href="<?php echo $detailUrl;?>" class="spu-name e2"><?php echo $nameStr ?: '--';?></a>
						<div class="spu-meta-row">
							<span class="cate-tag e1"><i class="fa fa-folder-open"></i> <?php echo $cateName;?></span>
							<span class="price-text"><?php echo htmlspecialchars($priceStr, ENT_QUOTES, 'UTF-8');?></span>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<?php if (!empty($list)) { ?>
	<div class="mt12">
		<?php echo page($size, $total);?>
	</div>
	<?php } ?>
</div>

<script type="text/javascript">
var cate_list = <?php echo json_encode($cateList ?? [], JSON_UNESCAPED_UNICODE);?>;
var cate = <?php echo (int)$cate;?>;
</script>
