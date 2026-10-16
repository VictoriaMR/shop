<div class="container-fluid" id="purchase-list-page">
	<form action="<?php echo adminUrl('product/purchaseList');?>" class="form-inline filter-bar" method="get">
		<?php if (!empty($shop_id)) { ?>
		<input type="hidden" name="shop_id" value="<?php echo (int)$shop_id;?>">
		<?php } ?>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="id" value="<?php echo $id > 0 ? $id : '';?>" placeholder="ID" autocomplete="off" style="width: 90px;">
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="item_id" value="<?php echo htmlspecialchars($item_id);?>" placeholder="产品源ID" autocomplete="off" style="width: 150px;">
		</div>
		<div class="form-group mr10 mb10">
			<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '');?>" placeholder="产品标题关键字" autocomplete="off" style="width: 180px;">
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
			<select class="form-control" name="status">
				<option value="-1">全部状态</option>
				<?php if (!empty($statusList)) {
					foreach ($statusList as $key => $value) {?>
				<option <?php if ($status == $key) { echo 'selected'; }?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
				<?php } }?>
			</select>
		</div>
		<div class="form-group mr10 mb10">
			<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($stime);?>" name="stime" placeholder="开始时间" autocomplete="off" style="width: 120px;"> - 
			<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($etime);?>" name="etime" placeholder="结束时间" autocomplete="off" style="width: 120px;">
		</div>
		<div class="form-group mb10">
			<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
		</div>
		<?php if (!empty($shop_id)) { ?>
		<div class="form-group ml10 mb10">
			<span class="shop-filter-tag">
				当前店铺: <?php echo htmlspecialchars($filterShop['name'] ?? ('#' . $shop_id));?>
				<a href="<?php echo adminUrl('product/purchaseList');?>" title="取消店铺筛选"><i class="glyphicon glyphicon-remove"></i></a>
			</span>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt6" id="data-list">
		<thead>
			<tr>
				<th width="65">ID</th>
				<th width="80">渠道</th>
				<th width="145">源ID</th>
				<th width="115">价格 / 销量</th>
				<th>产品标题</th>
				<th width="86">状态</th>
				<th width="220">店铺信息</th>
				<th width="90">添加人</th>
				<th width="165">时间</th>
				<th width="155">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)) { ?>
			<tr>
				<td colspan="10" class="tc c9 empty-row">暂无采购产品数据</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $key => $value) {
				$statusConstNormal = purchase()->spu()->getConst('STATUS_NORMAL');
				$statusConstSet    = purchase()->spu()->getConst('STATUS_SET');
				$statusConstUsed   = purchase()->spu()->getConst('STATUS_USED');
				$statusConstSpu    = purchase()->spu()->getConst('STATUS_SPU');
			?>
			<tr
				data-id="<?php echo $value['purchase_spu_id'];?>"
				data-status="<?php echo $value['status'];?>"
			>
				<td><?php echo $value['purchase_spu_id'];?></td>
				<td>
					<span class="channel-badge channel-<?php echo (int)$value['channel_id'];?>">
						<?php echo htmlspecialchars($channelList[$value['channel_id']] ?? '--');?>
					</span>
				</td>
				<td>
					<?php if (!empty($value['url'])) { ?>
					<a class="source-link" href="<?php echo htmlspecialchars($value['url']);?>" target="_blank" title="打开源站点商品详情">
						<span class="glyphicon glyphicon-link"></span> <?php echo htmlspecialchars($value['item_id']);?>
					</a>
					<?php } else { ?>
					<?php echo htmlspecialchars($value['item_id']);?>
					<?php } ?>
				</td>
				<td>
					<div class="price-text" title="最低采购价"><?php echo $value['price'] > 0 ? '¥' . $value['price'] : '--';?></div>
					<div class="sale-text" title="销量">销量: <?php echo $value['sale_count'] > 0 ? $value['sale_count'] : '--';?></div>
				</td>
				<td class="title-cell">
					<?php echo htmlspecialchars($value['name'] ?: '--');?>
					<?php if (!empty($value['spu_id'])) { ?>
					<a class="spu-link-badge" href="<?php echo adminUrl('product/detail', ['id' => $value['spu_id']]);?>" target="_blank" title="查看已生成SPU详情">
						SPU #<?php echo (int)$value['spu_id'];?>
					</a>
					<?php } ?>
				</td>
				<td>
					<span class="status-badge status-<?php echo (int)$value['status'];?>">
						<?php echo htmlspecialchars($statusList[$value['status']] ?? $value['status']);?>
					</span>
				</td>
				<td class="shop-cell"><?php echo purchase()->shop()->shopInfo($value['shop_info']);?></td>
				<td><?php echo $value['user_info'] ?: '--';?></td>
				<td class="time-cell">
					<div title="添加时间"><span class="time-tag">添</span> <?php echo $value['add_time'];?></div>
					<div class="text-muted" title="更新时间"><span class="time-tag">更</span> <?php echo $value['update_time'] ?: '--';?></div>
				</td>
				<td class="action-cell">
					<?php if (in_array($value['status'], [$statusConstNormal, $statusConstSet, $statusConstUsed])) { ?>
					<a target="_blank" class="btn btn-info btn-xs" href="<?php echo htmlspecialchars($value['url']);?>"><?php echo $value['status'] == $statusConstNormal ? '上传' : '更新';?></a>
					<?php } ?>
					<?php if (in_array($value['status'], [$statusConstSet, $statusConstUsed, $statusConstSpu])) { ?>
					<a class="btn btn-primary btn-xs" target="_blank" href="<?php echo adminUrl('product/operate', ['id' => $value['purchase_spu_id']]);?>">配置</a>
					<?php } ?>
					<button type="button" class="btn btn-default btn-xs btn-edit">编辑</button>
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
<!-- 编辑弹窗 -->
<form class="s-modal form-horizontal edit-status-modal">
	<input type="hidden" name="id" value="0">
	<input type="hidden" name="opn" value="edit">
	<p class="title"><span>状态修改</span></p>
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content">
		<div class="input-group">
			<div class="input-group-addon"><span>操作行为：</span></div>
			<select class="form-control" name="status">
				<option value="-1">请选择状态</option>
				<?php foreach ($statusList as $k => $v) { ?>
				<option value="<?php echo $k;?>"><?php echo htmlspecialchars($v);?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<button type="button" class="btn btn-primary btn-lg btn-block btn-save mt20">保存</button>
</form>