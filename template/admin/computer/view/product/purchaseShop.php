<div class="container-fluid" id="purchase-list-page">
	<form action="<?php echo adminUrl('product/purchaseShop');?>" class="form-inline" method="get">
		<div class="form-group mr10 mb10">
			<select class="form-control" name="channel_id">
				<option value="-1">全部渠道</option>
				<?php if (!empty($channelList)) {
					foreach ($channelList as $key => $value) {?>
				<option <?php if ($channel_id == $key){ echo 'selected';}?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="unique_id" value="<?php echo htmlspecialchars($unique_id);?>" placeholder="店铺源ID" autocomplete="off">
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name);?>" placeholder="店铺名称" autocomplete="off">
		</div>
		<div class="form-group mb10">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="90">渠道</th>
				<th width="150">源ID</th>
				<th>店铺名称</th>
				<th width="100">产品数量</th>
				<th width="200">店铺信息</th>
				<th width="150">添加时间</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="7" class="tc c9">暂无供应商店铺数据</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value) {
				$shopUrl = !empty($value['url']) ? (strpos($value['url'], 'http') === 0 ? $value['url'] : 'https://' . ltrim($value['url'], '/')) : '';
			?>
			<tr>
				<td><?php echo $value['purchase_shop_id'];?></td>
				<td><?php echo htmlspecialchars($channelList[$value['channel_id']] ?? '--');?></td>
				<td><?php echo htmlspecialchars($value['unique_id']);?></td>
				<td>
					<?php if ($shopUrl) { ?>
					<span class="glyphicon glyphicon-link"></span> <a href="<?php echo htmlspecialchars($shopUrl);?>" target="_blank"><?php echo htmlspecialchars($value['name'] ?: '--');?></a>
					<?php } else { ?>
					<?php echo htmlspecialchars($value['name'] ?: '--');?>
					<?php } ?>
				</td>
				<td><?php echo (int)$value['product_count'];?></td>
				<td><?php echo purchase()->shop()->shopInfo($value, false);?></td>
				<td>
					<span title="添加时间"><?php echo $value['add_time'];?></span>
				</td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<div class="row mt10">
		<div class="col-xs-12">
			<?php echo page($size, $total);?>
		</div>
	</div>
</div>