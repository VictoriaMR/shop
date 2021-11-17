<div class="container-fluid">
	<form action="<?php echo url('product');?>" class="form-inline" method="get">
		<div class="form-group mt10 mr20">
			<input type="text" class="form-control" name="spu_id" value="<?php echo empty($spuId) ? '' : $spuId;?>" placeholder="SPU ID" autocomplete="off">
		</div>
		<div class="form-group mt10 mr20">
			<select class="form-control" name="status" style="min-width:180px;">
				<option value="-1">请选择状态</option>
				<?php if (!empty($statusList)) {
					foreach ($statusList as $key => $value) {?>
				<option <?php if ($status==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mt10 mr20">
			<select class="form-control" name="site" style="min-width:180px;">
				<option value="-1">请选择站点</option>
				<?php if (!empty($siteList)) {
					foreach ($siteList as $key => $value) {?>
				<option <?php if ($site==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mt10 mr20">
			<select class="form-control" name="cate" style="min-width:180px;">
				<option value="-1">请选择分类</option>
				<?php if (!empty($cateList)) {
					foreach ($cateList as $key => $value) {?>
				<option <?php if ($cate==$value['cate_id']){ echo 'selected';}?> value="<?php echo $value['cate_id'];?>" <?php if ($value['level'] === 0){ echo 'disabled="disabled"';}?>><?php echo $value['level']>0 ? '&nbsp;&nbsp;&nbsp;': '';?><?php echo $value['name'];?></option>
				<?php } }?>
			</select>
		</div>
		<div class="mr20 form-group mt10">
			<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
			<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
		</div>
		<div class="mr20 form-group mt10">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
		</div>
		<div class="clear"></div>
	</form>
</div>
<?php if (!empty($list)) { ?>
<div class="container-fluid">
	<div id="spu-list" class="w100 mt20">
		<?php foreach ($list as $value) { ?>
		<div class="spu-item">
			<a href="<?php echo $value['url'];?>" class="block">
				<div class="spu-image">
					<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload">
				</div>
				<div class="name-content f600">
					<div class="spu-name e2 f400"><?php echo $value['name'];?></div>
					<div>
						<div class="w50 left e1">分类: <?php echo empty($cateList[$value['cate_id']]) ? '' : $cateList[$value['cate_id']]['name'];?></div>
						<div class="w50 right e1">站点: <?php echo $siteList[$value['site_id']];?></div>
						<div class="clear"></div>
					</div>
					<div class="e1">价格: <?php echo '￥'.$value['min_price'].' - ￥'.$value['max_price'];?></div>
					<div>
						<span>PID: <?php echo $value['spu_id'];?></span>
						<span class="right" style="color: <?php echo $value['status'] == 1 ? 'green' : 'red';?>"><?php echo $value['status_text'];?></span>
					</div>
				</div>
			</a>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	<?php echo page($size, $total);?>
</div>
<?php } ?>