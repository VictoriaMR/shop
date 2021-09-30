<div class="container-fluid" id="site-page">
	<div class="form-group right">
		<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增站点</button>
	</div>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">名称/模板</th>
				<th class="col-md-1">语言</th>
				<th class="col-md-1">货币</th>
				<th class="col-md-1">状态</th>
				<th class="col-md-1">备注/加入时间</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="10">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value) { ?>
			<tr data-id="<?php echo $value['site_id'];?>">
				<td class="col-md-1"><?php echo $value['site_id'];?></td>
				<td class="col-md-1"><?php echo $value['name'];?><br ><?php echo $value['path'];?></td>
				<td class="col-md-1">
					<?php echo empty($value['language']) ? '' : implode(' | ', array_column($value['language'], 'name2'));?>
				</td>
				<td class="col-md-1">
					<?php echo empty($value['currency']) ? '' : implode(' | ', array_column($value['currency'], 'name'));?>
				</td>
				<td class="col-md-1">
					<div class="switch_botton" data-status="<?php echo $value['status'];?>">
                        <div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
                    </div>
				</td>
				<td class="col-md-1">
					<?php echo empty($value['remark']) ? '--' : $value['remark'];?>
					<br />
					<?php echo $value['add_time'];?>
				</td>
				<td class="col-md-2">
					<a href="<?php echo url('site/siteInfo', ['id'=>$value['site_id']]);?>" class="btn btn-primary btn-xs mt2" type="button"><i class="glyphicon glyphicon-wrench"></i> 配置</a>
					<button class="btn btn-success btn-xs modify mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
				</td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<p>当前共 <?php echo count($list);?> 个站点</p>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-info" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	<form class="form-horizontal">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="f24 dealbox-title">编辑站点</div>
		<input type="hidden" name="site_id" value="0">
		<input type="hidden" name="opn" value="editSite">
		<div class="input-group">
			<div class="input-group-addon"><span>名称</span>：</div>
			<input class="form-control" name="name" required="required" autocomplete="off" />
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>模板</span>：</div>
			<input class="form-control" name="path" required="required" autocomplete="off" />
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>Keyword</span>：</div>
			<textarea class="form-control" name="keyword" required="required" autocomplete="off" rows="4"></textarea>
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>Desc</span>：</div>
			<textarea class="form-control" name="description" required="required" autocomplete="off" rows="4"></textarea>
		</div>
		<button type="botton" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
	</form>
	</div>
</div>