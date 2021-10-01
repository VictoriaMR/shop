<div class="container-fluid" id="icon-list">
	<div class="row-item">
		<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus"></i>添加新图标</button>
	</div>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">名称</th>
				<th class="col-md-1">排序</th>
				<th class="col-md-1">备注</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $value) {?>
			<tr class="item" data-id="<?php echo $value['icon_id'];?>">
				<td class="col-md-1"><?php echo $value['icon_id'];?></td>
				<td class="col-md-1">
					<div class="left text-content">
						<span class="glyphicon glyphicon-<?php echo $value['name'];?>"></span>
						<span class="cate_name"><?php echo $value['name'];?></span>
					</div>
				</td>
				<td class="col-md-1">
					<input type="text" name="sort" value="<?php echo $value['sort'];?>" class="form-control">
				</td>
				<td class="col-md-1">
					<?php echo $value['remark'];?>
				</td>
				<td class="col-md-2">
					<button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
					<button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<p>合计 <?php echo count($list);?> 个图标；</p>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">图标管理</div>
			<input type="hidden" name="icon_id" value="0">
			<input type="hidden" name="opn" value="editIconInfo">
			<div class="input-group">
				<div class="input-group-addon"><span>名称：</span></div>
				<input type="text" class="form-control" name="name" autocomplete="off" placeholder="图标的值" maxlength="32">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>排序：</span></div>
				<input type="text" class="form-control" name="sort" autocomplete="off" placeholder="排序" maxlength="5">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>备注：</span></div>
				<textarea class="form-control" name="remark" placeholder="内部使用备注" maxlength="64"></textarea>
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
		</form>
	</div>
</div>