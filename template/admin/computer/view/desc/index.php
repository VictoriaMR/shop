<?php
$statusMap = [
	-1 => '全部',
	0  => '未配置',
	1  => '部分配置',
	2  => '已配置',
];
$transLabel = [
	0 => ['label-danger', '未配置'],
	1 => ['label-warning', '部分配置'],
	2 => ['label-success', '已配置'],
];
?>
<div class="container-fluid" id="descname-list">
	<form action="<?php echo adminUrl();?>" method="get" class="form-inline search-form">
		<input type="hidden" name="status" value="<?php echo (int)$status;?>">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left status-filter" role="group" style="margin-right: 12px; margin-bottom: 10px;">
					<?php foreach ($statusMap as $key => $text){ ?>
					<button type="button" data-status="<?php echo $key;?>" class="btn <?php echo $status == $key ? 'btn-primary' : 'btn-default';?>"><?php echo $text;?></button>
					<?php } ?>
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');?>" placeholder="描述名称关键字" autocomplete="off">
				</div>
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
				<div class="form-group right" style="margin-bottom: 10px;">
					<button class="btn btn-success add" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增描述名称</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6">
		<thead>
			<tr>
				<th width="100">ID</th>
				<th>名称</th>
				<th width="140">语言配置</th>
				<th width="240">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="4">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) {
				$name  = htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');
				$trans = $transLabel[$value['is_translate']] ?? $transLabel[0];
			?>
			<tr class="item" data-id="<?php echo (int)$value['descn_id'];?>" data-name="<?php echo $name;?>">
				<td><?php echo (int)$value['descn_id'];?></td>
				<td><strong><?php echo $name;?></strong></td>
				<td><span class="label <?php echo $trans[0];?>"><?php echo $trans[1];?></span></td>
				<td>
					<button class="btn btn-primary btn-xs modify" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
					<button class="btn btn-info btn-xs ml4 language" type="button"><i class="glyphicon glyphicon-globe"></i> 多语言</button>
					<button class="btn btn-danger btn-xs ml4 delete" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-8 text-right"><?php echo page($size, $total);?></div>
	</div>
</div>

<!-- 描述名称编辑弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">描述名称管理</div>
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="opn" value="editDescInfo">
			<div class="input-group">
				<div class="input-group-addon"><span>名称：</span></div>
				<input type="text" class="form-control" name="name" maxlength="255" autocomplete="off">
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn mt20">确认</button>
		</form>
	</div>
</div>

<!-- 多语言弹窗 -->
<div id="dealbox-language" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">多语言配置&nbsp;<span class="glyphicon glyphicon-transfer f16" title="智能翻译空白项" style="cursor:pointer;"></span></div>
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="editDescLanguage">
			<div class="language-list"></div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn mt20">确认</button>
		</form>
	</div>
</div>