<?php
$curGroupId = (int)($search['group_id'] ?? 0);
$curStatus = (int)($search['status'] ?? -1);
$curKeyword = (string)($search['keyword'] ?? '');
?>
<div id="faq-page" class="faq-list-page" data-url="<?php echo adminUrl('faq/faqList');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-file-lines"></i>
			<span>文档管理</span>
			<span class="total-badge">共 <?php echo (int)($total ?? 0);?> 篇</span>
		</div>
		<button class="btn btn-success btn-xs add-btn" type="button">
			<i class="fa fa-plus"></i> 新增文章
		</button>
	</div>

	<!-- 搜索筛选栏（无重置按钮） -->
	<div class="filter-card">
		<form action="<?php echo adminUrl('faq/faqList');?>" method="get" class="filter-form">
			<div class="filter-row">
				<select name="group_id" class="form-control filter-select">
					<option value="0">全部分组</option>
					<?php foreach ($group ?? [] as $gId => $gName) { ?>
					<option value="<?php echo (int)$gId;?>" <?php echo $curGroupId === (int)$gId ? 'selected' : '';?>>
						<?php echo htmlspecialchars((string)$gName, ENT_QUOTES, 'UTF-8');?>
					</option>
					<?php } ?>
				</select>
				<select name="status" class="form-control filter-select">
					<option value="-1" <?php echo ($curStatus !== 0 && $curStatus !== 1) ? 'selected' : '';?>>全部状态</option>
					<option value="1" <?php echo $curStatus === 1 ? 'selected' : '';?>>已开启</option>
					<option value="0" <?php echo $curStatus === 0 ? 'selected' : '';?>>已关闭</option>
				</select>
			</div>
			<div class="filter-row">
				<input type="text" class="form-control filter-input" name="keyword" value="<?php echo htmlspecialchars($curKeyword, ENT_QUOTES, 'UTF-8');?>" placeholder="搜索文章标题关键字..." autocomplete="off">
				<button class="btn btn-info filter-submit" type="submit">
					<i class="fa fa-magnifying-glass"></i> 查询
				</button>
			</div>
		</form>
	</div>

	<!-- 文章卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-file-circle-question"></i>
				<p>暂无帮助文档数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$faqId = (int)$value['faq_id'];
				$gId = (int)($value['group_id'] ?? 0);
				$groupName = $group[$gId] ?? '--';
				$title = htmlspecialchars((string)$value['title'], ENT_QUOTES, 'UTF-8');
				$visitTotal = (int)($value['visit_total'] ?? 0);
				$status = (int)($value['status'] ?? 0);
			?>
			<div class="faq-card item" data-id="<?php echo $faqId;?>" data-title="<?php echo $title;?>">
				<div class="card-top">
					<div class="faq-info">
						<div class="faq-title-line">
							<span class="id-badge">#<?php echo $faqId;?></span>
							<strong class="faq-title"><?php echo $title;?></strong>
						</div>
						<div class="faq-meta-line">
							<span class="group-tag"><i class="fa fa-layer-group"></i> <?php echo htmlspecialchars((string)$groupName, ENT_QUOTES, 'UTF-8');?></span>
							<span class="visit-tag"><i class="fa fa-eye"></i> <?php echo $visitTotal;?> 次浏览</span>
						</div>
					</div>
					<div class="card-actions">
						<button class="btn btn-info btn-xs lang-btn" type="button" title="多语言管理" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-globe"></i></button>
						<button class="btn btn-primary btn-xs modify" type="button" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button class="btn btn-danger btn-xs delete" type="button" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
				<div class="card-bottom">
					<span class="status-hint">发布状态</span>
					<div class="switch-item">
						<span class="switch-txt"><?php echo $status === 1 ? '开启' : '关闭';?></span>
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
		<?php echo page($size ?? 30, $total ?? 0);?>
	</div>

	<script src="<?php echo siteUrl('kindEditor/kindeditor-min.js');?>"></script>

	<!-- 新增/编辑文章弹窗 -->
	<div id="dealbox-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">编辑文章</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="faq-form" onsubmit="return false;">
					<input type="hidden" name="faq_id" value="0">
					<input type="hidden" name="opn" value="editFaqInfo">
					<div class="form-group">
						<label class="form-label">文章标题 <span class="required">*</span></label>
						<input type="text" class="form-control" name="title" placeholder="请输入文章标题" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">所属分组 <span class="required">*</span></label>
						<select class="form-control" name="group_id">
							<option value="0">请选择分组</option>
							<?php foreach ($group ?? [] as $key => $val) { ?>
							<option value="<?php echo (int)$key;?>"><?php echo htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">状态</label>
						<select class="form-control" name="status">
							<option value="1">开启</option>
							<option value="0">关闭</option>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">浏览次数</label>
						<input type="number" class="form-control" name="visit_total" value="0" min="0" placeholder="请输入浏览次数">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 多语言列表弹窗（无底部关闭按钮） -->
	<div id="dealbox-language" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-globe"></i> 文章多语言管理</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="faq-lang-list-form" onsubmit="return false;">
					<input type="hidden" name="faq_id" value="0">
					<input type="hidden" name="name" value="">
					<div class="lang-source-bar">
						<span class="source-label">当前文章：</span>
						<strong class="source-name-text">-</strong>
					</div>
					<div class="lang-status-list"></div>
				</form>
			</div>
		</div>
	</div>

	<!-- 单语言文档内容编辑弹窗 -->
	<div id="dealbox-faq-language" class="modal-mask" style="display: none;">
		<div class="modal-panel modal-panel-lg">
			<div class="modal-header">
				<h4 class="dealbox-title">帮助文章编辑器</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="faq-lang-edit-form" onsubmit="return false;">
					<input type="hidden" name="faq_id" value="0">
					<input type="hidden" name="lan_id" value="0">
					<input type="hidden" name="opn" value="editFaqLanguage">
					<div class="form-group">
						<label class="form-label">文章标题 <span class="required">*</span></label>
						<input type="text" class="form-control" name="title" placeholder="请输入该语言下的文章标题" autocomplete="off">
					</div>
					<div class="form-group editor-group">
						<label class="form-label">文章内容 <span class="required">*</span></label>
						<textarea id="faq_editor" class="form-control" name="content" rows="8" placeholder="请输入文章内容"></textarea>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
