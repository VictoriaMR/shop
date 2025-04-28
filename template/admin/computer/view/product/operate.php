<?php if (!empty($info)){?>
<div class="container-fluid" id="add-product-page">
	<form action="<?php echo url("product/operate");?>" method="post" enctype="multipart/form-data">
		<div class="left product-info-content">
			<input type="hidden" name="opn" value="addProduct">
			<input type="hidden" name="id" value="<?php echo iget('id/d', 0);?>">
			<input type="hidden" name="cate_id" value="0">
			<input type="hidden" name="site_id" value="0">
			<input type="hidden" name="spu_image" value="<?php echo current($info['pdt_picture'] ?? []);?>">
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>站点：</dt>
				<dd class="item">
					<div class="title-line site-name" style="display: inline-block;"></div>
					<button type="button" class="btn btn-primary btn-xs change-site-btn">修改站点</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品分类：</dt>
				<dd class="item">
					<?php if (!empty($info['cate_id'])){
						$cateArr = category()->pCate($info['cate_id'], true, true);
					}?>
					<div class="title-line category-name" style="display: inline-block;"><?php echo implode(' - ', empty($cateArr)?[]:array_column($cateArr, 'name'));?></div>
					<button type="button" class="btn btn-primary btn-xs change-category-btn">修改分类</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品名称：</dt>
				<dd>
					<input type="text" class="form-control" name="spu_name" value="<?php echo $info['name'];?>">
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>邮费：</dt>
				<dd>
					<input type="text" class="form-control" name="post_fee" value="<?php echo $info['post_fee'];?>">
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品图片：</dt>
				<dd>
					<div class="left" style="width: 100px;padding-right: 10px;">
						<ul class="hy-sl-list-inline hy-sl-vertical">
							<li class="hy-sl-txt hy-sl-selected" data-id="2500">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">橱窗</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num"><?php echo count($info['pdt_picture'] ?? []);?></div>
							</li>
							<li class="hy-sl-txt" data-id="2510">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">素材</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num"><?php echo count($info['desc_picture'] ?? []);?></div>
							</li>
							<?php if (!empty($info['video'])){?>
							<li class="hy-sl-txt" data-id="2520">
								<div class="hy-sl-set">
									<a href="javascript:void(0);">视频</a>
									<i class="hy-sl-check"></i>
								</div>
								<div class="image-num"><?php echo empty($info['video']) ? 0 : 1;?></div>
							</li>
							<?php }?>
						</ul>
					</div>
					<div class="right spu-image" style="width: calc(100% - 100px);">
						<div class="pic-wrap" data-id="2500">
							<?php foreach ($info['pdt_picture'] ?? [] as $key=>$value){?>
							<div class="item">
								<div class="pic-thumb">
									<img draggable="true" src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value;?>" alt="" class="lazyload">
									<div class="cover"></div>
								</div>
								<div class="image-left-tips">
									<div class="bigImage" src="<?php echo $value;?>">大</div>
									<span class="image-selected">
										<input type="checkbox" checked name="main_img[]" value="<?php echo $value;?>">
									</span>
									<?php if ($key == 0) {?>
									<div class="spu-sign">SPU</div>
									<?php }?>
								</div>
								<div class="num"><?php echo $key+1;?></div>
								<div class="image-bottom-tips">
									<button class="btn btn-xs btn-primary set-spu-cover" type="button">SPU</button>
									<button class="btn btn-xs btn-success set-sku-cover" type="button">SKU</button>
								</div>
							</div>
							<?php }?>
						</div>
						<div class="pic-wrap" data-id="2510" style="display: none;">
							<?php foreach ($info['desc_picture'] ?? [] as $key=>$value){?>
							<div class="item">
								<div class="pic-thumb">
									<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value;?>" alt="" class="lazyload">
									<div class="cover"></div>
								</div>
								<div class="image-left-tips">
									<div class="bigImage" src="<?php echo $value;?>">大</div>
									<span class="image-selected">
										<input type="checkbox" name="desc_img[]" checked value="<?php echo $value;?>">
									</span>
								</div>
								<div class="num"><?php echo $key+1;?></div>
								<div class="image-bottom-tips">
									<button class="btn btn-xs btn-primary set-spu-cover" type="button">SPU</button>
									<button class="btn btn-xs btn-success set-sku-cover" type="button">SKU</button>
								</div>
							</div>
							<?php }?>
						</div>
						<div class="pic-wrap" data-id="2520" style="display: none;">
							<?php if (!empty($info['video'])){?>
							<div class="video-item">
								<video controls width="250" poster="<?php echo $info['video']['img'] ?? '';?>">
								 	<source src="<?php echo $info['video']['url'];?>" type="video/webm" />
								</video>
								<div class="image-left-tips">
									<span class="image-selected">
										<input type="checkbox" name="video[url]" checked value="<?php echo $info['video']['url'];?>" style="margin-right: 10px;" title="视频">
										<input type="checkbox" name="video[img]" <?php echo empty($info['video']['img'])?'':'checked';?> value="<?php echo $info['video']['img'] ?? '';?>" title="封面图">
									</span>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</dd>
			</dl>
			<dl class="field-row">
				<dt class="title-line"><strong class="must">*</strong>产品SKU：</dt>
				<dd>
					<table class="table table-bordered" id="sku-list">
						<thead>
							<tr>
								<th width="60">封面图</th>
								<th width="70">采购价</th>
								<th width="150">供应商SKU</th>
								<th width="150">映射属性</th>
								<th width="60">库存</th>
								<th width="60" class="batch-btn" data-type="unit">
									<span title="双击批量修改">单位</span>
								</th>
								<th width="60" class="batch-btn" data-type="weight">
									<span title="双击批量修改">重量(g)</span>
								</th>
								<th width="60" class="batch-btn" data-type="length">
									<span title="双击批量修改">长度(cm)</span>
								</th>
								<th width="60" class="batch-btn" data-type="width">
									<span title="双击批量修改">宽度(cm)</span>
								</th>
								<th width="60" class="batch-btn" data-type="hight">
									<span title="双击批量修改">高度(cm)</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($info['sku'] as $key=>$value){
								$check=true;
								foreach ($value['pvs'] as $pk=>$pv) {
									if (!isset($attrNs[$pk])){
										$check = false;
										break;
									}
									if (!isset($attrVs[$pv])) {
										$check = false;
										break;
									}
								}
							?>
							<tr data-sku="<?php echo $key;?>">
								<td class="<?php echo $check?'check':'';?>">
									<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['img'];?>" alt="" class="lazyload bigImage">
									<input type="hidden" class="form-control img" name="sku[<?php echo $key;?>][img]" value="<?php echo $value['img'];?>">
								</td>
								<td><input type="text" class="form-control price" name="sku[<?php echo $key;?>][price]" value="<?php echo $value['price'];?>"></td>
								<td>
									<div title="SKU属性">
										<?php foreach($value['pvs'] as $pk=>$pv) {?>
										<p><?php echo $pk.': '.$pv;?></p>
										<?php }?>
									</div>
									<p title="SKUID"><?php echo $key;?></p>
								</td>
								<td>
									<?php foreach ($value['pvs'] as $pk=>$pv) {?>
									<p>
										<span class="attr-name <?php echo isset($attrNs[$pk])?'success':'error';?>" data-name="<?php echo $pk;?>"><?php echo isset($attrNs[$pk])?$attrNs[$pk]['name']:$pk;?></span>
										<span>: </span>
										<span class="attr-value <?php echo isset($attrVs[$pv])?'success':'error';?>" data-name="<?php echo $pv;?>"><?php echo isset($attrVs[$pv])?$attrVs[$pv]['name']:$pv;?></span>
										<input type="hidden" name="sku[<?php echo $key;?>][attr][<?php echo $pk;?>]" value="<?php echo $pv;?>">
									</p>
									<?php if (!empty(isset($attrVs[$pv]['ext']))){?>
									<?php foreach($attrVs[$pv]['ext'] as $ek=>$ev){?>
									<p class="attr-map">
										<span class="attr-name success" data-name="<?php echo $ek;?>"><?php echo $ek;?></span>
										<span>: </span>
										<span class="attr-value success" data-name="<?php echo $ev;?>"><?php echo $ev;?></span>
										<input type="hidden" name="sku[<?php echo $key;?>][attr][<?php echo $ek;?>]" value="<?php echo $ev;?>">
									</p>
									<?php }?>
									<?php }?>
									<?php }?>
								</td>
								<td>
									<input type="text" class="form-control stock" name="sku[<?php echo $key;?>][stock]" value="<?php echo $value['stock'];?>">
								</td>
								<td>
									<select name="type" class="form-control unit" name="sku[<?php echo $key;?>][unit]">
										<option value="0" selected>--</option>
										<option value="1">件</option>
										<option value="2">个</option>
										<option value="3">套</option>
										<option value="4">打</option>
										<option value="5">箱</option>
									</select>
								</td>
								<td>
									<input type="text" class="form-control weight" name="sku[<?php echo $key;?>][weight]" placeholder="重量(g)" value="<?php echo $info['weight'] ?? '';?>">
								</td>
								<?php $tmpArr = purchase()->spu()->getVolume($info['volume'] ?? '');?>
								<td>
									<input type="text" class="form-control length" name="sku[<?php echo $key;?>][length]" value="<?php echo $tmpArr['length'] ?: '';?>" placeholder="长度(cm)">
								</td>
								<td>
									<input type="text" class="form-control width" name="sku[<?php echo $key;?>][width]" value="<?php echo $tmpArr['width'] ?: '';?>" placeholder="宽度(cm)">
								</td>
								<td>
									<input type="text" class="form-control hight" name="sku[<?php echo $key;?>][hight]" value="<?php echo $tmpArr['height'] ?: '';?>" placeholder="高度(cm)">
								</td>
							</tr>
							<?php }?>
						</tbody>
					</table>
				</dd>
			</dl>
			<div class="clear"></div>
			<dl class="field-row">
				<dt></dt>
				<dd>
					<button type="button" class="btn btn-success confirm-btn"><i class="glyphicon glyphicon-plus"></i> 确认</button>
				</dd>
			</dl>
		</div>
		<div class="left info-content">
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
				<dl class="field-row">
					<dt class="title-line">货号：</dt>
					<dd>
						<a href="<?php echo $info['url'];?>" target="_blank"><?php echo $info['channel_id'].' - '.$info['item_id'];?></a>
						<a href="<?php echo $info['url'];?>" target="_blank" class="glyphicon glyphicon-link"></a>
					</dd>
				</dl>
			</div>
			<div class="attr-info-content mt12">
				<h4>属性映射</h4>
				<table class="table table-bordered">
					<tr class="attr-rule">
						<td width="100">规则: </td>
						<td><input type="text" name="attr_rule_value" class="form-control" placeholder="输入分割字符串"></td>
					</tr>
					<tr class="attr-rule">
						<td>属性
							<button type="button" class="btn btn-primary btn-xs attr-rule-btn">生成</button>
						</td>
						<td class="attr-rule-name">
							<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性1">
							<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性2">
							<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性3">
							<input type="text" class="form-control" name="attr_rule_name[]" placeholder="属性4">
						</td>
					</tr>
					<?php foreach ($info['attr'] as $ak=>$av){?>
					<tr>
						<td>
							<div class="attr-item attr-name <?php echo isset($attrNs[$av['name']])?'success':'error';?>" data-name="<?php echo $av['name'];?>">
								<span><?php echo $av['name'];?></span>
								<span class="glyphicon glyphicon-edit"></span>
							</div>
						</td>
						<td>
							<?php foreach ($av['value'] as $avk=>$avv){?>
							<div class="attr-item attr-value <?php echo isset($attrVs[$avv['name']])?'success':'error';?>" data-name="<?php echo $avv['name'];?>" data-ext='<?php echo isset($attrVs[$avv['name']]['ext']) ? json_encode($attrVs[$avv['name']]['ext']) : '';?>'>
								<span><?php echo $avv['name'];?></span>
								<span class="glyphicon glyphicon-edit"></span>
							</div>
							<?php }?>
						</td>
					</tr>
					<?php }?>
				</table>
			</div>
			<div class="desc-info-content mt12">
				<h4>描述&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-success btn-add-desc">添加描述</button></h4>
				<div class="content">
					<?php foreach($info['detail'] as $value){?>
					<div class="item">
						<input type="text" class="form-control" name="desc[name][]" value="<?php echo $value['name'];?>">
						<span>: </span>
						<input type="text" class="form-control" name="desc[value][]" value="<?php echo $value['value'];?>">
						<i class="glyphicon glyphicon-remove"></i>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- 分类选择 -->
<div class="s-modal change-category-modal">
	<p class="title"><span>分类修改</span></p>
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content">
		<div class="form-horizontal">
			<div class="input-group">
				<div class="input-group-addon"><span>品类：</span></div>
				<select name="root_category_id" class="form-control">
					<option value="0">请选择品类</option>
					<?php foreach($cateList as $value){
						if ($value['parent_id'] == 0){?>
					<option value="<?php echo $value['cate_id'];?>"><?php echo $value['name'];?></option>
					<?php }} ?>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>分类：</span></div>
				<select name="category_id" class="form-control">
					<option value="0">请先选择品类</option>
				</select>
			</div>
		</div>
		<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
	</div>
</div>
<!-- 站点选择 -->
<div class="s-modal change-site-modal">
	<p class="title"><span>站点修改</span></p>
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content">
		<div class="form-horizontal">
			<div class="input-group">
				<div class="input-group-addon"><span>站点：</span></div>
				<select name="site_id" class="form-control">
					<option value="0">请选站点</option>
					<?php foreach($siteList as $value){?>
					<option value="<?php echo $value['site_id'];?>"><?php echo $value['name'];?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
	</div>
</div>
<!-- 属性映射弹窗 -->
<form class="map-modal s-modal">
	<p class="title"></p>
	<input type="hidden" name="from_name">
	<input type="hidden" name="type">
	<input type="hidden" name="opn" value="attrMap">
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content"></div>
</form>
<!-- 批量单位赋值 -->
<div class="batch-edit-modal s-modal">
	<p class="title"></p>
	<input type="hidden" name="type" value="">
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content"></div>
	<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
</div>
<!-- sku选择 -->
<div class="s-modal sku-modal">
	<p class="title" style="margin-bottom: 5px;"><span>SKU主图设置</span></p>
	<i class="glyphicon glyphicon-remove"></i>
	<div class="content">
		<div class="form-horizontal" style="max-height: calc(100vh - 200px);overflow-y: auto;">
			<?php foreach($info['sku'] as $key=>$value){
				$arr = [];
				foreach($value['pvs'] as $pk=>$pv) {
					$arr[] = $pk.': '.$pv;
				}
			?>
			<div class="checkbox">
			    <label>
			    	<input type="checkbox" data-sku="<?php echo $key;?>"> <?php echo implode('<br />', $arr);?>
			    </label>
			 </div>
			<?php }?>
		</div>
		<button type="button" class="mt20 btn btn-primary btn-lg btn-block btn-save">保存</button>
	</div>
</div>
<script>
const category = <?php echo json_encode($cateList, JSON_UNESCAPED_UNICODE);?>;
</script>
<?php }?>