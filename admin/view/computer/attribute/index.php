<div class="container-fluid" id="attribute-list">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="row-item">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<div class="left btn-group mr20" role="group">
				<button type="button" data-id="-1" class="btn <?php echo ($status == 1 || $status == 0 || $status == 2) ? 'btn-default' : 'btn-primary';?>">全部</button>
				<button type="button" data-id="0" class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default';?>">未翻译</button>
				<button type="button" data-id="1" class="btn <?php echo $status == 1 ? 'btn-primary' : 'btn-default';?>">部分翻译</button>
				<button type="button" data-id="2" class="btn <?php echo $status == 2 ? 'btn-primary' : 'btn-default';?>">已翻译</button>
			</div>
			<div class="left">
				<div class="mr20 form-group">
					<label for="contact">名称:</label>
					<input type="text" class="form-control" name="keyword" value="<?php echo $keyword;?>" placeholder="名称关键字" autocomplete="off">
				</div>
				<div class="mr20 form-group">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</form>
	<table class="table table-hover mt10">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-3">名称</th>
				<th class="col-md-1">语言配置</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value) { ?>
			<tr class="item" data-id="<?php echo $value['attr_id'];?>">
				<td class="col-md-1"><?php echo $value['attr_id'];?></td>
				<td class="col-md-3">
					<div class="left text-content">
						<span class="glyphicon glyphicon-globe"></span>
						&nbsp;
						<span class="cate_name"><?php echo $value['name'];?></span>
					</div>
				</td>
				<td class="col-md-1">
					<?php if ($value['is_translate'] == 2){?>
					<span class="green">已配置</span>
					<?php } elseif ($value['is_translate'] == 1){?>
					<span class="orange">部分配置</span>
					<?php } else {?>
					<span class="red">未配置</span>
					<?php } ?>
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
	<?php echo page($size, $total);?>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">属性管理</div>
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="opn" value="editAttrInfo">
			<div class="input-group">
				<div class="input-group-addon"><span>名称：</span></div>
				<input type="text" class="form-control" name="name" autocomplete="off">
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
		</form>
	</div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">多语言配置</div>
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="editAttrLanguage">
			<table class="table table-bordered table-hover">
				<tbody></tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>