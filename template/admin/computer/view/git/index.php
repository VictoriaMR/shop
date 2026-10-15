<div class="container-fluid" id="git-list">
	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="160">版本库 / 分支</th>
				<th width="140" class="tc">发布状态</th>
				<th width="150" class="tc">提交统计</th>
				<th width="190">最新版本号 / 作者</th>
				<th>最新提交说明</th>
				<th width="180">时间记录</th>
				<th width="230">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="7">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无版本库数据</div>
				</td>
			</tr>
			<?php } else { foreach ($list as $value) {
				$commitFull = $value['commit'] ?? '';
				$commitShort = $commitFull !== '' ? substr($commitFull, 0, 8) : '--';
				$pending = (int)($value['pending_count'] ?? 0);
				$totalCount = (int)($value['total_count'] ?? 0);
			?>
			<tr data-name="<?php echo htmlspecialchars($value['name']);?>" data-git-id="<?php echo (int)($value['git_id'] ?? 0);?>">
				<td>
					<div><strong><?php echo htmlspecialchars($value['name']);?></strong></div>
					<div class="text-muted f12 mt2"><i class="fa fa-code-fork"></i> <?php echo htmlspecialchars($value['branch'] ?? 'master');?></div>
				</td>
				<td class="tc">
					<?php if ($pending > 0) { ?>
					<span class="label label-warning"><?php echo $pending;?> 条待发布</span>
					<?php } elseif ((int)($value['status'] ?? -1) === 1) { ?>
					<span class="label label-success">已同步发布</span>
					<?php } else { ?>
					<span class="label label-default"><?php echo htmlspecialchars($value['status_text'] ?? '--');?></span>
					<?php } ?>
				</td>
				<td class="tc">
					<span>共 <strong><?php echo $totalCount;?></strong> 条</span>
					<?php if ($pending > 0) { ?>
					<div class="f12 orange mt2">待发布: <?php echo $pending;?></div>
					<?php } ?>
				</td>
				<td>
					<div><code title="<?php echo htmlspecialchars($commitFull);?>"><?php echo htmlspecialchars($commitShort);?></code></div>
					<?php if (!empty($value['author'])) { ?>
					<div class="text-muted f12 mt2"><i class="glyphicon glyphicon-user"></i> <?php echo htmlspecialchars($value['author']);?></div>
					<?php } ?>
				</td>
				<td><?php echo htmlspecialchars($value['info'] ?: '--');?></td>
				<td class="f12 text-muted">
					<div>提交: <?php echo htmlspecialchars($value['commit_time'] ?: '--');?></div>
					<div>发布: <?php echo htmlspecialchars($value['release_time'] ?: '--');?></div>
				</td>
				<td>
					<button type="button" class="btn btn-success btn-xs update-git-btn" data-id="<?php echo htmlspecialchars($value['name']);?>"><i class="glyphicon glyphicon-refresh"></i> 同步</button>
					<?php if ($pending > 0 && !empty($value['git_id'])) { ?>
					<button type="button" class="btn btn-warning btn-xs release-latest-btn ml6" data-id="<?php echo (int)$value['git_id'];?>"><i class="glyphicon glyphicon-send"></i> 发布最新</button>
					<?php } ?>
					<a href="<?php echo adminUrl('git/gitLog', ['id' => $value['name']]);?>" class="btn btn-primary btn-xs ml6"><i class="glyphicon glyphicon-list-alt"></i> 版本日志</a>
				</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>