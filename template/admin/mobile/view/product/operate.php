<?php
$operateId = iget('id/d', 0);
$cateArr = [];
if (!empty($info['cate_id'])) {
	$cateArr = category()->pCate($info['cate_id'], true, true);
}
$initCateId = !empty($info['cate_id']) ? (int)$info['cate_id'] : 0;
$initCateName = !empty($cateArr) ? implode(' - ', array_column($cateArr, 'name')) : '未选择分类';
$firstSpuImg = current($info['pdt_picture'] ?? []) ?: '';
?>
<div class="operate-page" id="add-product-page" data-url="<?php echo adminUrl('product/operate');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-wand-magic-sparkles"></i>
			<span class="cur-page-title">配置采购产品 <?php echo $operateId > 0 ? '#' . $operateId : '';?></span>
		</div>
		<div class="header-actions">
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
		<input type="hidden" name="site_id" value="0">
		<input type="hidden" name="spu_image" value="<?php echo htmlspecialchars((string)$firstSpuImg, ENT_QUOTES, 'UTF-8');?>">

		<!-- 1. 基础配置卡片 -->
		<div class="section-card">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-sliders"></i> 基础信息配置</span>
			</div>
			<div class="info-list">
				<div class="info-row">
					<span class="info-label"><strong class="must">*</strong> 站点</span>
					<div class="info-val-group">
						<span class="site-name info-text">未选择站点</span>
						<button type="button" class="btn btn-primary btn-xs change-site-btn">选择站点</button>
					</div>
				</div>
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
				<input type="text" class="form-control" name="spu_name" value="<?php echo htmlspecialchars((string)($info['name'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="请输入SPU产品标题">
			</div>
			<div class="form-group" style="margin-bottom:0;">
				<label class="form-label"><strong class="must">*</strong> 邮费</label>
				<input type="text" class="form-control" name="post_fee" value="<?php echo htmlspecialchars((string)($info['post_fee'] ?? '0'), ENT_QUOTES, 'UTF-8');?>" placeholder="邮费金额">
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

		<!-- 4. 属性映射卡片 -->
		<div class="section-card attr-info-content">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-tags"></i> 属性映射</span>
				<span class="section-tip">点击红色/绿色标签编辑映射</span>
			</div>
			<div class="attr-rule-box">
				<div class="attr-rule-top attr-rule">
					<input type="text" name="attr_rule_value" class="form-control" placeholder="分割字符长(如:2)">
					<button type="button" class="btn btn-primary btn-xs attr-rule-btn"><i class="fa fa-bolt"></i> 按规则生成</button>
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
				?>
				<div class="attr-map-row">
					<div class="attr-row-head">
						<span class="row-label">属性名：</span>
						<div class="attr-item attr-name <?php echo $hasNameMap ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?>">
							<span><?php echo htmlspecialchars($aName, ENT_QUOTES, 'UTF-8');?></span>
							<i class="fa fa-pen-to-square"></i>
						</div>
					</div>
					<div class="attr-row-vals">
						<?php foreach ($av['value'] ?? [] as $avv) {
							$vName = (string)($avv['name'] ?? '');
							$hasValMap = isset($attrVs[$vName]);
							$extJson = isset($attrVs[$vName]['ext']) ? json_encode($attrVs[$vName]['ext'], JSON_UNESCAPED_UNICODE) : '';
						?>
						<div class="attr-item attr-value <?php echo $hasValMap ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?>" data-ext='<?php echo htmlspecialchars((string)$extJson, ENT_QUOTES, 'UTF-8');?>'>
							<span><?php echo htmlspecialchars($vName, ENT_QUOTES, 'UTF-8');?></span>
							<i class="fa fa-pen-to-square"></i>
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
				<span class="section-title"><i class="fa fa-cubes"></i> 产品SKU (<?php echo count($info['sku'] ?? []);?>)</span>
			</div>
			<!-- 快捷批量设置栏 -->
			<div class="batch-bar">
				<span class="batch-label">批量赋值：</span>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="unit">单位</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="weight">重量</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="length">长度</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="width">宽度</button>
				<button type="button" class="btn btn-default btn-xs batch-btn" data-type="hight">高度</button>
			</div>

			<div id="sku-list" class="sku-operate-list">
				<?php
				$tmpVol = purchase()->spu()->getVolume($info['volume'] ?? '');
				foreach ($info['sku'] ?? [] as $key => $value) {
					$check = true;
					foreach ($value['pvs'] ?? [] as $pk => $pv) {
						if (!isset($attrNs[$pk]) || !isset($attrVs[$pv])) {
							$check = false;
							break;
						}
					}
					$skuImg = trim((string)($value['img'] ?? ''));
					$hasSkuImg = $skuImg !== '' && strpos($skuImg, 'image/common/') === false;
				?>
				<div class="sku-op-card sku-row" data-sku="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>">
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
								<span class="id-badge">源SKU: <?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?></span>
							</div>
							<div class="sku-orig-pvs">
								<?php foreach ($value['pvs'] ?? [] as $pk => $pv) { ?>
									<span class="orig-pv-tag"><?php echo htmlspecialchars($pk . ': ' . $pv, ENT_QUOTES, 'UTF-8');?></span>
								<?php } ?>
							</div>
							<div class="sku-mapped-cell">
								<?php foreach ($value['pvs'] ?? [] as $pk => $pv) { ?>
								<p class="mapped-line">
									<span class="attr-name <?php echo isset($attrNs[$pk]) ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars((string)$pk, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)(isset($attrNs[$pk]) ? $attrNs[$pk]['name'] : $pk), ENT_QUOTES, 'UTF-8');?></span>
									<span>: </span>
									<span class="attr-value <?php echo isset($attrVs[$pv]) ? 'success' : 'error';?>" data-name="<?php echo htmlspecialchars((string)$pv, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)(isset($attrVs[$pv]) ? $attrVs[$pv]['name'] : $pv), ENT_QUOTES, 'UTF-8');?></span>
									<input type="hidden" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][attr][<?php echo htmlspecialchars((string)$pk, ENT_QUOTES, 'UTF-8');?>]" value="<?php echo htmlspecialchars((string)$pv, ENT_QUOTES, 'UTF-8');?>">
								</p>
								<?php if (!empty($attrVs[$pv]['ext'])) {
									foreach ($attrVs[$pv]['ext'] as $ek => $ev) { ?>
								<p class="attr-map mapped-line">
									<span class="attr-name success" data-name="<?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?></span>
									<span>: </span>
									<span class="attr-value success" data-name="<?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?></span>
									<input type="hidden" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][attr][<?php echo htmlspecialchars((string)$ek, ENT_QUOTES, 'UTF-8');?>]" value="<?php echo htmlspecialchars((string)$ev, ENT_QUOTES, 'UTF-8');?>">
								</p>
								<?php } } ?>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="sku-inputs-grid">
						<div class="sku-field">
							<label>采购价</label>
							<input type="text" class="form-control price" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][price]" value="<?php echo htmlspecialchars((string)($value['price'] ?? ''), ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>库存</label>
							<input type="text" class="form-control stock" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][stock]" value="<?php echo htmlspecialchars((string)($value['stock'] ?? ''), ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>单位</label>
							<select class="form-control unit" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][unit]">
								<option value="0" selected>--</option>
								<option value="1">件</option>
								<option value="2">个</option>
								<option value="3">套</option>
								<option value="4">打</option>
								<option value="5">箱</option>
							</select>
						</div>
						<div class="sku-field">
							<label>重量(g)</label>
							<input type="text" class="form-control weight" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][weight]" placeholder="重量(g)" value="<?php echo htmlspecialchars((string)($info['weight'] ?? ''), ENT_QUOTES, 'UTF-8');?>">
						</div>
						<div class="sku-field">
							<label>长(cm)</label>
							<input type="text" class="form-control length" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][length]" value="<?php echo htmlspecialchars((string)($tmpVol['length'] ?: ''), ENT_QUOTES, 'UTF-8');?>" placeholder="长">
						</div>
						<div class="sku-field">
							<label>宽(cm)</label>
							<input type="text" class="form-control width" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][width]" value="<?php echo htmlspecialchars((string)($tmpVol['width'] ?: ''), ENT_QUOTES, 'UTF-8');?>" placeholder="宽">
						</div>
						<div class="sku-field">
							<label>高(cm)</label>
							<input type="text" class="form-control hight" name="sku[<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>][hight]" value="<?php echo htmlspecialchars((string)($tmpVol['height'] ?: ''), ENT_QUOTES, 'UTF-8');?>" placeholder="高">
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- 6. 描述信息卡片 -->
		<div class="section-card desc-info-content">
			<div class="section-header">
				<span class="section-title"><i class="fa fa-align-left"></i> 产品描述属性</span>
				<button type="button" class="btn btn-xs btn-success btn-add-desc"><i class="fa fa-plus"></i> 添加描述</button>
			</div>
			<div class="content desc-pair-list">
				<?php foreach ($info['detail'] ?? [] as $value) { ?>
				<div class="item desc-pair-row">
					<input type="text" class="form-control" name="desc[name][]" value="<?php echo htmlspecialchars((string)($value['name'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="属性名">
					<span class="pair-sep">:</span>
					<input type="text" class="form-control" name="desc[value][]" value="<?php echo htmlspecialchars((string)($value['value'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="属性值">
					<button type="button" class="btn btn-danger btn-xs icon-btn remove-desc-row" title="移除"><i class="fa fa-trash-can"></i></button>
				</div>
				<?php } ?>
			</div>
		</div>

		<!-- 提交保存按钮 -->
		<div class="submit-footer-bar">
			<button type="button" class="btn btn-success btn-lg btn-block confirm-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">
				<i class="fa fa-check"></i> 确认保存产品
			</button>
		</div>
	</form>

	<!-- 分类选择弹窗 -->
	<div class="modal-mask change-category-modal" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">分类修改</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label">一级品类</label>
					<select name="root_category_id" class="form-control">
						<option value="0">请选择品类</option>
						<?php foreach ($cateList ?? [] as $value) {
							if ((int)$value['parent_id'] === 0) { ?>
						<option value="<?php echo (int)$value['cate_id'];?>"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="form-group">
					<label class="form-label">子分类</label>
					<select name="category_id" class="form-control">
						<option value="0">请先选择品类</option>
					</select>
				</div>
				<button type="button" class="btn btn-primary btn-block btn-save">保存选择</button>
			</div>
		</div>
	</div>

	<!-- 站点选择弹窗 -->
	<div class="modal-mask change-site-modal" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">站点修改</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="form-label">所属站点</label>
					<select name="site_id" class="form-control">
						<option value="0">请选择站点</option>
						<?php foreach ($siteList ?? [] as $value) { ?>
						<option value="<?php echo (int)$value['site_id'];?>"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
						<?php } ?>
					</select>
				</div>
				<button type="button" class="btn btn-primary btn-block btn-save">保存选择</button>
			</div>
		</div>
	</div>

	<!-- 属性映射弹窗 -->
	<div class="modal-mask map-modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">属性映射</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form class="map-modal" onsubmit="return false;">
					<input type="hidden" name="from_name">
					<input type="hidden" name="type">
					<input type="hidden" name="opn" value="attrMap">
					<div class="content"></div>
				</form>
			</div>
		</div>
	</div>

	<!-- 批量单位/重量/尺寸赋值弹窗 -->
	<div class="modal-mask batch-edit-modal" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">批量赋值</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<input type="hidden" name="type" value="">
				<div class="content form-group"></div>
				<button type="button" class="btn btn-primary btn-block btn-save">确认批量赋值</button>
			</div>
		</div>
	</div>

	<!-- SKU主图设置弹窗 -->
	<div class="modal-mask sku-modal" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">SKU主图设置</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="sku-check-list">
					<?php foreach ($info['sku'] ?? [] as $key => $value) {
						$arr = [];
						foreach ($value['pvs'] ?? [] as $pk => $pv) {
							$arr[] = $pk . ': ' . $pv;
						}
					?>
					<label class="sku-check-item">
						<input type="checkbox" data-sku="<?php echo htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8');?>">
						<span><?php echo htmlspecialchars(implode(' / ', $arr), ENT_QUOTES, 'UTF-8');?></span>
					</label>
					<?php } ?>
				</div>
				<button type="button" class="btn btn-primary btn-block btn-save">确认配图</button>
			</div>
		</div>
	</div>

	<script>
	const category = <?php echo json_encode($cateList ?? [], JSON_UNESCAPED_UNICODE);?>;
	</script>
	<?php } ?>
</div>
