<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="form-group mr20">
			<select class="form-control" name="site" style="min-width:180px;">
				<option value="-1">请选择站点</option>
				<?php if (!empty($siteList)) {
					foreach ($siteList as $key => $value) {?>
				<option <?php if ($site==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mr20">
			<select class="form-control" name="cate" style="min-width:180px;">
				<option value="-1">请选择分类</option>
				<?php if (!empty($cateList)) {
					foreach ($cateList as $key => $value) {
					$disabled = false;
					if (isset($cateList[$key+1]) && $value['level'] < $cateList[$key+1]['level']) {
						$disabled = true;
					}
					$text = '';
					$step = '&nbsp;&nbsp;&nbsp;';
					for ($lev=0; $lev < $value['level']; $lev++) {
						$text .= $step;
					}
				?>
				<option <?php if ($cate==$value['cate_id']){ echo 'selected';}?> value="<?php echo $value['cate_id'];?>" <?php echo $disabled ? 'disabled="disabled"' : '';?>><?php echo $text;?><?php echo $value['name'];?></option>
				<?php } }?>
			</select>
		</div>
		<div class="mr20 form-group">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
		</div>
		<div class="form-group right">
			<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增站点品类</button>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-2">站点</th>
				<th class="col-md-2">分类</th>
				<th class="col-md-1">头像</th>
				<th class="col-md-1">排序</th>
				<th class="col-md-1">销售数</th>
				<th class="col-md-1">浏览数</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php $tempCateList = array_column($cateList, null, 'cate_id');
			foreach ($list as $key => $value) { ?>
			<tr class="item" data-id="<?php echo $value['item_id'];?>">
				<td class="col-md-1"><?php echo $value['item_id'];?></td>
				<td class="col-md-2">
					<?php echo $siteList[$value['site_id']];?>
				</td>
				<td class="col-md-2">
					<?php if (!empty($value['parent'])){?>
					<span class="f12 c6"><?php echo implode(' / ', array_column($value['parent'], 'name'));?></span>
					<br />
					<?php } ?>
					<span class="f600"><?php echo $tempCateList[$value['cate_id']]['name'];?></span>
				</td>
				<td class="col-md-1">
					<div class="avatar-hover">
						<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload">
					</div>
				</td>
				<td class="col-md-1">
					<input type="text" name="sort" value="<?php echo $value['sort'];?>" class="form-control">
				</td>
				<td class="col-md-1">
					<input type="text" name="sale_total" value="<?php echo $value['sale_total'];?>" class="form-control">
				</td>
				<td class="col-md-1">
					<input type="text" name="visit_total" value="<?php echo $value['visit_total'];?>" class="form-control">
				</td>
				<td class="col-md-2">
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
			<div class="f24 dealbox-title">添加站点品类</div>
			<input type="hidden" name="item_id" value="0">
			<input type="hidden" name="opn" value="editSiteCategory">
			<div class="input-group">
				<div class="input-group-addon"><span>站点：</span></div>
				<select class="form-control" name="site_id" required="required">
					<option value="-1">请选择站点</option>
					<?php if (!empty($siteList)) {
						foreach ($siteList as $key => $value) {?>
					<option <?php if ($site==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php } }?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>分类：</span></div>
				<select class="form-control" name="cate_id" required="required">
					<option value="-1">请选择分类</option>
					<?php if (!empty($cateList)) {
						foreach ($cateList as $key => $value) {
						$text = '';
						$step = '&nbsp;&nbsp;&nbsp;';
						for ($lev=0; $lev < $value['level']; $lev++) {
							$text .= $step;
						}
					?>
					<option <?php if ($cate==$value['cate_id']){ echo 'selected';}?> value="<?php echo $value['cate_id'];?>"><?php echo $text;?> <?php echo $value['name'];?></option>
					<?php } }?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
		</form>
	</div>
</div>