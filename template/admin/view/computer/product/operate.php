<div class="container-fluid" id="add-product-page">
	<div class="left" style="width:calc(100% - 400px);padding-right: 15px;">
		<form action="<?php echo url("product/operate");?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="opn" value="add">
			<dl class="field-row">
		        <dt class="title-line"><strong class="must">*</strong>产品分类：</dt>
		        <dd class="subclass">
		        	<div class="title-line" style="display: inline-block;"></div>
	                <button type="button" class="btn btn-primary btn-xs">修改分类</button>
		        </dd>
		    </dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品名称：</dt>
	            <dd>
	                <input type="text" class="form-control" name="spu_name" value="<?php echo $info['name'] ?? '';?>">
	            </dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品数据：</dt>
	            <dd style="margin-right: 10px;">
                    <div class="input-group input-product">
                        <span class="input-group-addon">重量</span>
                        <input type="text" class="form-control isNum" name="spu_weight" value="<?php echo $info['weight']??'';?>">
                        <span class="input-group-addon">克</span>
                    </div>
                </dd>
                <dd style="margin-right: 10px;">
                    <div class="input-group input-product">
                        <span class="input-group-addon">长度</span>
                        <input type="text" class="form-control isNum" name="spu_length" value="<?php echo $info['length']??'';?>">
                        <span class="input-group-addon">厘米</span>
                    </div>
                </dd>
                <dd style="margin-right: 10px;">
                    <div class="input-group input-product">
                        <span class="input-group-addon">宽度</span>
                        <input type="text" class="form-control isNum" name="spu_width" value="<?php echo $info['width']??'';?>">
                        <span class="input-group-addon">厘米</span>
                    </div>
                </dd>
                <dd>
                    <div class="input-group input-product">
                        <span class="input-group-addon">高度</span>
                        <input type="text" class="form-control isNum" name="spu_height" value="<?php echo $info['height']??'';?>">
                        <span class="input-group-addon">厘米</span>
                    </div>
                </dd>
			</dl>
			<dl class="field-row">
                <dt class="title-line">产品数据：</dt>
                <dd>
                	<div class="left" style="width: 100px;padding-left: 10px;">
                		<ul>
                			<li data-image_type_id="2520" class="hy-sl-txt hy-sl-image_type_id hy-sl-selected">
                				<div class="hy-sl-set"><a href="javascript:void(0);" title="ID: 2520">厂商橱窗</a><i class="hy-sl-check"></i></div>
                				<div class="image-num">5</div></li><li data-image_type_id="2520" class="hy-sl-txt hy-sl-image_type_id hy-sl-selected"><div class="hy-sl-set"><a href="javascript:void(0);" title="ID: 2520">厂商橱窗</a><i class="hy-sl-check"></i></div><div class="image-num">5</div></li>
                		</ul>
                	</div>
                	<div class="right spu-image" style="width: calc(100% - 100px);">
                		<div class="pic-wrap">
                			<?php foreach ($info['pdt_picture'] as $key=>$value){?>
                			<div class="item">
                				<div class="pic-thumb">
                					<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value;?>" alt="" class="lazyload">
                				</div>
                				<div class="image-left-tips">
                					<span class="image-selected">
                						<input type="checkbox">
                					</span>
                					<?php if ($key == 0) {?>
                					<div class="spu-sign">SPU</div>
                					<?php }?>
                				</div>
                				<div class="num"><?php echo $key+1;?></div>
                				<div class="image-bottom-tips">
                                    <button class="btn btn-xs btn-primary set-spu-cover" type="button">SPU</button>
                                    <button class="btn btn-xs btn-success set-sku-cover" type="button">SKU</button>
                                    <button class="btn btn-xs btn-default view-large-image" type="button">查看</button>
                                </div>
                			</div>
                			<?php }?>
                		</div>
                	</div>
                </dd>
            </dl>
		</form>
		
	</div>
	<div class="right" style="width: 400px;">
		<div class="supplier-info-content">
			<h4>供应商基本信息</h4>
			<dl class="field-row">
				<dt class="title-line">名称：</dt>
				<dd>
					<a href="https://<?php echo $shopInfo['url'];?>" target="_blank"><?php echo $shopInfo['name'];?></a>
					<a href="https://<?php echo $shopInfo['url'];?>" target="_blank" class="glyphicon glyphicon-link"></a>
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line">网址：</dt>
				<dd>
					<a href="https://<?php echo $shopInfo['url'];?>" target="_blank"><?php echo $shopInfo['url'];?></a>
					<a href="https://<?php echo $shopInfo['url'];?>" target="_blank" class="glyphicon glyphicon-link"></a>
				</dd>
			</dl>
			<table class="table table-bordered">
				<?php foreach ($info['attr'] as $ak=>$av){?>
				<tr>
					<td width="100">
						<div class="attr-item <?php echo isset($attrNs[$av['name']])?'success':'error';?>">
							<span><?php echo $av['name'];?></span>
							<span class="glyphicon glyphicon-edit"></span>
							<div class="mapping-popper">
								<div class="content">
									<input class="form-control" type="text" name="name" placeholder="请输入映射值">
									<button class="btn btn-primary btn-sm">保存</button>
								</div>
								<i class="glyphicon glyphicon-remove"></i>
							</div>
						</div>
					</td>
					<td>
						<?php foreach ($av['value'] as $avk=>$avv){?>
						<div class="attr-item <?php echo isset($attrVs[$avv['name']])?'success':'error';?>">
							<span><?php echo $avv['name'];?></span>
							<span class="glyphicon glyphicon-edit"></span>
						</div>
						<?php }?>
					</td>
				</tr>
				<?php }?>
			</table>
		</div>
	</div>
</div>