<div id="site-category-page" class="site-category-page">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-sitemap"></i>
			<span>站点品类管理</span>
		</div>
		<button class="btn btn-success btn-sm add-btn" type="button">
			<i class="fa fa-plus"></i> 新增站点品类
		</button>
	</div>

	<!-- 筛选框（无重置按钮） -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('category/siteCategory');?>" method="get" class="search-form">
			<div class="filter-row">
				<div class="filter-item">
					<select class="form-control" name="site">
						<option value="-1">请选择站点</option>
						<?php if (!empty($siteList)) {
							foreach ($siteList as $key => $value) { ?>
						<option value="<?php echo $key;?>" <?php echo (isset($site) && $site == $key) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="filter-item">
					<select class="form-control" name="cate">
						<option value="-1">请选择分类</option>
						<?php if (!empty($cateList)) {
							foreach ($cateList as $key => $value) {
								$disabled = isset($cateList[$key + 1]) && $value['level'] < $cateList[$key + 1]['level'];
								$text = str_repeat('　', max(0, (int)$value['level']));
						?>
						<option value="<?php echo (int)$value['cate_id'];?>" <?php echo (isset($cate) && $cate == $value['cate_id']) ? 'selected' : '';?> <?php echo $disabled ? 'disabled="disabled"' : '';?>><?php echo $text . htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
						<?php } } ?>
					</select>
				</div>
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无站点品类数据</p>
			</div>
		<?php } else {
			$tempCateList = !empty($cateList) ? array_column($cateList, null, 'cate_id') : [];
			foreach ($list as $value) {
				$avatar = trim((string)($value['avatar'] ?? ''));
				$hasIcon = $avatar !== '' && strpos($avatar, 'image/common/') === false;
				$cateName = $tempCateList[$value['cate_id']]['name'] ?? ('分类 #' . (int)$value['cate_id']);
				$siteName = $siteList[$value['site_id']] ?? ('站点 #' . (int)$value['site_id']);
		?>
			<div class="site-cate-card item" data-id="<?php echo (int)$value['item_id'];?>">
				<div class="card-top">
					<div class="left-box">
						<div class="cate-icon-upload" title="点击上传/更换图标">
							<?php if ($hasIcon) { ?>
								<img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');?>" alt="icon">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
						</div>
						<div class="info-wrap">
							<div class="title-line">
								<span class="id-badge">#<?php echo (int)$value['item_id'];?></span>
								<span class="site-tag"><?php echo htmlspecialchars((string)$siteName, ENT_QUOTES, 'UTF-8');?></span>
								<strong class="cate-name"><?php echo htmlspecialchars((string)$cateName, ENT_QUOTES, 'UTF-8');?></strong>
							</div>
							<?php if (!empty($value['parent'])) { ?>
								<div class="parent-path"><?php echo htmlspecialchars(implode(' / ', array_column($value['parent'], 'name')), ENT_QUOTES, 'UTF-8');?></div>
							<?php } ?>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-danger btn-xs delete" title="删除"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
				<div class="card-metrics">
					<div class="metric-field">
						<label>排序</label>
						<input type="number" name="sort" value="<?php echo (int)$value['sort'];?>" class="form-control">
					</div>
					<div class="metric-field">
						<label>销售数</label>
						<input type="number" name="sale_total" value="<?php echo (int)$value['sale_total'];?>" class="form-control">
					</div>
					<div class="metric-field">
						<label>浏览数</label>
						<input type="number" name="visit_total" value="<?php echo (int)$value['visit_total'];?>" class="form-control">
					</div>
				</div>
			</div>
		<?php } } ?>
	</div>

	<?php if (isset($size, $total)) { echo page($size, $total); } ?>

	<!-- 添加站点品类弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">添加站点品类</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="site-cate-form" onsubmit="return false;">
					<input type="hidden" name="item_id" value="0">
					<input type="hidden" name="opn" value="editSiteCategory">
					<div class="form-group">
						<label class="form-label">站点 <span class="required">*</span></label>
						<select class="form-control" name="site_id" required="required">
							<option value="-1">请选择站点</option>
							<?php if (!empty($siteList)) {
								foreach ($siteList as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo (isset($site) && $site == $key) ? 'selected' : '';?>><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">分类 <span class="required">*</span></label>
						<select class="form-control" name="cate_id" required="required">
							<option value="-1">请选择分类</option>
							<?php if (!empty($cateList)) {
								foreach ($cateList as $value) {
									$text = str_repeat('　', max(0, (int)$value['level']));
							?>
							<option value="<?php echo (int)$value['cate_id'];?>" <?php echo (isset($cate) && $cate == $value['cate_id']) ? 'selected' : '';?>><?php echo $text . htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></option>
							<?php } } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确 认</button>
				</form>
			</div>
		</div>
	</div>
</div>
