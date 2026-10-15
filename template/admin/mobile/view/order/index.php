<?php
$statusBadgeMap = [
	-1 => 'tag-default',
	0  => 'tag-warning',
	1  => 'tag-info',
	2  => 'tag-primary',
	3  => 'tag-success',
	4  => 'tag-danger',
	5  => 'tag-danger',
	6  => 'tag-danger',
];
?>
<div id="order-page" class="order-page" data-url="<?php echo adminUrl('order/index');?>">
	<!-- 顶部标题栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-receipt"></i>
			<span>订单列表</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 单</span>
		</div>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('order/index');?>" method="get" class="search-form" id="order-search-form">
			<input type="hidden" name="status" value="<?php echo (int)$status;?>">

			<div class="status-filter">
				<button type="button" data-status="-99" class="btn btn-xs <?php echo $status < -1 ? 'btn-primary' : 'btn-default';?>">全部</button>
				<?php foreach ($statusList ?? [] as $sKey => $sLabel) { ?>
				<button type="button" data-status="<?php echo (int)$sKey;?>" class="btn btn-xs <?php echo (int)$status === (int)$sKey ? 'btn-primary' : 'btn-default';?>"><?php echo htmlspecialchars((string)$sLabel, ENT_QUOTES, 'UTF-8');?></button>
				<?php } ?>
			</div>

			<div class="filter-grid">
				<select class="form-control" name="site_id">
					<option value="0">全部站点</option>
					<?php foreach ($siteList ?? [] as $k => $v) { ?>
					<option value="<?php echo (int)$k;?>" <?php echo (int)$site_id === (int)$k ? 'selected' : '';?>><?php echo htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');?></option>
					<?php } ?>
				</select>

				<select class="form-control" name="payment_id">
					<option value="0">全部支付账号</option>
					<?php foreach ($paymentAccount ?? [] as $k => $v) { ?>
					<option value="<?php echo (int)$k;?>" <?php echo (int)$payment_id === (int)$k ? 'selected' : '';?>><?php echo htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');?></option>
					<?php } ?>
				</select>
			</div>

			<div class="filter-grid">
				<input type="text" class="form-control" name="order_id" value="<?php echo htmlspecialchars((string)$order_id, ENT_QUOTES, 'UTF-8');?>" placeholder="订单ID" autocomplete="off">
				<input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars((string)$email, ENT_QUOTES, 'UTF-8');?>" placeholder="用户邮箱关键字" autocomplete="off">
			</div>

			<div class="filter-row">
				<input class="form-control date-input" type="date" value="<?php echo htmlspecialchars((string)$stime, ENT_QUOTES, 'UTF-8');?>" name="stime" placeholder="开始日期" autocomplete="off">
				<span class="date-sep">-</span>
				<input class="form-control date-input" type="date" value="<?php echo htmlspecialchars((string)$etime, ENT_QUOTES, 'UTF-8');?>" name="etime" placeholder="结束日期" autocomplete="off">
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 订单卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-receipt"></i>
				<p>暂无匹配的订单数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$orderIdVal = (int)$value['order_id'];
				$statusVal = (int)$value['status'];
				$badgeClass = $statusBadgeMap[$statusVal] ?? 'tag-default';
				$isPaid = !empty($value['pay_time']) && $value['pay_time'] !== '0000-00-00 00:00:00';
				$currency = htmlspecialchars((string)($value['currency'] ?: 'USD'), ENT_QUOTES, 'UTF-8');
				$memberTitle = !empty($value['member_name']) ? $value['member_name'] : ('用户#' . (int)$value['mem_id']);
			?>
			<div class="order-card item" data-id="<?php echo $orderIdVal;?>" data-status="<?php echo $statusVal;?>">
				<div class="card-main">
					<div class="order-head-info">
						<span class="id-badge">#<?php echo $orderIdVal;?></span>
						<span class="device-tag" title="<?php echo !empty($value['is_mobile']) ? '移动端下单' : 'PC端下单';?>">
							<?php if (!empty($value['is_mobile'])) { ?>
							<i class="fa fa-mobile-screen"></i> 移动
							<?php } else { ?>
							<i class="fa fa-desktop"></i> PC
							<?php } ?>
						</span>
						<span class="status-tag <?php echo $badgeClass;?>"><?php echo htmlspecialchars((string)$value['status_name'], ENT_QUOTES, 'UTF-8');?></span>
						<?php if ($isPaid) { ?>
						<span class="status-tag tag-success">已付款</span>
						<?php } else { ?>
						<span class="status-tag tag-default">未付款</span>
						<?php } ?>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-info btn-xs detail-btn" title="订单详情" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-eye"></i></button>
						<button type="button" class="btn btn-primary btn-xs status-btn" title="更新状态" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
					</div>
				</div>

				<div class="order-body">
					<div class="amount-bar">
						<div class="amount-main">
							<span class="amount-label">订单总额：</span>
							<strong class="amount-total"><?php echo $currency . ' ' . number_format((float)$value['order_total'], 2);?></strong>
						</div>
						<div class="amount-sub">
							商品 <?php echo number_format((float)$value['product_total'], 2);?> / 运费 <?php echo number_format((float)$value['shipping_fee'], 2);?>
						</div>
					</div>

					<div class="info-grid">
						<div class="info-row">
							<span class="info-label"><i class="fa fa-globe"></i> 站点/语言：</span>
							<span class="info-val"><strong><?php echo htmlspecialchars((string)$value['site_name'], ENT_QUOTES, 'UTF-8');?></strong> (<?php echo htmlspecialchars((string)($value['lan_id'] ?: '-'), ENT_QUOTES, 'UTF-8');?>)</span>
						</div>
						<div class="info-row">
							<span class="info-label"><i class="fa fa-user"></i> 客户信息：</span>
							<span class="info-val">
								<?php echo htmlspecialchars((string)$memberTitle, ENT_QUOTES, 'UTF-8');?>
								<small class="text-muted">[ID:<?php echo (int)$value['mem_id'];?>]</small>
								<?php if (!empty($value['member_email'])) { ?>
								<span class="email-text"><?php echo htmlspecialchars((string)$value['member_email'], ENT_QUOTES, 'UTF-8');?></span>
								<?php } ?>
							</span>
						</div>
						<div class="info-row">
							<span class="info-label"><i class="fa fa-credit-card"></i> 支付账号：</span>
							<span class="info-val"><?php echo htmlspecialchars((string)$value['payment_name'], ENT_QUOTES, 'UTF-8');?></span>
						</div>
					</div>

					<div class="time-footer">
						<span>下单：<?php echo !empty($value['add_time']) ? htmlspecialchars((string)$value['add_time'], ENT_QUOTES, 'UTF-8') : '-';?></span>
						<span>付款：<?php echo $isPaid ? htmlspecialchars((string)$value['pay_time'], ENT_QUOTES, 'UTF-8') : '-';?></span>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size, $total);?>

	<!-- 修改订单状态弹窗 -->
	<div id="dealbox-status" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-pen-to-square"></i> 更新订单状态</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="status-form" onsubmit="return false;">
					<input type="hidden" name="order_id" value="0">
					<input type="hidden" name="opn" value="modifyStatus">
					<div class="form-group">
						<label class="form-label">订单ID</label>
						<input type="text" class="form-control" name="order_id_Text" readonly>
					</div>
					<div class="form-group">
						<label class="form-label">订单状态 <span class="required">*</span></label>
						<select class="form-control" name="status">
							<?php foreach ($statusList ?? [] as $sKey => $sLabel) { ?>
							<option value="<?php echo (int)$sKey;?>"><?php echo htmlspecialchars((string)$sLabel, ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-status-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 更新中...">确认更新</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 订单详情只读弹窗（无底部关闭按钮） -->
	<div id="dealbox-detail" class="modal-mask" style="display: none;">
		<div class="modal-panel detail-modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-file-invoice"></i> 订单详情 <small id="detail-order-title" class="detail-sub-id"></small></h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body" id="detail-body"></div>
		</div>
	</div>
</div>
