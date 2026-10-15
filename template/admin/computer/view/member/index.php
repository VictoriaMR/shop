<div class="container-fluid" id="member-page">
	<form action="<?php echo adminUrl('member/index');?>" method="get" class="form-inline">
		<input type="hidden" name="status" value="<?php echo $search['status'];?>">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left" role="group">
					<button type="button" data-id="-1" class="btn <?php echo ($search['status'] == 1 || $search['status'] === 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
					<button type="button" data-id="0" class="btn <?php echo $search['status'] === 0 ? 'btn-primary' : 'btn-default';?>">未启用</button>
					<button type="button" data-id="1" class="btn <?php echo $search['status'] === 1 ? 'btn-primary' : 'btn-default';?>">已启用</button>
				</div>
				<div class="form-group ml12">
					<select name="dept_id" class="form-control">
						<option value="0">全部部门</option>
						<?php foreach ($deptList ?? [] as $dOpt) {
							$dLev = (int)($dOpt['level'] ?? 1);
							$dPrefix = $dLev > 1 ? str_repeat('　', $dLev - 1) . '└─ ' : '';
						?>
						<option value="<?php echo (int)$dOpt['dept_id'];?>" <?php echo (int)($search['dept_id'] ?? 0) === (int)$dOpt['dept_id'] ? 'selected' : '';?>><?php echo htmlspecialchars($dPrefix . $dOpt['name']);?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group ml6">
					<select name="pos_id" class="form-control">
						<option value="0">全部职位</option>
						<?php foreach ($posList ?? [] as $pOpt) { ?>
						<option value="<?php echo (int)$pOpt['pos_id'];?>" <?php echo (int)($search['pos_id'] ?? 0) === (int)$pOpt['pos_id'] ? 'selected' : '';?>><?php echo htmlspecialchars($pOpt['name']);?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group ml6">
					<input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($search['phone']);?>" placeholder="手机号码" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($search['name']);?>" placeholder="姓名 / 昵称关键字" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime']);?>" name="stime" placeholder="开始时间" autocomplete="off">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime']);?>" name="etime" placeholder="结束时间" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
				<div class="form-group right">
					<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增管理员</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="60">ID</th>
				<th width="60">头像</th>
				<th width="140">姓名 / 昵称</th>
				<th width="180">部门 / 职位</th>
				<th width="120">手机号码</th>
				<th width="160">邮箱</th>
				<th width="70">状态</th>
				<th width="170">时间记录</th>
				<th width="140">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="9">
					<div class="tc orange">
						<i class="glyphicon glyphicon-info-sign"></i> 暂无人员数据
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) { 
				$avatar = !empty($value['avatar']) ? $value['avatar'] : siteUrl('/image/common/male.jpg');
				$firstName = $value['first_name'] ?? '';
				$lastName = $value['last_name'] ?? '';
				$fullName = ($firstName === $lastName) ? $firstName : trim($firstName . ' ' . $lastName);
				$deptName = $value['dept_name'] ?? '';
				$deptPath = $value['dept_path'] ?? '';
				$posName = $value['pos_name'] ?? '';
				$rowInfo = [
					'mem_id'     => (int)$value['mem_id'],
					'name'       => $fullName,
					'first_name' => $firstName,
					'last_name'  => $lastName,
					'nick_name'  => $value['nick_name'] ?? '',
					'nickname'   => $value['nick_name'] ?? '',
					'mobile'     => $value['mobile'] ?? '',
					'email'      => $value['email'] ?? '',
					'dept_id'    => (int)($value['dept_id'] ?? 0),
					'pos_id'     => (int)($value['pos_id'] ?? 0),
					'status'     => (int)($value['status'] ?? 0),
				];
			?>
			<tr data-id="<?php echo $value['mem_id'];?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<td><strong><?php echo $value['mem_id'];?></strong></td>
				<td>
					<div class="avatar-hover">
						<img src="<?php echo $avatar;?>" alt="avatar">
					</div>
				</td>
				<td>
					<div><strong><?php echo htmlspecialchars($fullName !== '' ? $fullName : ($value['nick_name'] ?? '-'));?></strong></div>
					<?php if ($fullName !== '' && !empty($value['nick_name']) && $value['nick_name'] !== $fullName){ ?>
					<div class="text-muted f12"><?php echo htmlspecialchars($value['nick_name']);?></div>
					<?php } ?>
				</td>
				<td>
					<?php if ($deptName !== '' || $posName !== ''){ ?>
						<?php if ($deptName !== ''){ ?>
						<div title="<?php echo htmlspecialchars($deptPath);?>">
							<span class="label label-info"><i class="fa fa-sitemap"></i> <?php echo htmlspecialchars($deptName);?></span>
						</div>
						<?php } ?>
						<?php if ($posName !== ''){ ?>
						<div class="mt2">
							<span class="label label-default"><i class="fa fa-id-badge"></i> <?php echo htmlspecialchars($posName);?></span>
						</div>
						<?php } ?>
					<?php } else { ?>
						<span class="text-muted f12">--</span>
					<?php } ?>
				</td>
				<td><?php echo !empty($value['mobile']) ? htmlspecialchars($value['mobile']) : '-';?></td>
				<td><?php echo !empty($value['email']) ? htmlspecialchars($value['email']) : '-';?></td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['status'];?>" title="点击切换状态">
						<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td>
					<div title="注册时间"><span class="text-muted">增:</span> <?php echo $value['add_time'];?></div>
					<div title="最后登录时间"><span class="text-muted">登:</span> <?php echo !empty($value['login_time']) ? $value['login_time'] : '-';?></div>
				</td>
				<td>
					<button class="btn btn-primary btn-xs modify" type="button"><i class="glyphicon glyphicon-edit"></i> 编辑</button>
					<button class="btn btn-danger btn-xs delete ml6" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12">
			<?php echo page($search['size'], $total);?>
		</div>
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
				<span class="input-group-addon"><span>所属部门</span></span>
				<select class="form-control" name="dept_id">
					<option value="0">-- 未分配部门 --</option>
					<?php foreach ($deptList ?? [] as $dOpt) {
						$dLev = (int)($dOpt['level'] ?? 1);
						$dPrefix = $dLev > 1 ? str_repeat('　', $dLev - 1) . '└─ ' : '';
					?>
					<option value="<?php echo (int)$dOpt['dept_id'];?>"><?php echo htmlspecialchars($dPrefix . $dOpt['name']);?></option>
					<?php } ?>
				</select>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>所属职位</span></span>
				<select class="form-control" name="pos_id">
					<option value="0">-- 未分配职位 --</option>
					<?php foreach ($posList ?? [] as $pOpt) { ?>
					<option value="<?php echo (int)$pOpt['pos_id'];?>"><?php echo htmlspecialchars($pOpt['name']);?></option>
					<?php } ?>
				</select>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>手机号码</span></span>
				<input type="text" class="form-control" name="mobile" required="required" maxlength="11" placeholder="11位手机号" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>邮箱</span></span>
				<input type="text" class="form-control" name="email" required="required" maxlength="30" placeholder="电子邮箱" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>状态</span></span>
				<div class="form-control pt6">
					<div class="switch_botton" data-status="0">
						<div class="switch_status off"></div>
					</div>
					<input type="hidden" name="status" value="0">
				</div>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>密码</span></span>
				<input type="password" class="form-control" name="password" maxlength="30" placeholder="密码（编辑时不填表示不修改）" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>确认密码</span></span>
				<input type="password" class="form-control" name="repassword" maxlength="30" placeholder="再次输入密码" autocomplete="off">
			</div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>