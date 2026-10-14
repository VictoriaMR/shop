<div class="container-fluid" id="attribute-log-page">
	<form action="<?php echo adminUrl('attribute/log');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<select class="form-control" name="type">
						<option value="-1">全部业务类型</option>
						<?php foreach (($typeList ?? [0 => '属性名', 1 => '属性名多语言', 2 => '属性值', 3 => '属性值多语言']) as $k => $v){ ?>
							<option value="<?php echo $k;?>" <?php echo (string)($search['type'] ?? -1) === (string)$k ? 'selected' : '';?>><?php echo $v;?></option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="target_id" value="<?php echo htmlspecialchars($search['target_id'] ?? '', ENT_QUOTES, 'UTF-8');?>" placeholder="关联ID (属性名/属性值ID)" autocomplete="off" style="width: 170px;">
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '', ENT_QUOTES, 'UTF-8');?>" placeholder="操作简述关键字" autocomplete="off" style="width: 160px;">
				</div>

				<div class="form-group" style="margin-right: 8px; margin-bottom: 10px;">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime'] ?? '', ENT_QUOTES, 'UTF-8');?>" name="stime" placeholder="开始日期" autocomplete="off" style="width: 120px;">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime'] ?? '', ENT_QUOTES, 'UTF-8');?>" name="etime" placeholder="结束日期" autocomplete="off" style="width: 120px;">
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
				<th width="190">操作人</th>
				<th width="130">业务类型</th>
				<th width="180">关联属性 (ID)</th>
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
						<i class="glyphicon glyphicon-info-sign"></i> 暂无属性管理日志记录
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) { 
				$m = $value['member'] ?? [];
				$avatar = !empty($m['avatar']) ? $m['avatar'] : siteUrl('image/common/male.jpg');
				$firstName = $m['first_name'] ?? '';
				$lastName  = $m['last_name'] ?? '';
				$fullName  = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
				$nickName  = trim($m['nick_name'] ?? '');
				if ($fullName !== '' && $fullName !== $nickName) {
					$mainName = $fullName;
					$subName  = $nickName;
				} else {
					$mainName = $nickName ?: ('用户-' . $value['mem_id']);
					$subName  = $value['mem_id'] > 0 ? ('UID: ' . $value['mem_id']) : '';
				}

				$logType   = (int)($value['type'] ?? 0);
				$targetId  = (int)($value['target_id'] ?? 0);
				$targetName = !empty($value['target_name']) ? $value['target_name'] : ($targetId > 0 ? ('ID #' . $targetId) : '-');
				$targetDesc = $value['target_desc'] ?? ($targetId > 0 ? ('ID: ' . $targetId) : '-');

				$typeMap = [
					0 => ['name' => '属性名',       'class' => 'label-primary'],
					1 => ['name' => '属性名多语言', 'class' => 'label-info'],
					2 => ['name' => '属性值',       'class' => 'label-success'],
					3 => ['name' => '属性值多语言', 'class' => 'label-warning'],
				];
				$curTypeInfo = $typeMap[$logType] ?? ['name' => '属性', 'class' => 'label-default'];

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
			<tr>
				<td><strong><?php echo $value['log_id'];?></strong></td>
				<td>
					<div class="userAvatarInfo">
						<span class="avatarStyle"><img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');?>" alt="avatar"></span>
						<span class="two">
							<p class="name"><?php echo htmlspecialchars($mainName, ENT_QUOTES, 'UTF-8');?></p>
							<?php if ($subName){ ?>
							<p class="sub-name"><?php echo htmlspecialchars($subName, ENT_QUOTES, 'UTF-8');?></p>
							<?php } ?>
						</span>
					</div>
				</td>
				<td>
					<span class="label <?php echo $curTypeInfo['class'];?>" style="font-weight: normal; padding: 3px 8px; font-size: 12px;">
						<?php echo $curTypeInfo['name'];?>
					</span>
				</td>
				<td>
					<strong style="color: #0f172a; display: block; font-size: 13px;"><?php echo htmlspecialchars($targetName, ENT_QUOTES, 'UTF-8');?></strong>
					<span class="text-muted f12"><?php echo htmlspecialchars($targetDesc, ENT_QUOTES, 'UTF-8');?></span>
				</td>
				<td>
					<span style="font-weight: 500; color: #334155;"><?php echo htmlspecialchars($value['info'], ENT_QUOTES, 'UTF-8');?></span>
				</td>
				<td>
					<?php if (!empty($detailList)){ ?>
					<div class="detail-summary" style="font-size: 12px; line-height: 1.6;">
						<?php 
						$displayItems = array_slice($detailList, 0, 3);
						foreach ($displayItems as $item){
							if (is_array($item)) {
								$label = $item['label'] ?? ($item['field'] ?? '属性');
								if ($isDiff) {
									$oldVal = (string)($item['old'] ?? '-');
									$newVal = (string)($item['new'] ?? '-');
									echo '<div style="margin-bottom: 2px;">';
									echo '<span class="text-muted">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ':</span> ';
									echo '<span style="color:#64748b; text-decoration:line-through; margin-right:4px;">' . htmlspecialchars($oldVal, ENT_QUOTES, 'UTF-8') . '</span>';
									echo '<span style="color:#94a3b8; margin-right:4px;">&rarr;</span>';
									echo '<strong style="color:#0284c7;">' . htmlspecialchars($newVal, ENT_QUOTES, 'UTF-8') . '</strong>';
									echo '</div>';
								} else {
									$val = isset($item['val']) ? (string)$item['val'] : json_encode($item, JSON_UNESCAPED_UNICODE);
									echo '<div style="margin-bottom: 2px;">';
									echo '<span class="text-muted">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ':</span> ';
									echo '<strong style="color:#334155;">' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '</strong>';
									echo '</div>';
								}
							}
						}
						?>
						<?php if (count($detailList) > 3){ ?>
						<span class="text-muted f12">...共 <?php echo count($detailList);?> 项变动</span>
						<?php } ?>
						<button type="button" class="btn btn-default btn-xs view-detail-btn" style="margin-top: 4px;" 
							data-info="<?php echo htmlspecialchars($value['info'], ENT_QUOTES, 'UTF-8');?>"
							data-type="<?php echo $logType;?>"
							data-target="<?php echo htmlspecialchars($targetName . ' (' . $targetDesc . ')', ENT_QUOTES, 'UTF-8');?>"
							data-user="<?php echo htmlspecialchars($mainName . ($subName ? ' - ' . $subName : ''), ENT_QUOTES, 'UTF-8');?>"
							data-time="<?php echo $value['add_time'];?>"
							data-detail="<?php echo htmlspecialchars(json_encode($detailList, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
							<i class="glyphicon glyphicon-eye-open"></i> 查看完整明细
						</button>
					</div>
					<?php } else { ?>
					<span class="text-muted">-</span>
					<?php } ?>
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
			<?php echo page($size ?? 20, $total);?>
		</div>
	</div>
</div>

<!-- 查看字段明细模态框 -->
<div id="dealbox-log-detail" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 660px; max-width: 90vw;">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="dealbox-title">
			<span>字段变更记录明细</span>
		</div>
		
		<div class="modal-meta-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; margin-bottom: 15px; font-size: 13px;">
			<div style="margin-bottom: 4px;">
				<span class="text-muted">业务类别：</span>
				<strong class="modal-type-name" style="color: #0284c7;">-</strong>
				<span style="margin: 0 8px; color: #cbd5e1;">|</span>
				<span class="text-muted">操作行为：</span>
				<strong class="modal-action-title" style="color: #1e293b;">-</strong>
			</div>
			<div>
				<span class="text-muted">关联目标：</span>
				<strong class="modal-target-name" style="color: #334155;">-</strong>
				<span style="margin: 0 8px; color: #cbd5e1;">|</span>
				<span class="text-muted">操作人 / 时间：</span>
				<span class="modal-meta-info" style="color: #64748b;">-</span>
			</div>
		</div>

		<div class="diff-table-container" style="max-height: 380px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 15px;">
			<table class="table table-bordered table-striped" style="margin-bottom: 0; font-size: 13px;">
				<thead>
					<!-- 动态生成 -->
				</thead>
				<tbody id="diff-table-body">
					<!-- 动态插入 -->
				</tbody>
			</table>
		</div>

		<div class="modal-footer-custom" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 12px;">
			<button type="button" class="btn btn-default btn-sm copy-json-btn">
				<i class="glyphicon glyphicon-copy"></i> 复制原始数据 (JSON)
			</button>
			<button type="button" class="btn btn-primary btn-sm close-btn">关闭</button>
		</div>
	</div>
</div>
