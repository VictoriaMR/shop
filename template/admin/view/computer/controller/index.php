<div class="container-fluid" id="function-list">
	<div class="row-item">
		<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus"></i>添加新功能</button>
	</div>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">名称</th>
				<th class="col-md-1">控制器</th>
				<th class="col-md-1">排序</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php $sonControllerCount = 0; if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $value) {
				$sonControllerCount += count($value['son'] ?? []);
			?>
			<tr class="item" data-id="<?php echo $value['con_id'];?>" data-pid="<?php echo $value['parent_id'];?>">
				<td class="col-md-1"><?php echo $value['con_id'];?></td>
				<td class="col-md-1">
					<div class="left text-content">
						<span class="glyphicon glyphicon-<?php echo $value['icon'];?>"></span>
						<span class="cate_name"><?php echo $value['name'];?></span>
					</div>
				</td>
				<td class="col-md-1">
					<?php echo $value['value'];?>
				</td>
				<td class="col-md-1">
					<input type="text" name="sort" value="<?php echo $value['sort'];?>" class="form-control">
				</td>
				<td class="col-md-2">
					<button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
					<button class="btn btn-success btn-xs ml4 add"><span class="glyphicon glyphicon-plus"></span>&nbsp;增加</button>
					<?php if (empty($value['son'])){?>
					<button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
					<?php } ?>
				</td>
			</tr>
			<?php if (!empty($value['son'])){?>
			<?php foreach ($value['son'] as $sv) { ?>
			<tr class="item" data-id="<?php echo $sv['con_id'];?>" data-pid="<?php echo $sv['parent_id'];?>">
				<td class="col-md-1"><?php echo $sv['con_id'];?></td>
				<td class="col-md-1">
					<div class="left text-content" style="padding-left:30px;">
						<span class="glyphicon glyphicon-<?php echo $sv['icon'];?>"></span>
						<span class="cate_name"><?php echo $sv['name'];?></span>
					</div>
				</td>
				<td class="col-md-1">
					<?php echo $sv['value'];?>
				</td>
				<td class="col-md-1">
					<input type="text" name="sort" value="<?php echo $sv['sort'];?>" class="form-control">
				</td>
				<td class="col-md-2">
					<button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
					<button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<p>合计 <?php echo count($list);?> 个功能分组，<?php echo $sonControllerCount;?>个子功能；</p>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">功能管理</div>
			<input type="hidden" name="con_id" value="0">
			<input type="hidden" name="parent_id" value="0">
			<input type="hidden" name="opn" value="editInfo">
			<div class="input-group">
				<div class="input-group-addon"><span>名称：</span></div>
				<input type="text" class="form-control" name="name" autocomplete="off" placeholder="显示的名称" maxlength="32">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>控制器：</span></div>
				<input type="text" class="form-control" name="value" autocomplete="off" placeholder="控制器名称" maxlength="32">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>图标：</span></div>
				<select class="form-control" data-live-search="true" name="icon">
					<option value="">请选择</option>
					<?php foreach ($iconList as $value){?>
					<option value="<?php echo $value['name'];?>"><?php echo empty($value['remark']) ? $value['name'] : $value['name'].' - '.$value['remark'];?></option>
					<?php }?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>排序：</span></div>
				<input type="text" class="form-control" name="sort" autocomplete="off" placeholder="排序" maxlength="5">
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
		</form>
	</div>
</div>