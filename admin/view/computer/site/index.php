<div class="container-fluid" id="site-page">
	<div class="form-group right">
		<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增站点</button>
	</div>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">名称/模板</th>
				<th class="col-md-1">域名</th>
				<th class="col-md-2">语言</th>
				<th class="col-md-2">货币</th>
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
				<td class="col-md-1"><?php echo $value['title'];?><br ><?php echo $value['name'];?></td>
				<td class="col-md-1"><?php echo $value['domain'];?></td>
				<td class="col-md-2">
					<?php echo implode(' | ', array_column($value['language'], 'name'));?>
				</td>
				<td class="col-md-2">
					<?php echo implode(' | ', array_column($value['currency'], 'name'));?>
				</td>
				<td class="col-md-2">
					<a href="<?php echo url('site/siteInfo', ['id'=>$value['site_id']]);?>" class="btn btn-primary btn-xs mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 配置</a>
					<button class="btn btn-danger btn-xs delete mt2" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<?php echo page($size, $total);?>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">多语言配置</div>
			<input type="hidden" name="name" value="">
			<input type="hidden" name="value" value="value">
			<input type="hidden" name="site_id" value="0">
			<input type="hidden" name="opn" value="editLanguage">
			<table class="table table-bordered table-hover">
				<tbody>
					<tr>
						<th style="width:88px">语言名称</th>
						<th>文本 <span class="glyphicon glyphicon-transfer right f16" title="自动翻译"></span></th>
					</tr>
					<?php if (empty($language)){?>
					<tr><td colspan="2"><div class="tc co">没有获取到语言配置</div></td></tr>
					<?php } else { ?>
					<?php foreach ($language as $key => $value) {?>
					<tr data-id="<?php echo $value['tr_code'];?>">
						<th>
							<span><?php echo $value['name'];?></span>
						</th>
						<td class="p0">
							<textarea name="language[<?php echo $value['lan_id'];?>]" class="form-control" autocomplete="off"></textarea>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<button type="botton" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-info" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	<form class="form-horizontal">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="f24 dealbox-title">编辑</div>
		<input type="hidden" name="site_id" value="0">
		<input type="hidden" name="opn" value="editSite">
		<div class="input-group">
			<div class="input-group-addon"><span>名称</span>：</div>
			<input class="form-control" name="name" required="required" />
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>域名</span>：</div>
			<input class="form-control" name="domain" required="required" />
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>title</span>：</div>
			<textarea class="form-control" name="title" required="required"></textarea>
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>keyword</span>：</div>
			<textarea class="form-control" name="keyword" required="required"></textarea>
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>description</span>：</div>
			<textarea class="form-control" name="description" required="required"></textarea>
		</div>
		<button type="botton" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
	</form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	SITE.init();
});
</script>