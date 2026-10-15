<?php
$deptTotal = count($deptList ?? []);
$memTotal = count($memberList ?? []);
?>
<div id="perm-page" class="perm-page" data-url="<?php echo adminUrl('controller/permission');?>">
	<!-- 顶部授权对象选择卡片 -->
	<div class="target-selector-card">
		<div class="type-tabs" id="target-type-tabs">
			<button type="button" class="target-type-btn active" data-type="1">
				<i class="fa fa-sitemap"></i> 按部门 (<?php echo $deptTotal;?>)
			</button>
			<button type="button" class="target-type-btn" data-type="2">
				<i class="fa fa-user"></i> 按个人 (<?php echo $memTotal;?>)
			</button>
		</div>

		<div class="target-search-box">
			<i class="fa fa-magnifying-glass search-icon"></i>
			<input type="text" id="target-search" class="form-control" placeholder="筛选部门或人员名称 / ID..." autocomplete="off">
		</div>

		<!-- 部门列表 -->
		<div id="target-list-1" class="target-list-wrap">
			<?php if (empty($deptList)) { ?>
				<div class="empty-target">
					暂无部门，请先前往 <a href="<?php echo adminUrl('department/index');?>">部门管理</a> 创建
				</div>
			<?php } else { ?>
				<?php foreach ($deptList as $dept) {
					$dId = (int)$dept['dept_id'];
					$lev = (int)($dept['level'] ?? 1);
					$prefix = $lev > 1 ? str_repeat('　', $lev - 1) . '└─ ' : '';
					$cnt = (int)($deptPermCounts[$dId] ?? 0);
				?>
				<div class="perm-target-item"
					data-type="1"
					data-id="<?php echo $dId;?>"
					data-name="<?php echo htmlspecialchars((string)$dept['name'], ENT_QUOTES, 'UTF-8');?>"
					data-search="<?php echo htmlspecialchars(strtolower($dept['name'] . ' ' . $dId), ENT_QUOTES, 'UTF-8');?>">
					<div class="target-item-main">
						<span class="tree-prefix"><?php echo htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8');?></span>
						<strong class="target-name"><?php echo htmlspecialchars((string)$dept['name'], ENT_QUOTES, 'UTF-8');?></strong>
						<span class="target-id">#<?php echo $dId;?></span>
						<?php if ((int)$dept['status'] === 0) { ?>
							<span class="status-off-tag">停用</span>
						<?php } ?>
					</div>
					<span class="perm-cnt-badge"><?php echo $cnt;?></span>
				</div>
				<?php } ?>
			<?php } ?>
		</div>

		<!-- 个人列表 -->
		<div id="target-list-2" class="target-list-wrap hidden">
			<?php if (empty($memberList)) { ?>
				<div class="empty-target">暂无人员数据</div>
			<?php } else { ?>
				<?php foreach ($memberList as $mem) {
					$mId = (int)$mem['mem_id'];
					$fullName = trim(($mem['first_name'] ?? '') . ' ' . ($mem['last_name'] ?? ''));
					$displayName = ($mem['nick_name'] ?? '') ?: ($fullName ?: ('用户#' . $mId));
					$deptName = $mem['dept_name'] ?? '';
					$posName = $mem['pos_name'] ?? '';
					$isSuper = !empty($mem['is_super']);
					$cnt = (int)($memPermCounts[$mId] ?? 0);
				?>
				<div class="perm-target-item"
					data-type="2"
					data-id="<?php echo $mId;?>"
					data-name="<?php echo htmlspecialchars((string)$displayName, ENT_QUOTES, 'UTF-8');?>"
					data-dept-name="<?php echo htmlspecialchars((string)$deptName, ENT_QUOTES, 'UTF-8');?>"
					data-super="<?php echo $isSuper ? 1 : 0;?>"
					data-search="<?php echo htmlspecialchars(strtolower($displayName . ' ' . $fullName . ' ' . $mId . ' ' . ($mem['mobile'] ?? '') . ' ' . $deptName), ENT_QUOTES, 'UTF-8');?>">
					<div class="target-item-main">
						<div class="mem-line-top">
							<strong class="target-name"><?php echo htmlspecialchars((string)$displayName, ENT_QUOTES, 'UTF-8');?></strong>
							<span class="target-id">#<?php echo $mId;?></span>
						</div>
						<div class="mem-line-sub">
							<?php if ($deptName !== '') { ?>
								<span class="dept-tag"><?php echo htmlspecialchars((string)$deptName, ENT_QUOTES, 'UTF-8');?></span>
							<?php } else { ?>
								<span class="no-dept-tag">未分部门</span>
							<?php } ?>
							<?php if ($posName !== '') { ?>
								<span class="pos-tag"><?php echo htmlspecialchars((string)$posName, ENT_QUOTES, 'UTF-8');?></span>
							<?php } ?>
						</div>
					</div>
					<?php if ($isSuper) { ?>
						<span class="super-badge">超管</span>
					<?php } else { ?>
						<span class="perm-cnt-badge"><?php echo $cnt;?></span>
					<?php } ?>
				</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

	<!-- 当前对象操作工具栏 -->
	<div class="perm-toolbar-card">
		<div class="current-target-info">
			<span id="current-target-badge" class="target-badge badge-primary">未选择</span>
			<strong id="current-target-name" class="current-name">请选择部门或人员</strong>
			<span id="current-target-dept-hint" class="dept-hint"></span>
		</div>
		<div id="perm-toolbar-btns" class="toolbar-actions">
			<button type="button" class="btn btn-default btn-xs" id="btn-check-all">
				<i class="fa fa-square-check"></i> 全选
			</button>
			<button type="button" class="btn btn-default btn-xs" id="btn-uncheck-all">
				<i class="fa fa-square"></i> 清空
			</button>
			<button type="button" class="btn btn-primary btn-xs" id="btn-save-perm" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中">
				<i class="fa fa-floppy-disk"></i> 保存配置
			</button>
		</div>
	</div>

	<!-- 提示横幅 -->
	<div id="super-admin-notice" class="perm-notice notice-warn hidden">
		<i class="fa fa-shield-halved"></i>
		<span><strong>系统超级管理员（ID: 10000）</strong> 默认拥有后台所有控制器与全部 <code>public</code> 方法的访问权限，无需单独配置或修改。</span>
	</div>

	<div id="member-union-notice" class="perm-notice notice-info hidden">
		<i class="fa fa-circle-info"></i>
		<span><strong>权限合并说明：</strong>个人最终生效权限 = <strong>所属部门权限 ∪ 个人独立授权</strong>。带有 <span class="inh-badge">部门已授</span> 标记的项已由所属部门授权。</span>
	</div>

	<!-- 权限矩阵卡片列表 -->
	<div class="perm-matrix-list" id="perm-matrix-table">
		<?php foreach ($catalog ?? [] as $group) {
			$gId = (int)$group['group_id'];
			$items = $group['items'] ?? [];
		?>
		<div class="perm-group-card" data-group="<?php echo $gId;?>">
			<div class="group-header-row" data-group="<?php echo $gId;?>">
				<div class="group-header-left">
					<i class="fa fa-caret-down group-toggle-icon"></i>
					<i class="fa fa-folder-open group-folder-icon"></i>
					<strong class="group-title"><?php echo htmlspecialchars((string)$group['group_name'], ENT_QUOTES, 'UTF-8');?></strong>
					<span class="group-count">(<?php echo count($items);?>)</span>
				</div>
				<label class="chk-group-label" onclick="event.stopPropagation();">
					<input type="checkbox" class="chk-group-all">
					<span>全选本组</span>
				</label>
			</div>

			<div class="group-ctrl-list">
				<?php foreach ($items as $ctrlItem) {
					$ctrlName = (string)$ctrlItem['controller'];
					$menuName = $ctrlItem['menu_name'] ?: ($ctrlItem['default_title'] ?: $ctrlName);
					$methods = $ctrlItem['methods'] ?? [];
				?>
				<div class="ctrl-row" data-group="<?php echo $gId;?>" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>">
					<div class="ctrl-head">
						<div class="ctrl-title-wrap">
							<strong class="ctrl-menu-name"><?php echo htmlspecialchars((string)$menuName, ENT_QUOTES, 'UTF-8');?></strong>
							<code class="ctrl-code"><?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?></code>
						</div>
						<div class="ctrl-all-wrap">
							<span class="dept-inh-ctrl inh-badge hidden">部门已授</span>
							<label class="chk-ctrl-label">
								<input type="checkbox" class="chk-ctrl-all" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" value="*">
								<span>整控制器 <code>*</code></span>
							</label>
						</div>
					</div>
					<div class="ctrl-methods">
						<?php foreach ($methods as $m) {
							$actName = (string)$m['action'];
							$actTitle = (string)$m['title'];
						?>
						<label class="method-item" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" data-action="<?php echo htmlspecialchars($actName, ENT_QUOTES, 'UTF-8');?>">
							<input type="checkbox" class="chk-method" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" value="<?php echo htmlspecialchars($actName, ENT_QUOTES, 'UTF-8');?>">
							<span class="method-title"><?php echo htmlspecialchars($actTitle, ENT_QUOTES, 'UTF-8');?></span>
							<span class="method-act">(<?php echo htmlspecialchars($actName, ENT_QUOTES, 'UTF-8');?>)</span>
							<span class="dept-inh-method inh-badge hidden">部门已授</span>
						</label>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
