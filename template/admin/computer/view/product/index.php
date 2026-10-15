<div class="container-fluid">
	<form action="<?php echo adminUrl('product');?>" class="form-inline" method="get">
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="spu_id" value="<?php echo $spuId > 0 ? $spuId : '';?>" placeholder="SPU ID" autocomplete="off">
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '');?>" placeholder="产品名称关键字" autocomplete="off">
		</div>
		<div class="form-group mr10 mb10">
			<select class="form-control" name="status">
				<option value="-1">全部状态</option>
				<?php if (!empty($statusList)) {
					foreach ($statusList as $key => $value) {?>
				<option <?php if ($status == $key) { echo 'selected'; }?> value="<?php echo $key;?>"><?php echo $value;?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mr10 mb10">
			<select class="form-control" name="cate">
				<option value="0">全部产品分类</option>
			</select>
		</div>
		<div class="form-group mr10 mb10">
			<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($stime);?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
			<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($etime);?>" name="etime" placeholder="结束时间" autocomplete="off">
		</div>
		<div class="form-group mb10">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			<a class="btn btn-default ml6" href="<?php echo adminUrl('product');?>"><i class="glyphicon glyphicon-refresh"></i> 重置</a>
			<span class="ml10 c6">共 <strong><?php echo (int)$total;?></strong> 个产品</span>
		</div>
		<div class="clear"></div>
	</form>
</div>
<div class="container-fluid">
	<?php if (!empty($list)) { ?>
	<div id="spu-list" class="w100 mt6">
		<?php foreach ($list as $value) { ?>
		<div class="spu-item">
			<a href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" class="block" title="<?php echo htmlspecialchars($value['name']);?>">
				<div class="spu-image">
					<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload" alt="<?php echo htmlspecialchars($value['name']);?>">
				</div>
				<div class="name-content f600">
					<div class="spu-name e2 f400"><?php echo htmlspecialchars($value['name'] ?: '--');?></div>
					<div class="e1">分类: <?php echo htmlspecialchars($cateArr[$value['cate_id']] ?? '--');?></div>
					<div class="e1">价格: <?php echo $value['min_price'] < $value['max_price'] ? '￥'.$value['min_price'].' - ￥'.$value['max_price'] : '￥'.$value['min_price'];?></div>
					<div>
						<span>PID: <?php echo $value['spu_id'];?></span>
						<span class="right <?php echo $value['status'] == 1 ? 'green' : 'red';?>"><?php echo $value['status_text'];?></span>
					</div>
				</div>
			</a>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	<?php echo page($size, $total);?>
	<?php } else {?>
	<div class="tc c9 mt20">没有找到符合条件的产品数据</div>
	<?php }?>
</div>

<script type="text/javascript">
var cate_list = <?php echo json_encode($cateList, JSON_UNESCAPED_UNICODE);?>;
var site = <?php echo (int)$site;?>;
var cate = <?php echo (int)$cate;?>;
</script>