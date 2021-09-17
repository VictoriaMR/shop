<?php $this->load('common/order_header');?>
<div id="order-page">
	<div class="nav-list bg-f5">
		<ul>
			<li>
				<a <?php if (!$status){ echo 'class="active"';}?> href="<?php echo url('order');?>"><?php echo appT('all');?></a>
			</li>
			<li>
				<a <?php if ($status == 1){ echo 'class="active"';}?> href="<?php echo url('order', ['status'=>1]);?>"><?php echo appT('pending');?></a>
			</li>
			<li>
				<a <?php if ($status == 2){ echo 'class="active"';}?> href="<?php echo url('order', ['status'=>2]);?>"><?php echo appT('processing');?></a>
			</li>
			<li>
				<a <?php if ($status == 3){ echo 'class="active"';}?> href="<?php echo url('order', ['status'=>3]);?>"><?php echo appT('shipped');?></a>
			</li>
			<li>
				<a <?php if ($status == 4){ echo 'class="active"';}?> href="<?php echo url('order', ['status'=>4]);?>"><?php echo appT('review');?></a>
			</li>
			<li>
				<a <?php if ($status == 5){ echo 'class="active"';}?> href="<?php echo url('order', ['status'=>5]);?>"><?php echo appT('refund');?></a>
			</li>
		</ul>
	</div>
	<div class="p20"></div>
</div>