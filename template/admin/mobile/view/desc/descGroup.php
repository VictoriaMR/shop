<?php
$statusMap = [
	-1 => '全部',
	0  => '未配置',
	1  => '部分配置',
	2  => '已配置',
];
$transLabel = [
	0 => ['tag-danger', '未配置'],
	1 => ['tag-warning', '部分配置'],
	2 => ['tag-success', '已配置'],
];
?>
<div id="descgroup-list" class="desc-page" data-url="<?php echo adminUrl('desc/descGroup');?>">
	<!-- 顶部标题栏 + 新增按钮 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-layer-group"></i>
			<span>描述分组</span>
			<span class="total-badge">共 <?php echo (int)$total;?> 个</span>
		</div>
		<button class="btn btn-success btn-sm add" type="button">
			<i class="fa fa-plus"></i> 新增描述分组
		</button>
	</div>

	<!-- 筛选栏（无重置按钮） -->
	<div class="filter-box">
		<form action="<?php echo adminUrl('desc/descGroup');?>" method="get" class="search-form">
			<input type="hidden" name="status" value="<?php echo (int)$status;?>">
			<div class="status-filter">
				<?php foreach ($statusMap as $key => $text) { ?>
				<button type="button" data-status="<?php echo $key;?>" class="btn btn-xs <?php echo (int)$status === $key ? 'btn-primary' : 'btn-default';?>"><?php echo $text;?></button>
				<?php } ?>
			</div>
			<div class="filter-row">
				<div class="filter-item flex-1">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string)$keyword, ENT_QUOTES, 'UTF-8');?>" placeholder="输入描述分组关键字搜索..." autocomplete="off">
				</div>
				<button class="btn btn-info search-submit-btn" type="submit"><i class="fa fa-magnifying-glass"></i> 查询</button>
			</div>
		</form>
	</div>

	<!-- 列表区 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无匹配的描述分组数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$name = htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');
				$trans = $transLabel[$value['is_translate'] ?? 0] ?? $transLabel[0];
			?>
			<div class="desc-card item" data-id="<?php echo (int)$value['descg_id'];?>" data-name="<?php echo $name;?>">
				<div class="card-main">
					<div class="desc-info">
						<span class="id-badge">#<?php echo (int)$value['descg_id'];?></span>
						<strong class="desc-name"><?php echo $name;?></strong>
						<span class="trans-tag <?php echo $trans[0];?>"><?php echo $trans[1];?></span>
					</div>
					<div class="card-actions">
						<button class="btn btn-primary btn-xs modify" type="button" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button class="btn btn-info btn-xs language" type="button" title="多语言" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-globe"></i></button>
						<button class="btn btn-danger btn-xs delete" type="button" title="删除"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页组件 -->
	<?php echo page($size, $total);?>

	<!-- 描述分组编辑弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">描述分组管理</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="descg-form" onsubmit="return false;">
					<input type="hidden" name="id" value="0">
					<input type="hidden" name="opn" value="editDescGroupInfo">
					<div class="form-group">
						<label class="form-label">描述分组名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" maxlength="255" placeholder="请输入描述分组" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确 认</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 多语言配置弹窗 -->
	<div id="dealbox-language" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-globe"></i> 多语言配置</h4>
				<div class="modal-header-right">
					<button type="button" class="btn btn-info btn-xs translate-all-btn" title="智能翻译空白项">
						<i class="fa fa-language"></i> 智能翻译
					</button>
					<span class="modal-close"><i class="fa fa-xmark"></i></span>
				</div>
			</div>
			<div class="modal-body">
				<form id="descg-lang-form" onsubmit="return false;">
					<input type="hidden" name="id" value="0">
					<input type="hidden" name="name" value="">
					<input type="hidden" name="opn" value="editDescGroupLanguage">
					<div class="lang-source-bar">
						<span class="source-label">源描述分组：</span>
						<strong class="source-name-text">-</strong>
					</div>
					<div class="language-list"></div>
					<button type="button" class="btn btn-primary btn-block save-btn">保 存</button>
				</form>
			</div>
		</div>
	</div>
</div>
