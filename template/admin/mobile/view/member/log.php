<div class="log-container" id="member-log-page">
	<!-- 筛选卡片 -->
	<form action="<?php echo adminUrl('member/log');?>" method="get" class="search-card">
		<div class="filter-grid">
			<div class="filter-item">
				<select name="type" class="form-control">
					<?php foreach ($typeArr as $key => $title) { ?>
					<option value="<?php echo htmlspecialchars($key);?>" <?php echo ($search['type'] ?? 'all') === $key ? 'selected' : '';?>><?php echo htmlspecialchars($title);?></option>
					<?php } ?>
				</select>
			</div>
			<div class="filter-item">
				<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '');?>" placeholder="记录详情关键字" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($search['name'] ?? '');?>" placeholder="操作人姓名 / 昵称" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="text" class="form-control" name="ip" value="<?php echo htmlspecialchars($search['ip'] ?? '');?>" placeholder="客户端 IP 地址" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr($search['stime'] ?? '', 0, 10));?>" placeholder="开始日期">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr($search['etime'] ?? '', 0, 10));?>" placeholder="结束日期">
			</div>
		</div>

		<div class="filter-actions">
			<button class="btn btn-info btn-block" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 日志卡片列表 -->
	<div class="log-list" id="log-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-clipboard-list"></i>
			<p>暂无日志记录</p>
		</div>
		<?php } else { ?>
		<?php foreach ($list as $value) {
			$m = $value['member'] ?? [];
			$rawAvatar = $m['avatar'] ?? '';
			$hasCustomAvatar = $rawAvatar !== '' && strpos($rawAvatar, 'image/common/') === false;
			$infoText = $value['info'] ?? '';

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
		<div class="log-card">
			<div class="log-header">
				<div class="user-main">
					<div class="user-avatar">
						<?php if ($hasCustomAvatar) { ?>
						<img src="<?php echo htmlspecialchars($rawAvatar, ENT_QUOTES, 'UTF-8');?>" alt="avatar">
						<?php } else { ?>
						<i class="fa <?php echo (int)$value['mem_id'] > 0 ? 'fa-user' : 'fa-robot';?>"></i>
						<?php } ?>
					</div>
					<div class="user-titles">
						<div class="user-name e1"><?php echo htmlspecialchars($mainName);?></div>
						<?php if ($subName !== '') { ?>
						<div class="user-sub e1"><?php echo htmlspecialchars($subName);?></div>
						<?php } ?>
					</div>
				</div>
				<div class="log-meta-right">
					<span class="log-id-badge">#<?php echo (int)$value['log_id'];?></span>
					<span class="ip-badge"><?php echo !empty($value['ip']) ? htmlspecialchars($value['ip']) : '-';?></span>
				</div>
			</div>

			<div class="log-content">
				<?php echo htmlspecialchars($infoText);?>
			</div>

			<div class="log-footer">
				<i class="fa fa-clock"></i>
				<span><?php echo htmlspecialchars($value['add_time'] ?? '-');?></span>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页 -->
	<div class="mt12">
		<?php echo page($search['size'] ?? 20, $total);?>
	</div>
</div>
