<?php
$curStatus = (int)($search['status'] ?? -1);
$curKeyword = (string)($search['keyword'] ?? '');
?>
<div id="faq-page" class="faq-group-page" data-url="<?php echo adminUrl('faq/index');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-layer-group"></i>
			<span>分组管理</span>
			<span class="total-badge">共 <?php echo (int)($total ?? 0);?> 个</span>
		</div>
		<button class="btn btn-success btn-xs add-btn" type="button">
			<i class="fa fa-plus"></i> 新增分组
		</button>
	</div>

	<!-- 搜索筛选栏（无重置按钮） -->
	<div class="filter-card">
		<form action="<?php echo adminUrl('faq/index');?>" method="get" class="filter-form">
			<select name="status" class="form-control filter-select">
				<option value="-1" <?php echo ($curStatus !== 0 && $curStatus !== 1) ? 'selected' : '';?>>全部状态</option>
				<option value="1" <?php echo $curStatus === 1 ? 'selected' : '';?>>已开启</option>
				<option value="0" <?php echo $curStatus === 0 ? 'selected' : '';?>>已关闭</option>
			</select>
			<input type="text" class="form-control filter-input" name="keyword" value="<?php echo htmlspecialchars($curKeyword, ENT_QUOTES, 'UTF-8');?>" placeholder="搜索分组名称..." autocomplete="off">
			<button class="btn btn-info filter-submit" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</form>
	</div>

	<!-- 分组卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无分组数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$groupId = (int)$value['group_id'];
				$name = htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');
				$status = (int)($value['status'] ?? 0);
			?>
			<div class="group-card item" data-id="<?php echo $groupId;?>" data-name="<?php echo $name;?>">
				<div class="card-top">
					<div class="group-info">
						<span class="id-badge">#<?php echo $groupId;?></span>
						<strong class="group-name"><?php echo $name;?></strong>
					</div>
					<div class="card-actions">
						<button class="btn btn-info btn-xs lang-btn" type="button" title="多语言配置" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-globe"></i></button>
						<button class="btn btn-primary btn-xs modify" type="button" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button class="btn btn-danger btn-xs delete" type="button" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
				<div class="card-bottom">
					<a href="<?php echo adminUrl('faq/faqList', ['group_id' => $groupId]);?>" class="doc-link-badge">
						<i class="fa fa-file-lines"></i> 查看关联文档
					</a>
					<div class="switch-item">
						<span class="switch-txt">状态</span>
						<div class="switch_botton" data-status="<?php echo $status;?>" title="点击切换状态">
							<div class="switch_status <?php echo $status === 1 ? 'on' : 'off';?>"></div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="page-wrap">
		<?php echo page($size ?? 20, $total ?? 0);?>
	</div>

	<!-- 新增/编辑分组弹窗 -->
	<div id="dealbox-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">编辑分组</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="group-form" onsubmit="return false;">
					<input type="hidden" name="group_id" value="0">
					<input type="hidden" name="opn" value="editGroupInfo">
					<div class="form-group">
						<label class="form-label">分组名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" required="required" placeholder="请输入分组名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">状态</label>
						<select class="form-control" name="status">
							<option value="1">开启</option>
							<option value="0">关闭</option>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
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
					<button type="button" class="btn btn-info btn-xs translate-all-btn" title="智能翻译空白项" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-language"></i> 智能翻译
					</button>
					<span class="modal-close"><i class="fa fa-xmark"></i></span>
				</div>
			</div>
			<div class="modal-body">
				<form id="group-lang-form" onsubmit="return false;">
					<input type="hidden" name="group_id" value="0">
					<input type="hidden" name="name" value="">
					<input type="hidden" name="opn" value="editGroupLanguage">
					<div class="lang-source-bar">
						<span class="source-label">当前分组：</span>
						<strong class="source-name-text">-</strong>
					</div>
					<div class="language-list"></div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
