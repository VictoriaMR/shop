<div class="member-container" id="member-page">
	<!-- 筛选与操作栏 -->
	<form action="<?php echo adminUrl('member/index');?>" method="get" class="search-card">
		<input type="hidden" name="status" value="<?php echo (int)$search['status'];?>">

		<div class="toolbar-top">
			<div class="status-tabs btn-group" role="group">
				<button type="button" data-id="-1" class="status-tab <?php echo ($search['status'] !== 0 && $search['status'] !== 1) ? 'active' : '';?>">全部</button>
				<button type="button" data-id="0" class="status-tab <?php echo $search['status'] === 0 ? 'active' : '';?>">未启用</button>
				<button type="button" data-id="1" class="status-tab <?php echo $search['status'] === 1 ? 'active' : '';?>">已启用</button>
			</div>
			<button class="btn btn-success btn-add" id="add-data-btn" type="button">
				<i class="fa fa-plus"></i> 新增人员
			</button>
		</div>

		<div class="filter-grid">
			<div class="filter-item">
				<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($search['phone']);?>" placeholder="手机号码" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($search['name']);?>" placeholder="姓名 / 昵称" autocomplete="off">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr($search['stime'], 0, 10));?>" placeholder="开始日期">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr($search['etime'], 0, 10));?>" placeholder="结束日期">
			</div>
		</div>

		<div class="filter-actions">
			<button class="btn btn-info btn-block" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 人员卡片列表 -->
	<div class="member-list" id="data-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-users-slash"></i>
			<p>暂无人员数据</p>
		</div>
		<?php } else { ?>
		<?php foreach ($list as $value) {
			$rawAvatar = $value['avatar'] ?? '';
			$hasCustomAvatar = $rawAvatar !== '' && strpos($rawAvatar, 'image/common/') === false;
			$firstName = $value['first_name'] ?? '';
			$lastName = $value['last_name'] ?? '';
			$fullName = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
			$displayName = $fullName !== '' ? $fullName : ($value['nick_name'] ?? '-');
			$subNick = ($fullName !== '' && !empty($value['nick_name']) && $value['nick_name'] !== $fullName) ? $value['nick_name'] : '';
			$rowInfo = [
				'mem_id'     => $value['mem_id'],
				'name'       => $fullName,
				'first_name' => $firstName,
				'last_name'  => $lastName,
				'nick_name'  => $value['nick_name'] ?? '',
				'nickname'   => $value['nick_name'] ?? '',
				'mobile'     => $value['mobile'] ?? '',
				'email'      => $value['email'] ?? '',
				'status'     => (int)($value['status'] ?? 0),
			];
		?>
		<div class="member-card" data-id="<?php echo (int)$value['mem_id'];?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
			<div class="card-header">
				<div class="user-main">
					<div class="user-avatar">
						<?php if ($hasCustomAvatar) { ?>
						<img src="<?php echo htmlspecialchars($rawAvatar, ENT_QUOTES, 'UTF-8');?>" alt="avatar">
						<?php } else { ?>
						<i class="fa fa-user"></i>
						<?php } ?>
					</div>
					<div class="user-titles">
						<div class="user-name-row">
							<span class="user-name e1"><?php echo htmlspecialchars($displayName);?></span>
							<span class="user-id-badge">#<?php echo (int)$value['mem_id'];?></span>
						</div>
						<?php if ($subNick !== '') { ?>
						<div class="user-nick e1"><?php echo htmlspecialchars($subNick);?></div>
						<?php } ?>
					</div>
				</div>
				<div class="status-wrap">
					<div class="switch_botton" data-status="<?php echo (int)$value['status'];?>" title="点击切换状态">
						<div class="switch_status <?php echo (int)$value['status'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="info-row">
					<span class="info-label"><i class="fa fa-phone"></i> 手机号码</span>
					<span class="info-val"><?php echo !empty($value['mobile']) ? htmlspecialchars($value['mobile']) : '-';?></span>
				</div>
				<div class="info-row">
					<span class="info-label"><i class="fa fa-envelope"></i> 电子邮箱</span>
					<span class="info-val e1"><?php echo !empty($value['email']) ? htmlspecialchars($value['email']) : '-';?></span>
				</div>
				<div class="info-row">
					<span class="info-label"><i class="fa fa-calendar-plus"></i> 注册时间</span>
					<span class="info-val"><?php echo !empty($value['add_time']) ? htmlspecialchars($value['add_time']) : '-';?></span>
				</div>
				<div class="info-row">
					<span class="info-label"><i class="fa fa-clock"></i> 最后登录</span>
					<span class="info-val"><?php echo !empty($value['login_time']) ? htmlspecialchars($value['login_time']) : '-';?></span>
				</div>
			</div>

			<div class="card-footer">
				<button class="btn btn-xs btn-primary modify" type="button">
					<i class="fa fa-pen-to-square"></i> 编辑
				</button>
				<button class="btn btn-xs btn-danger delete" type="button">
					<i class="fa fa-trash-can"></i> 删除
				</button>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页 -->
	<div class="mt12">
		<?php echo page($search['size'], $total);?>
	</div>
</div>

<!-- 新增/编辑弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="mem_id" class="no_show" value="0">
			<input type="hidden" class="no_replace" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增管理员</div>
			<div class="input-group">
				<span class="input-group-addon"><span>名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="30" placeholder="真实姓名" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>昵称</span></span>
				<input type="text" class="form-control" name="nickname" required="required" maxlength="30" placeholder="管理昵称" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>手机号码</span></span>
				<input type="tel" class="form-control" name="mobile" required="required" maxlength="11" placeholder="11位手机号" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>邮箱</span></span>
				<input type="email" class="form-control" name="email" required="required" maxlength="50" placeholder="电子邮箱" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>状态</span></span>
				<div class="form-control switch-control">
					<div class="switch_botton" data-status="0">
						<div class="switch_status off"></div>
					</div>
					<input type="hidden" name="status" value="0">
				</div>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>密码</span></span>
				<input type="password" class="form-control" name="password" maxlength="30" placeholder="编辑时不填则不修改" autocomplete="new-password">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>确认密码</span></span>
				<input type="password" class="form-control" name="repassword" maxlength="30" placeholder="再次输入密码" autocomplete="new-password">
			</div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-lg btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>
