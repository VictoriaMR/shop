<?php
if (!function_exists('renderMobileAttrLogVal')) {
	function renderMobileAttrLogVal($val) {
		if ($val === null || $val === '') {
			return '<span class="text-muted">空</span>';
		}
		if (is_array($val)) {
			return '<code>' . htmlspecialchars(json_encode($val, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '</code>';
		}
		return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
	}
}
$typeMap = [
	0 => ['name' => '属性名',       'class' => 'biz-attr'],
	1 => ['name' => '属性名多语言', 'class' => 'biz-attr-lang'],
	2 => ['name' => '属性值',       'class' => 'biz-attrv'],
	3 => ['name' => '属性值多语言', 'class' => 'biz-attrv-lang'],
];
?>
<div id="attribute-log-page" class="attribute-log-page">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-clock-rotate-left"></i>
			<span>属性日志</span>
		</div>
		<span class="total-badge">共 <?php echo (int)$total;?> 条记录</span>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form method="get" action="<?php echo adminUrl('attribute/log');?>" class="search-form">
			<div class="filter-row">
				<div class="filter-item">
					<select class="form-control" name="type">
						<option value="-1">全部业务类型</option>
						<?php foreach (($typeList ?? [0 => '属性名', 1 => '属性名多语言', 2 => '属性值', 3 => '属性值多语言']) as $k => $v) { ?>
							<option value="<?php echo $k;?>" <?php echo (string)($search['type'] ?? -1) === (string)$k ? 'selected' : '';?>><?php echo htmlspecialchars($v, ENT_QUOTES, 'UTF-8');?></option>
						<?php } ?>
					</select>
				</div>
				<div class="filter-item">
					<input type="number" class="form-control" name="target_id" value="<?php echo htmlspecialchars((string)($search['target_id'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="关联ID (属性/属性值ID)">
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item">
					<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr((string)($search['stime'] ?? ''), 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="开始日期">
				</div>
				<span class="date-sep">至</span>
				<div class="filter-item">
					<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr((string)($search['etime'] ?? ''), 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="结束日期">
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string)($search['keyword'] ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="搜索操作简述关键字..." autocomplete="off">
				</div>
				<button type="submit" class="btn btn-info search-submit-btn"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 日志卡片列表 -->
	<div class="card-list" id="log-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无属性管理日志记录</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$m = $value['member'] ?? [];
				$avatar = $m['avatar'] ?? '';
				$hasCustomAvatar = !empty($avatar) && strpos($avatar, 'image/common/') === false;
				$firstName = $m['first_name'] ?? '';
				$lastName  = $m['last_name'] ?? '';
				$fullName  = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
				$nickName  = trim($m['nick_name'] ?? '');
				$displayName = $nickName ?: ($fullName ?: ('用户-' . (int)$value['mem_id']));

				$logType = (int)($value['type'] ?? 0);
				$curTypeInfo = $typeMap[$logType] ?? ['name' => '属性', 'class' => 'biz-attr'];

				$targetId = (int)($value['target_id'] ?? 0);
				$targetName = !empty($value['target_name']) ? $value['target_name'] : ($targetId > 0 ? ('ID #' . $targetId) : '-');
				$targetDesc = $value['target_desc'] ?? ($targetId > 0 ? ('ID: ' . $targetId) : '-');

				$infoText = (string)($value['info'] ?? '');
				$actionTagClass = 'act-default';
				if (strpos($infoText, '新增') !== false) {
					$actionTagClass = 'act-add';
				} elseif (strpos($infoText, '删除') !== false) {
					$actionTagClass = 'act-del';
				} elseif (strpos($infoText, '修改') !== false || strpos($infoText, '更改') !== false || strpos($infoText, '批量') !== false) {
					$actionTagClass = 'act-edit';
				}

				$detailRaw = $value['detail'] ?? '';
				$parsedDetail = !empty($detailRaw) ? json_decode($detailRaw, true) : null;
			?>
			<div class="log-card" data-id="<?php echo (int)$value['log_id'];?>">
				<!-- 顶部：操作人 + 业务类型 + 日志ID -->
				<div class="card-header">
					<div class="operator-box">
						<?php if ($hasCustomAvatar) { ?>
							<img class="operator-avatar" src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');?>" alt="avatar">
						<?php } else { ?>
							<span class="operator-avatar avatar-icon">
								<i class="fa <?php echo (int)$value['mem_id'] > 0 ? 'fa-user' : 'fa-robot';?>"></i>
							</span>
						<?php } ?>
						<div class="operator-meta">
							<div class="operator-name">
								<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');?>
								<?php if ((int)$value['mem_id'] > 0) { ?>
									<span class="uid-text">#<?php echo (int)$value['mem_id'];?></span>
								<?php } ?>
							</div>
							<div class="log-time"><?php echo $value['add_time'] ?: '-';?></div>
						</div>
					</div>
					<div class="header-right">
						<span class="biz-badge <?php echo $curTypeInfo['class'];?>"><?php echo $curTypeInfo['name'];?></span>
						<span class="id-badge">#<?php echo (int)$value['log_id'];?></span>
					</div>
				</div>

				<!-- 主体：操作动作 + 关联属性 + 变更摘要 -->
				<div class="card-body">
					<div class="action-line">
						<span class="action-badge <?php echo $actionTagClass;?>"><?php echo htmlspecialchars($infoText ?: '-', ENT_QUOTES, 'UTF-8');?></span>
						<span class="target-info">
							<strong><?php echo htmlspecialchars($targetName, ENT_QUOTES, 'UTF-8');?></strong>
							<span class="target-id">(<?php echo htmlspecialchars($targetDesc, ENT_QUOTES, 'UTF-8');?>)</span>
						</span>
					</div>

					<?php if (is_array($parsedDetail) && !empty($parsedDetail)) {
						$shown = 0;
						$totalFields = count($parsedDetail);
					?>
					<div class="diff-preview">
						<?php foreach ($parsedDetail as $field => $item) {
							if ($shown >= 2) break;
							$shown++;
							if (is_array($item)) {
								$fieldKey = (string)($item['field'] ?? $field);
								$label = htmlspecialchars((string)($item['label'] ?? $fieldKey), ENT_QUOTES, 'UTF-8');
								$isDiffItem = array_key_exists('old', $item) || array_key_exists('new', $item);
							} else {
								$fieldKey = (string)$field;
								$label = htmlspecialchars($fieldKey, ENT_QUOTES, 'UTF-8');
								$isDiffItem = false;
							}
						?>
							<?php if ($isDiffItem) { ?>
								<div class="diff-row">
									<span class="diff-field"><?php echo $label;?>：</span>
									<span class="diff-old"><?php echo renderMobileAttrLogVal($item['old'] ?? null);?></span>
									<i class="fa fa-arrow-right diff-arrow"></i>
									<span class="diff-new"><?php echo renderMobileAttrLogVal($item['new'] ?? null);?></span>
								</div>
							<?php } else {
								$snapVal = is_array($item) ? ($item['val'] ?? ($item['value'] ?? null)) : $item;
							?>
								<div class="diff-row">
									<span class="diff-field"><?php echo $label;?>：</span>
									<span class="diff-cur"><?php echo renderMobileAttrLogVal($snapVal);?></span>
								</div>
							<?php } ?>
						<?php } ?>
						<?php if ($totalFields > 2) { ?>
							<div class="diff-more">等共 <?php echo $totalFields;?> 项字段记录...</div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>

				<?php if (is_array($parsedDetail) && !empty($parsedDetail)) { ?>
				<div class="card-footer">
					<button type="button" class="btn btn-default btn-xs view-detail-btn"
						data-detail="<?php echo htmlspecialchars(json_encode($parsedDetail, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>"
						data-info="<?php echo htmlspecialchars($infoText, ENT_QUOTES, 'UTF-8');?>"
						data-type="<?php echo htmlspecialchars($curTypeInfo['name'], ENT_QUOTES, 'UTF-8');?>"
						data-target="<?php echo htmlspecialchars($targetName . ' (' . $targetDesc . ')', ENT_QUOTES, 'UTF-8');?>"
						data-user="<?php echo htmlspecialchars($displayName . ($value['mem_id'] > 0 ? ' (#' . (int)$value['mem_id'] . ')' : ''), ENT_QUOTES, 'UTF-8');?>"
						data-time="<?php echo htmlspecialchars($value['add_time'], ENT_QUOTES, 'UTF-8');?>">
						<i class="fa fa-eye"></i> 查看详情
					</button>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size ?? 20, $total);?>

	<!-- 详情弹窗（只读详情弹窗不带底部关闭按钮） -->
	<div id="dealbox-log-detail" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-file-lines"></i> 属性日志变更明细</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="detail-meta-box">
					<div class="detail-meta-top">
						<div>
							<span class="biz-badge modal-type-name">-</span>
							<strong class="modal-action-title">-</strong>
						</div>
						<button type="button" class="btn btn-default btn-xs copy-json-btn">
							<i class="fa fa-copy"></i> 复制JSON
						</button>
					</div>
					<div class="detail-meta-line">关联属性：<span class="modal-target-name">-</span></div>
					<div class="detail-meta-line modal-meta-info">-</div>
				</div>
				<div class="diff-detail-list" id="diff-table-body"></div>
			</div>
		</div>
	</div>
</div>
