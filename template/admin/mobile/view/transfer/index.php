<div id="transfer-page" class="transfer-page" data-url="<?php echo adminUrl('transfer/index');?>">
	<!-- 顶部标题栏 + 重构缓存按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-language"></i>
			<span>文本翻译</span>
			<span class="total-badge">共 <?php echo (int)($total ?? 0);?> 条</span>
		</div>
		<button class="btn btn-primary btn-sm reload" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 重构中">
			<i class="fa fa-arrows-rotate"></i> 重构缓存
		</button>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('transfer/index');?>" method="get" class="search-form">
			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string)($keyword ?? ''), ENT_QUOTES, 'UTF-8');?>" placeholder="输入原文名称关键字搜索..." autocomplete="off">
				</div>
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 列表区 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-language"></i>
				<p>暂无文本翻译数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$tranId = (int)($value['tran_id'] ?? 0);
				$type = (string)($value['type'] ?? '');
				$typeName = (string)($value['type_name'] ?? '');
				$name = (string)($value['name'] ?? '');
				$val = (string)($value['value'] ?? '');
				$rowInfo = [
					'tran_id'   => $tranId,
					'type'      => $type,
					'type_name' => $typeName,
					'name'      => $name,
					'value'     => $val,
				];
			?>
			<div class="transfer-card item" data-id="<?php echo $tranId;?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<div class="card-main">
					<div class="transfer-info">
						<span class="id-badge">#<?php echo $tranId;?></span>
						<span class="lang-tag"><i class="fa fa-globe"></i> <?php echo htmlspecialchars($type . ($typeName !== '' ? ' / ' . $typeName : ''), ENT_QUOTES, 'UTF-8');?></span>
					</div>
					<div class="card-actions">
						<button class="btn btn-primary btn-xs modify" type="button" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
					</div>
				</div>
				<div class="trans-body">
					<div class="text-row">
						<span class="text-label">名称</span>
						<div class="text-content source-text"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');?></div>
					</div>
					<div class="text-row">
						<span class="text-label">翻译</span>
						<div class="text-content target-text"><?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8');?></div>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size ?? 20, $total ?? 0);?>

	<!-- 编辑翻译弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-pen-to-square"></i> 编辑翻译</h4>
				<div class="modal-header-right">
					<button type="button" class="btn btn-info btn-xs auto-transfer-btn" title="智能翻译" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-language"></i> 自动翻译
					</button>
					<span class="modal-close"><i class="fa fa-xmark"></i></span>
				</div>
			</div>
			<div class="modal-body">
				<form id="transfer-form" onsubmit="return false;">
					<input type="hidden" name="tran_id" value="0">
					<input type="hidden" name="opn" value="editInfo">
					<input type="hidden" name="type" value="">
					<input type="hidden" name="name" value="">
					<div class="form-group">
						<label class="form-label">语言类型</label>
						<input type="text" class="form-control" name="type_name" maxlength="30" disabled="disabled" value="">
					</div>
					<div class="form-group">
						<label class="form-label">原文名称</label>
						<textarea class="form-control" name="name_display" rows="3" disabled="disabled"></textarea>
					</div>
					<div class="form-group">
						<label class="form-label">翻译文本 <span class="required">*</span></label>
						<textarea class="form-control" name="value" rows="3" placeholder="请输入翻译内容" required="required"></textarea>
					</div>
					<button type="button" class="btn btn-primary btn-block save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确 认</button>
				</form>
			</div>
		</div>
	</div>
</div>
