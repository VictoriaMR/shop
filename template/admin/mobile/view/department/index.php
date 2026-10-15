<?php
$totalCount = count($list ?? []);
$topCount = 0;
foreach ($list ?? [] as $d) {
	if ((int)($d['parent_id'] ?? 0) === 0) {
		$topCount++;
	}
}
?>
<div id="department-page" class="dept-page" data-url="<?php echo adminUrl('department/index');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-sitemap"></i>
			<span>部门管理</span>
			<span class="total-badge">共 <?php echo $totalCount;?> 个 / 顶级 <?php echo $topCount;?></span>
		</div>
		<div class="header-actions">
			<button type="button" class="btn btn-default btn-xs toggle-all-btn" title="全部折叠/展开">
				<i class="fa fa-compress"></i>
			</button>
			<button type="button" class="btn btn-success btn-xs add-top-btn">
				<i class="fa fa-plus"></i> 新增部门
			</button>
		</div>
	</div>

	<!-- 部门树形卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-sitemap"></i>
				<p>暂无部门数据，请点击右上角“新增部门”</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$deptId = (int)$value['dept_id'];
				$parentId = (int)$value['parent_id'];
				$lev = max(1, (int)($value['level'] ?? 1));
				$childCount = (int)($value['child_count'] ?? 0);
				$hasChild = !empty($value['has_child']);
				$isFirst = !empty($value['is_first']);
				$isLast = !empty($value['is_last']);
				$memCount = (int)($memberCountMap[$deptId] ?? 0);
				$status = (int)($value['status'] ?? 0);
				$remark = trim((string)($value['remark'] ?? ''));
				$indentRem = min(3, $lev - 1) * 0.12;
				$rowData = [
					'dept_id'   => $deptId,
					'parent_id' => $parentId,
					'name'      => $value['name'],
					'remark'    => $remark,
					'status'    => $status,
					'sort'      => (int)$value['sort'],
				];
			?>
			<div class="dept-card item lev-<?php echo $lev;?><?php echo $hasChild ? ' has-child' : '';?>"
				data-id="<?php echo $deptId;?>"
				data-pid="<?php echo $parentId;?>"
				data-lev="<?php echo $lev;?>"
				data-info="<?php echo htmlspecialchars(json_encode($rowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>"
				style="<?php echo $indentRem > 0 ? 'margin-left:' . $indentRem . 'rem;' : '';?>">
				<div class="card-top">
					<div class="dept-info">
						<?php if ($hasChild) { ?>
							<i class="fa fa-caret-down toggle-icon"></i>
						<?php } ?>
						<div class="dept-main">
							<div class="dept-title-line">
								<span class="id-badge">#<?php echo $deptId;?></span>
								<span class="lev-badge"><?php echo $lev;?>级<?php echo $childCount > 0 ? ' (' . $childCount . ')' : '';?></span>
								<strong class="dept-name"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></strong>
							</div>
							<?php if ($remark !== '') { ?>
								<div class="dept-remark"><?php echo htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');?></div>
							<?php } ?>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-primary btn-xs modify" title="编辑" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button type="button" class="btn btn-success btn-xs add-sub" title="新增子部门" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-plus"></i></button>
						<?php if ($childCount === 0) { ?>
						<button type="button" class="btn btn-danger btn-xs delete" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
						<?php } ?>
					</div>
				</div>

				<div class="card-bottom">
					<div class="mem-badge-wrap">
						<?php if ($memCount > 0) { ?>
							<a href="<?php echo adminUrl('member/index', ['dept_id' => $deptId]);?>" class="mem-count-badge" title="查看该部门人员">
								<i class="fa fa-user"></i> <?php echo $memCount;?> 人
							</a>
						<?php } else { ?>
							<span class="mem-zero-badge"><i class="fa fa-user"></i> 0 人</span>
						<?php } ?>
					</div>

					<div class="switch-item">
						<span class="switch-txt">启用</span>
						<div class="switch_botton" data-status="<?php echo $status;?>" title="点击切换状态">
							<div class="switch_status <?php echo $status === 1 ? 'on' : 'off';?>"></div>
						</div>
					</div>

					<div class="sort-group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirst ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirst ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLast ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLast ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 新增/编辑部门弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">新增部门</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="dept-form" onsubmit="return false;">
					<input type="hidden" name="dept_id" value="0">
					<input type="hidden" name="sort" value="0">
					<input type="hidden" name="opn" value="editDeptInfo">

					<div class="form-group">
						<label class="form-label">上级部门</label>
						<select class="form-control" name="parent_id">
							<option value="0">-- 顶级部门 --</option>
							<?php foreach ($list ?? [] as $opt) {
								$optLev = (int)($opt['level'] ?? 1);
								$prefix = $optLev > 1 ? str_repeat('　', $optLev - 1) . '└─ ' : '';
							?>
							<option value="<?php echo (int)$opt['dept_id'];?>"><?php echo htmlspecialchars($prefix . $opt['name'], ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">部门名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="请输入部门名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">部门备注</label>
						<input type="text" class="form-control" name="remark" maxlength="255" placeholder="部门职责或备注说明（选填）" autocomplete="off">
					</div>
					<div class="form-group switch-form-group">
						<label class="form-label mb0">启用状态</label>
						<div class="modal-switch-wrap">
							<div class="switch_botton" data-status="1">
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
