<div class="container-fluid" id="payment-log-page">
	<form action="<?php echo adminUrl('payment/log');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<select class="form-control" name="type">
						<option value="-1">全部业务类型</option>
						<option value="0" <?php echo (string)($search['type'] ?? -1) === '0' ? 'selected' : '';?>>账户配置 (payment)</option>
						<option value="1" <?php echo (string)($search['type'] ?? -1) === '1' ? 'selected' : '';?>>账户使用 (payment_used)</option>
					</select>
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="item_id" value="<?php echo htmlspecialchars($search['item_id'] ?? '');?>" placeholder="目标ID (payment_id / item_id)" autocomplete="off" style="width: 200px;">
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '');?>" placeholder="操作简述关键字" autocomplete="off" style="width: 150px;">
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime'] ?? '');?>" name="stime" placeholder="开始日期" autocomplete="off" style="width: 120px;">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime'] ?? '');?>" name="etime" placeholder="结束日期" autocomplete="off" style="width: 120px;">
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="log-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="200">操作人</th>
				<th width="120">业务类型</th>
				<th width="180">关联目标 (ID)</th>
				<th width="140">操作行为</th>
				<th>字段变更明细 / 数据快照</th>
				<th width="170">记录时间</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="7">
					<div class="tc orange">
						<i class="glyphicon glyphicon-info-sign"></i> 暂无支付管理日志记录
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) { 
				$m = $value['member'] ?? [];
				$avatar = $m['avatar'] ?? siteUrl('/image/common/male.jpg');
				$fullName = trim($m['fullName'] ?? '');
				$nickName = trim($m['nick_name'] ?? '');
				if ($fullName !== '' && $fullName !== $nickName) {
					$mainName = $fullName;
					$subName  = $nickName;
				} else {
					$mainName = $nickName ?: ('用户-' . $value['mem_id']);
					$subName  = $value['mem_id'] > 0 ? ('UID: ' . $value['mem_id']) : '';
				}

				$logType = (int)($value['type'] ?? 0);
				$itemId = (int)($value['item_id'] ?? 0);

				if ($logType === 0) {
					$typeName = '账户配置';
					$targetName = $paymentMap[$itemId] ?? ($itemId > 0 ? ('账户-' . $itemId) : '-');
					$targetDesc = '账户 ID: ' . $itemId;
				} else {
					$typeName = '账户使用';
					$targetName = '使用配置 #' . $itemId;
					$targetDesc = 'item_id: ' . $itemId;
				}

				// 解析字段变更 JSON
				$detailRaw = $value['detail'] ?? '';
				$detailList = [];
				if (!empty($detailRaw)) {
					$decoded = json_decode($detailRaw, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
						$detailList = $decoded;
					}
				}

				// 判断是 Diff（修改对比）还是 Snapshot（快照）
				$isDiff = false;
				if (!empty($detailList) && is_array($detailList)) {
					$first = reset($detailList);
					if (is_array($first) && (isset($first['old']) || isset($first['new']))) {
						$isDiff = true;
					}
				}
			?>
			<tr data-log-id="<?php echo $value['log_id'];?>">
				<td><strong><?php echo $value['log_id'];?></strong></td>
				<td>
					<div class="userAvatarInfo">
						<span class="avatarStyle"><img src="<?php echo $avatar;?>" alt="avatar"></span>
						<span class="two">
							<p class="name"><?php echo htmlspecialchars($mainName);?></p>
							<?php if (!empty($subName)){ ?>
							<p class="sub-name"><?php echo htmlspecialchars($subName);?></p>
							<?php } ?>
						</span>
					</div>
				</td>
				<td>
					<?php if ($logType === 0){ ?>
					<span class="label label-default" style="background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; font-weight:normal;">
						账户配置
					</span>
					<?php } else { ?>
					<span class="label label-default" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-weight:normal;">
						账户使用
					</span>
					<?php } ?>
				</td>
				<td>
					<strong style="color: #0f172a;"><?php echo htmlspecialchars($targetName);?></strong>
					<span class="text-muted f12" style="display:block;"><?php echo htmlspecialchars($targetDesc);?></span>
				</td>
				<td>
					<span style="font-weight: 500; color: #1e293b;">
						<?php echo htmlspecialchars($value['info']);?>
					</span>
				</td>
				<td>
					<div style="display: flex; align-items: center; justify-content: space-between;">
						<div class="field-diff-summary" style="font-size: 13px; max-width: 480px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
							<?php if (empty($detailList)){ ?>
								<span class="text-muted"><?php echo !empty($detailRaw) ? htmlspecialchars($detailRaw) : '无字段变动';?></span>
							<?php } elseif ($isDiff){ ?>
								<!-- 修改对比展示 -->
								<?php 
								$diffSnippets = [];
								foreach ($detailList as $item) {
									$label = $item['label'] ?? ($item['field'] ?? '字段');
									$old = $item['old'] ?? '';
									$new = $item['new'] ?? '';
									$diffSnippets[] = '<strong>' . htmlspecialchars($label) . ':</strong> <span style="color:#64748b; text-decoration:line-through;">' . htmlspecialchars($old) . '</span> &rarr; <span style="color:#0284c7; font-weight:600;">' . htmlspecialchars($new) . '</span>';
								}
								echo implode('<span style="margin: 0 8px; color: #cbd5e1;">|</span>', $diffSnippets);
								?>
							<?php } else { ?>
								<!-- 完整快照展示 -->
								<?php 
								$snapSnippets = [];
								foreach ($detailList as $item) {
									$label = $item['label'] ?? ($item['field'] ?? '属性');
									$val = $item['val'] ?? (is_scalar($item) ? $item : '');
									$snapSnippets[] = htmlspecialchars($label) . ': ' . htmlspecialchars($val);
								}
								echo '<span style="color: #475569;">' . htmlspecialchars(implode(' | ', $snapSnippets)) . '</span>';
								?>
							<?php } ?>
						</div>

						<?php if (!empty($detailList)){ ?>
						<button type="button" class="btn btn-default btn-xs view-detail-btn" style="margin-left: 8px; flex-shrink: 0;" data-detail="<?php echo htmlspecialchars($detailRaw);?>" data-info="<?php echo htmlspecialchars($value['info']);?>" data-type="<?php echo $logType;?>" data-target="<?php echo htmlspecialchars($targetName);?>" data-user="<?php echo htmlspecialchars($mainName);?>" data-time="<?php echo htmlspecialchars($value['add_time']);?>">
							<i class="glyphicon glyphicon-list-alt"></i> 明细
						</button>
						<?php } ?>
					</div>
				</td>
				<td>
					<span class="text-muted"><i class="glyphicon glyphicon-time f12"></i> <?php echo $value['add_time'];?></span>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12">
			<?php echo page($search['size'] ?? 20, $total);?>
		</div>
	</div>
</div>

<!-- 字段变更详情弹窗 -->
<div id="dealbox-log-detail" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 620px;">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">字段变更记录明细</div>
		
		<div style="margin-bottom: 14px; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px;">
			<div class="row">
				<div class="col-xs-6" style="margin-bottom: 6px;">
					<span class="text-muted f12">业务类型：</span>
					<strong class="modal-type-name">-</strong>
				</div>
				<div class="col-xs-6" style="margin-bottom: 6px;">
					<span class="text-muted f12">操作行为：</span>
					<strong class="modal-action-title text-primary">-</strong>
				</div>
				<div class="col-xs-6">
					<span class="text-muted f12">关联目标：</span>
					<span class="modal-target-name font-bold">-</span>
				</div>
				<div class="col-xs-6">
					<span class="text-muted f12">操作人 / 时间：</span>
					<span class="modal-meta-info text-muted f12">-</span>
				</div>
			</div>
		</div>

		<!-- 结构化字段对比表格容器 -->
		<div class="form-group" style="margin-bottom: 12px;">
			<label style="font-weight: 600; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
				<span>变更字段明细对比:</span>
				<button type="button" class="btn btn-default btn-xs copy-json-btn"><i class="glyphicon glyphicon-copy"></i> 复制原始数据</button>
			</label>
			
			<div class="diff-table-container" style="max-height: 280px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px;">
				<table class="table table-bordered table-striped" style="margin-bottom: 0; font-size: 13px;">
					<thead style="background: #f1f5f9;">
						<tr>
							<th width="140">字段名称</th>
							<th width="200">修改前 (原值)</th>
							<th>修改后 (新值)</th>
						</tr>
					</thead>
					<tbody id="diff-table-body">
						<!-- 动态插入 -->
					</tbody>
				</table>
			</div>
		</div>

		<div style="margin-top: 18px; text-align: right;">
			<button type="button" class="btn btn-default close-btn">关闭</button>
		</div>
	</div>
</div>
