<?php
$listCount = count($list ?? []);
?>
<div class="container-fluid" id="position-page">
	<form action="<?php echo adminUrl('department/position');?>" method="get" class="form-inline">
		<input type="hidden" name="status" value="<?php echo (int)($search['status'] ?? -1);?>">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left" role="group">
					<button type="button" data-id="-1" class="btn <?php echo ($search['status'] === 1 || $search['status'] === 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
					<button type="button" data-id="1" class="btn <?php echo $search['status'] === 1 ? 'btn-primary' : 'btn-default';?>">已启用</button>
					<button type="button" data-id="0" class="btn <?php echo $search['status'] === 0 ? 'btn-primary' : 'btn-default';?>">未启用</button>
				</div>
				<div class="form-group ml12">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($search['keyword'] ?? '');?>" placeholder="职位名称 / 备注关键字" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
				<div class="form-group right">
					<button class="btn btn-success" id="add-pos-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增职位</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="220">职位名称</th>
				<th width="120" class="tc">关联人数</th>
				<th>职位描述 / 备注</th>
				<th width="80" class="tc">状态</th>
				<th width="130" class="tc">排序</th>
				<th width="170">更新时间</th>
				<th width="150">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无职位数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach (array_values($list) as $idx => $value) {
				$posId = (int)$value['pos_id'];
				$memCount = (int)($memberCountMap[$posId] ?? 0);
				$isFirst = ($idx === 0 && (int)($search['page'] ?? 1) === 1);
				$isLast = ($idx === $listCount - 1);
				$rowData = [
					'pos_id' => $posId,
					'name'   => $value['name'],
					'remark' => $value['remark'] ?? '',
					'status' => (int)$value['status'],
					'sort'   => (int)$value['sort'],
				];
			?>
			<tr class="item" data-id="<?php echo $posId;?>" data-info="<?php echo htmlspecialchars(json_encode($rowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<td><strong><?php echo $posId;?></strong></td>
				<td>
					<i class="fa fa-id-badge text-muted"></i>
					<strong class="ml6"><?php echo htmlspecialchars($value['name']);?></strong>
				</td>
				<td class="tc">
					<?php if ($memCount > 0){ ?>
					<a href="<?php echo adminUrl('member/index', ['pos_id' => $posId]);?>" class="label label-info" title="点击查看该职位人员">
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
				<td class="f12 text-muted">
					<?php echo !empty($value['update_time']) ? $value['update_time'] : ($value['add_time'] ?? '--');?>
				</td>
				<td>
					<button type="button" class="btn btn-primary btn-xs modify"><i class="glyphicon glyphicon-edit"></i> 编辑</button>
					<button type="button" class="btn btn-danger btn-xs delete ml6"><i class="glyphicon glyphicon-trash"></i> 删除</button>
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

<!-- 新增/编辑职位弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="pos_id" value="0">
			<input type="hidden" name="sort" value="0">
			<input type="hidden" name="opn" value="editPosInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增职位</div>

			<div class="input-group">
				<span class="input-group-addon"><span>职位名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="请输入职位名称" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>职位备注</span></span>
				<input type="text" class="form-control" name="remark" maxlength="255" placeholder="职位职责或说明（选填）" autocomplete="off">
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
