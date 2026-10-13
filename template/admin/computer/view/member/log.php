<div class="container-fluid" id="member-log-page">
	<form action="<?php echo adminUrl('member/log');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select name="type" class="form-control">
						<?php foreach ($typeArr as $key => $title) { ?>
						<option value="<?php echo $key;?>" <?php echo ($search['type'] ?? 'all') === $key ? 'selected' : '';?>><?php echo $title;?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '');?>" placeholder="记录详情关键字" autocomplete="off">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($search['name'] ?? '');?>" placeholder="操作人姓名 / 昵称" autocomplete="off">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="ip" value="<?php echo htmlspecialchars($search['ip'] ?? '');?>" placeholder="IP 地址" autocomplete="off" style="width: 130px;">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime'] ?? '');?>" name="stime" placeholder="开始时间" autocomplete="off" style="width: 140px;">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime'] ?? '');?>" name="etime" placeholder="结束时间" autocomplete="off" style="width: 140px;">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
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
				
				// 操作人姓名与昵称处理（模仿图中简洁两行头像风格）
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
				<td>
					<span style="color: #333; word-break: break-all;"><?php echo htmlspecialchars($infoText);?></span>
				</td>
				<td>
					<span class="label label-default" style="background:#f4f5f9; color:#555; border:1px solid #e0e3ea; font-family: monospace; font-weight: normal; padding: 2px 6px;">
						<?php echo !empty($value['ip']) ? htmlspecialchars($value['ip']) : '-';?>
					</span>
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