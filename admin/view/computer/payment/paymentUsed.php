<div class="container-fluid">
	<form action="<?php echo url();?>" class="form-inline">
		<div class="col-md-12 pt10">
			<div class="mr20 form-group mt10">
				<label for="contact">站点:</label>
				<select class="form-control" name="site_id">
					<option value="">请选择站点</option>
					<?php foreach ($siteList as $key => $value){?>
					<option <?php echo $key == iget('site_id') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">支付类型:</label>
				<select class="form-control" name="type">
					<option value="">请选择类型</option>
					<?php foreach ($typeList as $key => $value){?>
					<option <?php echo $key == iget('type') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<label for="contact">支付账户:</label>
				<select class="form-control" name="payment_id">
					<option value="">请选择账户</option>
					<?php foreach ($paymentList as $key => $value){?>
					<option <?php echo $key == iget('payment_id') ? 'selected' : '';?> value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
			<div class="form-group mt10 right">
				<button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增账户使用</button>
			</div>
		</div>
		<div class="clear"></div>
	</form>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th class="col-md-1">ID</th>
				<th class="col-md-1">站点</th>
				<th class="col-md-1">支付账号</th>
				<th class="col-md-1">支付类型</th>
				<th class="col-md-1">添加时间</th>
				<th class="col-md-2">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="10">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value) { ?>
			<tr data-id="<?php echo $value['item_id'];?>">
				<td><?php echo $value['item_id'];?></td>
				<td><?php echo $siteList[$value['site_id']] ?? '';?></td>
				<td><?php echo $paymentList[$value['payment_id']] ?? '';?></td>
				<td><?php echo $typeList[$value['type']] ?? '';?></td>
				<td><?php echo $value['add_time'];?></td>
				<td>
					<button class="btn btn-primary btn-xs modify mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
					<button class="btn btn-danger btn-xs delete mt2" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="item_id" value="0">
			<input type="hidden" name="opn" value="editUsedInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">新增使用</div>
			<div class="input-group">
				<div class="input-group-addon"><span>站点</span></div>
				<select class="form-control" name="site_id" required="required">
					<option value="">请选择站点</option>
					<?php foreach ($siteList as $key => $value){?>
					<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>支付类型</span></div>
				<select class="form-control" name="type" required="required">
					<option value="">请选择类型</option>
					<?php foreach ($typeList as $key => $value){?>
					<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>支付账户</span></div>
				<select class="form-control" name="payment_id" required="required">
					<option value="">请选择账户</option>
					<?php foreach ($paymentList as $key => $value){?>
					<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php }?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save">确认</button>
		</form>
	</div>
</div>