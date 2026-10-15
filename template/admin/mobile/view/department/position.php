<?php
$listCount = count($list ?? []);
$curStatus = (int)($search['status'] ?? -1);
$curKeyword = (string)($search['keyword'] ?? '');
?>
<div id="position-page" class="pos-page" data-url="<?php echo adminUrl('department/position');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-id-badge"></i>
			<span>职位管理</span>
			<span class="total-badge">共 <?php echo (int)($total ?? 0);?> 个</span>
		</div>
		<button type="button" class="btn btn-success btn-xs" id="add-pos-btn">
			<i class="fa fa-plus"></i> 新增职位
		</button>
	</div>

	<!-- 搜索筛选栏（无重置按钮） -->
	<div class="filter-card">
		<form action="<?php echo adminUrl('department/position');?>" method="get" class="filter-form">
			<select name="status" class="form-control filter-select">
				<option value="-1" <?php echo ($curStatus !== 0 && $curStatus !== 1) ? 'selected' : '';?>>全部状态</option>
				<option value="1" <?php echo $curStatus === 1 ? 'selected' : '';?>>已启用</option>
				<option value="0" <?php echo $curStatus === 0 ? 'selected' : '';?>>未启用</option>
			</select>
			<input type="text" class="form-control filter-input" name="keyword" value="<?php echo htmlspecialchars($curKeyword, ENT_QUOTES, 'UTF-8');?>" placeholder="职位名称 / 备注关键字" autocomplete="off">
			<button class="btn btn-info filter-submit" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</form>
	</div>

	<!-- 职位卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-id-badge"></i>
				<p>暂无职位数据</p>
			</div>
		<?php } else { ?>
			<?php foreach (array_values($list) as $idx => $value) {
				$posId = (int)$value['pos_id'];
				$memCount = (int)($memberCountMap[$posId] ?? 0);
				$status = (int)($value['status'] ?? 0);
				$remark = trim((string)($value['remark'] ?? ''));
				$timeStr = !empty($value['update_time']) ? $value['update_time'] : ($value['add_time'] ?? '--');
				$isFirst = ($idx === 0 && (int)($search['page'] ?? 1) === 1);
				$isLast = ($idx === $listCount - 1);
				$rowData = [
					'pos_id' => $posId,
					'name'   => $value['name'],
					'remark' => $remark,
					'status' => $status,
					'sort'   => (int)$value['sort'],
				];
			?>
			<div class="pos-card item"
				data-id="<?php echo $posId;?>"
				data-info="<?php echo htmlspecialchars(json_encode($rowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<div class="card-top">
					<div class="pos-info">
						<div class="pos-title-line">
							<span class="id-badge">#<?php echo $posId;?></span>
							<strong class="pos-name"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></strong>
						</div>
						<?php if ($remark !== '') { ?>
							<div class="pos-remark"><?php echo htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');?></div>
						<?php } ?>
						<div class="pos-time">
							<i class="fa fa-clock"></i> <?php echo htmlspecialchars((string)$timeStr, ENT_QUOTES, 'UTF-8');?>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-primary btn-xs modify" title="编辑" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button type="button" class="btn btn-danger btn-xs delete" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>

				<div class="card-bottom">
					<div class="mem-badge-wrap">
						<?php if ($memCount > 0) { ?>
							<a href="<?php echo adminUrl('member/index', ['pos_id' => $posId]);?>" class="mem-count-badge" title="查看该职位人员">
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

	<div class="page-wrap">
		<?php echo page($search['size'] ?? 20, $total ?? 0);?>
	</div>

	<!-- 新增/编辑职位弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">新增职位</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="pos-form" onsubmit="return false;">
					<input type="hidden" name="pos_id" value="0">
					<input type="hidden" name="sort" value="0">
					<input type="hidden" name="opn" value="editPosInfo">

					<div class="form-group">
						<label class="form-label">职位名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="请输入职位名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">职位备注</label>
						<input type="text" class="form-control" name="remark" maxlength="255" placeholder="职位职责或说明（选填）" autocomplete="off">
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
