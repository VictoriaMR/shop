<div class="container-fluid" id="static-cache-page">
	<div class="row">
		<div class="col-xs-12">
			<span class="text-muted">当前共 <strong><?php echo count($list ?? []);?></strong> 个静态资源缓存文件</span>
			<?php if (!empty($list)) { ?>
			<div class="right">
				<button class="btn btn-danger btn-sm delete" data-id="all" type="button"><i class="glyphicon glyphicon-trash"></i> 清除全部缓存</button>
			</div>
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>

	<table class="table table-hover table-middle mt12" id="data-list">
		<thead>
			<tr>
				<th>缓存文件路径</th>
				<th width="100" class="tc">类型</th>
				<th width="130">文件大小</th>
				<th width="170">最后修改时间</th>
				<th width="150">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="5">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无静态缓存文件</div>
				</td>
			</tr>
			<?php } else { foreach ($list as $value) {
				$ext = strtolower($value['ext'] ?? pathinfo($value['name'], PATHINFO_EXTENSION));
			?>
			<tr>
				<td><code><?php echo htmlspecialchars($value['name']);?></code></td>
				<td class="tc">
					<span class="label <?php echo $ext === 'css' ? 'label-success' : ($ext === 'js' ? 'label-warning' : 'label-default');?>"><?php echo htmlspecialchars(strtoupper($ext ?: 'FILE'));?></span>
				</td>
				<td><?php echo sprintf('%.2f', ($value['size'] ?? 0) / 1024) . ' KB';?></td>
				<td class="text-muted f12"><?php echo htmlspecialchars($value['c_time'] ?? '--');?></td>
				<td>
					<a href="<?php echo adminUrl('site/staticDetail', ['name' => $value['name']]);?>" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-eye-open"></i> 查看</a>
					<button class="btn btn-danger btn-xs delete ml6" type="button" data-id="<?php echo htmlspecialchars($value['name']);?>"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>