<?php
$spuId = (int)($info['spu_id'] ?? 0);
?>
<div class="detail-page" id="product-detail-page" data-id="<?php echo $spuId;?>" data-url="<?php echo adminUrl('product/detail');?>">
	<!-- 顶部导航栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-cube"></i>
			<span class="cur-page-title">SPU详情 <?php echo $spuId > 0 ? '#' . $spuId : '';?></span>
		</div>
		<div class="header-actions">
			<a href="<?php echo adminUrl('product/index');?>" class="btn btn-default btn-xs" title="返回SPU列表">
				<i class="fa fa-arrow-left"></i> 返回列表
			</a>
		</div>
	</div>

	<?php if (empty($info)) { ?>
		<div class="empty-state">
			<i class="fa fa-circle-exclamation"></i>
			<p>找不到该产品数据</p>
		</div>
	<?php } else {
		$isOpen = (int)($info['status'] ?? 0) === 1;
		$statusText = htmlspecialchars((string)($statusList[$info['status']] ?? '--'), ENT_QUOTES, 'UTF-8');
		$catePath = !empty($info['category']) ? implode(' - ', array_column($info['category'], 'name')) : '--';
		$postFee = $info['data']['post_fee'] ?? '0.00';
		$volumeVal = $info['data']['volume'] ?? '--';
		$weightVal = $info['data']['weight'] ?? '0';
		$shopIdVal = $info['data']['shop_id'] ?? '';
		$shopNameVal = $info['shop']['name'] ?? '';
		$shopUrlVal = $info['shop']['url'] ?? '';
		$supplierVal = $info['data']['supplier'] ?? '';
		$supplierItemId = $info['data']['item_id'] ?? '';
		$supplierItemUrl = $info['data']['item_url'] ?? '';
	?>
	<!-- 基础信息与供应商信息卡片 -->
	<div class="section-card">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-circle-info"></i> 基础与扩展信息</span>
			<span class="id-badge">PID #<?php echo $spuId;?></span>
		</div>
		<div class="spu-name-box">
			<div class="spu-name-text name"><?php echo htmlspecialchars((string)($info['name'] ?? '--'), ENT_QUOTES, 'UTF-8');?></div>
			<button type="button" class="btn btn-info btn-xs icon-btn name-trans-btn" title="名称多语言配置" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
				<i class="fa fa-globe"></i>
			</button>
		</div>

		<div class="info-list">
			<div class="info-row">
				<span class="info-label">产品状态</span>
				<div class="info-val-group">
					<span class="status-badge j-show-spu-status <?php echo $isOpen ? 'status-on' : 'status-off';?>" data-status="<?php echo (int)$info['status'];?>"><?php echo $statusText;?></span>
					<button type="button" class="btn btn-primary btn-xs icon-btn status-btn" title="修改状态"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">免运费</span>
				<div class="info-val-group">
					<div class="switch_botton free-ship" data-status="<?php echo (int)($info['free_ship'] ?? 0);?>">
						<div class="switch_status <?php echo (int)($info['free_ship'] ?? 0) === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">产品分类</span>
				<div class="info-val-group">
					<span class="info-text"><?php echo htmlspecialchars((string)$catePath, ENT_QUOTES, 'UTF-8');?></span>
					<button type="button" class="btn btn-primary btn-xs icon-btn category-btn" title="修改分类"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">邮费</span>
				<div class="info-val-group">
					<span class="info-text"><?php echo htmlspecialchars((string)$postFee, ENT_QUOTES, 'UTF-8');?></span>
					<button type="button" class="btn btn-primary btn-xs icon-btn data-btn" data-name="post_fee" data-label="邮费" data-val="<?php echo htmlspecialchars((string)$postFee, ENT_QUOTES, 'UTF-8');?>" title="修改邮费"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">体积</span>
				<div class="info-val-group">
					<span class="info-text"><?php echo htmlspecialchars((string)$volumeVal, ENT_QUOTES, 'UTF-8');?></span>
					<button type="button" class="btn btn-primary btn-xs icon-btn data-btn" data-name="volume" data-label="体积" data-val="<?php echo htmlspecialchars((string)($info['data']['volume'] ?? ''), ENT_QUOTES, 'UTF-8');?>" title="修改体积"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">重量(g)</span>
				<div class="info-val-group">
					<span class="info-text"><?php echo htmlspecialchars((string)$weightVal, ENT_QUOTES, 'UTF-8');?></span>
					<button type="button" class="btn btn-primary btn-xs icon-btn data-btn" data-name="weight" data-label="重量" data-val="<?php echo htmlspecialchars((string)$weightVal, ENT_QUOTES, 'UTF-8');?>" title="修改重量"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>
			<div class="info-row">
				<span class="info-label">时间记录</span>
				<div class="info-val-group">
					<span class="info-text time-text"><?php echo htmlspecialchars((string)($info['add_time'] ?? '--'), ENT_QUOTES, 'UTF-8');?> / <?php echo htmlspecialchars((string)($info['update_time'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
				</div>
			</div>
		</div>

		<!-- 供应商折叠信息 -->
		<div class="supplier-subcard">
			<div class="supplier-row">
				<span class="s-label"><i class="fa fa-store"></i> 店铺：</span>
				<span class="s-val">
					<?php if ($shopUrlVal) { ?>
						<a href="<?php echo htmlspecialchars((strpos($shopUrlVal, 'http') === 0 ? $shopUrlVal : 'https://' . $shopUrlVal), ENT_QUOTES, 'UTF-8');?>" target="_blank">
							<?php echo htmlspecialchars((string)($shopNameVal ?: $shopUrlVal), ENT_QUOTES, 'UTF-8');?>
							<?php if ($shopIdVal) { ?>(#<?php echo htmlspecialchars((string)$shopIdVal, ENT_QUOTES, 'UTF-8');?>)<?php } ?>
							<i class="fa fa-arrow-up-right-from-square"></i>
						</a>
					<?php } else { ?>
						<?php echo htmlspecialchars((string)($shopNameVal ?: '--'), ENT_QUOTES, 'UTF-8');?>
					<?php } ?>
				</span>
			</div>
			<div class="supplier-row">
				<span class="s-label"><i class="fa fa-link"></i> 货源：</span>
				<span class="s-val">
					<?php if ($supplierItemUrl) { ?>
						<a href="<?php echo htmlspecialchars((string)$supplierItemUrl, ENT_QUOTES, 'UTF-8');?>" target="_blank">
							<?php echo htmlspecialchars((string)(($supplierVal ? $supplierVal . ' - ' : '') . ($supplierItemId ?: '查看源链接')), ENT_QUOTES, 'UTF-8');?>
							<i class="fa fa-arrow-up-right-from-square"></i>
						</a>
					<?php } else { ?>
						<?php echo htmlspecialchars((string)(($supplierVal ? $supplierVal . ' - ' : '') . ($supplierItemId ?: '--')), ENT_QUOTES, 'UTF-8');?>
					<?php } ?>
				</span>
			</div>
		</div>
	</div>

	<!-- 产品主图列表卡片 -->
	<div class="section-card">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-images"></i> 产品图片 (<?php echo count($info['image'] ?? []);?>)</span>
			<button type="button" class="btn btn-success btn-xs upload-image" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
				<i class="fa fa-upload"></i> 上传图片
			</button>
		</div>
		<?php if (empty($info['image'])) { ?>
			<div class="empty-inline">暂无产品图片</div>
		<?php } else { ?>
		<div class="image-grid">
			<?php foreach ($info['image'] as $value) {
				$rawImg = trim((string)($value['image'] ?? ''));
				$hasImg = $rawImg !== '' && strpos($rawImg, 'image/common/') === false;
				$fullImg = str_replace('/400', '', $rawImg);
				$isMainCover = (int)$value['attach_id'] === (int)$info['attach_id'];
			?>
			<div class="spu-image img-card" data-id="<?php echo (int)$value['attach_id'];?>" data-item_id="<?php echo (int)$value['item_id'];?>">
				<div class="image-tcell">
					<?php if ($hasImg) { ?>
						<img src="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" alt="SPU图">
					<?php } else { ?>
						<i class="fa fa-image"></i>
					<?php } ?>
					<?php if ($isMainCover) { ?>
						<span class="main-cover-tag">主图</span>
					<?php } ?>
				</div>
				<div class="image-btn">
					<div class="img-top-controls">
						<input type="text" name="sort" class="form-control sort-input" value="<?php echo (int)$value['sort'];?>" placeholder="排序">
						<div class="img-icon-btns">
							<?php if ($hasImg) { ?>
							<a class="btn btn-default btn-xs icon-btn" target="_blank" href="<?php echo htmlspecialchars($fullImg, ENT_QUOTES, 'UTF-8');?>" title="查看大图"><i class="fa fa-magnifying-glass"></i></a>
							<?php } ?>
							<button type="button" class="btn btn-danger btn-xs icon-btn delete-spu-img-btn" title="删除图片" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
						</div>
					</div>
					<div class="img-bottom-controls">
						<?php if (!$isMainCover) { ?>
						<button type="button" class="btn btn-primary btn-xs spu-btn">设为主图</button>
						<?php } ?>
						<button type="button" class="btn btn-success btn-xs sku-btn">配SKU</button>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<!-- SKU列表卡片 -->
	<div class="section-card">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-sitemap"></i> SKU列表 (<?php echo count($info['sku'] ?? []);?>)</span>
			<span class="section-tip">点击数值可修改</span>
		</div>
		<?php if (empty($info['sku'])) { ?>
			<div class="empty-inline">暂无SKU数据</div>
		<?php } else { ?>
		<div class="sku-card-list">
			<?php foreach ($info['sku'] as $value) {
				$skuId = (int)$value['sku_id'];
				$skuImg = trim((string)($value['image'] ?? ''));
				$hasSkuImg = $skuImg !== '' && strpos($skuImg, 'image/common/') === false;
				$skuStatus = (int)($value['status'] ?? 0);
			?>
			<div class="sku-card item-row" data-id="<?php echo $skuId;?>">
				<div class="sku-card-top">
					<div class="sku-image">
						<?php if ($hasSkuImg) { ?>
							<img src="<?php echo htmlspecialchars($skuImg, ENT_QUOTES, 'UTF-8');?>" alt="SKU">
						<?php } else { ?>
							<i class="fa fa-image"></i>
						<?php } ?>
					</div>
					<div class="sku-head-info">
						<div class="sku-id-line">
							<span class="id-badge">SKU #<?php echo $skuId;?></span>
							<?php if (!empty($value['item_id'])) { ?>
								<span class="sub-badge">源: <?php echo htmlspecialchars((string)$value['item_id'], ENT_QUOTES, 'UTF-8');?></span>
							<?php } ?>
						</div>
						<div class="sku-attrs">
							<?php if (!empty($value['attr'])) {
								foreach ($value['attr'] as $attrValue) { ?>
								<span class="spec-chip"><?php echo htmlspecialchars((string)($attrValue['attr_name'] . ': ' . $attrValue['attv_name']), ENT_QUOTES, 'UTF-8');?></span>
							<?php } } ?>
						</div>
					</div>
					<div class="sku-status-box">
						<div class="switch_botton sku-status" data-status="<?php echo $skuStatus;?>">
							<div class="switch_status <?php echo $skuStatus === 1 ? 'on' : 'off';?>"></div>
						</div>
					</div>
				</div>
				<div class="sku-metrics-grid">
					<div class="metric-item can-edit" data-name="price" title="点击修改售价">
						<span class="m-label">售价 <i class="fa fa-pen"></i></span>
						<span class="m-val price-val"><?php echo htmlspecialchars((string)$value['price'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="metric-item can-edit" data-name="cost_price" title="点击修改成本价">
						<span class="m-label">成本价 <i class="fa fa-pen"></i></span>
						<span class="m-val"><?php echo htmlspecialchars((string)$value['cost_price'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="metric-item can-edit" data-name="stock" title="点击修改库存">
						<span class="m-label">库存 <i class="fa fa-pen"></i></span>
						<span class="m-val"><?php echo htmlspecialchars((string)$value['stock'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="metric-item can-edit" data-name="weight" title="点击修改重量">
						<span class="m-label">重量(g) <i class="fa fa-pen"></i></span>
						<span class="m-val"><?php echo htmlspecialchars((string)$value['weight'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="metric-item can-edit metric-wide" data-name="volume" title="点击修改体积">
						<span class="m-label">体积(mm) <i class="fa fa-pen"></i></span>
						<span class="m-val"><?php echo htmlspecialchars((string)($value['volume'] ?: '0'), ENT_QUOTES, 'UTF-8');?></span>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<!-- 属性列表卡片 -->
	<div class="section-card">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-tags"></i> 属性图片配置</span>
			<span class="section-tip">点击图标上传属性图</span>
		</div>
		<?php if (empty($info['attr_map'])) { ?>
			<div class="empty-inline">暂无属性数据</div>
		<?php } else { ?>
		<div class="attr-group-list">
			<?php foreach ($info['attr_map'] as $kv) { ?>
			<div class="attr-group-block">
				<div class="attr-group-title"><?php echo htmlspecialchars((string)$kv['attr_name'], ENT_QUOTES, 'UTF-8');?></div>
				<div class="attr-son-list">
					<?php foreach ($kv['son'] as $value) {
						$attrImg = trim((string)($value['image'] ?? ''));
						$hasAttrImg = $attrImg !== '' && strpos($attrImg, 'image/common/') === false;
					?>
					<div class="attr-son-card" data-attrn_id="<?php echo (int)$kv['attrn_id'];?>" data-attrv_id="<?php echo (int)$value['attrv_id'];?>" data-spu_id="<?php echo $spuId;?>">
						<div class="sku-attr-image" title="点击上传/更换属性图">
							<?php if ($hasAttrImg) { ?>
								<img src="<?php echo htmlspecialchars($attrImg, ENT_QUOTES, 'UTF-8');?>" alt="属性图">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
						</div>
						<div class="attr-son-name e1"><?php echo htmlspecialchars((string)$value['attv_name'], ENT_QUOTES, 'UTF-8');?></div>
						<div class="card-actions">
							<?php if ($hasAttrImg) { ?>
							<button type="button" class="btn btn-danger btn-xs icon-btn delete-attr-image-btn" title="删除属性图片" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
								<i class="fa fa-trash-can"></i>
							</button>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<!-- 描述列表卡片 -->
	<div class="section-card" id="sku-desc-content">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-list-check"></i> 描述列表</span>
			<div class="header-actions">
				<button type="button" class="btn btn-info btn-xs desc-group-btn"><i class="fa fa-layer-group"></i> 分组</button>
				<button type="button" class="btn btn-success btn-xs add-desc-btn"><i class="fa fa-plus"></i> 增加</button>
			</div>
		</div>
		<?php if (empty($info['desc'])) { ?>
			<div class="empty-inline">暂无描述文本</div>
		<?php } else { ?>
		<div class="desc-group-list">
			<?php foreach ($info['desc'] as $item) { ?>
			<div class="desc-group-box">
				<div class="desc-group-header">
					<i class="fa fa-folder"></i>
					<span><?php echo htmlspecialchars((string)($item['group'] ?: '默认分组'), ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="desc-items">
					<?php foreach ($item['list'] as $value) {
						$descItemId = (int)$value['item_id'];
					?>
					<div class="desc-item-card" data-id="<?php echo $descItemId;?>">
						<div class="desc-check-col">
							<span class="desc-check-icon"><i class="fa fa-circle-check"></i></span>
						</div>
						<div class="desc-main-col">
							<div class="desc-name"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></div>
							<div class="desc-val"><?php echo htmlspecialchars((string)$value['value'], ENT_QUOTES, 'UTF-8');?></div>
						</div>
						<div class="desc-right-col">
							<input type="text" name="sort" value="<?php echo (int)$value['sort'];?>" class="form-control desc-sort-input" title="排序">
							<div class="card-actions">
								<button type="button" class="btn btn-primary btn-xs icon-btn edit-desc-btn" title="编辑描述" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
								<button type="button" class="btn btn-danger btn-xs icon-btn delete-desc-btn" title="删除描述" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<!-- 描述图片卡片 -->
	<div class="section-card">
		<div class="section-header">
			<span class="section-title"><i class="fa fa-file-image"></i> 描述图片 (<?php echo count($info['introduce'] ?? []);?>)</span>
			<button type="button" class="btn btn-success btn-xs upload-introduce-image" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
				<i class="fa fa-upload"></i> 上传图片
			</button>
		</div>
		<?php if (empty($info['introduce'])) { ?>
			<div class="empty-inline">暂无描述图片</div>
		<?php } else { ?>
		<div class="image-grid">
			<?php foreach ($info['introduce'] as $value) {
				$introImg = trim((string)($value['image'] ?? ''));
				$hasIntroImg = $introImg !== '' && strpos($introImg, 'image/common/') === false;
				$fullIntroImg = str_replace('/400', '', $introImg);
			?>
			<div class="spu-introduce-image img-card" data-id="<?php echo (int)$value['attach_id'];?>">
				<div class="image-tcell">
					<?php if ($hasIntroImg) { ?>
						<img src="<?php echo htmlspecialchars($introImg, ENT_QUOTES, 'UTF-8');?>" alt="描述图">
					<?php } else { ?>
						<i class="fa fa-image"></i>
					<?php } ?>
				</div>
				<div class="image-btn" data-id="<?php echo (int)$value['item_id'];?>">
					<div class="img-top-controls">
						<input type="text" name="sort" class="form-control sort-input" value="<?php echo (int)$value['sort'];?>" placeholder="排序">
						<div class="img-icon-btns">
							<?php if ($hasIntroImg) { ?>
							<a class="btn btn-default btn-xs icon-btn" target="_blank" href="<?php echo htmlspecialchars($fullIntroImg, ENT_QUOTES, 'UTF-8');?>" title="查看大图"><i class="fa fa-magnifying-glass"></i></a>
							<?php } ?>
							<button type="button" class="btn btn-danger btn-xs icon-btn delete-introduce-btn" title="删除描述图片" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<!-- 状态弹窗 -->
	<div id="status-dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">产品状态</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group">
						<label class="form-label">状态选择</label>
						<select class="form-control" name="status">
							<?php foreach ($statusList as $key => $value) { ?>
							<option value="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>" <?php echo ((string)$key === (string)$info['status']) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 分类弹窗 -->
	<div id="category-dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">产品分类</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group">
						<label class="form-label">所属分类</label>
						<select class="form-control" name="cate_id">
							<?php foreach ($siteCate ?? [] as $k => $v) {
								$paddingStr = str_repeat('　', max(0, (int)($v['level'] ?? 0)));
								$isDisabled = (($siteCate[$k + 1]['level'] ?? 0) > $v['level']);
							?>
							<option value="<?php echo (int)$v['cate_id'];?>" <?php echo ((int)$info['cate_id'] === (int)$v['cate_id']) ? 'selected' : '';?> <?php echo $isDisabled ? 'disabled="disabled"' : '';?>><?php echo $paddingStr . htmlspecialchars((string)$v['name'], ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 性别弹窗 -->
	<div id="gender-dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">产品性别属性</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group">
						<label class="form-label">性别</label>
						<select class="form-control" name="gender">
							<option value="0">默认</option>
							<option value="1">男</option>
							<option value="2">女</option>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 多语言弹窗 -->
	<div id="dealbox-language" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-globe"></i> 多语言配置</h4>
				<div class="modal-header-right">
					<button type="button" class="btn btn-info btn-xs translate-all-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-language"></i> 智能翻译
					</button>
					<span class="modal-close"><i class="fa fa-xmark"></i></span>
				</div>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="name" value="">
					<input type="hidden" name="opn" value="editSpuLanguage">
					<div class="language-list"></div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- SKU主图配置弹窗 -->
	<div id="dealbox-sku-image" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">SKU主图配置</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="opn" value="editSkuInfo">
					<input type="hidden" name="attach_id" value="0">
					<div class="sku-select-list">
						<?php foreach ($info['sku'] ?? [] as $value) { ?>
						<label class="sku-select-item item" data-id="<?php echo (int)$value['attach_id'];?>">
							<input type="checkbox" name="sku_id[]" value="<?php echo (int)$value['sku_id'];?>">
							<span class="id-badge">#<?php echo (int)$value['sku_id'];?></span>
							<span class="sku-select-attrs"><?php echo htmlspecialchars(implode(' / ', array_column($value['attr'] ?? [], 'attv_name')), ENT_QUOTES, 'UTF-8');?></span>
						</label>
						<?php } ?>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- SKU数值管理弹窗 -->
	<div id="dealbox-sku-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">SKU管理</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="opn" value="editSkuInfo">
					<input type="hidden" name="sku_id" value="0">
					<div class="form-group">
						<label class="form-label sku-field-label">数值</label>
						<input type="text" name="" value="" class="form-control name" autocomplete="off">
					</div>
					<div class="dual-btn-row">
						<button type="button" class="btn btn-success save-btn batch-save-btn">批量修改全部SKU</button>
						<button type="button" class="btn btn-primary save-btn">仅修改当前</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- SPU描述文本管理弹窗 -->
	<div id="dealbox-desc" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">描述文本</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="item_id" value="0">
					<input type="hidden" name="opn" value="modifySpuDesc">
					<div class="form-group">
						<label class="form-label">描述名 <span class="required">*</span></label>
						<textarea name="name" rows="2" class="form-control" placeholder="请输入描述名称"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">描述值 <span class="required">*</span></label>
						<textarea name="value" rows="3" class="form-control" placeholder="请输入描述内容"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">排序</label>
						<input type="number" name="sort" class="form-control" value="0">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- SPU扩展数据弹窗 -->
	<div id="dealbox-data" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">扩展数据</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="spu_id" value="<?php echo $spuId;?>">
					<input type="hidden" name="name" value="">
					<input type="hidden" name="opn" value="modifySpuData">
					<div class="form-group">
						<label class="form-label data-field-label">数值</label>
						<input type="text" name="value" class="form-control" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 描述分组弹窗 -->
	<div id="desc-group-dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">描述分组</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form onsubmit="return false;">
					<input type="hidden" name="opn" value="editDescGroupInfo">
					<div class="form-group">
						<label class="form-label">目标分组</label>
						<select class="form-control" name="group_id">
							<option value="0">默认分组</option>
							<?php foreach ($groupList ?? [] as $value) { ?>
							<option value="<?php echo (int)$value['descg_id'];?>"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
