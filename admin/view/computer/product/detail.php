<div class="detail-page" data-id="<?php echo $info['spu_id'];?>">
	<?php if (empty($info)) {?>
	<p class="cred tc">找不到产品</p>
	<?php } else { ?>
	<dl class="field-row">
		<dt>产品PID：</dt>
		<dd class="red"><?php echo $info['spu_id'];?></dd>
		<dd>
			<dl class="in-field-row">
				<dt>状态：</dt>
				<dd class="<?php echo $info['status'] == 1 ? 'green' : 'red';?> j-show-spu-status" data-status="<?php echo $info['status'];?>"><?php echo $statusList[$info['status']];?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs status-btn">修改</button>
				</dd>
			</dl>
		</dd>
		<dd>
			<dl class="in-field-row">
				<dt>免运费：</dt>
				<dd>
					<div class="switch_botton free-ship" data-status="<?php echo $info['free_ship'];?>">
						<div class="switch_status <?php echo $info['free_ship'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</dd>
			</dl>
		</dd>
		<dd>
			<dl class="in-field-row">
				<dt>记录时间：</dt>
				<dd><?php echo $info['add_time'];?></dd>
			</dl>
		</dd>
	</dl>
	<dl class="field-row">
		<dt>产品分类：</dt>
		<dd>
			<dd><?php echo implode(' - ', array_column($cateInfo, 'name'));?></dd>
			<dd>
				<button type="button" class="btn btn-primary btn-xs category-btn">修改</button>
			</dd>
		</dd>
	</dl>
	<dl class="field-row">
		<dt>产品名称：</dt>
		<dd>
			<dd>
				<span class="glyphicon glyphicon-globe"></span>
				<span class="name"><?php echo $info['name'];?></span>
			</dd>
			<dd>
				<button type="button" class="btn btn-primary btn-xs category-btn">修改</button>
			</dd>
		</dd>
	</dl>
	<?php } ?>
</div>
<div id="status-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	    	<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
	    	<input type="hidden" name="opn" value="editInfo">
	    	<input type="hidden" name="is_ajax" value="1">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">产品状态</div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>名称</span></div>
	            <select class="form-control" name="status">
	            	<?php foreach($statusList as $key => $value) {?>
	            	<option value="<?php echo $key;?>" <?php echo $key == $info['status'] ? 'selected' : '';?>><?php echo $value;?></option>
	            	<?php } ?>
	            </select>
	        </div>
	        <button type="button" class="btn btn-primary btn-lg btn-block save">确认</button>
	    </form>
	</div>
</div>
<div id="category-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	    	<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
	    	<input type="hidden" name="opn" value="editInfo">
	    	<input type="hidden" name="is_ajax" value="1">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">产品状态</div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>分类</span></div>
	            <select class="form-control" name="cate_id">
	            	<?php foreach($cateList as $key => $value) {?>
	            	<option value="<?php echo $value['cate_id'];?>" <?php echo $value['cate_id'] == $info['cate_id'] ? 'selected' : '';?>><?php for($i=1;$i<=$value['level'];$i++){echo '--';}?><?php echo $value['name'];?></option>
	            	<?php } ?>
	            </select>
	        </div>
	        <button type="button" class="btn btn-primary btn-lg btn-block save">确认</button>
	    </form>
	</div>
</div>