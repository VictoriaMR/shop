<?php
$status = (int)($search['status'] ?? -1);
$keyword = $search['keyword'] ?? '';
?>
<div id="site-page" class="site-mobile-page" data-url="<?php echo adminUrl('site/index');?>">
	<!-- 顶部标题栏 + 新增按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-globe"></i>
			<span>站点列表</span>
			<span class="total-badge">共 <?php echo count($list ?? []);?> 个</span>
		</div>
		<button class="btn btn-success btn-xs add-btn" type="button">
			<i class="fa fa-plus"></i> 新增站点
		</button>
	</div>

	<!-- 筛选栏 -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('site/index');?>" method="get" class="search-form">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<div class="status-filter">
				<button type="button" data-status="-1" class="filter-tab <?php echo ($status !== 0 && $status !== 1) ? 'active' : '';?>">全部</button>
				<button type="button" data-status="1" class="filter-tab <?php echo $status === 1 ? 'active' : '';?>">已启用</button>
				<button type="button" data-status="0" class="filter-tab <?php echo $status === 0 ? 'active' : '';?>">未启用</button>
			</div>
			<div class="filter-row">
				<div class="filter-item">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');?>" placeholder="站点名称 / 域名 / 模板路径" autocomplete="off">
				</div>
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 站点卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-folder-open"></i>
			<p>暂无站点数据</p>
		</div>
		<?php } else { ?>
		<?php foreach ($list as $value) {
			$siteId = (int)$value['site_id'];
			$isAdmin = ($value['type'] === 'admin');
			$langs = !empty($value['language']) && is_array($value['language']) ? array_column($value['language'], 'name2') : [];
			$currs = !empty($value['currency']) && is_array($value['currency']) ? array_column($value['currency'], 'code') : [];
			$rowInfo = [
				'site_id'      => $siteId,
				'name'         => $value['name'] ?? '',
				'type'         => $value['type'] ?? 'home',
				'path'         => $value['path'] ?? '',
				'domain'       => $value['domain'] ?? '',
				'email'        => $value['email'] ?? '',
				'view_cache'   => (int)($value['view_cache'] ?? 0),
				'static_cache' => (int)($value['static_cache'] ?? 0),
				'debug'        => (int)($value['debug'] ?? 0),
				'status'       => (int)($value['status'] ?? 0),
			];
		?>
		<div class="site-card item" data-id="<?php echo $siteId;?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
			<div class="card-header">
				<div class="site-title-wrap">
					<span class="id-badge">#<?php echo $siteId;?></span>
					<span class="site-name"><?php echo htmlspecialchars($value['name'] ?? '', ENT_QUOTES, 'UTF-8');?></span>
					<span class="type-tag <?php echo $isAdmin ? 'admin' : 'home';?>"><?php echo htmlspecialchars($value['type'] ?: 'home', ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="card-actions">
					<a href="<?php echo adminUrl('site/siteInfo', ['id' => $siteId]);?>" class="btn btn-primary btn-icon" title="站点配置"><i class="fa fa-sliders"></i></a>
					<button class="btn btn-info btn-icon modify" type="button" title="编辑站点" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
				</div>
			</div>

			<div class="card-body">
				<div class="status-bar">
					<div class="switch-group">
						<span class="switch-label">启用状态</span>
						<div class="switch_botton site-status-switch" data-status="<?php echo (int)$value['status'];?>" title="点击切换站点状态">
							<div class="switch_status <?php echo (int)$value['status'] === 1 ? 'on' : 'off';?>"></div>
						</div>
					</div>
					<div class="flag-tags">
						<span class="flag-pill <?php echo !empty($value['view_cache']) ? 'on' : 'off';?>">视图缓存</span>
						<span class="flag-pill <?php echo !empty($value['static_cache']) ? 'on' : 'off';?>">静态缓存</span>
						<span class="flag-pill <?php echo !empty($value['debug']) ? 'warn' : 'off';?>">Debug</span>
					</div>
				</div>

				<div class="meta-grid">
					<div class="meta-row">
						<span class="meta-label">绑定域名：</span>
						<code class="meta-code"><?php echo htmlspecialchars($value['domain'] ?: '--', ENT_QUOTES, 'UTF-8');?></code>
					</div>
					<div class="meta-row">
						<span class="meta-label">模板目录：</span>
						<span class="meta-val"><?php echo htmlspecialchars($value['path'] ?? '--', ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<?php if (!empty($value['email'])) { ?>
					<div class="meta-row">
						<span class="meta-label">联系邮箱：</span>
						<span class="meta-val"><?php echo htmlspecialchars($value['email'], ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<?php } ?>
					<div class="meta-row">
						<span class="meta-label">关联语言：</span>
						<div class="tag-group">
							<?php if (empty($langs)) { ?>
							<span class="text-muted">--</span>
							<?php } else { foreach ($langs as $lName) { ?>
							<span class="mini-tag lang-tag"><?php echo htmlspecialchars($lName, ENT_QUOTES, 'UTF-8');?></span>
							<?php } } ?>
						</div>
					</div>
					<div class="meta-row">
						<span class="meta-label">关联货币：</span>
						<div class="tag-group">
							<?php if (empty($currs)) { ?>
							<span class="text-muted">--</span>
							<?php } else { foreach ($currs as $cCode) { ?>
							<span class="mini-tag curr-tag"><?php echo htmlspecialchars($cCode, ENT_QUOTES, 'UTF-8');?></span>
							<?php } } ?>
						</div>
					</div>
					<div class="meta-row time-row">
						<span>创建：<?php echo $value['add_time'] ?: '--';?></span>
						<?php if (!empty($value['update_time'])) { ?>
						<span>更新：<?php echo $value['update_time'];?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>

	<!-- 新增/编辑站点弹窗 -->
	<div id="dealbox-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">编辑站点</h4>
				<span class="modal-close close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="site-form" method="post">
					<input type="hidden" name="site_id" value="0">
					<input type="hidden" name="opn" value="editSite">

					<div class="form-group">
						<label class="form-label">站点名称 <span class="required">*</span></label>
						<input class="form-control" name="name" required="required" maxlength="64" placeholder="请输入站点名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">站点类型</label>
						<select class="form-control" name="type">
							<option value="home">前台商城 (home)</option>
							<option value="admin">管理后台 (admin)</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">绑定域名</label>
						<input class="form-control" name="domain" maxlength="64" placeholder="例如: clothestome.com" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">模板目录 <span class="required">*</span></label>
						<input class="form-control" name="path" required="required" maxlength="32" placeholder="template 下的目录名，如 newsme" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">联系邮箱</label>
						<input class="form-control" name="email" maxlength="64" placeholder="support@example.com（选填）" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">缓存与调试开关</label>
						<div class="check-group">
							<label class="check-item">
								<input type="checkbox" name="view_cache" value="1"> 视图缓存
							</label>
							<label class="check-item">
								<input type="checkbox" name="static_cache" value="1"> 静态缓存
							</label>
							<label class="check-item">
								<input type="checkbox" name="debug" value="1"> Debug模式
							</label>
						</div>
					</div>
					<div class="form-group switch-form-row">
						<label class="form-label m0">站点启用状态</label>
						<div>
							<div class="switch_botton modal-status-switch" data-status="1">
								<div class="switch_status on"></div>
							</div>
							<input type="hidden" name="status" value="1">
						</div>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
