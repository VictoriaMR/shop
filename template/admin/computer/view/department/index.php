<?php
$totalCount = count($list ?? []);
$topCount = 0;
foreach ($list ?? [] as $d) {
	if ((int)($d['parent_id'] ?? 0) === 0) {
		$topCount++;
	}
}
?>
<div class="container-fluid" id="department-page">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-inline">
				<div class="form-group left">
					<span class="text-muted">
						共 <strong><?php echo $totalCount;?></strong> 个部门（顶级部门 <strong><?php echo $topCount;?></strong> 个）
					</span>
				</div>
				<div class="form-group right">
					<button class="btn btn-default btn-sm toggle-all-btn" type="button">
						<i class="fa fa-arrows-alt"></i> 全部折叠/展开
					</button>
					<button class="btn btn-success btn-sm add-top-btn ml6" type="button">
						<i class="fa fa-plus-circle"></i> 新增顶级部门
					</button>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="280">部门名称 / 层级</th>
				<th width="120" class="tc">层级 / 下级</th>
				<th width="110" class="tc">关联人数</th>
				<th>部门描述 / 备注</th>
				<th width="80" class="tc">状态</th>
				<th width="130" class="tc">同级排序</th>
				<th width="220">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange"><i class="fa fa-info-circle"></i> 暂无部门数据，请点击右上角“新增顶级部门”</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) {
				$deptId = (int)$value['dept_id'];
				$parentId = (int)$value['parent_id'];
				$lev = (int)($value['level'] ?? 1);
				$prefix = $lev > 1 ? str_repeat('　', $lev - 1) . '└─ ' : '';
				$childCount = (int)($value['child_count'] ?? 0);
				$hasChild = !empty($value['has_child']);
				$isFirst = !empty($value['is_first']);
				$isLast = !empty($value['is_last']);
				$memCount = (int)($memberCountMap[$deptId] ?? 0);
				$rowData = [
					'dept_id'   => $deptId,
					'parent_id' => $parentId,
					'name'      => $value['name'],
					'remark'    => $value['remark'] ?? '',
					'status'    => (int)$value['status'],
					'sort'      => (int)$value['sort'],
				];
			?>
			<tr class="item<?php echo $lev === 1 ? ' info' : '';?><?php echo $hasChild ? ' pointer' : '';?>" data-id="<?php echo $deptId;?>" data-pid="<?php echo $parentId;?>" data-lev="<?php echo $lev;?>" data-info="<?php echo htmlspecialchars(json_encode($rowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>" <?php echo $hasChild ? 'title="点击折叠/展开下级部门"' : '';?>>
				<td><strong><?php echo $deptId;?></strong></td>
				<td>
					<span class="text-muted"><?php echo htmlspecialchars($prefix);?></span>
					<?php if ($hasChild){ ?>
					<i class="fa fa-caret-down toggle-icon text-muted"></i>
					<?php } ?>
					<i class="fa <?php echo $lev === 1 ? 'fa-sitemap' : 'fa-folder-o';?> text-muted ml2"></i>
					<strong class="dept-name ml2"><?php echo htmlspecialchars($value['name']);?></strong>
				</td>
				<td class="tc">
					<span class="label <?php echo $lev === 1 ? 'label-primary' : 'label-default';?>">
						<?php echo $lev;?> 级<?php echo $childCount > 0 ? ' (' . $childCount . ')' : '';?>
					</span>
				</td>
				<td class="tc">
					<?php if ($memCount > 0){ ?>
					<a href="<?php echo adminUrl('member/index', ['dept_id' => $deptId]);?>" class="label label-info" title="点击查看该部门人员">
						<i class="fa fa-user"></i> <?php echo $memCount;?> 人
					</a>
					<?php } else { ?>
					<span class="text-muted f12">0 人</span>
					<?php } ?>
				</td>
				<td>
					<span class="text-muted f12"><?php echo $value['remark'] !== '' ? htmlspecialchars($value['remark']) : '--';?></span>
				</td>
				<td class="tc">
					<div class="switch_botton" data-status="<?php echo (int)$value['status'];?>" title="点击切换状态">
						<div class="switch_status <?php echo (int)$value['status'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td class="tc">
					<div class="btn-group btn-group-xs" role="group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" <?php echo $isFirst ? 'disabled' : '';?>><i class="fa fa-angle-double-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" <?php echo $isFirst ? 'disabled' : '';?>><i class="fa fa-arrow-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" <?php echo $isLast ? 'disabled' : '';?>><i class="fa fa-arrow-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" <?php echo $isLast ? 'disabled' : '';?>><i class="fa fa-angle-double-down"></i></button>
					</div>
				</td>
				<td>
					<button type="button" class="btn btn-primary btn-xs modify"><i class="fa fa-edit"></i> 编辑</button>
					<button type="button" class="btn btn-success btn-xs ml6 add-sub"><i class="fa fa-plus"></i> 子部门</button>
					<?php if ($childCount === 0){ ?>
					<button type="button" class="btn btn-danger btn-xs ml6 delete"><i class="fa fa-trash"></i> 删除</button>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>

<!-- 新增/编辑部门弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="dept_id" value="0">
			<input type="hidden" name="sort" value="0">
			<input type="hidden" name="opn" value="editDeptInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增部门</div>

			<div class="input-group">
				<span class="input-group-addon"><span>上级部门</span></span>
				<select class="form-control" name="parent_id">
					<option value="0">-- 顶级部门 --</option>
					<?php foreach ($list ?? [] as $opt){
						$optLev = (int)($opt['level'] ?? 1);
						$prefix = $optLev > 1 ? str_repeat('　', $optLev - 1) . '└─ ' : '';
					?>
					<option value="<?php echo (int)$opt['dept_id'];?>"><?php echo htmlspecialchars($prefix . $opt['name']);?></option>
					<?php } ?>
				</select>
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>部门名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="请输入部门名称" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>部门备注</span></span>
				<input type="text" class="form-control" name="remark" maxlength="255" placeholder="部门职责或备注说明（选填）" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>状态</span></span>
				<div class="form-control pt6">
					<div class="switch_botton" data-status="1">
						<div class="switch_status on"></div>
					</div>
					<input type="hidden" name="status" value="1">
				</div>
			</div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
			</div>
		</form>
	</div>
</div>
