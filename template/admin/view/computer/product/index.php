<div class="container-fluid">
	<form action="<?php echo adminUrl('product');?>" class="form-inline" method="get">
		<div class="form-group">
			<input type="text" class="form-control" name="spu_id" value="<?php echo empty($spuId) ? '' : $spuId;?>" placeholder="SPU ID" autocomplete="off">
		</div>
		<div class="form-group">
			<select class="form-control" name="status">
				<option value="-1">请选择状态</option>
				<?php if (!empty($statusList)) {
					foreach ($statusList as $key => $value) {?>
				<option <?php if ($status==$key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo $value;?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group">
			<select class="form-control" name="cate">
				<option value="-1">请选择分类</option>
			</select>
		</div>
		<div class="form-group">
			<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
			<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
		</div>
		<div class="form-group">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
		</div>
		<div class="clear"></div>
	</form>
</div>
<div class="container-fluid">
	<?php if (!empty($list)) { ?>
	<div id="spu-list" class="w100 mt6">
		<?php foreach ($list as $value) { ?>
		<div class="spu-item">
			<a href="<?php echo adminUrl('product/detail', ['id'=>$value['spu_id']]);?>" class="block">
				<div class="spu-image">
					<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
				</div>
				<div class="name-content f600">
					<div class="spu-name e2 f400"><?php echo $value['name'];?></div>
					<div>
						<div class="e1">分类: <?php echo $cateArr[$value['cate_id']] ?? '--';?></div>
					</div>
					<div class="e1">价格: <?php echo $value['min_price'] < $value['max_price'] ? '￥'.$value['min_price'].' - ￥'.$value['max_price'] : '￥'.$value['min_price'];?></div>
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
	<?php } else {?>
	<p class="orange">没有找到产品数据</p>
	<?php }?>
</div>

<script type="text/javascript">
var cate_list = <?php echo json_encode($cateList, JSON_UNESCAPED_UNICODE);?>;
var site = <?php echo $site;?>;
var cate = <?php echo $cate;?>;
</script>