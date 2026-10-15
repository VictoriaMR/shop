<?php
$deptTotal = count($deptList ?? []);
$memTotal = count($memberList ?? []);
?>
<div class="container-fluid" id="perm-page">
	<div class="row">
		<!-- 左侧：授权对象选择（部门 / 个人） -->
		<div class="col-sm-4 col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="btn-group btn-group-justified" role="group" id="target-type-tabs">
						<div class="btn-group" role="group">
							<button type="button" class="btn btn-primary btn-sm target-type-btn" data-type="1">
								<i class="fa fa-sitemap"></i> 按部门 (<?php echo $deptTotal;?>)
							</button>
						</div>
						<div class="btn-group" role="group">
							<button type="button" class="btn btn-default btn-sm target-type-btn" data-type="2">
								<i class="fa fa-user"></i> 按个人 (<?php echo $memTotal;?>)
							</button>
						</div>
					</div>
					<div class="mt6">
						<input type="text" id="target-search" class="form-control input-sm" placeholder="筛选部门或人员名称 / ID..." autocomplete="off">
					</div>
				</div>

				<!-- 部门列表 -->
				<div id="target-list-1" class="list-group target-list-wrap">
					<?php if (empty($deptList)){ ?>
					<div class="list-group-item tc text-muted">
						暂无部门，请先前往<a href="<?php echo adminUrl('department/index');?>">部门管理</a>创建
					</div>
					<?php } else { ?>
					<?php foreach ($deptList as $dept) {
						$dId = (int)$dept['dept_id'];
						$lev = (int)($dept['level'] ?? 1);
						$prefix = $lev > 1 ? str_repeat('　', $lev - 1) . '└─ ' : '';
						$cnt = (int)($deptPermCounts[$dId] ?? 0);
					?>
					<a href="javascript:;" class="list-group-item perm-target-item" data-type="1" data-id="<?php echo $dId;?>" data-name="<?php echo htmlspecialchars($dept['name'], ENT_QUOTES, 'UTF-8');?>" data-search="<?php echo htmlspecialchars(strtolower($dept['name'] . ' ' . $dId), ENT_QUOTES, 'UTF-8');?>">
						<span class="badge perm-cnt-badge"><?php echo $cnt;?></span>
						<span><?php echo htmlspecialchars($prefix);?></span>
						<strong><?php echo htmlspecialchars($dept['name']);?></strong>
						<span class="text-muted f12">#<?php echo $dId;?></span>
						<?php if ((int)$dept['status'] === 0){ ?>
						<span class="label label-default">停用</span>
						<?php } ?>
					</a>
					<?php } ?>
					<?php } ?>
				</div>

				<!-- 个人列表 -->
				<div id="target-list-2" class="list-group target-list-wrap hidden">
					<?php if (empty($memberList)){ ?>
					<div class="list-group-item tc text-muted">暂无人员数据</div>
					<?php } else { ?>
					<?php foreach ($memberList as $mem) {
						$mId = (int)$mem['mem_id'];
						$fullName = trim(($mem['first_name'] ?? '') . ' ' . ($mem['last_name'] ?? ''));
						$displayName = ($mem['nick_name'] ?? '') ?: ($fullName ?: ('用户#' . $mId));
						$deptName = $mem['dept_name'] ?? '';
						$posName = $mem['pos_name'] ?? '';
						$isSuper = !empty($mem['is_super']);
						$cnt = (int)($memPermCounts[$mId] ?? 0);
					?>
					<a href="javascript:;" class="list-group-item perm-target-item" data-type="2" data-id="<?php echo $mId;?>" data-name="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');?>" data-dept-name="<?php echo htmlspecialchars($deptName, ENT_QUOTES, 'UTF-8');?>" data-super="<?php echo $isSuper ? 1 : 0;?>" data-search="<?php echo htmlspecialchars(strtolower($displayName . ' ' . $fullName . ' ' . $mId . ' ' . ($mem['mobile'] ?? '') . ' ' . $deptName), ENT_QUOTES, 'UTF-8');?>">
						<?php if ($isSuper){ ?>
						<span class="badge">超管</span>
						<?php } else { ?>
						<span class="badge perm-cnt-badge"><?php echo $cnt;?></span>
						<?php } ?>
						<div>
							<strong><?php echo htmlspecialchars($displayName);?></strong>
							<span class="text-muted f12">(#<?php echo $mId;?>)</span>
						</div>
						<div class="mt2">
							<?php if ($deptName !== ''){ ?>
							<span class="label label-info"><?php echo htmlspecialchars($deptName);?></span>
							<?php } else { ?>
							<span class="text-muted f12">未分部门</span>
							<?php } ?>
							<?php if ($posName !== ''){ ?>
							<span class="label label-default ml2"><?php echo htmlspecialchars($posName);?></span>
							<?php } ?>
						</div>
					</a>
					<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>

		<!-- 右侧：控制器 & Public 方法权限矩阵 -->
		<div class="col-sm-8 col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="left">
						<span class="text-muted">当前授权对象：</span>
						<span id="current-target-badge" class="label label-primary">未选择</span>
						<strong id="current-target-name" class="ml6">请在左侧点击选择部门或人员</strong>
						<span id="current-target-dept-hint" class="text-muted f12 ml6"></span>
					</div>
					<div id="perm-toolbar-btns" class="right">
						<button type="button" class="btn btn-default btn-xs" id="btn-check-all">
							<i class="fa fa-check-square-o"></i> 全选
						</button>
						<button type="button" class="btn btn-default btn-xs ml6" id="btn-uncheck-all">
							<i class="fa fa-square-o"></i> 清空
						</button>
						<button type="button" class="btn btn-primary btn-xs ml6" id="btn-save-perm">
							<i class="fa fa-save"></i> 保存权限配置
						</button>
					</div>
					<div class="clear"></div>
				</div>

				<div class="panel-body">
					<div id="super-admin-notice" class="alert alert-warning hidden">
						<i class="fa fa-shield"></i> <strong>系统超级管理员（ID: 10000）</strong> 默认拥有后台所有控制器与全部 <code>public</code> 方法的访问权限，无需单独配置或修改。
					</div>

					<div id="member-union-notice" class="alert alert-info hidden">
						<i class="fa fa-info-circle"></i> <strong>权限合并说明：</strong>个人最终生效权限 = <strong>所属部门权限 ∪ 个人独立授权</strong>。带有 <span class="label label-info">部门已授</span> 标记的项表示已由其所属部门授权，可在此基础上额外勾选个人独立权限。
					</div>

					<table class="table table-hover table-middle" id="perm-matrix-table">
						<thead>
							<tr>
								<th width="200">控制器模块</th>
								<th width="180">整控制器授权</th>
								<th>Public 方法权限（可单独勾选具体方法）</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($catalog ?? [] as $group) { ?>
							<tr class="info group-header-row" data-group="<?php echo (int)$group['group_id'];?>">
								<td colspan="2">
									<i class="fa fa-folder-open"></i>
									<strong class="ml2"><?php echo htmlspecialchars($group['group_name']);?></strong>
									<span class="text-muted f12 ml6">(<?php echo count($group['items']);?> 个控制器)</span>
								</td>
								<td class="text-right">
									<label class="checkbox-inline">
										<input type="checkbox" class="chk-group-all"> 全选本组
									</label>
								</td>
							</tr>
							<?php foreach ($group['items'] as $ctrlItem) {
								$ctrlName = $ctrlItem['controller'];
								$menuName = $ctrlItem['menu_name'] ?: ($ctrlItem['default_title'] ?: $ctrlName);
								$methods = $ctrlItem['methods'] ?? [];
							?>
							<tr class="ctrl-row" data-group="<?php echo (int)$group['group_id'];?>" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>">
								<td>
									<strong><?php echo htmlspecialchars($menuName);?></strong>
									<code class="ml2"><?php echo htmlspecialchars($ctrlName);?></code>
								</td>
								<td>
									<label class="checkbox-inline">
										<input type="checkbox" class="chk-ctrl-all" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" value="*">
										<span>整个控制器</span> <code>*</code>
									</label>
									<span class="dept-inh-ctrl label label-info hidden ml2">部门已授</span>
								</td>
								<td>
									<?php foreach ($methods as $m) {
										$actName = $m['action'];
										$actTitle = $m['title'];
									?>
									<label class="checkbox-inline method-item" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" data-action="<?php echo htmlspecialchars($actName, ENT_QUOTES, 'UTF-8');?>">
										<input type="checkbox" class="chk-method" data-controller="<?php echo htmlspecialchars($ctrlName, ENT_QUOTES, 'UTF-8');?>" value="<?php echo htmlspecialchars($actName, ENT_QUOTES, 'UTF-8');?>">
										<span><?php echo htmlspecialchars($actTitle);?></span>
										<span class="text-muted f12">(<?php echo htmlspecialchars($actName);?>)</span>
										<span class="dept-inh-method label label-info hidden">部门已授</span>
									</label>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
