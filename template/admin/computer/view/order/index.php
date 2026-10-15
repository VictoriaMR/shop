<?php
$statusBadgeMap = [
	-1 => 'label-default',
	0  => 'label-warning',
	1  => 'label-info',
	2  => 'label-primary',
	3  => 'label-success',
	4  => 'label-danger',
	5  => 'label-danger',
	6  => 'label-danger',
];
?>
<div class="container-fluid" id="order-page">
	<form action="<?php echo adminUrl('order/index');?>" method="get" class="form-inline" id="order-search-form">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left status-filter" role="group" style="margin-right: 12px; margin-bottom: 10px;">
					<button type="button" data-status="-99" class="btn <?php echo $status < -1 ? 'btn-primary' : 'btn-default';?>">全部</button>
					<?php foreach ($statusList ?? [] as $sKey => $sLabel){ ?>
					<button type="button" data-status="<?php echo (int)$sKey;?>" class="btn <?php echo (int)$status === (int)$sKey ? 'btn-primary' : 'btn-default';?>"><?php echo htmlspecialchars($sLabel);?></button>
					<?php } ?>
				</div>
				<input type="hidden" name="status" value="<?php echo (int)$status;?>">

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="site_id">
						<option value="0">全部站点</option>
						<?php foreach ($siteList ?? [] as $k => $v){ ?>
						<option value="<?php echo (int)$k;?>" <?php echo (int)$site_id === (int)$k ? 'selected' : '';?>><?php echo htmlspecialchars($v);?></option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="payment_id">
						<option value="0">全部支付账号</option>
						<?php foreach ($paymentAccount ?? [] as $k => $v){ ?>
						<option value="<?php echo (int)$k;?>" <?php echo (int)$payment_id === (int)$k ? 'selected' : '';?>><?php echo htmlspecialchars($v);?></option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="order_id" value="<?php echo htmlspecialchars((string)$order_id);?>" placeholder="订单ID" autocomplete="off" style="width: 120px;">
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars((string)$email);?>" placeholder="用户邮箱关键字" autocomplete="off" style="width: 170px;">
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars((string)$stime);?>" name="stime" placeholder="开始时间" autocomplete="off" style="width: 135px;">
					<span class="text-muted">-</span>
					<input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars((string)$etime);?>" name="etime" placeholder="结束时间" autocomplete="off" style="width: 135px;">
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<button class="btn btn-info" type="submit"><i class="fa fa-search"></i> 查询</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="110">订单ID / 终端</th>
				<th width="140">站点 / 语种</th>
				<th width="190">用户信息</th>
				<th width="150">订单金额</th>
				<th width="160">支付方式 / 状态</th>
				<th width="110">订单状态</th>
				<th width="180">时间记录</th>
				<th width="140">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange"><i class="fa fa-info-circle"></i> 暂无订单数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value){
				$badgeClass = $statusBadgeMap[(int)$value['status']] ?? 'label-default';
				$isPaid = !empty($value['pay_time']) && $value['pay_time'] !== '0000-00-00 00:00:00';
				$currency = htmlspecialchars($value['currency'] ?: 'USD');
			?>
			<tr data-id="<?php echo (int)$value['order_id'];?>" data-status="<?php echo (int)$value['status'];?>">
				<td>
					<div><strong>#<?php echo (int)$value['order_id'];?></strong></div>
					<div class="text-muted f12">
						<?php if (!empty($value['is_mobile'])){ ?>
						<i class="fa fa-mobile-screen" title="移动端下单"></i> 移动端
						<?php } else { ?>
						<i class="fa fa-desktop" title="PC端下单"></i> PC端
						<?php } ?>
					</div>
				</td>
				<td>
					<div><strong><?php echo htmlspecialchars($value['site_name']);?></strong></div>
					<div class="text-muted f12">语言: <?php echo htmlspecialchars($value['lan_id'] ?: '-');?></div>
				</td>
				<td>
					<?php if (!empty($value['member_name']) || !empty($value['member_email'])){ ?>
					<div><strong><?php echo htmlspecialchars($value['member_name'] ?: ('用户#' . $value['mem_id']));?></strong> <span class="text-muted f12">[ID:<?php echo (int)$value['mem_id'];?>]</span></div>
					<?php if (!empty($value['member_email'])){ ?>
					<div class="text-muted f12"><?php echo htmlspecialchars($value['member_email']);?></div>
					<?php } ?>
					<?php } else { ?>
					<span class="text-muted">用户ID: <?php echo (int)$value['mem_id'];?></span>
					<?php } ?>
				</td>
				<td>
					<div style="color: #e7502b; font-weight: 600;">
						<?php echo $currency . ' ' . number_format((float)$value['order_total'], 2);?>
					</div>
					<div class="text-muted f12" title="商品小计 + 运费">
						商品: <?php echo number_format((float)$value['product_total'], 2);?> / 运费: <?php echo number_format((float)$value['shipping_fee'], 2);?>
					</div>
				</td>
				<td>
					<div><?php echo htmlspecialchars($value['payment_name']);?></div>
					<div class="mt2">
						<?php if ($isPaid){ ?>
						<span class="label label-success" style="font-weight: normal;">已付款</span>
						<?php } else { ?>
						<span class="label label-default" style="font-weight: normal;">未付款</span>
						<?php } ?>
					</div>
				</td>
				<td>
					<span class="label <?php echo $badgeClass;?>" style="font-weight: normal; padding: 4px 8px;">
						<?php echo htmlspecialchars($value['status_name']);?>
					</span>
				</td>
				<td>
					<div title="下单时间"><span class="text-muted">下:</span> <?php echo !empty($value['add_time']) ? $value['add_time'] : '-';?></div>
					<div title="付款时间"><span class="text-muted">付:</span> <?php echo $isPaid ? $value['pay_time'] : '-';?></div>
				</td>
				<td style="white-space: nowrap;">
					<button type="button" class="btn btn-info btn-xs detail-btn"><i class="fa fa-eye"></i> 详情</button>
					<button type="button" class="btn btn-primary btn-xs ml4 status-btn"><i class="fa fa-pen-to-square"></i> 状态</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12">
			<?php echo page($size, $total);?>
		</div>
	</div>
</div>

<!-- 修改订单状态弹窗 -->
<div id="dealbox-status" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 420px;">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">更新订单状态</div>
			<input type="hidden" name="order_id" value="0">
			<input type="hidden" name="opn" value="modifyStatus">
			<div class="input-group">
				<div class="input-group-addon"><span>订单ID：</span></div>
				<input type="text" class="form-control" name="order_id_Text" readonly style="background: #f8fafc;">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>订单状态：</span></div>
				<select class="form-control" name="status">
					<?php foreach ($statusList ?? [] as $sKey => $sLabel){ ?>
					<option value="<?php echo (int)$sKey;?>"><?php echo htmlspecialchars($sLabel);?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-status-btn mt10">确认更新</button>
		</form>
	</div>
</div>

<!-- 订单详情弹窗 -->
<div id="dealbox-detail" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 720px; max-width: 94vw;">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<div class="f24 dealbox-title">订单详情 <small id="detail-order-title" class="text-muted"></small></div>
		<div id="detail-body" style="max-height: 70vh; overflow-y: auto; padding-right: 4px;"></div>
	</div>
</div>