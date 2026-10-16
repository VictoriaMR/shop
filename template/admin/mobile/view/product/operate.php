<?php
$operateId = iget('id/d', 0);
$saveData = $info['save_data'] ?? [];
$initCateId = (int)($saveData['cate_id'] ?? ($info['cate_id'] ?? 0));
$cateArr = $initCateId > 0 ? category()->pCate($initCateId, true, true) : [];
$initCateName = !empty($cateArr) ? implode(' - ', array_column($cateArr, 'name')) : '未选择分类';
$firstSpuImg = $saveData['spu_image'] ?? (current($info['pdt_picture'] ?? []) ?: '');
$savedSpuName = $saveData['spu_name'] ?? ($info['name'] ?? '');
$savedPostFee = $saveData['post_fee'] ?? ($info['post_fee'] ?? '0');
$savedSkus = ($saveData['sku'] ?? []) ?: [];
$savedAttrGroups = isset($saveData['attr_group_checked']) && is_array($saveData['attr_group_checked']) ? array_flip($saveData['attr_group_checked']) : null;

$descList = [];
if (!empty($saveData['desc']['name']) && is_array($saveData['desc']['name'])) {
	$savedDescNames = [];
	foreach ($saveData['desc']['name'] as $dk => $dName) {
		$descList[] = [
			'name'    => $dName,
			'value'   => $saveData['desc']['value'][$dk] ?? '',
			'checked' => true,
			'is_junk' => false,
		];
		$savedDescNames[$dName] = true;
	}
	foreach ($info['detail'] ?? [] as $origDesc) {
		$origName = $origDesc['name'] ?? '';
		if ($origName !== '' && !isset($savedDescNames[$origName])) {
			$descList[] = [
				'name'    => $origName,
				'value'   => $origDesc['value'] ?? '',
				'checked' => false,
				'is_junk' => !empty($origDesc['is_junk']),
			];
		}
	}
} else {
	foreach ($info['detail'] ?? [] as $origDesc) {
		$descList[] = [
			'name'    => $origDesc['name'] ?? '',
			'value'   => $origDesc['value'] ?? '',
			'checked' => isset($origDesc['default_checked']) ? (bool)$origDesc['default_checked'] : true,
			'is_junk' => !empty($origDesc['is_junk']),
		];
	}
}
?>
<div class="operate-page" id="add-product-page" data-url="<?php echo adminUrl('product/operate');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-wand-magic-sparkles"></i>
			<span class="cur-page-title">配置采购产品 <?php echo $operateId > 0 ? '#' . $operateId : '';?></span>
		</div>
		<div class="header-actions">
			<?php if (!empty($nextId)) { ?>
			<a href="<?php echo adminUrl('product/operate', ['id' => $nextId]);?>" class="btn btn-info btn-xs">
				下一条 #<?php echo (int)$nextId;?> <i class="fa fa-angles-right"></i>
			</a>
			<?php } ?>
			<a href="<?php echo adminUrl('product/purchaseList');?>" class="btn btn-default btn-xs">
				<i class="fa fa-arrow-left"></i> 返回采购池
			</a>
		</div>
	</div>

	<?php if (empty($info)) { ?>
		<div class="empty-state">
			<i class="fa fa-circle-exclamation"></i>
			<p>产品源数据不存在，请重新上传</p>
		</div>
	<?php } else { ?>
	<form id="operate-form" action="<?php echo adminUrl('product/operate');?>" method="post" enctype="multipart/form-data" onsubmit="return false;">
		<input type="hidden" name="opn" value="addProduct">
		<input type="hidden" name="id" value="<?php echo $operateId;?>">
		<input type="hidden" name="cate_id" value="<?php echo $initCateId;?>">
		<input type="hidden" name="spu_image" value="<?php echo htmlspecialchars((string)$firstSpuImg, ENT_QUOTES, 'UTF-8');?>">
		<input type="hidden" name="submit_mode" id="submit-mode-input" value="save">

		<!-- 1. 基础配置卡片 -->
		<div class="section-card">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-sliders"></i> 基础信息配置</span>
			</div>
			<div class="info-list">
				<div class="info-row">
					<span class="info-label"><strong class="must">*</strong> 产品分类</span>
					<div class="info-val-group">
						<span class="category-name info-text"><?php echo htmlspecialchars((string)$initCateName, ENT_QUOTES, 'UTF-8');?></span>
						<button type="button" class="btn btn-primary btn-xs change-category-btn">选择分类</button>
					</div>
				</div>
			</div>
			<div class="form-group mt10">
				<label class="form-label"><strong class="must">*</strong> 产品名称</label>
				<input type="text" class="form-control" name="spu_name" value="<?php echo htmlspecialchars((string)$savedSpuName, ENT_QUOTES, 'UTF-8');?>" placeholder="请输入SPU产品标题">
			</div>
			<div class="form-group" style="margin-bottom:0;">
				<label class="form-label"><strong class="must">*</strong> 邮费</label>
				<input type="text" class="form-control" name="post_fee" value="<?php echo htmlspecialchars((string)$savedPostFee, ENT_QUOTES, 'UTF-8');?>" placeholder="邮费金额">
			</div>
		</div>

		<!-- 2. 供应商信息卡片 -->
		<div class="section-card supplier-info-content">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-store"></i> 供应商基本信息</span>
			</div>
			<div class="supplier-meta">
				<div class="s-row">
					<span class="s-label">店铺名称：</span>
					<span class="s-val">
						<?php if (!empty($shopInfo['url'])) { ?>
							<a href="https://<?php echo htmlspecialchars((string)$shopInfo['url'], ENT_QUOTES, 'UTF-8');?>" target="_blank">
								<?php echo htmlspecialchars((string)($shopInfo['name'] ?: $shopInfo['url']), ENT_QUOTES, 'UTF-8');?>
								<i class="fa fa-arrow-up-right-from-square"></i>
							</a>
						<?php } else { ?>
							<?php echo htmlspecialchars((string)($shopInfo['name'] ?? '--'), ENT_QUOTES, 'UTF-8');?>
						<?php } ?>
					</span>
				</div>
				<div class="s-row">
					<span class="s-label">源货号：</span>
					<span class="s-val">
						<a href="<?php echo htmlspecialchars((string)($info['url'] ?? 'javascript:;'), ENT_QUOTES, 'UTF-8');?>" target="_blank">
							<?php echo htmlspecialchars((string)(($info['channel_id'] ?? '') . ' - ' . ($info['item_id'] ?? '')), ENT_QUOTES, 'UTF-8');?>
							<i class="fa fa-arrow-up-right-from-square"></i>
						</a>
					</span>
				</div>
			</div>
		</div>

		<!-- 3. 产品图片与视频卡片 -->
		<div class="section-card">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-images"></i> 产品媒体素材</span>
				<ul class="media-tabs hy-sl-list-inline">
					<li class="hy-sl-txt hy-sl-selected" data-id="2500">
						<span>橱窗</span>
						<em class="image-num"><?php echo count($info['pdt_picture'] ?? []);?></em>
					</li>
					<li class="hy-sl-txt" data-id="2510">
						<span>素材</span>
						<em class="image-num"><?php echo count($info['desc_picture'] ?? []);?></em>
					</li>
					<?php if (!empty($info['video'])) { ?>
					<li class="hy-sl-txt" data-id="2520">
						<span>视频</span>
						<em class="image-num">1</em>
					</li>
					<?php } ?>
				</ul>
			</div>

			<div class="spu-image">
				<!-- 橱窗图 -->
				<div class="pic-wrap" data-id="2500">
					<?php foreach ($info['pdt_picture'] ?? [] as $key => $value) {
						$rawImg = trim((string)$value);
						$hasImg = $rawImg !== '' && strpos($rawImg, 'image/common/') === false;
					?>
					<div class="item">
						<div class="pic-thumb">
							<?php if ($hasImg) { ?>
								<img src="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" alt="橱窗图">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
						</div>
						<div class="image-left-tips">
							<label class="image-selected">
								<input type="checkbox" checked name="main_img[]" value="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>">
							</label>
							<?php if ($key == 0) { ?>
								<div class="spu-sign">SPU</div>
							<?php } ?>
							<?php if ($hasImg) { ?>
								<a href="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" target="_blank" class="bigImage" title="查看大图"><i class="fa fa-expand"></i></a>
							<?php } ?>
						</div>
						<div class="num"><?php echo $key + 1;?></div>
						<div class="image-bottom-tips">
							<button class="btn btn-xs btn-primary set-spu-cover" type="button">设SPU</button>
							<button class="btn btn-xs btn-success set-sku-cover" type="button">配SKU</button>
						</div>
					</div>
					<?php } ?>
				</div>

				<!-- 素材图 -->
				<div class="pic-wrap" data-id="2510" style="display: none;">
					<?php foreach ($info['desc_picture'] ?? [] as $key => $value) {
						$rawImg = trim((string)$value);
						$hasImg = $rawImg !== '' && strpos($rawImg, 'image/common/') === false;
					?>
					<div class="item">
						<div class="pic-thumb">
							<?php if ($hasImg) { ?>
								<img src="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" alt="素材图">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
						</div>
						<div class="image-left-tips">
							<label class="image-selected">
								<input type="checkbox" name="desc_img[]" checked value="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>">
							</label>
							<?php if ($hasImg) { ?>
								<a href="<?php echo htmlspecialchars($rawImg, ENT_QUOTES, 'UTF-8');?>" target="_blank" class="bigImage" title="查看大图"><i class="fa fa-expand"></i></a>
							<?php } ?>
						</div>
						<div class="num"><?php echo $key + 1;?></div>
						<div class="image-bottom-tips">
							<button class="btn btn-xs btn-primary set-spu-cover" type="button">设SPU</button>
							<button class="btn btn-xs btn-success set-sku-cover" type="button">配SKU</button>
						</div>
					</div>
					<?php } ?>
				</div>

				<!-- 视频 -->
				<div class="pic-wrap" data-id="2520" style="display: none;">
					<?php if (!empty($info['video'])) { ?>
					<div class="video-item">
						<video controls poster="<?php echo htmlspecialchars((string)($info['video']['img'] ?? ''), ENT_QUOTES, 'UTF-8');?>">
							<source src="<?php echo htmlspecialchars((string)($info['video']['url'] ?? ''), ENT_QUOTES, 'UTF-8');?>" type="video/webm">
						</video>
						<div class="video-check-bar">
							<label class="video-check-label">
								<input type="checkbox" name="video[url]" checked value="<?php echo htmlspecialchars((string)($info['video']['url'] ?? ''), ENT_QUOTES, 'UTF-8');?>"> 启用视频
							</label>
							<label class="video-check-label">
								<input type="checkbox" name="video[img]" <?php echo empty($info['video']['img']) ? '' : 'checked';?> value="<?php echo htmlspecialchars((string)($info['video']['img'] ?? ''), ENT_QUOTES, 'UTF-8');?>"> 启用封面图
							</label>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<!-- 4. 属性映射卡片（零弹窗行内极速工作台） -->
		<div class="section-card attr-info-content">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-tags"></i> 属性极速工作台</span>
				<div class="attr-quick-bar">
					<button type="button" class="btn btn-xs btn-default" id="btn-attr-group-all">全选</button>
					<button type="button" class="btn btn-xs btn-default" id="btn-attr-group-invert">反选</button>
					<button type="button" class="btn btn-xs btn-warning" id="btn-attr-ignore-single">忽略单值</button>
					<button type="button" class="btn btn-xs btn-primary" id="btn-attr-clean-noise">去噪</button>
					<button type="button" class="btn btn-xs btn-success" id="btn-attr-map-raw">全用原值</button>
					<button type="button" class="btn btn-xs btn-info" id="btn-attr-sync-img">同步图</button>
				</div>
			</div>
			<div class="attr-help-tip">
				行内输入框可直接改名并实时同步SKU（已自动剥离促销后缀）；点击 <code>+</code> 可添加衍生多属性。
			</div>
			<div class="attr-rule-box">
				<div class="attr-rule-top attr-rule">
					<select name="attr_rule_target" class="form-control">
						<option value="">全部属性组</option>
						<?php foreach ($info['attr'] ?? [] as $av) { ?>
						<option value="<?php echo htmlspecialchars((string)($av['name'] ?? ''), ENT_QUOTES, 'UTF-8');?>">仅：<?php echo htmlspecialchars((string)($av['name'] ?? ''), ENT_QUOTES, 'UTF-8');?></option>
						<?php } ?>
					</select>
					<input type="text" name="attr_rule_value" class="form-control" placeholder="分隔符或字数">
					<button type="button" class="btn btn-primary btn-xs attr-rule-btn"><i class="fa fa-bolt"></i> 生成</button>
				</div>
				<div class="attr-rule-name">
					<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性1">
					<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性2">
					<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性3">
					<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性4">
				</div>
			</div>

			<div class="attr-map-groups">
				<?php foreach ($info['attr'] ?? [] as $av) {
					$aName = (string)($av['name'] ?? '');
					$hasNameMap = isset($attrNs[$aName]);
					$mappedAttrName = $hasNameMap ? ($attrNs[$aName]['attrn_name'] ?? $attrNs[$aName]['name']) : ($av['clean_name'] ?? $aName);
					$valCount = count($av['value'] ?? []);
					$defaultGroupChecked = isset($av['default_checked']) ? (bool)$av['default_checked'] : true;
					$isGroupChecked = $savedAttrGroups === null ? $defaultGroupChecked : isset($savedAttrGroups[$aName]);
				?>
				<div class="attr-map-row <?php echo $isGroupChecked ? '' : 'attr-group-disabled';?>" data-attr-group="<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>" data-val-count="<?php echo $valCount;?>">
					<div class="attr-row-head">
						<label class="attr-group-check-label">
							<input type="checkbox" class="attr-group-check" name="attr_group_checked[]" value="<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>" <?php echo $isGroupChecked ? 'checked' : '';?>>
							<span>入库</span>
						</label>
						<?php if ($valCount <= 1) { ?>
						<span class="single-attr-badge">单值</span>
						<?php } ?>
						<div class="attr-item attr-name success" data-name="<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>" title="<?php echo htmlspecialchars($mappedAttrName, ENT_QUOTES, 'UTF-8');?>">
							<span><?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?></span>
						</div>
						<input type="text" class="form-control input-sm inline-attr-name" name="attr_name_map[<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>]" data-from-name="<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>" value="<?php echo htmlspecialchars($mappedAttrName, ENT_QUOTES, 'UTF-8');?>" <?php echo $isGroupChecked ? '' : 'disabled';?>>
						<?php if ($valCount > 1) { ?>
						<div class="attr-row-sub-actions">
							<a href="javascript:void(0);" class="attr-val-select-all">全选值</a>
							<span>·</span>
							<a href="javascript:void(0);" class="attr-val-select-invert">反选值</a>
						</div>
						<?php } ?>
					</div>
					<div class="attr-row-vals">
						<?php foreach ($av['value'] ?? [] as $avv) {
							$vName = (string)($avv['name'] ?? '');
							$cleanVName = (string)($avv['clean_name'] ?? $vName);
							$valMapEntry = $attrVs[$vName] ?? ($attrVs[$cleanVName] ?? null);
							$mappedValName = $valMapEntry ? ($valMapEntry['attrv_name'] ?? $valMapEntry['name']) : $cleanVName;
							$extArr = ($valMapEntry && !empty($valMapEntry['ext']) && is_array($valMapEntry['ext'])) ? $valMapEntry['ext'] : [];
							$extJson = !empty($extArr) ? json_encode($extArr, JSON_UNESCAPED_UNICODE) : '';
						?>
						<div class="attr-item attr-value success" data-name="<?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?>" data-mapped="<?php echo htmlspecialchars($mappedValName, ENT_QUOTES, 'UTF-8');?>" data-img="<?php echo htmlspecialchars((string)($avv['img'] ?? ''), ENT_QUOTES, 'UTF-8');?>" data-ext='<?php echo htmlspecialchars((string)$extJson, ENT_QUOTES, 'UTF-8');?>'>
							<input type="checkbox" class="attr-val-check" <?php echo $isGroupChecked ? 'checked' : '';?>>
							<span class="attr-val-text"><?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?></span>
							<span class="inline-arrow">→</span>
							<input type="text" class="form-control input-sm inline-attr-val" name="attr_val_map[<?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?>][main]" data-from-val="<?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?>" value="<?php echo htmlspecialchars($mappedValName, ENT_QUOTES, 'UTF-8');?>" <?php echo $isGroupChecked ? '' : 'disabled';?>>
							<input type="hidden" class="inline-attr-ext" name="attr_val_map[<?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?>][ext]" value='<?php echo htmlspecialchars((string)$extJson, ENT_QUOTES, 'UTF-8');?>' <?php echo $isGroupChecked ? '' : 'disabled';?>>
							<span class="ext-tag-wrap">
								<?php foreach ($extArr as $ek => $ev) { ?>
								<em class="ext-tag">+<?php echo htmlspecialchars($ek . ':' . $ev, ENT_QUOTES, 'UTF-8');?></em>
								<?php } ?>
							</span>
							<span class="btn-open-ext" title="添加衍生属性">+</span>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- 5. 产品SKU配置卡片 -->
		<div class="section-card">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-cubes"></i> 产品SKU (<span id="checked-sku-count">0</span>/<span id="total-sku-count"><?php echo count($info['sku'] ?? []);?></span>)</span>
				<div class="sku-quick-btns">
					<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-all">全选</button>
					<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-invert">反选</button>
					<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-instock">有效库存</button>
					<button type="button" class="btn btn-default btn-xs" id="btn-sku-toggle-unchecked">隐藏未选</button>
				</div>
			</div>
			<!-- 快捷批量设置栏 -->
			<div class="batch-bar">
				<span class="batch-label">批量赋值：</span>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="price">采购价</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="stock">库存</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="unit">单位</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="weight">重量</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="length">长度</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="width">宽度</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="hight">高度</button>
			</div>

			<div id="sku-list" class="sku-operate-list">
				<?php
				$tmpVol = purchase()->spu()->getVolume($info['volume'] ?? '');
				$attrDefaultCheckedMap = [];
				foreach ($info['attr'] ?? [] as $avItem) {
					$attrDefaultCheckedMap[$avItem['name'] ?? ''] = isset($avItem['default_checked']) ? (bool)$avItem['default_checked'] : true;
				}
				foreach ($info['sku'] ?? [] as $key => $value) {
					$pvs = $value['pvs'] ?? [];
					$cleanPvs = $value['clean_pvs'] ?? [];
					$savedRow = $savedSkus[$key] ?? null;
					$defaultRowChecked = isset($value['default_checked']) ? (bool)$value['default_checked'] : ((int)($value['stock'] ?? 0) > 0 && (float)($value['price'] ?? 0) > 0);
					$isRowChecked = $savedSkus ? ($savedRow !== null) : $defaultRowChecked;
					$skuImg = trim((string)($savedRow['img'] ?? ($value['img'] ?? '')));
					$hasSkuImg = $skuImg !== '' && strpos($skuImg, 'image/common/') === false;
					$skuPrice = $savedRow['price'] ?? ($value['price'] ?? '');
					$skuStock = $savedRow['stock'] ?? ($value['stock'] ?? '');
					$skuUnit = (int)($savedRow['unit'] ?? 0);
					$skuWeight = $savedRow['weight'] ?? ($info['weight'] ?? '');
					$skuLength = $savedRow['length'] ?? ($tmpVol['length'] ?: '');
					$skuWidth = $savedRow['width'] ?? ($tmpVol['width'] ?: '');
					$skuHight = $savedRow['hight'] ?? ($tmpVol['height'] ?: '');
					$check = true;
				?>
				<div class="sku-op-card sku-row <?php echo $isRowChecked ? '' : 'sku-row-disabled';?>" data-sku="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>">
					<div class="sku-op-top">
						<div class="sku-op-thumb <?php echo $check ? 'check' : '';?>">
							<?php if ($hasSkuImg) { ?>
								<img src="<?php echo htmlspecialchars($skuImg, ENT_QUOTES, 'UTF-8');?>" alt="SKU">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
							<input type="hidden" class="form-control img" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][img]" value="<?php echo htmlspecialchars($skuImg, ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-op-meta">
							<div class="sku-key-line">
								<label class="sku-row-check-label">
									<input type="checkbox" class="sku-row-check" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][checked]" value="1" <?php echo $isRowChecked ? 'checked' : '';?>>
									<span class="id-badge">源SKU: <?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?></span>
								</label>
							</div>
							<div class="sku-orig-pvs">
								<?php foreach ($pvs as $pk => $pv) { ?>
									<span class="orig-pv-tag"><?php echo htmlspecialchars($pk . ': ' . $pv, ENT_QUOTES, 'UTF-8');?></span>
								<?php } ?>
							</div>
							<div class="sku-mapped-cell">
								<?php foreach ($pvs as $pk => $pv) {
									$cleanPv = $cleanPvs[$pk] ?? $pv;
									$nMapped = isset($attrNs[$pk]);
									$valMapEntry = $attrVs[$pv] ?? ($attrVs[$cleanPv] ?? null);
									$mapAttrN = $nMapped ? ($attrNs[$pk]['attrn_name'] ?? $attrNs[$pk]['name']) : $pk;
									$mapAttrV = $valMapEntry ? ($valMapEntry['attrv_name'] ?? $valMapEntry['name']) : $cleanPv;
									$groupEnabled = $savedAttrGroups === null ? ($attrDefaultCheckedMap[$pk] ?? true) : isset($savedAttrGroups[$pk]);
								?>
								<div class="sku-attr-group <?php echo $groupEnabled ? '' : 'sku-attr-ignored';?>" data-from-name="<?php echo htmlspecialchars((string)$pk, ENT_QUOTES, 'UTF-8');?>" data-from-val="<?php echo htmlspecialchars((string)$pv, ENT_QUOTES, 'UTF-8');?>">
									<p class="mapped-line sku-attr-main">
										<input type="checkbox" class="sku-single-attr-check" <?php echo $groupEnabled ? 'checked' : '';?>>
										<span class="attr-name success" data-name="<?php echo htmlspecialchars((string)$pk, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$mapAttrN, ENT_QUOTES, 'UTF-8');?></span>
										<span>: </span>
										<span class="attr-value success" data-name="<?php echo htmlspecialchars((string)$pv, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$mapAttrV, ENT_QUOTES, 'UTF-8');?></span>
										<input type="hidden" class="sku-attr-input" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][attr][<?php echo htmlspecialchars((string)$mapAttrN, ENT_QUOTES, 'UTF-8');?>]" value="<?php echo htmlspecialchars((string)$mapAttrV, ENT_QUOTES, 'UTF-8');?>" <?php echo $groupEnabled ? '' : 'disabled';?>>
									</p>
									<?php if (!empty($valMapEntry['ext']) && is_array($valMapEntry['ext'])) {
										foreach ($valMapEntry['ext'] as $ek => $ev) { ?>
									<p class="attr-map mapped-line">
										<span class="attr-name success" data-name="<?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?></span>
										<span>: </span>
										<span class="attr-value success" data-name="<?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?></span>
										<input type="hidden" class="sku-attr-input" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][attr][<?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?>]" value="<?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?>" <?php echo $groupEnabled ? '' : 'disabled';?>>
									</p>
									<?php } } ?>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="sku-inputs-grid">
						<div class="sku-field">
							<label>采购价</label>
							<input type="text" class="form-control price" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][price]" value="<?php echo htmlspecialchars((string)$skuPrice, ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>库存</label>
							<input type="text" class="form-control stock" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][stock]" value="<?php echo htmlspecialchars((string)$skuStock, ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>单位</label>
							<select class="form-control unit" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][unit]">
								<option value="0" <?php echo $skuUnit === 0 ? 'selected' : '';?>>--</option>
								<option value="1" <?php echo $skuUnit === 1 ? 'selected' : '';?>>件</option>
								<option value="2" <?php echo $skuUnit === 2 ? 'selected' : '';?>>个</option>
								<option value="3" <?php echo $skuUnit === 3 ? 'selected' : '';?>>套</option>
								<option value="4" <?php echo $skuUnit === 4 ? 'selected' : '';?>>打</option>
								<option value="5" <?php echo $skuUnit === 5 ? 'selected' : '';?>>箱</option>
							</select>
						</div>
						<div class="sku-field">
							<label>重量(g)</label>
							<input type="text" class="form-control weight" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][weight]" placeholder="重量(g)" value="<?php echo htmlspecialchars((string)$skuWeight, ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>长(cm)</label>
							<input type="text" class="form-control length" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][length]" value="<?php echo htmlspecialchars((string)$skuLength, ENT_QUOTES, 'UTF-8');?>" placeholder="长">
						</div>
						<div class="sku-field">
							<label>宽(cm)</label>
							<input type="text" class="form-control width" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][width]" value="<?php echo htmlspecialchars((string)$skuWidth, ENT_QUOTES, 'UTF-8');?>" placeholder="宽">
						</div>
						<div class="sku-field">
							<label>高(cm)</label>
							<input type="text" class="form-control hight" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][hight]" value="<?php echo htmlspecialchars((string)$skuHight, ENT_QUOTES, 'UTF-8');?>" placeholder="高">
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- 6. 描述信息卡片 -->
		<div class="section-card desc-info-content">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-align-left"></i> 产品描述属性 (<span id="desc-checked-count">0</span>/<span id="desc-count"><?php echo count($descList);?></span>)</span>
				<div class="desc-quick-btns">
					<button type="button" class="btn btn-xs btn-warning btn-clean-junk-desc">过滤冗余</button>
					<button type="button" class="btn btn-xs btn-default btn-check-all-desc">全选</button>
					<button type="button" class="btn btn-xs btn-default btn-invert-desc">反选</button>
					<button type="button" class="btn btn-xs btn-success btn-add-desc"><i class="fa fa-plus"></i> 添加</button>
				</div>
			</div>
			<div class="content desc-pair-list">
				<?php foreach ($descList as $value) {
					$isDescChecked = $value['checked'] ?? true;
					$isJunk = !empty($value['is_junk']);
				?>
				<div class="item desc-pair-row <?php echo $isDescChecked ? '' : 'desc-item-disabled';?>" data-junk="<?php echo $isJunk ? '1' : '0';?>">
					<input type="checkbox" class="desc-item-check" <?php echo $isDescChecked ? 'checked' : '';?>>
					<?php if ($isJunk) { ?>
					<span class="junk-desc-badge">冗余</span>
					<?php } ?>
					<input type="text" class="form-control" name="desc[name][]" value="<?php echo htmlspecialchars((string)($value['name'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="属性名" <?php echo $isDescChecked ? '' : 'disabled';?>>
					<span class="pair-sep">:</span>
					<input type="text" class="form-control" name="desc[value][]" value="<?php echo htmlspecialchars((string)($value['value'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="属性值" <?php echo $isDescChecked ? '' : 'disabled';?>>
					<button type="button" class="btn btn-danger btn-xs icon-btn remove-desc-row" title="移除"><i class="fa fa-trash-can"></i></button>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- 提交保存按钮 -->
		<div class="submit-footer-bar">
			<button type="button" class="btn btn-default btn-lg confirm-btn" data-mode="save" data-loading-text="保存中...">
				保存配置
			</button>
			<button type="button" class="btn btn-primary btn-lg confirm-btn" data-mode="save_next" data-loading-text="保存跳转中...">
				保存并下一条
			</button>
			<button type="button" class="btn btn-success btn-lg confirm-btn" data-mode="direct" data-loading-text="入库中...">
				直通入库
			</button>
		</div>
	</form>

	<!-- 分类选择弹窗 -->
	<div class="dealbox hidden change-category-modal">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">分类修改</div>
			<div class="form-horizontal">
				<div class="input-group">
					<span class="input-group-addon"><span>一级品类</span></span>
					<select name="root_category_id" class="form-control">
						<option value="0">请选择品类</option>
						<?php foreach ($cateList ?? [] as $value) {
							if ((int)$value['parent_id'] === 0) { ?>
						<option value="<?php echo (int)$value['cate_id'];?>"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="input-group">
					<span class="input-group-addon"><span>子分类</span></span>
					<select name="category_id" class="form-control">
						<option value="0">请先选择品类</option>
					</select>
				</div>
				<div class="mt20">
					<button type="button" class="btn btn-primary btn-lg btn-block btn-save">保存选择</button>
				</div>
			</div>
		</div>
	</div>

	<!-- 属性映射弹窗 -->
	<div class="dealbox hidden map-modal-box">
		<div class="mask"></div>
		<div class="centerShow">
			<form class="map-modal form-horizontal" onsubmit="return false;">
				<button type="button" class="close" aria-hidden="true">&times;</button>
				<div class="dealbox-title title">属性映射</div>
				<input type="hidden" name="from_name">
				<input type="hidden" name="type">
				<input type="hidden" name="opn" value="attrMap">
				<div class="content"></div>
			</form>
		</div>
	</div>

	<!-- 批量单位/重量/尺寸赋值弹窗 -->
	<div class="dealbox hidden batch-edit-modal">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title title">批量赋值</div>
			<input type="hidden" name="type" value="">
			<div class="content"></div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-lg btn-block btn-save">确认批量赋值</button>
			</div>
		</div>
	</div>

	<!-- SKU主图设置弹窗 -->
	<div class="dealbox hidden sku-modal">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title title">SKU主图设置</div>
			<div class="sku-check-list">
				<?php foreach ($info['sku'] ?? [] as $key => $value) {
					$arr = [];
					foreach ($value['pvs'] ?? [] as $pk => $pv) {
						$arr[] = $pk . ': ' . $pv;
					}
				?>
				<label class="sku-check-item">
					<input type="checkbox" data-sku="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>">
					<span><?php echo htmlspecialchars(!empty($arr) ? implode(' / ', $arr) : '单品默认规格', ENT_QUOTES, 'UTF-8');?></span>
				</label>
				<?php } ?>
			</div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-lg btn-block btn-save">确认配图</button>
			</div>
		</div>
	</div>

	<script>
	const category = <?php echo json_encode($cateList ?? [], JSON_UNESCAPED_UNICODE);?>;
	</script>
	<?php } ?>
</div>
