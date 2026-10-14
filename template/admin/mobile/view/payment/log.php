<?php
if (!function_exists('renderMobilePaymentLogVal')) {
	function renderMobilePaymentLogVal($field, $val, $typeList, $siteList, $paymentMap) {
		if ($val === null || $val === '') {
			return '<span class="text-muted">空</span>';
		}
		if (is_array($val)) {
			return '<code>' . htmlspecialchars(json_encode($val, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '</code>';
		}
		$strVal = (string)$val;
		if ($field === 'status') {
			if ($strVal === '1' || $strVal === '启用') {
				return '<span class="val-tag tag-success">启用</span>';
			}
			if ($strVal === '0' || $strVal === '停用') {
				return '<span class="val-tag tag-default">停用</span>';
			}
		}
		if ($field === 'is_sandbox') {
			if ($strVal === '1' || strpos($strVal, '沙盒') !== false) {
				return '<span class="val-tag tag-warning">沙盒环境</span>';
			}
			if ($strVal === '0' || strpos($strVal, '生产') !== false || strpos($strVal, '正式') !== false) {
				return '<span class="val-tag tag-info">正式环境</span>';
			}
		}
		if ($field === 'type' && is_numeric($strVal) && isset($typeList[(int)$strVal])) {
			return htmlspecialchars($typeList[(int)$strVal] . ' (' . $strVal . ')', ENT_QUOTES, 'UTF-8');
		}
		if ($field === 'site_id' && is_numeric($strVal) && isset($siteList[(int)$strVal])) {
			return htmlspecialchars($siteList[(int)$strVal] . ' (#' . $strVal . ')', ENT_QUOTES, 'UTF-8');
		}
		if ($field === 'payment_id' && is_numeric($strVal) && isset($paymentMap[(int)$strVal])) {
			return htmlspecialchars($paymentMap[(int)$strVal] . ' (#' . $strVal . ')', ENT_QUOTES, 'UTF-8');
		}
		if (in_array($field, ['app_key', 'secret_key', 'webhook_key'], true)) {
			$short = mb_strlen($strVal) > 24 ? mb_substr($strVal, 0, 10) . '...' . mb_substr($strVal, -6) : $strVal;
			return '<code>' . htmlspecialchars($short, ENT_QUOTES, 'UTF-8') . '</code>';
		}
		return htmlspecialchars($strVal, ENT_QUOTES, 'UTF-8');
	}
}
?>
<div id="payment-log-page" class="payment-log-page"
	data-types="<?php echo htmlspecialchars(json_encode($typeList ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>"
	data-sites="<?php echo htmlspecialchars(json_encode($siteList ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>"
	data-payments="<?php echo htmlspecialchars(json_encode($paymentMap ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">

	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-clock-rotate-left"></i>
			<span>支付日志</span>
		</div>
		<span class="total-badge">共 <?php echo (int)$total;?> 条记录</span>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form method="get" action="<?php echo adminUrl('payment/log');?>" class="search-form">
			<div class="filter-row">
				<div class="filter-item">
					<select class="form-control" name="type">
						<option value="-1" <?php if ((string)$search['type'] === '-1' || $search['type'] === '') echo 'selected';?>>全部业务类型</option>
						<option value="0" <?php if ((string)$search['type'] === '0') echo 'selected';?>>账户配置</option>
						<option value="1" <?php if ((string)$search['type'] === '1') echo 'selected';?>>账户使用</option>
					</select>
				</div>
				<div class="filter-item">
					<input type="number" class="form-control" name="item_id" value="<?php echo htmlspecialchars((string)$search['item_id'], ENT_QUOTES, 'UTF-8');?>" placeholder="关联ID (账号/配置ID)">
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item">
					<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr((string)$search['stime'], 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="开始日期">
				</div>
				<span class="date-sep">至</span>
				<div class="filter-item">
					<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr((string)$search['etime'], 0, 10), ENT_QUOTES, 'UTF-8');?>" placeholder="结束日期">
				</div>
			</div>
			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string)$search['keyword'], ENT_QUOTES, 'UTF-8');?>" placeholder="搜索操作说明关键词..." autocomplete="off">
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
				<p>暂无匹配的支付操作日志</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$member = $value['member'] ?? [];
				$avatar = $member['avatar'] ?? '';
				$hasCustomAvatar = !empty($avatar) && strpos($avatar, 'image/common/') === false;
				$displayName = $member['nick_name'] ?: ($member['fullName'] ?: ('用户-' . $value['mem_id']));

				$isUsedLog = (int)$value['type'] === 1;
				$typeLabel = $isUsedLog ? '账户使用' : '账户配置';
				$typeBadgeClass = $isUsedLog ? 'biz-used' : 'biz-account';

				$itemId = (int)$value['item_id'];
				$targetName = '';
				if (!$isUsedLog && isset($paymentMap[$itemId])) {
					$targetName = $paymentMap[$itemId];
				}
				$parsedDetail = !empty($value['detail']) ? json_decode($value['detail'], true) : null;
				if (!$targetName && is_array($parsedDetail)) {
					foreach ($parsedDetail as $k => $v) {
						if (is_array($v)) {
							$fKey = $v['field'] ?? $k;
							$fVal = $v['val'] ?? ($v['value'] ?? ($v['new'] ?? ''));
							if ($fKey === 'name' && $fVal !== '') {
								$targetName = (string)$fVal;
								break;
							}
							if ($fKey === 'site_id' && $fVal !== '') {
								$targetName = is_numeric($fVal) ? ($siteList[(int)$fVal] ?? ('站点#' . $fVal)) : (string)$fVal;
								break;
							}
						} elseif ($k === 'name' && is_scalar($v) && (string)$v !== '') {
							$targetName = (string)$v;
							break;
						}
					}
				}

				$infoText = (string)($value['info'] ?? '');
				$actionTagClass = 'act-default';
				if (strpos($infoText, '新增') !== false) {
					$actionTagClass = 'act-add';
				} elseif (strpos($infoText, '删除') !== false) {
					$actionTagClass = 'act-del';
				} elseif (strpos($infoText, '修改') !== false || strpos($infoText, '更改') !== false) {
					$actionTagClass = 'act-edit';
				} elseif (strpos($infoText, '查看') !== false) {
					$actionTagClass = 'act-view';
				}
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
						<span class="biz-badge <?php echo $typeBadgeClass;?>"><?php echo $typeLabel;?></span>
						<span class="id-badge">#<?php echo (int)$value['log_id'];?></span>
					</div>
				</div>

				<!-- 主体：操作动作 + 关联对象 + 变更摘要 -->
				<div class="card-body">
					<div class="action-line">
						<span class="action-badge <?php echo $actionTagClass;?>"><?php echo htmlspecialchars($infoText ?: '-', ENT_QUOTES, 'UTF-8');?></span>
						<span class="target-info">
							<?php if ($targetName) { ?>
								<strong><?php echo htmlspecialchars($targetName, ENT_QUOTES, 'UTF-8');?></strong>
							<?php } ?>
							<span class="target-id">(ID: <?php echo $itemId;?>)</span>
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
									<span class="diff-old"><?php echo renderMobilePaymentLogVal($fieldKey, $item['old'] ?? null, $typeList, $siteList, $paymentMap);?></span>
									<i class="fa fa-arrow-right diff-arrow"></i>
									<span class="diff-new"><?php echo renderMobilePaymentLogVal($fieldKey, $item['new'] ?? null, $typeList, $siteList, $paymentMap);?></span>
								</div>
							<?php } else {
								$snapVal = is_array($item) ? ($item['val'] ?? ($item['value'] ?? null)) : $item;
							?>
								<div class="diff-row">
									<span class="diff-field"><?php echo $label;?>：</span>
									<span class="diff-cur"><?php echo renderMobilePaymentLogVal($fieldKey, $snapVal, $typeList, $siteList, $paymentMap);?></span>
								</div>
							<?php } ?>
						<?php } ?>
						<?php if ($totalFields > 2) { ?>
							<div class="diff-more">等共 <?php echo $totalFields;?> 项字段记录...</div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>

				<?php if (!empty($value['detail']) && $value['detail'] !== '[]' && $value['detail'] !== '{}') { ?>
				<div class="card-footer">
					<button type="button" class="btn btn-default btn-xs view-detail-btn"
						data-detail="<?php echo htmlspecialchars($value['detail'], ENT_QUOTES, 'UTF-8');?>"
						data-info="<?php echo htmlspecialchars($infoText, ENT_QUOTES, 'UTF-8');?>"
						data-type="<?php echo $typeLabel;?>"
						data-target="<?php echo htmlspecialchars(($targetName ? $targetName . ' ' : '') . '(ID:' . $itemId . ')', ENT_QUOTES, 'UTF-8');?>"
						data-user="<?php echo htmlspecialchars($displayName . ($value['mem_id'] > 0 ? ' (#' . $value['mem_id'] . ')' : ''), ENT_QUOTES, 'UTF-8');?>"
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
	<?php echo page($search['size'], $total);?>

	<!-- 详情弹窗 -->
	<div id="dealbox-log-detail" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-file-lines"></i> 日志变更详情</h4>
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
					<div class="detail-meta-line">关联对象：<span class="modal-target-name">-</span></div>
					<div class="detail-meta-line modal-meta-info">-</div>
				</div>
				<div class="diff-detail-list" id="diff-table-body"></div>
			</div>
		</div>
	</div>
</div>
