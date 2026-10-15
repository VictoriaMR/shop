<?php
$moduleBadgeMap = [
	0 => ['label-primary', '人员管理'],
	1 => ['label-info', '部门管理'],
	2 => ['label-success', '职位管理'],
];
?>
<div class="container-fluid" id="member-log-page">
	<form action="<?php echo adminUrl('member/log');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group">
					<select name="log_type" class="form-control">
						<option value="-1" <?php echo (int)($search['log_type'] ?? -1) === -1 ? 'selected' : '';?>>全部日志模块</option>
						<?php foreach ($moduleTypes ?? [] as $mKey => $mTitle) { ?>
						<option value="<?php echo (int)$mKey;?>" <?php echo (int)($search['log_type'] ?? -1) === (int)$mKey ? 'selected' : '';?>><?php echo htmlspecialchars($mTitle);?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group ml6">
					<select name="type" class="form-control">
						<?php foreach ($typeArr as $key => $title) { ?>
						<option value="<?php echo $key;?>" <?php echo ($search['type'] ?? 'all') === $key ? 'selected' : '';?>><?php echo $title;?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group ml6">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '');?>" placeholder="记录详情关键字" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($search['name'] ?? '');?>" placeholder="操作人姓名 / 昵称" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<input type="text" class="form-control" name="ip" value="<?php echo htmlspecialchars($search['ip'] ?? '');?>" placeholder="IP 地址" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime'] ?? '');?>" name="stime" placeholder="开始时间" autocomplete="off">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime'] ?? '');?>" name="etime" placeholder="结束时间" autocomplete="off">
				</div>
				<div class="form-group ml6">
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
				<th width="110" class="tc">所属模块</th>
				<th>记录详情</th>
				<th width="140">客户端 IP</th>
				<th width="170">记录时间</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="6">
					<div class="tc orange">
						<i class="glyphicon glyphicon-info-sign"></i> 暂无日志记录
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) { 
				$m = $value['member'] ?? [];
				$avatar = !empty($m['avatar']) ? $m['avatar'] : siteUrl('/image/common/male.jpg');
				$infoText = $value['info'];
				$logModType = (int)($value['type'] ?? 0);
				$modBadge = $moduleBadgeMap[$logModType] ?? $moduleBadgeMap[0];
				
				$fullName = trim($m['fullName'] ?? '');
				$nickName = trim($m['nick_name'] ?? '');
				if ($fullName !== '' && $fullName !== $nickName) {
					$mainName = $fullName;
					$subName  = $nickName;
				} else {
					$mainName = $nickName ?: ('用户-' . $value['mem_id']);
					$subName  = $value['mem_id'] > 0 ? ('UID: ' . $value['mem_id']) : '';
				}
			?>
			<tr>
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
				<td class="tc">
					<span class="label <?php echo $modBadge[0];?>"><?php echo $modBadge[1];?></span>
				</td>
				<td><?php echo htmlspecialchars($infoText);?></td>
				<td>
					<code><?php echo !empty($value['ip']) ? htmlspecialchars($value['ip']) : '-';?></code>
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