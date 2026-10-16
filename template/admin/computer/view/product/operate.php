<?php if (!empty($info)) {
	$saveData = $info['save_data'] ?? [];
	$savedCateId = (int)($saveData['cate_id'] ?? ($info['cate_id'] ?? 0));
	$catePathArr = $savedCateId > 0 ? category()->pCate($savedCateId, true, true) : [];
	$savedCateName = !empty($catePathArr) ? implode(' - ', array_column($catePathArr, 'name')) : '';

	$savedSpuName = $saveData['spu_name'] ?? ($info['name'] ?? '');
	$savedPostFee = $saveData['post_fee'] ?? ($info['post_fee'] ?? 0);
	$pdtPictures = $info['pdt_picture'] ?? [];
	$descPictures = $info['desc_picture'] ?? [];
	$savedSpuImage = $saveData['spu_image'] ?? (current($pdtPictures) ?: '');
	$savedMainImgs = isset($saveData['main_img']) && is_array($saveData['main_img']) ? array_flip($saveData['main_img']) : null;
	$savedDescImgs = isset($saveData['desc_img']) && is_array($saveData['desc_img']) ? array_flip($saveData['desc_img']) : null;
	$savedSkus = ($saveData['sku'] ?? []) ?: [];

	$savedAttrGroups = isset($saveData['attr_group_checked']) && is_array($saveData['attr_group_checked']) ? array_flip($saveData['attr_group_checked']) : null;
	$savedAttrNameMap = (!empty($saveData['attr_name_map']) && is_array($saveData['attr_name_map'])) ? $saveData['attr_name_map'] : [];
	$savedAttrValMap = (!empty($saveData['attr_val_map']) && is_array($saveData['attr_val_map'])) ? $saveData['attr_val_map'] : [];

	// 记录默认自动忽略的通用单值属性组
	$attrGroupDefaultOff = [];
	foreach ($info['attr'] ?? [] as $av) {
		if (!empty($av['is_default_single']) && !empty($av['name'])) {
			$attrGroupDefaultOff[$av['name']] = true;
		}
	}

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
		$cleanDesc = [];
		$junkDesc = [];
		foreach ($info['detail'] ?? [] as $origDesc) {
			$isJunk = !empty($origDesc['is_junk']);
			$row = [
				'name'    => $origDesc['name'] ?? '',
				'value'   => $origDesc['value'] ?? '',
				'checked' => !$isJunk,
				'is_junk' => $isJunk,
			];
			if ($isJunk) {
				$junkDesc[] = $row;
			} else {
				$cleanDesc[] = $row;
			}
		}
		$descList = array_merge($cleanDesc, $junkDesc);
	}

	$shopName = $shopInfo['name'] ?? ($info['seller']['shop_name'] ?? '--');
	$shopUrl = $shopInfo['url'] ?? trim(str_replace(['https://', 'http://'], '', $info['seller']['shop_url'] ?? ''), '/');
	$channelName = $channelList[$info['channel_id']] ?? $info['channel_id'];
?>
<div class="container-fluid" id="add-product-page">
	<form action="<?php echo adminUrl('product/operate');?>" method="post" enctype="multipart/form-data">
		<div class="left product-info-content">
			<input type="hidden" name="opn" value="addProduct">
			<input type="hidden" name="id" value="<?php echo (int)$info['purchase_spu_id'];?>">
			<input type="hidden" name="cate_id" value="<?php echo $savedCateId;?>">
			<input type="hidden" name="spu_image" value="<?php echo htmlspecialchars($savedSpuImage);?>">
			<input type="hidden" name="submit_mode" id="submit-mode-input" value="save">

			<div class="panel-card">
				<div class="row-inline">
					<dl class="field-row half-row">
						<dt class="title-line"><strong class="must">*</strong>产品分类：</dt>
						<dd class="item">
							<div class="title-line category-name"><?php echo htmlspecialchars($savedCateName ?: '未选择');?></div>
							<?php if (!empty($info['is_recommended_cate']) && empty($saveData['cate_id'])) {?>
							<span class="label label-success" title="根据同供应商店铺历史入库记录自动匹配">同店推荐</span>
							<?php }?>
							<button type="button" class="btn btn-primary btn-xs change-category-btn">选择分类</button>
							<?php if (!empty($nextId)) {?>
							<a href="<?php echo adminUrl('product/operate', ['id' => $nextId]);?>" class="btn btn-default btn-xs ml10" title="暂不处理当前商品，直接跳到下一条待处理商品">跳过看下一条 (#<?php echo (int)$nextId;?>) &raquo;</a>
							<?php }?>
						</dd>
					</dl>
				</div>
				<div class="row-inline">
					<dl class="field-row name-row">
						<dt class="title-line"><strong class="must">*</strong>产品名称：</dt>
						<dd>
							<input type="text" class="form-control" name="spu_name" value="<?php echo htmlspecialchars($savedSpuName);?>" placeholder="请输入SPU产品标题">
						</dd>
					</dl>
					<dl class="field-row fee-row">
						<dt class="title-line"><strong class="must">*</strong>邮费(元)：</dt>
						<dd>
							<input type="text" class="form-control" name="post_fee" value="<?php echo htmlspecialchars((string)$savedPostFee);?>" placeholder="0">
						</dd>
					</dl>
				</div>
			</div>

			<dl class="field-row panel-card">
				<dt class="title-line"><strong class="must">*</strong>产品图片：</dt>
				<dd>
					<div class="left img-nav-col">
						<ul class="hy-sl-list-inline hy-sl-vertical">
							<li class="hy-sl-txt hy-sl-selected" data-id="2500">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">橱窗</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num"><?php echo count($pdtPictures);?></div>
							</li>
							<li class="hy-sl-txt" data-id="2510">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">素材</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num"><?php echo count($descPictures);?></div>
							</li>
							<?php if (!empty($info['video']['url'])) {?>
							<li class="hy-sl-txt" data-id="2520">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">视频</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num">1</div>
							</li>
							<?php }?>
						</ul>
						<div class="img-batch-btns mt10">
							<button type="button" class="btn btn-default btn-xs btn-block img-check-all">全选当前</button>
							<button type="button" class="btn btn-default btn-xs btn-block img-check-invert" style="margin-top:5px;">反选当前</button>
						</div>
					</div>
					<div class="right spu-image" style="width: calc(100% - 100px);">
						<div class="pic-wrap" data-id="2500">
							<?php if (empty($pdtPictures)) {?>
							<div class="empty-tip">暂无橱窗主图</div>
							<?php } else { foreach ($pdtPictures as $key => $value) {
								$isChecked = $savedMainImgs === null ? true : isset($savedMainImgs[$value]);
								$isSpuCover = ($savedSpuImage !== '' && $savedSpuImage === $value) || ($savedSpuImage === '' && $key === 0);
							?>
							<div class="item">
								<div class="pic-thumb">
									<img draggable="true" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo htmlspecialchars($value);?>" alt="" class="lazyload">
									<div class="cover"></div>
								</div>
								<div class="image-left-tips">
									<div class="bigImage" src="<?php echo htmlspecialchars($value);?>">大</div>
									<span class="image-selected">
										<input type="checkbox" <?php echo $isChecked ? 'checked' : '';?> name="main_img[]" value="<?php echo htmlspecialchars($value);?>">
									</span>
									<?php if ($isSpuCover) {?>
									<div class="spu-sign">SPU</div>
									<?php }?>
								</div>
								<div class="num"><?php echo $key + 1;?></div>
								<div class="image-bottom-tips">
									<button class="btn btn-xs btn-primary set-spu-cover" type="button">SPU</button>
									<button class="btn btn-xs btn-success set-sku-cover" type="button">SKU</button>
								</div>
							</div>
							<?php } }?>
						</div>
						<div class="pic-wrap" data-id="2510" style="display: none;">
							<?php if (empty($descPictures)) {?>
							<div class="empty-tip">暂无详情素材图</div>
							<?php } else { foreach ($descPictures as $key => $value) {
								$isChecked = $savedDescImgs === null ? true : isset($savedDescImgs[$value]);
								$isSpuCover = ($savedSpuImage !== '' && $savedSpuImage === $value);
							?>
							<div class="item">
								<div class="pic-thumb">
									<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo htmlspecialchars($value);?>" alt="" class="lazyload">
									<div class="cover"></div>
								</div>
								<div class="image-left-tips">
									<div class="bigImage" src="<?php echo htmlspecialchars($value);?>">大</div>
									<span class="image-selected">
										<input type="checkbox" name="desc_img[]" <?php echo $isChecked ? 'checked' : '';?> value="<?php echo htmlspecialchars($value);?>">
									</span>
									<?php if ($isSpuCover) {?>
									<div class="spu-sign">SPU</div>
									<?php }?>
								</div>
								<div class="num"><?php echo $key + 1;?></div>
								<div class="image-bottom-tips">
									<button class="btn btn-xs btn-primary set-spu-cover" type="button">SPU</button>
									<button class="btn btn-xs btn-success set-sku-cover" type="button">SKU</button>
								</div>
							</div>
							<?php } }?>
						</div>
						<div class="pic-wrap" data-id="2520" style="display: none;">
							<?php if (!empty($info['video']['url'])) {?>
							<div class="video-item">
								<video controls width="250" poster="<?php echo htmlspecialchars($info['video']['img'] ?? '');?>">
									<source src="<?php echo htmlspecialchars($info['video']['url']);?>" type="video/webm" />
								</video>
								<div class="image-left-tips">
									<span class="image-selected">
										<input type="checkbox" name="video[url]" checked value="<?php echo htmlspecialchars($info['video']['url']);?>" style="margin-right: 10px;" title="视频">
										<input type="checkbox" name="video[img]" <?php echo empty($info['video']['img']) ? '' : 'checked';?> value="<?php echo htmlspecialchars($info['video']['img'] ?? '');?>" title="封面图">
									</span>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
				</dd>
			</dl>

			<dl class="field-row panel-card">
				<dt class="title-line"><strong class="must">*</strong>产品SKU：</dt>
				<dd>
					<div class="sku-toolbar">
						<div class="sku-stats">
							<span>共 <strong id="total-sku-count"><?php echo count($info['sku']);?></strong> 个SKU</span>
							<span class="ml10">已勾选 <strong id="checked-sku-count" class="text-primary">0</strong> 个</span>
							<span class="ml10">有效属性已映射 <strong id="mapped-sku-count" class="text-success">0</strong> 个</span>
						</div>
						<div class="sku-actions">
							<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-all">全选</button>
							<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-invert">反选</button>
							<button type="button" class="btn btn-default btn-xs" id="btn-sku-select-instock">仅选有效/有库存</button>
							<button type="button" class="btn btn-default btn-xs" id="btn-sku-toggle-unchecked">隐藏未选SKU</button>
						</div>
					</div>
					<div class="sku-table-container">
						<table class="table table-bordered table-hover" id="sku-list">
							<thead>
								<tr>
									<th width="36" class="tc"><input type="checkbox" id="sku-check-all" checked title="全选/取消全选"></th>
									<th width="64">封面图</th>
									<th width="82" class="batch-btn" data-type="price">
										<span title="点击批量修改采购价">采购价 <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="170">供应商SKU</th>
									<th width="200">入库属性(可单选)</th>
									<th width="74" class="batch-btn" data-type="stock">
										<span title="点击批量修改库存">库存 <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="72" class="batch-btn" data-type="unit">
										<span title="点击批量修改单位">单位 <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="74" class="batch-btn" data-type="weight">
										<span title="点击批量修改重量">重量(g) <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="72" class="batch-btn" data-type="length">
										<span title="点击批量修改长度">长度(cm) <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="72" class="batch-btn" data-type="width">
										<span title="点击批量修改宽度">宽度(cm) <i class="glyphicon glyphicon-edit"></i></span>
									</th>
									<th width="72" class="batch-btn" data-type="hight">
										<span title="点击批量修改高度">高度(cm) <i class="glyphicon glyphicon-edit"></i></span>
									</th>
								</tr>
								<tr class="sku-batch-row" title="在此行输入将直接批量同步到所有已勾选的SKU">
									<td colspan="2" class="tc text-primary" style="font-size:11px;font-weight:600;vertical-align:middle;">快捷填充 ↓</td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="price" placeholder="统一价"></td>
									<td colspan="2" class="text-muted" style="font-size:11px;vertical-align:middle;">右侧直接输入或回车即可同步填充已勾选SKU</td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="stock" placeholder="统一库存"></td>
									<td>
										<select class="form-control input-sm batch-inline-input" data-type="unit">
											<option value="">单位</option>
											<option value="0">--</option>
											<option value="1">件</option>
											<option value="2">个</option>
											<option value="3">套</option>
											<option value="4">打</option>
											<option value="5">箱</option>
										</select>
									</td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="weight" placeholder="统一重"></td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="length" placeholder="统一长"></td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="width" placeholder="统一宽"></td>
									<td><input type="text" class="form-control input-sm batch-inline-input" data-type="hight" placeholder="统一高"></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$defaultVol = purchase()->spu()->getVolume($info['volume'] ?? '');
								foreach ($info['sku'] as $key => $value) {
									$pvs = $value['pvs'] ?? [];
									$savedRow = $savedSkus[$key] ?? null;
									$isJunkSku = !empty($value['is_junk']);
									$isRowChecked = $savedSkus ? ($savedRow !== null) : !$isJunkSku;
									$skuImg = $savedRow['img'] ?? ($value['img'] ?? '');
									$skuPrice = $savedRow['price'] ?? ($value['price'] ?? 0);
									$skuStock = $savedRow['stock'] ?? ($value['stock'] ?? 0);
									$skuUnit = (int)($savedRow['unit'] ?? 0);
									$skuWeight = $savedRow['weight'] ?? ($info['weight'] ?? '');
									$skuLength = $savedRow['length'] ?? ($defaultVol['length'] ?: '');
									$skuWidth = $savedRow['width'] ?? ($defaultVol['width'] ?: '');
									$skuHight = $savedRow['hight'] ?? ($defaultVol['height'] ?: '');

									$check = true;
									foreach ($pvs as $pk => $pv) {
										$groupEnabled = $savedAttrGroups === null ? empty($attrGroupDefaultOff[$pk]) : isset($savedAttrGroups[$pk]);
										$pvClean = purchase()->spu()->cleanAttrText((string)$pv);
										$hasN = isset($attrNs[$pk]) || isset($savedAttrNameMap[$pk]);
										$hasV = isset($attrVs[$pv]) || isset($attrVs[$pvClean]) || isset($savedAttrValMap[$pv]['main']);
										if ($groupEnabled && (!$hasN || !$hasV)) {
											$check = false;
											break;
										}
									}
								?>
								<tr data-sku="<?php echo htmlspecialchars((string)$key);?>" data-junk="<?php echo $isJunkSku ? '1' : '0';?>" class="<?php echo $isRowChecked ? '' : 'sku-row-disabled';?>">
									<td class="tc">
										<input type="checkbox" class="sku-row-check" name="sku[<?php echo htmlspecialchars((string)$key);?>][checked]" value="1" <?php echo $isRowChecked ? 'checked' : '';?>>
									</td>
									<td class="sku-img-cell <?php echo $check ? 'check' : '';?>">
										<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo htmlspecialchars($skuImg ?: siteUrl('image/common/noimg.svg'));?>" alt="" class="lazyload bigImage">
										<input type="hidden" class="form-control img" name="sku[<?php echo htmlspecialchars((string)$key);?>][img]" value="<?php echo htmlspecialchars($skuImg);?>">
									</td>
									<td>
										<input type="text" class="form-control price" name="sku[<?php echo htmlspecialchars((string)$key);?>][price]" value="<?php echo htmlspecialchars((string)$skuPrice);?>">
									</td>
									<td class="supplier-sku-cell">
										<div title="SKU属性">
											<?php if (empty($pvs)) {?>
											<p class="text-muted">单品默认规格</p>
											<?php } else { foreach ($pvs as $pk => $pv) {?>
											<p><span class="text-muted"><?php echo htmlspecialchars($pk);?>:</span> <?php echo htmlspecialchars($pv);?></p>
											<?php } }?>
										</div>
										<p class="sku-id-text" title="SKUID">ID: <?php echo htmlspecialchars((string)$key);?></p>
									</td>
									<td class="mapped-attr-cell">
										<?php foreach ($pvs as $pk => $pv) {
											$pvClean = purchase()->spu()->cleanAttrText((string)$pv);
											$nMapped = isset($attrNs[$pk]) || !empty($savedAttrNameMap[$pk]);
											$vMapInfo = $attrVs[$pv] ?? ($attrVs[$pvClean] ?? null);
											$vMapped = ($vMapInfo !== null) || !empty($savedAttrValMap[$pv]['main']);
											$mapAttrN = $savedAttrNameMap[$pk] ?? ($nMapped ? ($attrNs[$pk]['attrn_name'] ?? $attrNs[$pk]['name']) : $pk);
											$mapAttrV = $savedAttrValMap[$pv]['main'] ?? ($vMapInfo ? ($vMapInfo['attrv_name'] ?? $vMapInfo['name']) : ($pvClean ?: $pv));
											$extArr = [];
											if (!empty($savedAttrValMap[$pv]['ext'])) {
												$extArr = is_string($savedAttrValMap[$pv]['ext']) ? (json_decode($savedAttrValMap[$pv]['ext'], true) ?: []) : $savedAttrValMap[$pv]['ext'];
											} elseif (!empty($vMapInfo['ext']) && is_array($vMapInfo['ext'])) {
												$extArr = $vMapInfo['ext'];
											}
											$groupEnabled = $savedAttrGroups === null ? empty($attrGroupDefaultOff[$pk]) : isset($savedAttrGroups[$pk]);
										?>
										<div class="sku-attr-group <?php echo $groupEnabled ? '' : 'sku-attr-ignored';?>" data-from-name="<?php echo htmlspecialchars($pk);?>" data-from-val="<?php echo htmlspecialchars($pv);?>">
											<p class="sku-attr-main">
												<input type="checkbox" class="sku-single-attr-check" <?php echo $groupEnabled ? 'checked' : '';?> title="勾选入库该属性，取消则忽略">
												<span class="attr-name <?php echo $nMapped ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($pk);?>"><?php echo htmlspecialchars($mapAttrN);?></span>
												<span>: </span>
												<span class="attr-value <?php echo $vMapped ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($pv);?>"><?php echo htmlspecialchars($mapAttrV);?></span>
												<input type="hidden" class="sku-attr-input" name="sku[<?php echo htmlspecialchars((string)$key);?>][attr][<?php echo htmlspecialchars($mapAttrN);?>]" value="<?php echo htmlspecialchars($mapAttrV);?>" <?php echo $groupEnabled ? '' : 'disabled';?>>
											</p>
											<?php if (!empty($extArr) && is_array($extArr)) {?>
											<?php foreach ($extArr as $ek => $ev) {?>
											<p class="attr-map">
												<span class="attr-name success" data-name="<?php echo htmlspecialchars($ek);?>"><?php echo htmlspecialchars($ek);?></span>
												<span>: </span>
												<span class="attr-value success" data-name="<?php echo htmlspecialchars($ev);?>"><?php echo htmlspecialchars($ev);?></span>
												<input type="hidden" class="sku-attr-input" name="sku[<?php echo htmlspecialchars((string)$key);?>][attr][<?php echo htmlspecialchars($ek);?>]" value="<?php echo htmlspecialchars($ev);?>" <?php echo $groupEnabled ? '' : 'disabled';?>>
											</p>
											<?php }?>
											<?php }?>
										</div>
										<?php }?>
									</td>
									<td>
										<input type="text" class="form-control stock" name="sku[<?php echo htmlspecialchars((string)$key);?>][stock]" value="<?php echo htmlspecialchars((string)$skuStock);?>">
									</td>
									<td>
										<select class="form-control unit" name="sku[<?php echo htmlspecialchars((string)$key);?>][unit]">
											<option value="0" <?php echo $skuUnit === 0 ? 'selected' : '';?>>--</option>
											<option value="1" <?php echo $skuUnit === 1 ? 'selected' : '';?>>件</option>
											<option value="2" <?php echo $skuUnit === 2 ? 'selected' : '';?>>个</option>
											<option value="3" <?php echo $skuUnit === 3 ? 'selected' : '';?>>套</option>
											<option value="4" <?php echo $skuUnit === 4 ? 'selected' : '';?>>打</option>
											<option value="5" <?php echo $skuUnit === 5 ? 'selected' : '';?>>箱</option>
										</select>
									</td>
									<td>
										<input type="text" class="form-control weight" name="sku[<?php echo htmlspecialchars((string)$key);?>][weight]" placeholder="重量(g)" value="<?php echo htmlspecialchars((string)$skuWeight);?>">
									</td>
									<td>
										<input type="text" class="form-control length" name="sku[<?php echo htmlspecialchars((string)$key);?>][length]" value="<?php echo htmlspecialchars((string)$skuLength);?>" placeholder="长度(cm)">
									</td>
									<td>
										<input type="text" class="form-control width" name="sku[<?php echo htmlspecialchars((string)$key);?>][width]" value="<?php echo htmlspecialchars((string)$skuWidth);?>" placeholder="宽度(cm)">
									</td>
									<td>
										<input type="text" class="form-control hight" name="sku[<?php echo htmlspecialchars((string)$key);?>][hight]" value="<?php echo htmlspecialchars((string)$skuHight);?>" placeholder="高度(cm)">
									</td>
								</tr>
								<?php }?>
							</tbody>
						</table>
					</div>
				</dd>
			</dl>
			<div class="clear"></div>
			<div class="submit-bar">
				<button type="button" class="btn btn-default btn-lg confirm-btn" data-mode="save">
					<i class="glyphicon glyphicon-floppy-disk"></i> 保存配置并返回
				</button>
				<button type="button" class="btn btn-primary btn-lg confirm-btn" data-mode="save_next" title="快捷键: Ctrl + Enter">
					<i class="glyphicon glyphicon-forward"></i> 保存并处理下一条<?php echo !empty($nextId) ? ' (#' . (int)$nextId . ')' : '';?>
				</button>
				<button type="button" class="btn btn-success btn-lg confirm-btn" data-mode="direct" title="立即下载图片并写入正式SPU/SKU表">
					<i class="glyphicon glyphicon-flash"></i> 立即直通入库
				</button>
			</div>
		</div>

		<div class="left info-content">
			<div class="supplier-info-content side-card">
				<h4>供应商基本信息</h4>
				<dl class="field-row">
					<dt class="title-line">店铺：</dt>
					<dd>
						<?php if ($shopUrl) {?>
						<a href="https://<?php echo htmlspecialchars($shopUrl);?>" target="_blank"><?php echo htmlspecialchars($shopName);?> <i class="glyphicon glyphicon-link"></i></a>
						<?php } else {?>
						<span><?php echo htmlspecialchars($shopName);?></span>
						<?php }?>
					</dd>
				</dl>
				<?php if (!empty($shopInfo['info'])) {?>
				<dl class="field-row">
					<dt class="title-line">评分：</dt>
					<dd><?php echo purchase()->shop()->shopInfo($shopInfo, false);?></dd>
				</dl>
				<?php }?>
				<dl class="field-row">
					<dt class="title-line">货源：</dt>
					<dd>
						<a href="<?php echo htmlspecialchars($info['url']);?>" target="_blank">
							<span class="label label-info"><?php echo htmlspecialchars((string)$channelName);?></span>
							<?php echo htmlspecialchars((string)$info['item_id']);?>
							<i class="glyphicon glyphicon-link"></i>
						</a>
					</dd>
				</dl>
				<dl class="field-row">
					<dt class="title-line">状态：</dt>
					<dd>
						<?php $statusList = purchase()->spu()->getStatusList();?>
						<span class="label <?php echo $info['status'] == 3 ? 'label-success' : 'label-primary';?>">
							<?php echo htmlspecialchars($statusList[$info['status']] ?? '已上传');?>
						</span>
						<?php if (!empty($info['price']) && $info['price'] > 0) {?>
						<span class="ml10 text-danger">最低价: ¥<?php echo htmlspecialchars((string)$info['price']);?></span>
						<?php }?>
					</dd>
				</dl>
			</div>

			<?php if (!empty($info['attr'])) {?>
			<div class="attr-info-content side-card mt12">
				<h4>
					规格属性工作台
					<div class="pull-right attr-quick-bar">
						<button type="button" class="btn btn-xs btn-default" id="btn-attr-group-all" title="全选所有属性维度与属性值">全选</button>
						<button type="button" class="btn btn-xs btn-default" id="btn-attr-group-invert" title="反选属性维度">反选</button>
						<button type="button" class="btn btn-xs btn-warning" id="btn-attr-ignore-single" title="一键忽略只有单个属性值的属性维度">忽略单值</button>
						<button type="button" class="btn btn-xs btn-primary" id="btn-attr-clean-noise" title="一键去除属性值中的【现货】【包邮】等促销噪音词">去噪</button>
						<button type="button" class="btn btn-xs btn-success" id="btn-attr-map-raw" title="将未映射的属性一键确认使用当前输入框的值">一键确认</button>
						<button type="button" class="btn btn-xs btn-info" id="btn-attr-sync-img" title="将属性值自带图片同步到对应SKU主图">同步图</button>
					</div>
				</h4>
				<div class="attr-help-tip">
					<i class="glyphicon glyphicon-flash"></i> <strong>零弹窗内联模式</strong>：直接在输入框改字即可实时同步左侧 SKU（按 <code>Enter</code> 跳下一项），保存时自动记入词典；点击 <code>+</code> 可配衍生属性。
				</div>
				<table class="table table-bordered">
					<tr class="attr-rule">
						<td width="125">规则拆分:</td>
						<td>
							<div class="rule-line">
								<select name="attr_rule_target" class="form-control input-sm" style="margin-bottom:5px;">
									<option value="">应用到：全部属性组</option>
									<?php foreach ($info['attr'] as $ak => $av) {?>
									<option value="<?php echo htmlspecialchars($av['name']);?>">仅拆分：<?php echo htmlspecialchars($av['name']);?></option>
									<?php }?>
								</select>
								<input type="text" name="attr_rule_value" class="form-control input-sm" placeholder="分隔符(如 | 或 空格) 或 固定字数">
							</div>
						</td>
					</tr>
					<tr class="attr-rule">
						<td>
							目标属性
							<button type="button" class="btn btn-primary btn-xs attr-rule-btn mt6">一键拆分</button>
						</td>
						<td class="attr-rule-name">
							<input type="text" class="form-control input-sm" name="attr_rule_name[]" placeholder="属性1 (主属性)">
							<input type="text" class="form-control input-sm" name="attr_rule_name[]" placeholder="属性2 (衍生)">
							<input type="text" class="form-control input-sm" name="attr_rule_name[]" placeholder="属性3 (衍生)">
							<input type="text" class="form-control input-sm" name="attr_rule_name[]" placeholder="属性4 (衍生)">
						</td>
					</tr>
					<?php foreach ($info['attr'] as $ak => $av) {
						$rawGroupName = (string)($av['name'] ?? '');
						$nMapped = isset($attrNs[$rawGroupName]) || !empty($savedAttrNameMap[$rawGroupName]);
						$mappedAttrName = $savedAttrNameMap[$rawGroupName] ?? ($nMapped ? ($attrNs[$rawGroupName]['attrn_name'] ?? $attrNs[$rawGroupName]['name']) : $rawGroupName);
						$valCount = count($av['value'] ?? []);
						$isGroupChecked = $savedAttrGroups === null ? empty($av['is_default_single']) : isset($savedAttrGroups[$rawGroupName]);
					?>
					<tr data-attr-group="<?php echo htmlspecialchars($rawGroupName);?>" data-val-count="<?php echo $valCount;?>" class="<?php echo $isGroupChecked ? '' : 'attr-group-disabled';?>">
						<td>
							<div class="attr-group-head">
								<label class="attr-group-check-label" title="勾选则将该属性维度入库，取消则不入库该属性">
									<input type="checkbox" class="attr-group-check" name="attr_group_checked[]" value="<?php echo htmlspecialchars($rawGroupName);?>" <?php echo $isGroupChecked ? 'checked' : '';?>>
									<span>入库</span>
								</label>
								<?php if ($valCount <= 1) {?>
								<span class="single-attr-badge" title="该属性仅单个值，可自由选择入库或忽略">单值</span>
								<?php }?>
							</div>
							<div class="inline-attr-name-box">
								<div class="attr-item attr-name <?php echo $nMapped ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($rawGroupName);?>" title="点击可弹窗编辑: <?php echo htmlspecialchars($mappedAttrName);?>">
									<span><?php echo htmlspecialchars($rawGroupName);?></span>
									<span class="glyphicon glyphicon-edit"></span>
								</div>
								<input type="text" class="form-control input-sm inline-attr-name" name="attr_name_map[<?php echo htmlspecialchars($rawGroupName);?>]" data-from-name="<?php echo htmlspecialchars($rawGroupName);?>" value="<?php echo htmlspecialchars($mappedAttrName);?>" placeholder="标准属性名">
							</div>
							<?php if ($valCount > 1) {?>
							<div class="attr-row-sub-actions">
								<a href="javascript:void(0);" class="attr-val-select-all">全选值</a>
								<span>·</span>
								<a href="javascript:void(0);" class="attr-val-select-invert">反选值</a>
							</div>
							<?php }?>
						</td>
						<td>
							<div class="attr-val-inline-list">
							<?php foreach ($av['value'] ?? [] as $avk => $avv) {
								$vName = (string)($avv['name'] ?? '');
								$vClean = (string)($avv['clean_name'] ?? $vName);
								$vMapInfo = $attrVs[$vName] ?? ($attrVs[$vClean] ?? null);
								$vMapped = ($vMapInfo !== null) || !empty($savedAttrValMap[$vName]['main']);
								$mappedValName = $savedAttrValMap[$vName]['main'] ?? ($vMapInfo ? ($vMapInfo['attrv_name'] ?? $vMapInfo['name']) : $vClean);
								$extData = [];
								if (!empty($savedAttrValMap[$vName]['ext'])) {
									$extData = is_string($savedAttrValMap[$vName]['ext']) ? (json_decode($savedAttrValMap[$vName]['ext'], true) ?: []) : $savedAttrValMap[$vName]['ext'];
								} elseif (!empty($vMapInfo['ext']) && is_array($vMapInfo['ext'])) {
									$extData = $vMapInfo['ext'];
								}
								$extJson = !empty($extData) ? json_encode($extData, JSON_UNESCAPED_UNICODE) : '';
							?>
							<div class="attr-item attr-value <?php echo $vMapped ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($vName);?>" data-clean="<?php echo htmlspecialchars($vClean);?>" data-mapped="<?php echo htmlspecialchars($mappedValName);?>" data-img="<?php echo htmlspecialchars($avv['img'] ?? '');?>" data-ext="<?php echo htmlspecialchars($extJson, ENT_QUOTES, 'UTF-8');?>" title="<?php echo htmlspecialchars($vName);?>">
								<input type="checkbox" class="attr-val-check" <?php echo $isGroupChecked ? 'checked' : '';?> title="勾选/取消该属性值">
								<?php if (!empty($avv['img'])) {?>
								<img src="<?php echo htmlspecialchars($avv['img']);?>" class="attr-mini-img bigImage" data-src="<?php echo htmlspecialchars($avv['img']);?>">
								<?php }?>
								<span class="attr-val-text" title="原值: <?php echo htmlspecialchars($vName);?> (点击弹窗高级配置)"><?php echo htmlspecialchars($vName);?></span>
								<span class="inline-arrow">→</span>
								<input type="text" class="form-control input-sm inline-attr-val" name="attr_val_map[<?php echo htmlspecialchars($vName);?>][main]" data-from-val="<?php echo htmlspecialchars($vName);?>" value="<?php echo htmlspecialchars($mappedValName);?>" placeholder="映射值">
								<input type="hidden" class="inline-attr-ext" name="attr_val_map[<?php echo htmlspecialchars($vName);?>][ext]" value="<?php echo htmlspecialchars($extJson, ENT_QUOTES, 'UTF-8');?>">
								<span class="ext-tag-wrap">
									<?php foreach ($extData as $ek => $ev) {?>
									<em class="mapped-tag">+<?php echo htmlspecialchars($ek . ':' . $ev);?></em>
									<?php }?>
								</span>
								<span class="glyphicon glyphicon-plus-sign btn-open-ext" title="添加/编辑衍生属性"></span>
							</div>
							<?php }?>
							</div>
						</td>
					</tr>
					<?php }?>
				</table>
			</div>
			<?php }?>

			<div class="desc-info-content side-card mt12">
				<h4>
					描述属性 (<span id="desc-checked-count">0</span>/<span id="desc-count"><?php echo count($descList);?></span>)
					<div class="pull-right">
						<button type="button" class="btn btn-xs btn-default btn-check-all-desc">全选</button>
						<button type="button" class="btn btn-xs btn-default btn-invert-desc">反选</button>
						<button type="button" class="btn btn-xs btn-warning btn-clean-junk-desc" title="自动取消勾选货号/品牌/跨境专供等冗余属性">过滤冗余</button>
						<button type="button" class="btn btn-xs btn-success btn-add-desc"><i class="glyphicon glyphicon-plus"></i> 添加</button>
						<button type="button" class="btn btn-xs btn-danger btn-clear-desc">清空</button>
					</div>
				</h4>
				<div class="content">
					<?php foreach ($descList as $value) {
						$isDescChecked = $value['checked'] ?? true;
						$isJunkDesc = !empty($value['is_junk']);
					?>
					<div class="item <?php echo $isDescChecked ? '' : 'desc-item-disabled';?>" data-junk="<?php echo $isJunkDesc ? '1' : '0';?>">
						<input type="checkbox" class="desc-item-check" <?php echo $isDescChecked ? 'checked' : '';?> title="勾选入库该描述属性">
						<input type="text" class="form-control input-sm" name="desc[name][]" value="<?php echo htmlspecialchars($value['name'] ?? '');?>" placeholder="属性名" <?php echo $isDescChecked ? '' : 'disabled';?>>
						<span>: </span>
						<input type="text" class="form-control input-sm" name="desc[value][]" value="<?php echo htmlspecialchars($value['value'] ?? '');?>" placeholder="属性值" <?php echo $isDescChecked ? '' : 'disabled';?>>
						<?php if ($isJunkDesc) {?>
						<span class="junk-desc-badge" title="系统识别为1688/淘宝冗余描述，已默认不选">冗余</span>
						<?php }?>
						<i class="glyphicon glyphicon-remove" title="删除"></i>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- 分类选择 -->
<div class="dealbox hidden change-category-modal">
	<div class="mask"></div>
	<div class="centerShow">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">分类修改</div>
		<div class="form-horizontal">
			<div class="input-group">
				<div class="input-group-addon"><span>品类：</span></div>
				<select name="root_category_id" class="form-control">
					<option value="0">请选择品类</option>
					<?php foreach ($cateList as $value) {
						if ($value['parent_id'] == 0) {?>
					<option value="<?php echo $value['cate_id'];?>"><?php echo htmlspecialchars($value['name']);?></option>
					<?php } }?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>分类：</span></div>
				<select name="category_id" class="form-control">
					<option value="0">请先选择品类</option>
				</select>
			</div>
		</div>
		<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
	</div>
</div>

<!-- 属性映射弹窗 -->
<div class="dealbox hidden map-modal">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal" onsubmit="return false;">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">属性映射</div>
			<input type="hidden" name="from_name">
			<input type="hidden" name="type">
			<input type="hidden" name="opn" value="attrMap">
			<div class="content"></div>
		</form>
	</div>
</div>

<!-- 批量赋值弹窗 -->
<div class="dealbox hidden batch-edit-modal">
	<div class="mask"></div>
	<div class="centerShow">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">批量设置</div>
		<input type="hidden" name="type" value="">
		<div class="content"></div>
		<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
	</div>
</div>

<!-- SKU主图选择弹窗 -->
<div class="dealbox hidden sku-modal">
	<div class="mask"></div>
	<div class="centerShow">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">SKU主图设置</div>
		<div class="content">
			<div class="sku-modal-toolbar">
				<button type="button" class="btn btn-default btn-xs" id="sku-modal-all">全选</button>
				<button type="button" class="btn btn-default btn-xs" id="sku-modal-invert">反选</button>
			</div>
			<div class="form-horizontal sku-modal-list">
				<?php foreach ($info['sku'] as $key => $value) {
					$arr = [];
					foreach ($value['pvs'] ?? [] as $pk => $pv) {
						$arr[] = htmlspecialchars($pk . ': ' . $pv);
					}
				?>
				<div class="checkbox">
					<label>
						<input type="checkbox" data-sku="<?php echo htmlspecialchars((string)$key);?>">
						<?php echo !empty($arr) ? implode('<br />', $arr) : '单品默认规格';?>
					</label>
				</div>
				<?php }?>
			</div>
			<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
		</div>
	</div>
</div>
<script>
const category = <?php echo json_encode($cateList, JSON_UNESCAPED_UNICODE);?>;
</script>
<?php }?>