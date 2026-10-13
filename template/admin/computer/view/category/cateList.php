<?php
$currentCate = null;
foreach ($pList as $p) {
	if ($p['cate_id'] == $cid) {
		$currentCate = $p;
		break;
	}
}
$currentName = $currentCate['name'] ?? '全部类目';
$currentCount = $currentCate['count'] ?? count($list);
?>
<div class="container-fluid" id="category-list" data-cid="<?php echo $cid;?>">
	<!-- 左侧一级品类导航 -->
	<div class="col-md-2" style="padding-left: 0; padding-right: 10px;">
		<div class="panel panel-default" style="border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); margin-bottom: 10px;">
			<div class="panel-heading" style="padding: 10px 14px; background: #f8fafc; font-weight: 600; color: #334155; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
				<span><i class="fa fa-th-list" style="color: #64748b; margin-right: 4px;"></i> 一级品类</span>
				<span class="badge" style="background: #0284c7;"><?php echo count($pList);?></span>
			</div>
			<div style="padding: 8px 10px; background: #fff; border-bottom: 1px solid #f1f5f9;">
				<input type="text" class="form-control input-sm search-parent-cate" placeholder="搜索品类..." style="border-radius: 3px; font-size: 12px;" autocomplete="off">
			</div>
			<div class="list-group parent-cate-list" style="max-height: calc(100vh - 220px); overflow-y: auto; margin-bottom: 0;">
				<?php foreach ($pList as $value){ 
					$isActive = ($value['cate_id'] == $cid);
				?>
				<a href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>" class="list-group-item<?php echo $isActive ? ' active' : '';?>" style="border-radius: 0; border-left: none; border-right: none; padding: 10px 12px;">
					<span class="badge" title="点击直接为此品类添加子类目" data-id="<?php echo $value['cate_id'];?>" style="<?php echo $isActive ? 'background: #fff; color: #0284c7; font-weight: 600;' : 'background: #e2e8f0; color: #475569;';?>">
						<?php echo $value['count'];?>
					</span>
					<span class="cate-item-name"><?php echo htmlspecialchars($value['name']);?></span>               
				</a>
				<?php } ?>
			</div>
		</div>
	</div>

	<!-- 右侧子类目列表及操作 -->
	<div class="col-md-10" style="padding-left: 6px; padding-right: 0;">
		<div class="row" style="margin-bottom: 10px;">
			<div class="col-xs-12">
				<div class="form-inline" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
					<div class="form-group" style="margin-bottom: 4px;">
						<span style="font-size: 15px; font-weight: 600; color: #1e293b;">
							<i class="fa fa-folder-open" style="color: #0284c7; margin-right: 6px;"></i>
							<?php echo htmlspecialchars($currentName);?>
							<span class="text-muted" style="font-size: 13px; font-weight: normal; margin-left: 8px;">
								(共 <strong><?php echo (int)$currentCount;?></strong> 个子类目)
							</span>
						</span>
					</div>
					<div class="form-group" style="margin-bottom: 4px;">
						<button type="button" class="btn btn-default btn-sm toggle-all-btn" style="margin-right: 6px;"><i class="fa fa-arrows-alt"></i> 全部折叠/展开</button>
						<button type="button" class="btn btn-success btn-sm add-top-sub-btn" style="margin-right: 6px;"><i class="fa fa-plus-circle"></i> 新增子类目</button>
						<a href="<?php echo adminUrl('category/index');?>" class="btn btn-default btn-sm"><i class="fa fa-list"></i> 一级品类列表</a>
					</div>
				</div>
			</div>
		</div>

		<table class="table table-hover table-middle mt6" id="data-list">
			<thead>
				<tr>
					<th width="60">ID</th>
					<th width="320">类目名称 / 层级</th>
					<th width="110">SEO / 翻译</th>
					<th width="80" class="tc">状态</th>
					<th width="80" class="tc">展示</th>
					<th width="130" class="tc">排序</th>
					<th width="70" class="tc">图标</th>
					<th width="240">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($list)){ ?>
				<tr>
					<td colspan="8">
						<div class="tc orange">
							<i class="glyphicon glyphicon-info-sign"></i> 暂无子类目数据
						</div>
					</td>
				</tr>
				<?php } else { ?>
				<?php foreach ($list as $key => $value) { 
					$lev = (int)$value['level'];
					$indent = max(0, ($lev - 1) * 22);
				?>
				<tr class="item<?php echo $lev == 1 ? ' info' : '';?>" data-lev="<?php echo $lev;?>" data-id="<?php echo $value['cate_id'];?>" data-pid="<?php echo $value['parent_id'];?>" style="cursor: pointer;" title="点击展开/收起下级子分类">
					<td><strong><?php echo $value['cate_id'];?></strong></td>
					<td>
						<div class="left text-content" style="<?php echo $indent > 0 ? 'padding-left: ' . $indent . 'px;' : '';?> display: flex; align-items: center;">
							<?php if ($lev > 1){ ?>
							<span class="text-muted" style="margin-right: 6px; font-family: monospace; font-size: 13px; color: #94a3b8;">└─</span>
							<?php } ?>
							<span data-type="0" class="fa fa-globe <?php echo $value['is_translate']==2 ? 'green' : ($value['is_translate']==1 ? 'orange' : 'red');?>" title="多语言配置 (名称)" style="margin-right: 6px; cursor: pointer;"></span>
							<strong class="cate_name" style="color: #0f172a; font-size: 13px;"><?php echo htmlspecialchars($value['name']);?></strong>
							<?php if (!empty($value['name_en'])){ ?>
							<span class="text-muted f12" style="margin-left: 6px;">(<?php echo htmlspecialchars($value['name_en']);?>)</span>
							<?php } ?>
						</div>
					</td>
					<td>
						<div style="font-size: 12px; line-height: 18px;">
							<span class="green" style="font-weight: 600;" title="Keyword 关键词">K:</span>
							<span title="Keyword 多语言配置" data-type="1" class="fa fa-globe" style="cursor: pointer;"></span>
							<span class="orange" style="font-weight: 600; margin-left: 8px;" title="Description 描述">D:</span>
							<span title="Description 多语言配置" data-type="2" class="fa fa-globe" style="cursor: pointer;"></span>
						</div>
					</td>
					<td class="tc">
						<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status" title="点击切换启用/停用">
							<div class="switch_status <?php echo $value['status'] ? 'on' : 'off';?>"></div>
						</div>
					</td>
					<td class="tc">
						<div class="switch_botton" data-status="<?php echo $value['is_show'];?>" data-type="is_show" title="点击切换是否在前台展示">
							<div class="switch_status <?php echo $value['is_show'] ? 'on' : 'off';?>"></div>
						</div>
					</td>
					<td class="tc">
						<div class="btn-group btn-group-xs" role="group">
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><i class="fa fa-angle-double-up"></i></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><i class="fa fa-arrow-up"></i></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><i class="fa fa-arrow-down"></i></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><i class="fa fa-angle-double-down"></i></button>
						</div>
					</td>
					<td class="tc">
						<div class="avatar-hover" style="display: inline-block;">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload" style="width: 28px; height: 28px; object-fit: cover; border-radius: 3px; border: 1px solid #e2e8f0; cursor: pointer;" title="点击上传/更换分类图标">
						</div>
					</td>
					<td style="white-space: nowrap;">
						<a class="btn btn-info btn-xs" href="<?php echo adminUrl('category/attrUsed', ['cid'=>$value['cate_id']]);?>"><i class="fa fa-tags"></i> 属性</a>
						<button type="button" class="btn btn-primary btn-xs modify" style="margin-left: 2px;"><i class="fa fa-edit"></i> 编辑</button>
						<button type="button" class="btn btn-success btn-xs add" style="margin-left: 2px;"><i class="fa fa-plus"></i> 下级</button>
						<button type="button" class="btn btn-danger btn-xs delete" style="margin-left: 2px;"><i class="fa fa-trash"></i> 删除</button>
					</td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<!-- 添加/编辑子类目弹窗 -->
<div id="dealbox" class="s-modal" data-mask="true" style="width: 480px;">
	<i class="fa fa-times"></i>
	<div class="title" style="font-size: 18px; font-weight: 600; color: #1e293b; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; margin-bottom: 16px;">品类管理</div>
	<form class="form-horizontal">
		<input type="hidden" name="cate_id" value="0">
		<input type="hidden" name="parent_id" value="0">
		<input type="hidden" name="opn" value="editInfo">
		<div class="input-group parent-group" style="width: 100%; margin-bottom: 12px; display: none;">
			<div class="input-group-addon" style="width: 90px;"><span>父级分类</span></div>
			<input type="text" class="form-control" name="parent_name" readonly value="" style="background: #f8fafc;">
		</div>
		<div class="input-group" style="width: 100%; margin-bottom: 12px;">
			<div class="input-group-addon" style="width: 90px;"><span>中文名称</span></div>
			<input type="text" class="form-control" name="name" required="required" placeholder="子类目中文名称" autocomplete="off" style="width: 100%;">
		</div>
		<div class="input-group" style="width: 100%; margin-bottom: 12px;">
			<div class="input-group-addon" style="width: 90px;"><span>英文名称</span></div>
			<input type="text" class="form-control" name="name_en" placeholder="英文名称 (可选)" autocomplete="off" style="width: 100%;">
		</div>
		<button type="button" class="btn btn-primary btn-block save-btn mt12" style="padding: 9px 0; font-size: 14px;">确认保存</button>
	</form>
</div>

<!-- 多语言弹窗 -->
<div id="dealbox-language" class="s-modal" data-mask="true" style="width: 620px;">
	<i class="fa fa-times"></i>
	<div class="title" style="font-size: 18px; font-weight: 600; color: #1e293b; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; margin-bottom: 16px;">多语言配置</div>
	<form class="form-horizontal">
		<input type="hidden" name="cate_id" value="0">
		<input type="hidden" name="type" value="0">
		<input type="hidden" name="cate_name" value="">
		<input type="hidden" name="opn" value="editLanguage">
		<div style="max-height: 460px; overflow-y: auto; margin-bottom: 15px;">
			<table class="table table-bordered table-hover" style="margin-bottom: 0;">
				<tbody></tbody>
			</table>
		</div>
		<button type="button" class="btn btn-primary btn-block save-btn mt12" style="padding: 9px 0; font-size: 14px;">确认保存</button>
	</form>
</div>