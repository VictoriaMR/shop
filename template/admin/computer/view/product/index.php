<div class="container-fluid" id="spu-index-page">
	<!-- 顶部筛选栏 -->
	<div class="filter-bar">
		<form action="<?php echo adminUrl('product/index');?>" class="form-inline" method="get" id="spu-filter-form">
			<?php if (!empty($shopId)) { ?>
			<input type="hidden" name="shop_id" value="<?php echo (int)$shopId;?>">
			<div class="form-group mr8 mb10">
				<span class="shop-filter-tag">
					<i class="fa fa-store"></i>
					店铺：<?php echo htmlspecialchars($filterShop['name'] ?? ('ID:' . $shopId));?>
					<a href="<?php echo adminUrl('product/index', ['status' => $status, 'cate' => $cate, 'keyword' => $keyword]);?>" title="清除店铺筛选">&times;</a>
				</span>
			</div>
			<?php } ?>
			<div class="form-group mr8 mb10">
				<input type="text" class="form-control" name="spu_id" value="<?php echo $spuId > 0 ? $spuId : '';?>" placeholder="SPU ID (PID)" style="width: 115px;" autocomplete="off">
			</div>
			<div class="form-group mr8 mb10">
				<input type="text" class="form-control" name="item_id" value="<?php echo htmlspecialchars($itemId ?? '');?>" placeholder="货源商品ID" style="width: 145px;" autocomplete="off">
			</div>
			<div class="form-group mr8 mb10">
				<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '');?>" placeholder="产品名称关键字" style="width: 200px;" autocomplete="off">
			</div>
			<div class="form-group mr8 mb10">
				<select class="form-control" name="cate" style="min-width: 170px;">
					<option value="0">全部产品分类</option>
				</select>
			</div>
			<div class="form-group mr8 mb10">
				<select class="form-control" name="status">
					<option value="-1">全部状态</option>
					<?php if (!empty($statusList)) {
						foreach ($statusList as $key => $value) { ?>
					<option <?php if ($status == $key) { echo 'selected'; }?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="form-group mr8 mb10">
				<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($stime);?>" name="stime" placeholder="入库开始日期" style="width: 115px;" autocomplete="off"> - 
				<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($etime);?>" name="etime" placeholder="入库结束日期" style="width: 115px;" autocomplete="off">
			</div>
			<div class="form-group mb10">
				<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> 查询</button>
			</div>
			<div class="clear"></div>
		</form>
	</div>

	<!-- 状态快捷Tab与视图切换工具栏 -->
	<div class="spu-toolbar">
		<div class="status-tabs">
			<?php
			$baseParams = [
				'spu_id'  => $spuId > 0 ? $spuId : null,
				'item_id' => $itemId !== '' ? $itemId : null,
				'shop_id' => $shopId > 0 ? $shopId : null,
				'keyword' => $keyword !== '' ? $keyword : null,
				'cate'    => $cate > 0 ? $cate : null,
				'stime'   => $stime !== '' ? $stime : null,
				'etime'   => $etime !== '' ? $etime : null,
			];
			$baseParams = array_filter($baseParams, function($v) { return $v !== null; });
			?>
			<a href="<?php echo adminUrl('product/index', array_merge($baseParams, ['status' => -1]));?>" class="status-tab <?php echo $status == -1 ? 'active' : '';?>">
				全部产品 <span class="tab-count"><?php echo (int)($statusCounts[-1] ?? 0);?></span>
			</a>
			<?php foreach ($statusList as $sKey => $sLabel) { ?>
			<a href="<?php echo adminUrl('product/index', array_merge($baseParams, ['status' => $sKey]));?>" class="status-tab status-tab-<?php echo $sKey;?> <?php echo $status == $sKey ? 'active' : '';?>">
				<?php echo htmlspecialchars($sLabel);?> <span class="tab-count"><?php echo (int)($statusCounts[$sKey] ?? 0);?></span>
			</a>
			<?php } ?>
		</div>
		<div class="view-switcher">
			<span class="c9 mr8 f12">共 <strong class="c3"><?php echo (int)$total;?></strong> 款 SPU</span>
			<div class="btn-group btn-group-xs" role="group">
				<button type="button" class="btn btn-default view-mode-btn active" data-mode="grid" title="网格卡片视图">
					<i class="fa fa-th-large"></i> 卡片
				</button>
				<button type="button" class="btn btn-default view-mode-btn" data-mode="table" title="紧凑表格视图">
					<i class="fa fa-list"></i> 列表
				</button>
			</div>
		</div>
	</div>

	<?php if (!empty($list)) {
		$supplierLabels = [
			'taobao' => ['name' => '淘宝', 'cls' => 'sup-taobao'],
			'tmall'  => ['name' => '天猫', 'cls' => 'sup-tmall'],
			'1688'   => ['name' => '1688', 'cls' => 'sup-1688'],
		];
	?>
	<!-- 视图1：网格卡片视图 -->
	<div id="spu-grid-view" class="spu-grid">
		<?php foreach ($list as $value) {
			$sData = $value['spu_data'] ?? [];
			$shopInfo = $value['shop_info'] ?? [];
			$supKey = strtolower($sData['supplier'] ?? '');
			$supBadge = $supplierLabels[$supKey] ?? ($supKey ? ['name' => $supKey, 'cls' => 'sup-default'] : null);
			$priceDisplay = $value['min_price'] < $value['max_price']
				? '￥' . $value['min_price'] . ' ~ ￥' . $value['max_price']
				: '￥' . $value['min_price'];
		?>
		<div class="spu-card" data-id="<?php echo $value['spu_id'];?>">
			<div class="spu-card-media">
				<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="spu-img-link" title="<?php echo htmlspecialchars($value['name']);?>">
					<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload" alt="<?php echo htmlspecialchars($value['name']);?>">
				</a>
				<div class="card-top-left">
					<span class="pid-badge">#<?php echo $value['spu_id'];?></span>
					<?php if ($supBadge) { ?>
					<span class="sup-badge <?php echo $supBadge['cls'];?>"><?php echo htmlspecialchars($supBadge['name']);?></span>
					<?php } ?>
				</div>
				<div class="card-top-right">
					<span class="spu-status-pill status-<?php echo (int)$value['status'];?> j-quick-status" data-id="<?php echo $value['spu_id'];?>" data-status="<?php echo (int)$value['status'];?>" title="点击快速切换上/下架状态">
						<?php echo htmlspecialchars($value['status_text']);?>
					</span>
				</div>
				<div class="card-bottom-overlay">
					<span>SKU: <strong><?php echo $value['active_sku_count'];?></strong>/<?php echo $value['sku_count'];?></span>
					<span>库存: <strong><?php echo $value['total_stock'];?></strong></span>
				</div>
			</div>
			<div class="spu-card-body">
				<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="spu-card-title e2" title="<?php echo htmlspecialchars($value['name']);?>">
					<?php echo htmlspecialchars($value['name'] ?: '--');?>
				</a>
				<div class="spu-card-price-row">
					<span class="price-range"><?php echo $priceDisplay;?></span>
					<div class="spu-flags">
						<?php if (!empty($value['free_ship'])) { ?>
						<span class="flag-tag flag-freeship">免邮</span>
						<?php } ?>
						<?php if (!empty($value['is_hot'])) { ?>
						<span class="flag-tag flag-hot">热门</span>
						<?php } ?>
					</div>
				</div>
				<div class="spu-card-meta">
					<div class="meta-line e1" title="分类：<?php echo htmlspecialchars($cateArr[$value['cate_id']] ?? '--');?>">
						<span class="meta-label">分类:</span>
						<a href="<?php echo adminUrl('product/index', ['cate' => $value['cate_id']]);?>" class="meta-link"><?php echo htmlspecialchars($cateArr[$value['cate_id']] ?? '--');?></a>
					</div>
					<?php if (!empty($shopInfo['name'])) { ?>
					<div class="meta-line e1" title="店铺：<?php echo htmlspecialchars($shopInfo['name']);?>">
						<span class="meta-label">店铺:</span>
						<a href="<?php echo adminUrl('product/index', ['shop_id' => $shopInfo['purchase_shop_id']]);?>" class="meta-link"><?php echo htmlspecialchars($shopInfo['name']);?></a>
					</div>
					<?php } elseif (!empty($sData['item_id'])) { ?>
					<div class="meta-line e1">
						<span class="meta-label">货源:</span>
						<span class="c6"><?php echo htmlspecialchars($sData['item_id']);?></span>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="spu-card-footer">
				<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> 管理详情</a>
				<div class="footer-right-actions">
					<?php if (!empty($sData['item_url'])) { ?>
					<a href="<?php echo htmlspecialchars($sData['item_url']);?>" target="_blank" class="btn btn-default btn-xs" title="打开第三方货源链接"><i class="fa fa-link"></i> 货源</a>
					<?php } ?>
					<button type="button" class="btn <?php echo $value['status'] == 1 ? 'btn-warning' : 'btn-success';?> btn-xs j-quick-status" data-id="<?php echo $value['spu_id'];?>" data-status="<?php echo (int)$value['status'];?>">
						<?php echo $value['status'] == 1 ? '下架' : '上架';?>
					</button>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>

	<!-- 视图2：紧凑列表视图 -->
	<div id="spu-table-view" class="spu-table-wrap hidden">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="70">PID</th>
					<th width="68">主图</th>
					<th>产品名称 / 货源信息</th>
					<th width="140">所属分类</th>
					<th width="150">售价区间</th>
					<th width="110">SKU / 总库存</th>
					<th width="75">免运费</th>
					<th width="75">热门</th>
					<th width="95">状态</th>
					<th width="145">时间</th>
					<th width="120">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $value) {
					$sData = $value['spu_data'] ?? [];
					$shopInfo = $value['shop_info'] ?? [];
					$supKey = strtolower($sData['supplier'] ?? '');
					$supBadge = $supplierLabels[$supKey] ?? ($supKey ? ['name' => $supKey, 'cls' => 'sup-default'] : null);
					$priceDisplay = $value['min_price'] < $value['max_price']
						? '￥' . $value['min_price'] . ' ~ ￥' . $value['max_price']
						: '￥' . $value['min_price'];
				?>
				<tr data-id="<?php echo $value['spu_id'];?>">
					<td class="f600">
						<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>">#<?php echo $value['spu_id'];?></a>
					</td>
					<td>
						<div class="table-spu-thumb">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload bigImage" alt="">
						</div>
					</td>
					<td>
						<div class="mb4">
							<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="f600 f13 table-title-link"><?php echo htmlspecialchars($value['name'] ?: '--');?></a>
						</div>
						<div class="f12 c9">
							<?php if ($supBadge) { ?>
							<span class="sup-badge <?php echo $supBadge['cls'];?> mr6"><?php echo htmlspecialchars($supBadge['name']);?></span>
							<?php } ?>
							<?php if (!empty($sData['item_id'])) { ?>
							<span class="mr8">货源ID: 
								<?php if (!empty($sData['item_url'])) { ?>
								<a href="<?php echo htmlspecialchars($sData['item_url']);?>" target="_blank" class="c-primary"><?php echo htmlspecialchars($sData['item_id']);?> <i class="fa fa-link"></i></a>
								<?php } else { echo htmlspecialchars($sData['item_id']); } ?>
							</span>
							<?php } ?>
							<?php if (!empty($shopInfo['name'])) { ?>
							<span>店铺: <a href="<?php echo adminUrl('product/index', ['shop_id' => $shopInfo['purchase_shop_id']]);?>"><?php echo htmlspecialchars($shopInfo['name']);?></a></span>
							<?php } ?>
						</div>
					</td>
					<td>
						<a href="<?php echo adminUrl('product/index', ['cate' => $value['cate_id']]);?>"><?php echo htmlspecialchars($cateArr[$value['cate_id']] ?? '--');?></a>
					</td>
					<td>
						<span class="price-range"><?php echo $priceDisplay;?></span>
						<?php if (!empty($sData['post_fee']) && $sData['post_fee'] > 0) { ?>
						<div class="f12 c9">运费: ￥<?php echo $sData['post_fee'];?></div>
						<?php } ?>
					</td>
					<td>
						<div>SKU: <strong><?php echo $value['active_sku_count'];?></strong> / <?php echo $value['sku_count'];?></div>
						<div class="f12 c9">库存: <?php echo $value['total_stock'];?></div>
					</td>
					<td>
						<div class="switch_botton j-switch-field" data-field="free_ship" data-status="<?php echo (int)$value['free_ship'];?>">
							<div class="switch_status <?php echo $value['free_ship'] == 1 ? 'on' : 'off';?>"></div>
						</div>
					</td>
					<td>
						<div class="switch_botton j-switch-field" data-field="is_hot" data-status="<?php echo (int)$value['is_hot'];?>">
							<div class="switch_status <?php echo $value['is_hot'] == 1 ? 'on' : 'off';?>"></div>
						</div>
					</td>
					<td>
						<span class="spu-status-pill status-<?php echo (int)$value['status'];?> j-quick-status" data-id="<?php echo $value['spu_id'];?>" data-status="<?php echo (int)$value['status'];?>" title="点击快速切换">
							<?php echo htmlspecialchars($value['status_text']);?>
						</span>
					</td>
					<td class="f12 c6">
						<div title="入库时间"><?php echo $value['add_time'] ?: '--';?></div>
						<?php if (!empty($value['update_time'])) { ?>
						<div class="c9" title="更新时间"><?php echo $value['update_time'];?></div>
						<?php } ?>
					</td>
					<td>
						<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> 详情</a>
						<button type="button" class="btn <?php echo $value['status'] == 1 ? 'btn-warning' : 'btn-success';?> btn-xs j-quick-status" data-id="<?php echo $value['spu_id'];?>" data-status="<?php echo (int)$value['status'];?>">
							<?php echo $value['status'] == 1 ? '下架' : '上架';?>
						</button>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="mt10">
		<?php echo page($size, $total);?>
	</div>
	<?php } else { ?>
	<div class="empty-state">
		<i class="fa fa-inbox"></i>
		<p>没有找到符合条件的 SPU 产品数据</p>
		<a href="<?php echo adminUrl('product/purchaseList');?>" class="btn btn-primary btn-sm mt10">前往采购产品列表入库</a>
	</div>
	<?php } ?>
</div>

<script type="text/javascript">
var cate_list = <?php echo json_encode($cateList, JSON_UNESCAPED_UNICODE);?>;
var cate = <?php echo (int)$cate;?>;
</script>