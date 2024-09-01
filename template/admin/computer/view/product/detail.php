<div class="container-fluid detail-page" data-id="<?php echo $info['spu_id'];?>">
	<?php if (empty($info)) {?>
	<p class="cred tc">找不到产品</p>
	<?php } else { ?>
	<div class="field-row w100">
		<div class="w50">
			<dl class="field-row">
				<dt>产品PID：</dt>
				<dd class="red"><?php echo $info['spu_id'];?></dd>
			</dl>
			<dl class="field-row">
				<dt>状态：</dt>
				<dd class="<?php echo $info['status'] == 1 ? 'green' : 'red';?> j-show-spu-status" data-status="<?php echo $info['status'];?>"><?php echo $statusList[$info['status']];?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs status-btn">修改</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>免运费：</dt>
				<dd>
					<div class="switch_botton free-ship" data-status="<?php echo $info['free_ship'];?>">
						<div class="switch_status <?php echo $info['free_ship'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>产品分类：</dt>
				<dd><?php echo implode(' - ', array_column($info['category'], 'name'));?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs category-btn">修改</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>邮费：</dt>
				<dd><?php echo $info['data']['post_fee'];?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs data-btn" data-name="post_fee">修改</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>体积：</dt>
				<dd><?php echo $info['data']['volume'];?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs data-btn" data-name="volume">修改</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>重量：</dt>
				<dd><?php echo $info['data']['weight'];?></dd>
				<dd>
					<button type="button" class="btn btn-primary btn-xs data-btn" data-name="weight">修改</button>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>产品名称：</dt>
				<dd>
					<span class="glyphicon glyphicon-globe name-trans-btn"></span>
					<span class="name f14 f600"><?php echo $info['name'];?></span>
				</dd>
			</dl>
			<dl class="field-row">
				<dt>操作时间：</dt>
				<dd>
					<span title="添加时间"><?php echo $info['add_time'];?></span>
					<span>&nbsp;-&nbsp;</span>
					<span title="修改时间"><?php echo $info['update_time'] ?: '--';?></span>
				</dd>
			</dl>
		</div>
		<div class="w50">
			<div class="pl10">
				<dl class="field-row">
					<dt>店铺ID：</dt>
					<dd><?php echo empty($info['data']['shop_id'])?'':$info['data']['shop_id'];?></dd>
				</dl>
				<dl class="field-row">
					<dt>店铺名称：</dt>
					<dd><?php echo empty($info['shop']['name'])?'':$info['shop']['name'];?></dd>
				</dl>
				<dl class="field-row">
					<dt>店铺链接：</dt>
					<dd>
						<a href="<?php echo empty($info['shop']['url'])?'javascript:;':$info['shop']['url'];?>" target="_blank"><?php echo empty($info['shop']['url'])?'':$info['shop']['url'];?> <span class="glyphicon glyphicon-link"></span></a>
					</dd>
				</dl>
				<dl class="field-row">
					<dt>供应商：</dt>
					<dd><?php echo empty($info['data']['supplier'])?'':$info['data']['supplier'];?></dd>
				</dl>
				<dl class="field-row">
					<dt>供应商SPU：</dt>
					<dd><?php echo $info['data']['item_id'];?></dd>
				</dl>
				<dl class="field-row">
					<dt>SPU链接：</dt>
					<dd>
						<a href="<?php echo $info['data']['item_url'];?>" target="_blank"><?php echo $info['data']['item_url'];?> <span class="glyphicon glyphicon-link"></span></a>
					</dd>
				</dl>
			</div>
		</div>
	</div>
	<dl class="field-row" style="margin-bottom: 4px">
		<dt>产品图片：</dt>
		<dd>
			<button class="btn btn-success btn-xs upload-image">上传图片</button>
		</dd>
	</dl>
	<dl>
		<?php foreach ($info['image'] as $key => $value) {?>
		<div class="spu-image" data-id="<?php echo $value['attach_id'];?>" data-item_id="<?php echo $value['item_id'];?>">
			<div class="image-tcell">
				<img src="<?php echo siteUrl('/image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
			</div>
			<div class="image-btn">
				<a class="btn btn-default btn-xs" target="_blank" href="<?php echo str_replace('/400', '', $value['image']);?>"><span class="glyphicon glyphicon-search"></span></a>
				<?php if ($value['attach_id'] != $info['attach_id']){?>
				<button class="btn btn-primary btn-xs spu-btn">SPU</button>
				<?php }?>
				<button class="btn btn-success btn-xs sku-btn">SKU</button>
				<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>" placeholder="排序">
				<span class="glyphicon glyphicon-trash"></span>
			</div>
		</div>
		<?php }?>
		<div class="clear"></div>
	</dl>
	<dl class="field-row mt10" style="margin-bottom: 4px">
		<dt>SKU列表 <span class="co f12">(售价、原价、成本、库存双击修改)</span>：</dt>
	</dl>
	<dl>
		<table class="table table-bordered table-hover" width="100%">
			<tbody>
				<tr>
					<th width="80">封面图</th>
					<th width="60">ID</th>
					<th width="180">
						<span title="SKU属性">规格</span>
					</th>
					<th width="90">状态</th>
					<th width="90">售价</th>
					<th width="90">成本价</th>
					<th width="70">库存</th>
					<th width="70">重量(g)</th>
					<th width="90">体积(mm)</th>
					<th width="60">供应商SKU</th>
					<th width="140">上架时间</th>
				</tr>
				<?php foreach($info['sku'] as $value){?>
				<tr data-id="<?php echo $value['sku_id'];?>">
					<td>
						<div class="sku-image">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
						</div>
					</td>
					<td><?php echo $value['sku_id'];?></td>
					<td>
						<?php foreach ($value['attr'] as $attrValue){?>
						<p><?php echo $attrValue['attr_name'].': '.$attrValue['attv_name'];?></p>
						<?php }?>
					</td>
					<td>
						<div class="switch_botton sku-status" data-status="<?php echo $value['status'];?>">
							<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
						</div>
					</td>
					<td class="can-edit" data-name="price"><?php echo $value['price'];?></td>
					<td class="can-edit" data-name="cost_price"><?php echo $value['cost_price'];?></td>
					<td class="can-edit" data-name="stock"><?php echo $value['stock'];?></td>
					<td class="can-edit" data-name="weight"><?php echo $value['weight'];?></td>
					<td class="can-edit" data-name="volume"><?php echo $value['volume'];?></td>
					<td><?php echo $value['item_id'];?></td>
					<td>
						<?php echo $value['add_time'];?><br />
						<?php echo $value['update_time'] ? $value['update_time'] : '-';?>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</dl>
	<dl class="field-row mt10">
		<div class="w50">
			<div class="pr10">
				<dl class="field-row mt10" style="margin-bottom: 4px">
					<dt>属性列表	<span class="co f12">(修改图片, 快捷属性映射)</span>：</dt>
				</dl>
				<table class="table table-bordered" width="50%">
					<tbody>
						<tr>
							<th width="50">属性名</th>
							<th width="50">属性值</th>
							<th width="20">图片</th>
							<th width="50">操作</th>
						</tr>
						<?php foreach($info['attr_map'] as $kv){
							$count = 0;
							foreach ($kv['son'] as $value){
						?>
						<tr data-attrn_id="<?php echo $kv['attrn_id'];?>" data-attrv_id="<?php echo $value['attrv_id'];?>" data-spu_id="<?php echo $info['spu_id'];?>">
							<?php if ($count == 0){?>
							<td rowspan="<?php echo count($kv['son']);?>" class="tc">
								<?php echo $kv['attr_name'];?>
							</td>
							<?php }?>
							<td><?php echo $value['attv_name'];?></td>
							<td>
								<div class="sku-attr-image">
									<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
								</div>
								<?php if (!empty($value['image'])){?>
								<button class="btn btn-danger btn-xs delete-attr-image-btn">删除</button>
								<?php }?>
							</td>
							<td><button type="button" class="btn btn-primary btn-xs">映射</button></td>
						</tr>
						<?php $count++;}}?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="w50">
			<div class="pr10" id="sku-desc-content">
				<dl class="field-row mt10" style="margin-bottom: 4px">
					<dt>描述列表	<span class="co f12">(修改)</span>：<button class="btn btn-success btn-xs add-desc-btn">增加</button> <button class="btn btn-info btn-xs desc-group-btn">分组</button></dt>
				</dl>
				<table class="table table-bordered" width="50%">
					<thead>
						<tr>
							<th width="10">选择</th>
							<th width="40">描述名</th>
							<th width="50">描述值</th>
							<th width="10">排序</th>
							<th width="20">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($info['desc'] as $item){?>
						<tr>
							<td colspan="5"><?php echo $item['group']?:'默认分组';?></td>
						</tr>
						<?php foreach($item['list'] as $value){?>
						<tr data-id="<?php echo $value['item_id'];?>">
							<td class="tc f20">
								<span class="glyphicon glyphicon-ok-circle"></span>
							</td>
							<td><?php echo $value['name'];?></td>
							<td><?php echo $value['value'];?></td>
							<td>
								<input type="text" name="sort" value="<?php echo $value['sort'];?>" class="form-control">
							</td>
							<td>
								<button class="btn btn-primary btn-xs edit-desc-btn"><span class="glyphicon glyphicon-edit"></span> 编辑</button>
								<button class="btn btn-danger btn-xs delete-desc-btn"><span class="glyphicon glyphicon-trash"></span> 删除</button>
							</td>
						</tr>
						<?php }?>
						<?php }?>
					</tbody>
				</table>
			</div>
		</div>
	</dl>
	<dl class="field-row mt10" style="margin-bottom: 4px">
		<dt>描述图片：</dt>
		<dd>
			<button class="btn btn-success btn-xs upload-introduce-image">上传图片</button>
		</dd>
	</dl>
	<dl>
		<?php foreach ($info['introduce'] as $key => $value) {?>
		<div class="spu-introduce-image" data-id="<?php echo $value['attach_id'];?>">
			<div class="image-tcell">
				<img src="<?php echo siteUrl('/image/common/noimg.svg');?>" data-src="<?php echo $value['image'];?>" class="lazyload">
			</div>
			<div class="image-btn" data-id="<?php echo $value['item_id'];?>">
				<a class="btn btn-default btn-xs" target="_blank" href="<?php echo str_replace('/400', '', $value['image']);?>"><span class="glyphicon glyphicon-search"></span></a>
				<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>" placeholder="排序">
				<span class="glyphicon glyphicon-trash delete-introduce-btn"></span>
			</div>
		</div>
		<?php }?>
		<div class="clear"></div>
	</dl>
	<?php } ?>
</div>
<!-- 状态弹窗 -->
<div id="status-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	    	<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
	    	<input type="hidden" name="opn" value="editInfo">
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
	        <button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认</button>
	    </form>
	</div>
</div>
<!-- 分类弹窗 -->
<div id="category-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">产品分类</div>
			<div class="input-group">
				<div class="input-group-addon"><span>分类</span></div>
				<select class="form-control" name="cate_id">
					<?php foreach ($siteCate as $k=>$v){
						$paddingStr = '';
						for ($i=0; $i < $v['level']; $i++) { 
							$paddingStr .= '&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					?>
					<option value="<?php echo $v['cate_id'];?>" <?php echo $info['cate_id']==$v['cate_id']?'selected':'';?> <?php if(($siteCate[$k+1]['level'] ?? 0) > $v['level']){ echo 'disabled="disabled"';}?>><?php echo $paddingStr.$v['name'];?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认</button>
		</form>
	</div>
</div>
<!-- 性别弹窗 -->
<div id="gender-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">产品性别属性</div>
			<div class="input-group">
				<div class="input-group-addon"><span>性别</span></div>
				<select class="form-control" name="gender">
					<option value="0">默认</option>
					<option value="1">男</option>
					<option value="2">女</option>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认</button>
		</form>
	</div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 600px">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">多语言配置</div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="editSpuLanguage">
			<table class="table table-bordered table-hover">
				<tbody></tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>
<!-- SKU主图管理 -->
<div id="dealbox-sku-image" class="hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 500px">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">SKU主图配置</div>
			<input type="hidden" name="opn" value="editSkuInfo">
			<input type="hidden" name="attach_id" value="0">
			<table class="table table-hover">
				<tbody>
					<tr>
						<th>操作</th>
						<th>SKUID</th>
						<th>属性</th>
					</tr>
					<?php foreach($info['sku'] as $value){?>
					<tr class="item" data-id="<?php echo $value['attach_id'];?>">
						<td>
							<input type="checkbox" name="sku_id[]" value="<?php echo $value['sku_id'];?>">
						</td>	
						<td>
							<?php echo $value['sku_id'];?>
						</td>
						<td>
							<?php echo implode(' ', array_column($value['attr'], 'attv_name'));?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>
<!-- SKU属性管理 -->
<div id="dealbox-sku-info" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">SKU管理</div>
			<input type="hidden" name="opn" value="editSkuInfo">
			<input type="hidden" name="sku_id" value="0">
			<input type="text" name="" value="" class="form-control name">
			<div class="mt20">
				<button type="button" class="btn btn-success w30 save-btn batch-save-btn">批量修改</button>
				<button type="button" class="btn btn-primary w30 right save-btn ">确认</button>
			</div>
		</form>
	</div>
</div>
<!-- SPU描述文本管理 -->
<div id="dealbox-desc" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">描述文本</div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="item_id" value="0">
			<input type="hidden" name="opn" value="modifySpuDesc">
			<div class="input-group">
				<div class="input-group-addon"><span>描述名: </span></div>
				<textarea name="name" class="form-control"></textarea>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>描述值: </span></div>
				<textarea name="value" class="form-control"></textarea>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>排序: </span></div>
				<input type="text" name="sort" class="form-control">
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>
<!-- SPU扩展数据管理 -->
<div id="dealbox-data" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title"></div>
			<input type="hidden" name="spu_id" value="<?php echo $info['spu_id'];?>">
			<input type="hidden" name="name" value="">
			<input type="hidden" name="opn" value="modifySpuData">
			<div class="input-group">
				<div class="input-group-addon"><span></span></div>
				<input type="input" name="value" class="form-control" />
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>
<!-- 性别弹窗 -->
<div id="desc-group-dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="opn" value="editDescGroupInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">描述分组</div>
			<div class="input-group">
				<div class="input-group-addon"><span>描述分组</span></div>
				<select class="form-control" name="group_id">
					<option value="0">默认</option>
					<?php foreach ($groupList as $value){?>
					<option value="<?php echo $value['descg_id'];?>"><?php echo $value['name'];?></option>
					<?php }?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn">确认</button>
		</form>
	</div>
</div>
<!-- 映射弹窗 -->