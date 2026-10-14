<div id="category-page" class="category-page" data-url="<?php echo adminUrl('category/index');?>">
	<!-- 顶部标题栏 + 新增按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-folder-tree"></i>
			<span>品类管理</span>
			<span class="total-badge">共 <?php echo count($list ?? []);?> 个</span>
		</div>
		<button class="btn btn-success btn-sm" id="add-data-btn" type="button">
			<i class="fa fa-plus"></i> 新增品类
		</button>
	</div>

	<!-- 品类卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无一级品类数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$cateId = (int)$value['cate_id'];
				$name = htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');
				$nameEn = htmlspecialchars((string)($value['name_en'] ?? ''), ENT_QUOTES, 'UTF-8');
				$subCount = (int)($value['count'] ?? 0);
				$status = (int)($value['status'] ?? 0);
			?>
			<div class="cate-card item" data-id="<?php echo $cateId;?>">
				<div class="card-top">
					<div class="cate-info">
						<span class="id-badge">#<?php echo $cateId;?></span>
						<div class="name-wrap">
							<strong class="cate_name"><?php echo $name;?></strong>
							<?php if ($nameEn !== '') { ?>
								<span class="cate-name-en"><?php echo $nameEn;?></span>
							<?php } ?>
						</div>
					</div>
					<div class="switch_botton" data-status="<?php echo $status;?>" data-type="status" title="点击切换启用/停用">
						<div class="switch_status <?php echo $status === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
				<div class="card-bottom">
					<a href="<?php echo adminUrl('category/cateList', ['cid' => $cateId]);?>" class="sub-count-badge">
						<i class="fa fa-sitemap"></i> <?php echo $subCount;?> 个子类目
					</a>
					<div class="card-actions">
						<a class="btn btn-default btn-xs" href="<?php echo adminUrl('category/cateList', ['cid' => $cateId]);?>" title="子类目管理"><i class="fa fa-list-ul"></i></a>
						<a class="btn btn-info btn-xs" href="<?php echo adminUrl('category/attrUsed', ['cid' => $cateId]);?>" title="属性映射"><i class="fa fa-tags"></i></a>
						<button class="btn btn-primary btn-xs modify" type="button" title="编辑" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button class="btn btn-danger btn-xs delete" type="button" title="删除"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 新增/编辑品类弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">新增品类</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="cate-form" onsubmit="return false;">
					<input type="hidden" name="cate_id" value="0">
					<input type="hidden" name="parent_id" value="0">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group">
						<label class="form-label">中文名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" maxlength="64" placeholder="请输入品类中文名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">英文名称</label>
						<input type="text" class="form-control" name="name_en" maxlength="64" placeholder="请输入品类英文名称 (可选)" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
