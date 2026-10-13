<div class="container-fluid" id="category-page">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-inline">
				<div class="form-group" style="margin-bottom: 10px;">
					<span class="text-muted" style="line-height: 34px;">
						一级品类列表（共 <strong><?php echo count($list);?></strong> 个品类）
					</span>
				</div>
				<div class="form-group right" style="margin-bottom: 10px;">
					<button class="btn btn-success" id="add-data-btn" type="button"><i class="fa fa-plus-circle"></i> 新增品类</button>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="70">ID</th>
				<th width="260">品类名称</th>
				<th width="140">子类目数</th>
				<th width="100">状态</th>
				<th width="260">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="5">
					<div class="tc orange">
						<i class="fa fa-info-circle"></i> 暂无品类数据
					</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $key => $value) { ?>
			<tr class="item" data-id="<?php echo $value['cate_id'];?>">
				<td><strong><?php echo $value['cate_id'];?></strong></td>
				<td>
					<strong style="color: #0f172a; font-size: 13px;" class="cate_name"><?php echo htmlspecialchars($value['name']);?></strong>
					<?php if (!empty($value['name_en'])){ ?>
					<span class="text-muted f12" style="display:block;"><?php echo htmlspecialchars($value['name_en']);?></span>
					<?php } ?>
				</td>
				<td>
					<a href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>" class="label label-info" style="font-size: 12px; font-weight: normal; padding: 3px 8px; text-decoration: none;" title="点击进入子类目管理">
						<i class="fa fa-th-list"></i> <?php echo (int)($value['count'] ?? 0);?> 个子类目
					</a>
				</td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status" title="点击切换启用/停用">
						<div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td style="white-space: nowrap;">
					<a class="btn btn-default btn-xs" href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>"><i class="fa fa-th-list"></i> 子类目</a>
					<a class="btn btn-info btn-xs" href="<?php echo adminUrl('category/attrUsed', ['cid'=>$value['cate_id']]);?>" style="margin-left: 3px;"><i class="fa fa-tags"></i> 属性映射</a>
					<button class="btn btn-primary btn-xs modify" type="button" style="margin-left: 3px;"><i class="fa fa-edit"></i> 编辑</button>
					<button class="btn btn-danger btn-xs delete" type="button" style="margin-left: 3px;"><i class="fa fa-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>

<!-- 添加/编辑品类弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 480px;">
		<form class="form-horizontal">
			<input type="hidden" name="cate_id" value="0">
			<input type="hidden" name="parent_id" value="0">
			<input type="hidden" name="opn" value="editInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增品类</div>
			
			<div class="input-group" style="width: 100%; margin-bottom: 14px;">
				<span class="input-group-addon" style="width: 100px;"><span>中文名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="64" placeholder="品类中文名称" autocomplete="off" style="width: 100%;">
			</div>

			<div class="input-group" style="width: 100%; margin-bottom: 14px;">
				<span class="input-group-addon" style="width: 100px;"><span>英文名称</span></span>
				<input type="text" class="form-control" name="name_en" maxlength="64" placeholder="英文名称 (可选)" autocomplete="off" style="width: 100%;">
			</div>

			<div style="margin-top: 20px;">
				<button type="button" class="btn btn-primary btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>