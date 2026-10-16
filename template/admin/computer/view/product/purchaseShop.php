<div class="container-fluid" id="purchase-list-page">
	<form action="<?php echo adminUrl('product/purchaseShop');?>" class="form-inline filter-bar" method="get">
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="id" value="<?php echo !empty($id) && $id > 0 ? (int)$id : '';?>" placeholder="ID" autocomplete="off" style="width: 90px;">
		</div>
		<div class="form-group mr10 mb10">
			<select class="form-control" name="channel_id">
				<option value="-1">全部渠道</option>
				<?php if (!empty($channelList)) {
					foreach ($channelList as $key => $value) {?>
				<option <?php if ($channel_id == $key) { echo 'selected'; }?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="unique_id" value="<?php echo htmlspecialchars($unique_id);?>" placeholder="店铺源ID" autocomplete="off" style="width: 160px;">
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name);?>" placeholder="店铺名称" autocomplete="off" style="width: 200px;">
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
				<th width="160">源ID</th>
				<th>店铺名称</th>
				<th width="110">产品数量</th>
				<th width="260">店铺评分信息</th>
				<th width="160">添加时间</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="8" class="tc c9 empty-row">暂无供应商店铺数据</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $key => $value) {
				$shopUrl = !empty($value['url']) ? (strpos($value['url'], 'http') === 0 ? $value['url'] : 'https://' . ltrim($value['url'], '/')) : '';
				$pCount = (int)$value['product_count'];
			?>
			<tr>
				<td><?php echo $value['purchase_shop_id'];?></td>
				<td>
					<span class="channel-badge channel-<?php echo (int)$value['channel_id'];?>">
						<?php echo htmlspecialchars($channelList[$value['channel_id']] ?? '--');?>
					</span>
				</td>
				<td>
					<?php if (!empty($value['unique_id'])) { ?>
					<a class="source-link" href="<?php echo htmlspecialchars($shopUrl);?>" target="_blank" title="打开源店铺首页">
						<span class="glyphicon glyphicon-link"></span> <?php echo htmlspecialchars($value['unique_id']);?>
					</a>
					<?php } else { ?>
					<?php echo htmlspecialchars($value['unique_id'] ?: '--');?>
					<?php } ?>
				</td>
				<td>
					<a class="product-title-link" href="<?php echo url('Product/purchaseList', ['shop_id' => $value['purchase_shop_id']]);?>">
						<span class="glyphicon glyphicon-link"></span> <?php echo htmlspecialchars($value['name'] ?: '--');?>
					</a>
				</td>
				<td>
					<?php if ($pCount > 0) { ?>
					<a class="count-badge" href="<?php echo adminUrl('product/purchaseList', ['shop_id' => $value['purchase_shop_id']]);?>" title="查看该店铺下的采购产品">
						<?php echo $pCount;?>
					</a>
					<?php } else { ?>
					<span class="text-muted">0</span>
					<?php } ?>
				</td>
				<td class="shop-cell"><?php echo purchase()->shop()->shopInfo($value, false);?></td>
				<td class="time-cell">
					<span title="添加时间"><?php echo $value['add_time'];?></span>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<div class="row mt10">
		<div class="col-xs-12">
			<?php echo page($size, $total);?>
		</div>
	</div>
</div>