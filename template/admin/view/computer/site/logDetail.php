<div class="container-fluid">
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td>
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $value) {
				$value = trim($value);
				if (empty($value)) continue;
			?>
			<tr>
				<td><?php echo $value;?></td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
</div>