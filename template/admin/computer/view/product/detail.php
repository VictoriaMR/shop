<div class="container-fluid detail-page" data-id="<?php echo $info['spu_id'] ?? 0;?>" data-post-fee="<?php echo (float)($info['data']['post_fee'] ?? 0);?>">
	<?php if (empty($info)) { ?>
	<div class="empty-state mt20">
		<i class="glyphicon glyphicon-exclamation-sign"></i>
		<p>找不到该 SPU 产品数据</p>
		<a href="<?php echo adminUrl('product/index');?>" class="btn btn-primary btn-sm mt10">返回 SPU 列表</a>
	</div>
	<?php } else {
		$skuCount = count($info['sku'] ?? []);
		$activeSkuCount = 0;
		$totalStock = 0;
		foreach ($info['sku'] ?? [] as $sItem) {
			if ((int)$sItem['status'] === 1) {
				$activeSkuCount++;
			}
			$totalStock += (int)($sItem['stock'] ?? 0);
		}
		$imageCount = count($info['image'] ?? []);
		$introCount = count($info['introduce'] ?? []);
		$descCount = 0;
		foreach ($info['desc'] ?? [] as $dGroup) {
			$descCount += count($dGroup['list'] ?? []);
		}
		$catePathStr = implode(' > ', array_column($info['category'] ?? [], 'name')) ?: '--';
		$postFee = (float)($info['data']['post_fee'] ?? 0);
	?>
	<!-- 顶部 Sticky 概览与导航栏 -->
	<div class="detail-sticky-bar">
		<div class="sticky-left">
			<a href="<?php echo adminUrl('product/index');?>" class="btn btn-default btn-xs" title="返回SPU列表">
				<i class="glyphicon glyphicon-menu-left"></i> 列表
			</a>
			<span class="spu-pid-tag">PID #<?php echo $info['spu_id'];?></span>
			<span class="spu-status-badge status-<?php echo (int)$info['status'];?> j-show-spu-status" data-status="<?php echo (int)$info['status'];?>">
				<?php echo htmlspecialchars($statusList[$info['status']] ?? '--');?>
			</span>
			<button type="button" class="btn <?php echo $info['status'] == 1 ? 'btn-warning' : 'btn-success';?> btn-xs j-quick-toggle-spu-status" data-status="<?php echo (int)$info['status'];?>">
				<?php echo $info['status'] == 1 ? '一键下架' : '一键上架';?>
			</button>
			<span class="spu-price-pill">
				售价：<strong id="spu-price-range-text">￥<?php echo $info['min_price'] < $info['max_price'] ? $info['min_price'] . ' ~ ￥' . $info['max_price'] : $info['min_price'];?></strong>
			</span>
		</div>
		<div class="sticky-nav">
			<a href="#sec-base" class="sec-nav-link">基础与货源</a>
			<a href="#sec-images" class="sec-nav-link">主图 (<?php echo $imageCount;?>)</a>
			<a href="#sec-attrs" class="sec-nav-link">属性规格 (<?php echo count($info['attr_map'] ?? []);?>)</a>
			<a href="#sec-skus" class="sec-nav-link">SKU矩阵 (<?php echo $skuCount;?>)</a>
			<a href="#sec-desc" class="sec-nav-link">描述与详情图 (<?php echo $descCount;?>/<?php echo $introCount;?>)</a>
		</div>
		<div class="sticky-right">
			<?php if (!empty($info['purchase_spu_id'])) { ?>
			<a href="<?php echo adminUrl('product/operate', ['id' => $info['purchase_spu_id']]);?>" class="btn btn-info btn-xs" title="前往采购入库工作台重新调整入库配置">
				<i class="glyphicon glyphicon-retweet"></i> 入库配置台
			</a>
			<?php } ?>
			<?php if (!empty($info['data']['item_url'])) { ?>
			<a href="<?php echo htmlspecialchars($info['data']['item_url']);?>" target="_blank" class="btn btn-default btn-xs">
				<i class="glyphicon glyphicon-link"></i> 货源原链接
			</a>
			<?php } ?>
		</div>
	</div>

	<!-- 第一区：SPU 核心配置 & 供应商货源档案 -->
	<div id="sec-base" class="detail-grid-2col">
		<!-- 左卡：SPU 核心信息 -->
		<div class="detail-card">
			<div class="detail-card-header">
				<span class="card-title"><i class="glyphicon glyphicon-tag"></i> SPU 核心配置</span>
				<div class="card-actions">
					<button type="button" class="btn btn-default btn-xs status-btn"><i class="glyphicon glyphicon-cog"></i> 状态设置</button>
					<button type="button" class="btn btn-primary btn-xs name-trans-btn"><i class="glyphicon glyphicon-globe"></i> 多语言与标题</button>
				</div>
			</div>
			<div class="detail-card-body">
				<div class="info-title-box">
					<span class="name f15 f600" id="spu-main-title"><?php echo htmlspecialchars($info['name']);?></span>
					<button type="button" class="btn btn-link btn-xs name-trans-btn" title="修改标题与多语言翻译"><i class="glyphicon glyphicon-edit"></i></button>
				</div>
				<div class="info-kv-grid">
					<div class="kv-item">
						<span class="kv-label">产品分类：</span>
						<span class="kv-val">
							<span class="cate-path-badge"><?php echo htmlspecialchars($catePathStr);?></span>
							<button type="button" class="btn btn-primary btn-xs category-btn ml6">修改</button>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">开关配置：</span>
						<span class="kv-val switch-group-inline">
							<span class="switch-label">免运费</span>
							<div class="switch_botton free-ship" data-status="<?php echo (int)$info['free_ship'];?>">
								<div class="switch_status <?php echo $info['free_ship'] == 1 ? 'on' : 'off';?>"></div>
							</div>
							<span class="switch-label ml15">热门推荐</span>
							<div class="switch_botton is-hot" data-status="<?php echo (int)($info['is_hot'] ?? 0);?>">
								<div class="switch_status <?php echo ($info['is_hot'] ?? 0) == 1 ? 'on' : 'off';?>"></div>
							</div>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">基础邮费：</span>
						<span class="kv-val">
							<strong class="c-danger">￥<span class="j-data-val" data-name="post_fee"><?php echo $postFee;?></span></strong>
							<button type="button" class="btn btn-default btn-xs data-btn ml6" data-name="post_fee" data-title="基础邮费">修改</button>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">包装重量：</span>
						<span class="kv-val">
							<span class="j-data-val" data-name="weight"><?php echo (int)($info['data']['weight'] ?? 0);?></span> g
							<button type="button" class="btn btn-default btn-xs data-btn ml6" data-name="weight" data-title="包装重量(g)">修改</button>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">包装体积：</span>
						<span class="kv-val">
							<span class="j-data-val" data-name="volume"><?php echo htmlspecialchars($info['data']['volume'] ?: '--');?></span>
							<button type="button" class="btn btn-default btn-xs data-btn ml6" data-name="volume" data-title="包装体积(长,宽,高)">修改</button>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">入库/更新：</span>
						<span class="kv-val c6 f12">
							<?php echo $info['add_time'];?> / <?php echo $info['update_time'] ?: '--';?>
						</span>
					</div>
				</div>
			</div>
		</div>

		<!-- 右卡：供应商与货源档案 -->
		<div class="detail-card">
			<div class="detail-card-header">
				<span class="card-title"><i class="glyphicon glyphicon-home"></i> 供应商与货源档案</span>
				<?php if (!empty($info['data']['shop_id'])) { ?>
				<div class="card-actions">
					<a href="<?php echo adminUrl('product/index', ['shop_id' => $info['data']['shop_id']]);?>" class="btn btn-default btn-xs">
						<i class="glyphicon glyphicon-th-large"></i> 同店已入库SPU
					</a>
					<a href="<?php echo adminUrl('product/purchaseList', ['shop_id' => $info['data']['shop_id']]);?>" class="btn btn-default btn-xs">
						<i class="glyphicon glyphicon-list"></i> 同店采购池
					</a>
				</div>
				<?php } ?>
			</div>
			<div class="detail-card-body">
				<div class="info-kv-grid">
					<div class="kv-item">
						<span class="kv-label">货源渠道：</span>
						<span class="kv-val">
							<span class="sup-tag"><?php echo !empty($info['data']['supplier']) ? htmlspecialchars(strtoupper($info['data']['supplier'])) : '--';?></span>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">货源商品ID：</span>
						<span class="kv-val">
							<?php if (!empty($info['data']['item_id'])) { ?>
							<code class="item-code"><?php echo htmlspecialchars($info['data']['item_id']);?></code>
							<?php } else { echo '--'; } ?>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">货源链接：</span>
						<span class="kv-val e1">
							<?php if (!empty($info['data']['item_url'])) { ?>
							<a href="<?php echo htmlspecialchars($info['data']['item_url']);?>" target="_blank" class="c-primary">
								<?php echo htmlspecialchars($info['data']['item_url']);?> <span class="glyphicon glyphicon-link"></span>
							</a>
							<?php } else { echo '--'; } ?>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">供应商店铺：</span>
						<span class="kv-val">
							<?php if (!empty($info['shop']['name'])) { ?>
							<strong><?php echo htmlspecialchars($info['shop']['name']);?></strong>
							<span class="c9 f12 ml6">(ID: <?php echo (int)$info['data']['shop_id'];?>)</span>
							<?php } elseif (!empty($info['data']['shop_id'])) { ?>
							店铺 ID: <?php echo (int)$info['data']['shop_id'];?>
							<?php } else { echo '--'; } ?>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">店铺主页：</span>
						<span class="kv-val e1">
							<?php if (!empty($info['shop']['url'])) { ?>
							<a href="<?php echo htmlspecialchars($info['shop']['url']);?>" target="_blank" class="c-primary">
								<?php echo htmlspecialchars($info['shop']['url']);?> <span class="glyphicon glyphicon-link"></span>
							</a>
							<?php } else { echo '--'; } ?>
						</span>
					</div>
					<div class="kv-item">
						<span class="kv-label">库存概览：</span>
						<span class="kv-val">
							共 <strong><?php echo $skuCount;?></strong> 个SKU（在售 <strong class="c-success"><?php echo $activeSkuCount;?></strong> 个），总库存 <strong><?php echo $totalStock;?></strong> 件
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 第二区：SPU 主图画廊 -->
	<div id="sec-images" class="detail-card mt12">
		<div class="detail-card-header">
			<div>
				<span class="card-title"><i class="glyphicon glyphicon-picture"></i> SPU 主图画廊 (<?php echo $imageCount;?>)</span>
				<span class="card-subtitle">点击图片可放大预览；修改排序数字自动保存；点击「封面」设为商品主图</span>
			</div>
			<div class="card-actions">
				<button type="button" class="btn btn-success btn-xs upload-image"><i class="glyphicon glyphicon-plus"></i> 上传主图</button>
			</div>
		</div>
		<div class="detail-card-body">
			<div class="media-gallery-grid">
				<?php foreach ($info['image'] as $key => $value) {
					$isCover = ($value['attach_id'] == $info['attach_id']);
					$origUrl = str_replace(['_400.webp', '/400'], ['.webp', ''], $value['image']);
				?>
				<div class="spu-image <?php echo $isCover ? 'is-cover' : '';?>" data-id="<?php echo $value['attach_id'];?>" data-item_id="<?php echo $value['item_id'];?>">
					<?php if ($isCover) { ?>
					<span class="cover-ribbon">主封面</span>
					<?php } ?>
					<div class="image-tcell">
						<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload bigImage" alt="">
					</div>
					<div class="image-btn">
						<a class="btn btn-default btn-xs" target="_blank" href="<?php echo htmlspecialchars($origUrl);?>" title="新窗口查看原图"><span class="glyphicon glyphicon-search"></span></a>
						<?php if (!$isCover) { ?>
						<button type="button" class="btn btn-primary btn-xs spu-btn" title="设为SPU主封面">封面</button>
						<?php } ?>
						<button type="button" class="btn btn-success btn-xs sku-btn" title="绑定为指定SKU图片">SKU</button>
						<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>" placeholder="排序" title="排序值">
						<span class="glyphicon glyphicon-trash" title="删除主图"></span>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<!-- 第三区：规格属性看板 -->
	<?php if (!empty($info['attr_map'])) { ?>
	<div id="sec-attrs" class="detail-card mt12">
		<div class="detail-card-header">
			<div>
				<span class="card-title"><i class="glyphicon glyphicon-th"></i> 规格属性看板</span>
				<span class="card-subtitle">点击规格缩略图可批量替换同规格所有SKU图片；点击规格文字可在下方SKU列表中快速过滤</span>
			</div>
			<div class="card-actions">
				<button type="button" class="btn btn-default btn-xs hidden" id="clear-attr-filter-btn"><i class="glyphicon glyphicon-remove"></i> 清除规格过滤</button>
			</div>
		</div>
		<div class="detail-card-body">
			<?php foreach ($info['attr_map'] as $kv) { ?>
			<div class="attr-group-row">
				<div class="attr-group-label">
					<?php echo htmlspecialchars($kv['attr_name']);?>
					<span class="attr-val-count">(<?php echo count($kv['son']);?>)</span>
				</div>
				<div class="attr-values-wrap">
					<?php foreach ($kv['son'] as $value) { ?>
					<div class="attr-val-chip" data-attrn_id="<?php echo $kv['attrn_id'];?>" data-attrv_id="<?php echo $value['attrv_id'];?>" data-spu_id="<?php echo $info['spu_id'];?>" data-attv-name="<?php echo htmlspecialchars($value['attv_name']);?>">
						<div class="sku-attr-image" title="点击上传/更换该规格图片">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'] ?: siteUrl('image/common/noimg.svg');?>" class="lazyload">
						</div>
						<div class="attr-chip-info j-filter-sku-by-attr" title="点击筛选包含此规格的SKU">
							<div class="attr-chip-name"><?php echo htmlspecialchars($value['attv_name']);?></div>
							<div class="attr-chip-meta"><?php echo (int)($value['sku_count'] ?? 0);?> 个SKU</div>
						</div>
						<?php if (!empty($value['image'])) { ?>
						<button type="button" class="btn btn-danger btn-xs delete-attr-image-btn" title="移除该规格图">&times;</button>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<!-- 第四区：SKU 规格矩阵工作台 -->
	<div id="sec-skus" class="detail-card mt12">
		<div class="detail-card-header">
			<div>
				<span class="card-title"><i class="glyphicon glyphicon-list-alt"></i> SKU 规格矩阵 (<?php echo $skuCount;?>)</span>
				<span class="card-subtitle">单击或双击【售价 / 成本价 / 库存 / 重量 / 体积】单元格可直接行内修改，回车或失焦自动保存</span>
			</div>
			<div class="card-actions">
				<button type="button" class="btn btn-warning btn-xs recalc-sku-price-btn" title="根据(成本价 + 基础邮费)阶梯公式重新计算勾选或全部SKU售价">
					<i class="glyphicon glyphicon-flash"></i> 按成本+邮费重算售价
				</button>
			</div>
		</div>
		<!-- SKU 过滤与批量修改工具栏 -->
		<div class="sku-workbench-bar">
			<div class="sku-filter-left">
				<div class="btn-group btn-group-xs" id="sku-status-filter">
					<button type="button" class="btn btn-default active" data-status="all">全部 (<?php echo $skuCount;?>)</button>
					<button type="button" class="btn btn-default" data-status="1">在售 (<?php echo $activeSkuCount;?>)</button>
					<button type="button" class="btn btn-default" data-status="0">已下架 (<?php echo $skuCount - $activeSkuCount;?>)</button>
				</div>
				<input type="text" id="sku-keyword-input" class="form-control input-sm" placeholder="输入规格词/SKU ID实时过滤..." style="width: 210px;">
				<span id="sku-active-filter-tag" class="sku-filter-tag hidden"></span>
			</div>
			<div class="sku-batch-right">
				<span class="c6 f12 mr6">已选 <strong id="sku-checked-count">0</strong> 项：</span>
				<select id="sku-batch-field" class="form-control input-sm" style="width: 110px;">
					<option value="price">售价 (￥)</option>
					<option value="cost_price">成本价 (￥)</option>
					<option value="stock">库存</option>
					<option value="weight">重量 (g)</option>
					<option value="volume">体积 (mm)</option>
				</select>
				<input type="text" id="sku-batch-val" class="form-control input-sm" placeholder="输入批量值" style="width: 105px;">
				<button type="button" class="btn btn-primary btn-xs" id="apply-sku-batch-btn">批量应用</button>
				<button type="button" class="btn btn-success btn-xs j-batch-sku-status" data-status="1">批量上架</button>
				<button type="button" class="btn btn-default btn-xs j-batch-sku-status" data-status="0">批量下架</button>
			</div>
		</div>
		<div class="detail-card-body p0">
			<table class="table table-bordered table-hover mb0" id="sku-matrix-table">
				<thead>
					<tr>
						<th width="36" class="tc"><input type="checkbox" id="check-all-skus"></th>
						<th width="64">封面图</th>
						<th width="70">SKU ID</th>
						<th>规格属性</th>
						<th width="78">状态</th>
						<th width="105">售价 (￥) <i class="glyphicon glyphicon-pencil c9 f12"></i></th>
						<th width="105">成本价 (￥) <i class="glyphicon glyphicon-pencil c9 f12"></i></th>
						<th width="95" title="毛利 = 售价 - 成本价 - 基础邮费(￥<?php echo $postFee;?>)">毛利参考</th>
						<th width="85">库存 <i class="glyphicon glyphicon-pencil c9 f12"></i></th>
						<th width="85">重量(g) <i class="glyphicon glyphicon-pencil c9 f12"></i></th>
						<th width="110">体积(mm) <i class="glyphicon glyphicon-pencil c9 f12"></i></th>
						<th width="120">供应商SKU</th>
						<th width="135">上/更新时间</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($info['sku'] as $value) {
						$costPrice = (float)($value['cost_price'] ?? 0);
						$salePrice = (float)($value['price'] ?? 0);
						$profit = round($salePrice - $costPrice - $postFee, 2);
						$attrTextArr = [];
						foreach ($value['attr'] as $av) {
							$attrTextArr[] = $av['attr_name'] . ':' . $av['attv_name'];
						}
						$searchKey = strtolower($value['sku_id'] . ' ' . ($value['item_id'] ?? '') . ' ' . implode(' ', $attrTextArr));
					?>
					<tr class="sku-row" data-id="<?php echo $value['sku_id'];?>" data-status="<?php echo (int)$value['status'];?>" data-search="<?php echo htmlspecialchars($searchKey);?>">
						<td class="tc">
							<input type="checkbox" class="sku-row-check" value="<?php echo $value['sku_id'];?>">
						</td>
						<td>
							<div class="sku-image">
								<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'] ?: siteUrl('image/common/noimg.svg');?>" class="lazyload bigImage">
							</div>
						</td>
						<td class="f600">#<?php echo $value['sku_id'];?></td>
						<td>
							<div class="sku-spec-tags">
								<?php foreach ($value['attr'] as $attrValue) { ?>
								<span class="spec-tag">
									<em><?php echo htmlspecialchars($attrValue['attr_name']);?>:</em>
									<?php echo htmlspecialchars($attrValue['attv_name']);?>
								</span>
								<?php } ?>
							</div>
						</td>
						<td>
							<div class="switch_botton sku-status" data-status="<?php echo (int)$value['status'];?>">
								<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
							</div>
						</td>
						<td class="can-edit f600 c-danger" data-name="price"><?php echo $value['price'];?></td>
						<td class="can-edit" data-name="cost_price"><?php echo $costPrice;?></td>
						<td class="sku-profit-cell <?php echo $profit >= 0 ? 'c-success' : 'c-danger';?>">
							￥<?php echo number_format($profit, 2, '.', '');?>
						</td>
						<td class="can-edit" data-name="stock"><?php echo (int)$value['stock'];?></td>
						<td class="can-edit" data-name="weight"><?php echo (int)($value['weight'] ?? 0);?></td>
						<td class="can-edit" data-name="volume"><?php echo htmlspecialchars($value['volume'] ?? '');?></td>
						<td class="f12 c6"><?php echo htmlspecialchars($value['item_id'] ?? '--');?></td>
						<td class="f12 c9">
							<div><?php echo $value['add_time'];?></div>
							<?php if (!empty($value['update_time'])) { ?>
							<div><?php echo $value['update_time'];?></div>
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- 第五区：描述属性列表 & 详情介绍图片 -->
	<div id="sec-desc" class="detail-grid-desc mt12">
		<!-- 左栏：描述属性列表 -->
		<div class="detail-card" id="sku-desc-content">
			<div class="detail-card-header">
				<div>
					<span class="card-title"><i class="glyphicon glyphicon-align-left"></i> 描述属性列表 (<?php echo $descCount;?>)</span>
					<span class="card-subtitle">点击选择图标可批量分组或批量删除</span>
				</div>
				<div class="card-actions">
					<button type="button" class="btn btn-default btn-xs" id="toggle-all-desc-btn">全选/反选</button>
					<button type="button" class="btn btn-success btn-xs add-desc-btn"><i class="glyphicon glyphicon-plus"></i> 增加</button>
					<button type="button" class="btn btn-info btn-xs desc-group-btn"><i class="glyphicon glyphicon-folder-open"></i> 分组</button>
					<button type="button" class="btn btn-danger btn-xs batch-delete-desc-btn"><i class="glyphicon glyphicon-trash"></i> 批量删除</button>
				</div>
			</div>
			<div class="detail-card-body p0">
				<table class="table table-bordered table-hover mb0">
					<thead>
						<tr>
							<th width="46" class="tc">选择</th>
							<th width="140">描述名</th>
							<th>描述值</th>
							<th width="68">排序</th>
							<th width="115">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($info['desc'])) {
							foreach ($info['desc'] as $item) { ?>
						<tr class="desc-group-header">
							<td colspan="5" class="f600"><?php echo htmlspecialchars($item['group'] ?: '默认分组');?></td>
						</tr>
						<?php foreach ($item['list'] as $value) { ?>
						<tr class="desc-item-row" data-id="<?php echo $value['item_id'];?>">
							<td class="tc f18 pointer desc-check-cell">
								<span class="glyphicon glyphicon-ok-circle"></span>
							</td>
							<td class="desc-name-cell"><?php echo htmlspecialchars($value['name']);?></td>
							<td class="desc-val-cell"><?php echo htmlspecialchars($value['value']);?></td>
							<td>
								<input type="text" name="sort" value="<?php echo $value['sort'];?>" class="form-control input-sm tc">
							</td>
							<td>
								<button type="button" class="btn btn-primary btn-xs edit-desc-btn"><span class="glyphicon glyphicon-edit"></span></button>
								<button type="button" class="btn btn-danger btn-xs delete-desc-btn"><span class="glyphicon glyphicon-trash"></span></button>
							</td>
						</tr>
						<?php } } } else { ?>
						<tr>
							<td colspan="5" class="tc c9 p20">暂无描述属性数据</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- 右栏：详情介绍长图 -->
		<div class="detail-card" id="sec-intro">
			<div class="detail-card-header">
				<div>
					<span class="card-title"><i class="glyphicon glyphicon-camera"></i> 详情介绍图片 (<?php echo $introCount;?>)</span>
					<span class="card-subtitle">点击图片放大查看，修改排序值失焦即存</span>
				</div>
				<div class="card-actions">
					<button type="button" class="btn btn-success btn-xs upload-introduce-image"><i class="glyphicon glyphicon-plus"></i> 上传详情图</button>
				</div>
			</div>
			<div class="detail-card-body">
				<?php if (!empty($info['introduce'])) { ?>
				<div class="media-gallery-grid">
					<?php foreach ($info['introduce'] as $idx => $value) {
						$origIntroUrl = str_replace(['_400.webp', '/400'], ['.webp', ''], $value['image']);
					?>
					<div class="spu-introduce-image" data-id="<?php echo $value['attach_id'];?>" data-item_id="<?php echo $value['item_id'];?>">
						<span class="intro-index-badge">#<?php echo $idx + 1;?></span>
						<div class="image-tcell">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload bigImage" alt="">
						</div>
						<div class="image-btn" data-id="<?php echo $value['item_id'];?>">
							<a class="btn btn-default btn-xs" target="_blank" href="<?php echo htmlspecialchars($origIntroUrl);?>" title="查看原图"><span class="glyphicon glyphicon-search"></span></a>
							<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>" placeholder="排序">
							<span class="glyphicon glyphicon-trash delete-introduce-btn" title="删除详情图"></span>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } else { ?>
				<div class="tc c9 p20">暂无详情介绍图片</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<!-- 状态弹窗 -->
<div id="status-dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">修改产品状态</div>
			<div class="input-group">
				<div class="input-group-addon"><span>当前状态</span></div>
				<select class="form-control" name="status">
					<?php foreach ($statusList as $key => $value) { ?>
					<option value="<?php echo $key;?>" <?php echo $key == $info['status'] ? 'selected' : '';?>><?php echo $value;?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认保存</button>
		</form>
	</div>
</div>

<!-- 分类弹窗 -->
<div id="category-dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">修改产品分类</div>
			<div class="input-group">
				<div class="input-group-addon"><span>所属分类</span></div>
				<select class="form-control" name="cate_id">
					<?php foreach ($siteCate as $k => $v) {
						$paddingStr = '';
						for ($i = 0; $i < $v['level']; $i++) {
							$paddingStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						$hasChildren = (($siteCate[$k + 1]['level'] ?? 0) > $v['level']);
					?>
					<option value="<?php echo $v['cate_id'];?>" <?php echo $info['cate_id'] == $v['cate_id'] ? 'selected' : '';?> <?php if ($hasChildren) { echo 'disabled="disabled"'; }?>><?php echo $paddingStr . htmlspecialchars($v['name']);?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认保存</button>
		</form>
	</div>
</div>

<!-- 多语言弹窗 -->
<div id="dealbox-language" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 640px">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">标题与多语言配置</div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="editSpuLanguage">
			<table class="table table-bordered table-hover mb10">
				<tbody></tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt10">确认保存</button>
		</form>
	</div>
</div>

<!-- SKU主图管理 -->
<div id="dealbox-sku-image" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 560px">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">绑定图片至指定 SKU</div>
			<input type="hidden" name="opn" value="editSkuInfo">
			<input type="hidden" name="attach_id" value="0">
			<div style="max-height: 55vh; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px;">
				<table class="table table-hover mb0">
					<thead>
						<tr>
							<th width="50"><input type="checkbox" id="modal-check-all-sku-img"></th>
							<th width="80">SKUID</th>
							<th>规格属性</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($info['sku'] as $value) { ?>
						<tr class="item" data-id="<?php echo $value['attach_id'];?>">
							<td>
								<input type="checkbox" name="sku_id[]" value="<?php echo $value['sku_id'];?>">
							</td>
							<td>#<?php echo $value['sku_id'];?></td>
							<td><?php echo htmlspecialchars(implode(' / ', array_column($value['attr'], 'attv_name')));?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt15">确认绑定</button>
		</form>
	</div>
</div>

<!-- SKU单项或全量修改弹窗 -->
<div id="dealbox-sku-info" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">SKU管理</div>
			<input type="hidden" name="opn" value="editSkuInfo">
			<input type="hidden" name="sku_id" value="0">
			<div class="form-group" style="margin: 0 0 15px 0;">
				<input type="text" name="" value="" class="form-control name">
			</div>
			<div class="flex" style="gap: 10px;">
				<button type="button" class="btn btn-warning w50 save-btn batch-save-btn">应用至全部SKU</button>
				<button type="button" class="btn btn-primary w50 save-btn">仅修改当前SKU</button>
			</div>
		</form>
	</div>
</div>

<!-- SPU描述文本管理 -->
<div id="dealbox-desc" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">描述文本</div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="item_id" value="0">
			<input type="hidden" name="opn" value="modifySpuDesc">
			<div class="input-group">
				<div class="input-group-addon"><span>描述名</span></div>
				<textarea name="name" class="form-control" rows="2"></textarea>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>描述值</span></div>
				<textarea name="value" class="form-control" rows="2"></textarea>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>排序值</span></div>
				<input type="text" name="sort" class="form-control">
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt15">确认保存</button>
		</form>
	</div>
</div>

<!-- SPU扩展数据管理 -->
<div id="dealbox-data" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">修改扩展参数</div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="modifySpuData">
			<div class="input-group">
				<div class="input-group-addon"><span>参数值</span></div>
				<input type="text" name="value" class="form-control" autocomplete="off">
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt15">确认保存</button>
		</form>
	</div>
</div>

<!-- 描述分组弹窗 -->
<div id="desc-group-dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="opn" value="editDescGroupInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">设置描述分组</div>
			<div class="input-group">
				<div class="input-group-addon"><span>目标分组</span></div>
				<select class="form-control" name="group_id">
					<option value="0">默认分组</option>
					<?php foreach ($groupList as $value) { ?>
					<option value="<?php echo $value['descg_id'];?>"><?php echo htmlspecialchars($value['name']);?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认保存</button>
		</form>
	</div>
</div>
<?php } ?>