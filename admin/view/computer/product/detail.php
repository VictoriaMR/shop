<div class="detail-page">
	<dl class="field-row">
		<dt>产品PID：</dt>
		<dd class="red"><?php echo $info['spu_id'];?></dd>
		<dd>
			<dl class="in-field-row">
				<dt>状态：</dt>
				<dd class="red j-show-spu-status" data-status="0">已下架</dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs j-edit-btn j-spu-status">修改</button>
				</dd>
			</dl>
		</dd>
		<dd>
			<dl class="in-field-row">
				<dt>免运费：</dt>
				<dd>
					<button type="button" class="btn btn-primary btn-xs j-edit-btn j-spu-free-shipping">修改</button>
				</dd>
			</dl>
		</dd>
		<dd>
			<button class="btn btn-default btn-xs" id="show_product_detail">产品描述</button>
		</dd>
	</dl>
</div>