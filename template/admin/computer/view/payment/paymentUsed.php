<div class="container-fluid" id="payment-used-page">
	<form action="<?php echo adminUrl('payment/paymentUsed');?>" method="get" class="form-inline">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="site_id">
						<option value="">全部站点</option>
						<?php foreach ($siteList as $key => $value){ ?>
						<option <?php echo (string)$key === (string)iget('site_id') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="type">
						<option value="">全部支付类型</option>
						<?php foreach ($typeList as $key => $value){ ?>
						<option <?php echo (string)$key === (string)iget('type') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<select class="form-control" name="payment_id">
						<option value="">全部支付账户</option>
						<?php foreach ($paymentList as $key => $value){ ?>
						<option <?php echo (string)$key === (string)iget('payment_id') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?> (ID: <?php echo $key;?>)</option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>

				<div class="form-group right" style="margin-bottom: 10px;">
					<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增账户使用</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="200">站点名称</th>
				<th width="260">关联支付账号</th>
				<th width="140">支付类型</th>
				<th width="180">添加时间</th>
				<th width="140">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="6">
					<div class="tc orange">
						<i class="glyphicon glyphicon-info-sign"></i> 暂无账户使用配置数据
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) { 
				$siteName = $siteList[$value['site_id']] ?? ('站点-' . $value['site_id']);
				$paymentName = $paymentList[$value['payment_id']] ?? ('账户-' . $value['payment_id']);
				$typeName = $typeList[$value['type']] ?? ('类型-' . $value['type']);
			?>
			<tr data-id="<?php echo $value['item_id'];?>">
				<td><strong><?php echo $value['item_id'];?></strong></td>
				<td>
					<span class="label label-default" style="background:#eef2f6; color:#475569; border:1px solid #cbd5e1; font-weight:normal; font-size:12px; padding:3px 7px;">
						<?php echo htmlspecialchars($siteName);?>
					</span>
				</td>
				<td>
					<strong style="color: #0f172a;"><?php echo htmlspecialchars($paymentName);?></strong>
					<span class="text-muted f12" style="display:block;">账号 ID: <?php echo $value['payment_id'];?></span>
				</td>
				<td>
					<?php echo htmlspecialchars($typeName);?>
				</td>
				<td>
					<span class="text-muted"><i class="glyphicon glyphicon-time f12"></i> <?php echo $value['add_time'];?></span>
				</td>
				<td>
					<button class="btn btn-primary btn-xs modify" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
					<button class="btn btn-danger btn-xs delete" type="button" style="margin-left: 4px;"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12">
			<?php echo page($size ?? 20, $total);?>
		</div>
	</div>
</div>

<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 520px;">
		<form class="form-horizontal">
			<input type="hidden" name="item_id" value="0">
			<input type="hidden" name="opn" value="editUsedInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增账户使用</div>
			
			<div class="input-group" style="width: 100%; margin-bottom: 14px;">
				<span class="input-group-addon" style="width: 110px;"><span>站点</span></span>
				<select class="form-control" name="site_id" required="required" style="width: 100%;">
					<option value="">请选择站点</option>
					<?php foreach ($siteList as $key => $value){ ?>
					<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
					<?php } ?>
				</select>
			</div>

			<div class="input-group" style="width: 100%; margin-bottom: 14px;">
				<span class="input-group-addon" style="width: 110px;"><span>支付类型</span></span>
				<select class="form-control" name="type" required="required" style="width: 100%;">
					<option value="">请选择支付类型</option>
					<?php foreach ($typeList as $key => $value){ ?>
					<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?></option>
					<?php } ?>
				</select>
			</div>

			<div class="input-group" style="width: 100%; margin-bottom: 14px;">
				<span class="input-group-addon" style="width: 110px;"><span>支付账户</span></span>
				<select class="form-control" name="payment_id" required="required" style="width: 100%;">
					<option value="">请选择关联支付账户</option>
					<?php foreach ($paymentList as $key => $value){ ?>
					<option value="<?php echo $key;?>"><?php echo htmlspecialchars($value);?> (ID: <?php echo $key;?>)</option>
					<?php } ?>
				</select>
			</div>

			<div style="margin-top: 20px;">
				<button type="button" class="btn btn-primary btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>