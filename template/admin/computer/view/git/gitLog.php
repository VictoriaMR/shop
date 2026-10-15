<?php
$curId = $id ?? '';
$curStatus = isset($status) ? (int)$status : -1;
$curKeyword = $keyword ?? '';
$defaultLib = $curId !== '' ? $curId : ($git_library[0] ?? 'shop');
?>
<div class="container-fluid" id="git-log-page">
	<form action="<?php echo adminUrl('git/gitLog');?>" method="get" class="form-inline">
		<input type="hidden" name="id" value="<?php echo htmlspecialchars($curId);?>">
		<input type="hidden" name="status" value="<?php echo $curStatus;?>">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left lib-filter" role="group">
					<button type="button" data-id="" class="btn <?php echo $curId === '' ? 'btn-primary' : 'btn-default';?>">全部库</button>
					<?php foreach ($git_library as $value) { ?>
					<button type="button" data-id="<?php echo htmlspecialchars($value);?>" class="btn <?php echo $curId === $value ? 'btn-primary' : 'btn-default';?>"><?php echo htmlspecialchars($value);?></button>
					<?php } ?>
				</div>

				<div class="btn-group left status-filter ml12" role="group">
					<button type="button" data-status="-1" class="btn <?php echo ($curStatus === 1 || $curStatus === 0) ? 'btn-default' : 'btn-primary';?>">全部状态</button>
					<button type="button" data-status="1" class="btn <?php echo $curStatus === 1 ? 'btn-primary' : 'btn-default';?>">已发布</button>
					<button type="button" data-status="0" class="btn <?php echo $curStatus === 0 ? 'btn-primary' : 'btn-default';?>">待发布</button>
				</div>

				<div class="form-group ml12">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($curKeyword, ENT_QUOTES, 'UTF-8');?>" placeholder="版本号 / 作者 / 提交信息" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>

				<div class="form-group right">
					<button id="update-git" type="button" class="btn btn-success" data-id="<?php echo htmlspecialchars($defaultLib);?>">
						<i class="glyphicon glyphicon-refresh"></i> 同步版本库 (<?php echo htmlspecialchars($defaultLib);?>)
					</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="75">ID</th>
				<th width="110">版本库</th>
				<th width="95" class="tc">状态</th>
				<th width="140">作者</th>
				<th width="160">版本号 (Commit)</th>
				<th>提交信息</th>
				<th width="180">时间记录</th>
				<th width="95">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="8">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无版本日志记录</div>
				</td>
			</tr>
			<?php } else { foreach ($list as $value) {
				$commitFull = $value['commit'] ?? '';
				$commitShort = $commitFull !== '' ? substr($commitFull, 0, 10) : '--';
				$isReleased = !empty($value['status']);
			?>
			<tr data-id="<?php echo (int)$value['git_id'];?>">
				<td><strong><?php echo (int)$value['git_id'];?></strong></td>
				<td><span class="label label-info"><?php echo htmlspecialchars($value['library']);?></span></td>
				<td class="tc">
					<?php if ($isReleased) { ?>
					<span class="label label-success">已发布</span>
					<?php } else { ?>
					<span class="label label-warning">待发布</span>
					<?php } ?>
				</td>
				<td><?php echo htmlspecialchars($value['author'] ?: '--');?></td>
				<td>
					<code title="完整版本号: <?php echo htmlspecialchars($commitFull);?>"><?php echo htmlspecialchars($commitShort);?></code>
				</td>
				<td><?php echo htmlspecialchars($value['info'] ?: '--');?></td>
				<td class="f12 text-muted">
					<div>提交: <?php echo htmlspecialchars($value['commit_time'] ?: '--');?></div>
					<div>发布: <?php echo htmlspecialchars($value['release_time'] ?: '--');?></div>
				</td>
				<td>
					<?php if (!$isReleased) { ?>
					<button type="button" class="btn btn-primary btn-xs release-btn"><i class="glyphicon glyphicon-send"></i> 发布</button>
					<?php } else { ?>
					<span class="text-muted f12">--</span>
					<?php } ?>
				</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>

	<?php echo page($size, $total);?>
</div>