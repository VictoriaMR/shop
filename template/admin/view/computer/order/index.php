<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="col-md-12 pt10">
			<div class="form-group mt10 mr20">
				<label for="short_name">订单号:</label>
				<input type="text" class="form-control" name="order_id" value="<?php echo $order_id;?>" placeholder="订单号" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">email:</label>
				<input type="text" class="form-control" name="email" value="<?php echo $email;?>" placeholder="订单邮箱" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<select class="form-control" name="site_id" style="min-width:180px">
					<option value="0" <?php if(!$site_id){echo 'selected';}?>>请选择站点</option>
					<?php if (!empty($siteList)){?>
					<?php foreach($siteList as $key=>$value){?>
					<option value="<?php echo $key;?>" <?php if($site_id==$key){echo 'selected';}?>><?php echo $value;?></option>
					<?php }?>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<select class="form-control" name="status" style="min-width:180px">
					<option value="-1" <?php if($status<0){echo 'selected';}?>>请选择状态</option>
					<?php if (!empty($statusList)){?>
					<?php foreach($statusList as $key=>$value){?>
					<option value="<?php echo $key;?>" <?php if($status==$key){echo 'selected';}?>><?php echo $value;?></option>
					<?php }?>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<select class="form-control" name="payment_id" style="min-width:180px">
					<option value="0" <?php if($status<0){echo 'selected';}?>>请选择支付账号</option>
					<?php if (!empty($paymentAccount)){?>
					<?php foreach($paymentAccount as $key=>$value){?>
					<option value="<?php echo $key;?>" <?php if($payment_id==$key){echo 'selected';}?>><?php echo $value;?></option>
					<?php }?>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">日期:</label>
				<input class="form-control form_datetime" type="text" value="<?php echo $stime;?>" name="stime" placeholder="开始时间" autocomplete="off"> - 
				<input class="form-control form_datetime" type="text" value="<?php echo $etime;?>" name="etime" placeholder="结束时间" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th width="120">订单号</th>
				<th width="140">站点</th>
				<th width="180">用户信息</th>
				<th width="140">订单状态</th>
				<th width="140">付款状态</th>
				<th width="200">订单时间</th>
				<th width="200">付款时间</th>
				<th>操作</th>
			</tr>
			<?php if (empty($list)){?>
			<tr>
				<td colspan="10" class="orange tc">暂无数据</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value){?>
			<tr>
				<td></td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
	<?php echo page($size, $total);?>
</div>