<?php
$currentCate = null;
foreach ($pList as $p) {
	if ((int)$p['cate_id'] === (int)$cid) {
		$currentCate = $p;
		break;
	}
}
$currentName = $currentCate['name'] ?? '全部类目';
$currentCount = $currentCate['count'] ?? count($list ?? []);
?>
<div id="category-list" class="catelist-page" data-cid="<?php echo (int)$cid;?>" data-name="<?php echo htmlspecialchars((string)$currentName, ENT_QUOTES, 'UTF-8');?>" data-url="<?php echo adminUrl('category/cateList');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-folder-open"></i>
			<span class="cur-cate-title"><?php echo htmlspecialchars((string)$currentName, ENT_QUOTES, 'UTF-8');?></span>
			<span class="total-badge"><?php echo (int)$currentCount;?> 个子类目</span>
		</div>
		<div class="header-actions">
			<button type="button" class="btn btn-default btn-xs toggle-all-btn" title="全部折叠/展开">
				<i class="fa fa-compress"></i>
			</button>
			<a href="<?php echo adminUrl('category/index');?>" class="btn btn-default btn-xs" title="返回一级品类列表">
				<i class="fa fa-list"></i>
			</a>
			<button type="button" class="btn btn-success btn-xs add-top-sub-btn">
				<i class="fa fa-plus"></i> 新增子类目
			</button>
		</div>
	</div>

	<!-- 一级品类切换栏 -->
	<?php if (!empty($pList)) { ?>
	<div class="parent-switch-box">
		<div class="switch-select-row">
			<span class="switch-label"><i class="fa fa-layer-group"></i> 一级品类：</span>
			<select class="form-control parent-cate-select">
				<?php foreach ($pList as $value) {
					$isActive = ((int)$value['cate_id'] === (int)$cid);
				?>
				<option value="<?php echo adminUrl('category/cateList', ['cid' => $value['cate_id']]);?>" <?php echo $isActive ? 'selected' : '';?>>
					<?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?> (<?php echo (int)$value['count'];?>)
				</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<?php } ?>

	<!-- 子类目树形卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>当前品类下暂无子类目数据</p>
			</div>
		<?php } else { ?>
			<?php foreach ($list as $value) {
				$cateId = (int)$value['cate_id'];
				$parentId = (int)$value['parent_id'];
				$lev = max(1, (int)$value['level']);
				$name = htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');
				$nameEn = htmlspecialchars((string)($value['name_en'] ?? ''), ENT_QUOTES, 'UTF-8');
				$status = (int)($value['status'] ?? 0);
				$isShow = (int)($value['is_show'] ?? 0);
				$transState = (int)($value['is_translate'] ?? 0);
				$transClass = $transState === 2 ? 'trans-ok' : ($transState === 1 ? 'trans-part' : 'trans-none');
				$avatar = trim((string)($value['avatar'] ?? ''));
				$hasIcon = $avatar !== '' && strpos($avatar, 'image/common/') === false;
				$indentRem = min(3, $lev - 1) * 0.12;
			?>
			<div class="subcate-card item lev-<?php echo $lev;?>"
				data-lev="<?php echo $lev;?>"
				data-id="<?php echo $cateId;?>"
				data-pid="<?php echo $parentId;?>"
				data-name="<?php echo $name;?>"
				style="<?php echo $indentRem > 0 ? 'margin-left:' . $indentRem . 'rem;' : '';?>">
				<div class="card-top">
					<div class="cate-left">
						<div class="cate-icon-upload" title="点击上传/更换图标">
							<?php if ($hasIcon) { ?>
								<img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8');?>" alt="icon">
							<?php } else { ?>
								<i class="fa fa-image"></i>
							<?php } ?>
						</div>
						<div class="cate-info">
							<div class="cate-title-line">
								<span class="id-badge">#<?php echo $cateId;?></span>
								<span class="lev-badge">L<?php echo $lev;?></span>
								<strong class="cate_name"><?php echo $name;?></strong>
							</div>
							<?php if ($nameEn !== '') { ?>
								<div class="cate-name-en"><?php echo $nameEn;?></div>
							<?php } ?>
						</div>
					</div>
					<div class="card-actions">
						<a class="btn btn-info btn-xs" href="<?php echo adminUrl('category/attrUsed', ['cid' => $cateId]);?>" title="属性映射"><i class="fa fa-tags"></i></a>
						<button type="button" class="btn btn-primary btn-xs modify" title="编辑" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button type="button" class="btn btn-success btn-xs add" title="新增下级"><i class="fa fa-plus"></i></button>
						<button type="button" class="btn btn-danger btn-xs delete" title="删除"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>

				<div class="card-bottom">
					<!-- 多语言与 SEO -->
					<div class="lang-seo-group">
						<span class="lang-trigger <?php echo $transClass;?>" data-type="0" title="名称多语言配置">
							<i class="fa fa-globe"></i>
						</span>
						<span class="lang-trigger seo-k" data-type="1" title="Keyword 多语言配置">K</span>
						<span class="lang-trigger seo-d" data-type="2" title="Description 多语言配置">D</span>
					</div>

					<!-- 状态与展示开关 -->
					<div class="switch-group">
						<div class="switch-item">
							<span class="switch-txt">启用</span>
							<div class="switch_botton" data-status="<?php echo $status;?>" data-type="status">
								<div class="switch_status <?php echo $status ? 'on' : 'off';?>"></div>
							</div>
						</div>
						<div class="switch-item">
							<span class="switch-txt">展示</span>
							<div class="switch_botton" data-status="<?php echo $isShow;?>" data-type="is_show">
								<div class="switch_status <?php echo $isShow ? 'on' : 'off';?>"></div>
							</div>
						</div>
					</div>

					<!-- 排序按钮 -->
					<div class="sort-group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 新增/编辑子类目弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title">子类目管理</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="subcate-form" onsubmit="return false;">
					<input type="hidden" name="cate_id" value="0">
					<input type="hidden" name="parent_id" value="0">
					<input type="hidden" name="opn" value="editInfo">
					<div class="form-group parent-group" style="display: none;">
						<label class="form-label">父级分类</label>
						<input type="text" class="form-control" name="parent_name" readonly value="">
					</div>
					<div class="form-group">
						<label class="form-label">中文名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" maxlength="64" placeholder="请输入子类目中文名称" autocomplete="off">
					</div>
					<div class="form-group">
						<label class="form-label">英文名称</label>
						<input type="text" class="form-control" name="name_en" maxlength="64" placeholder="请输入英文名称 (可选)" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 多语言/SEO配置弹窗 -->
	<div id="dealbox-language" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title title"><i class="fa fa-globe"></i> 多语言配置</h4>
				<div class="modal-header-right">
					<button type="button" class="btn btn-info btn-xs translate-all-btn" title="智能翻译空白项">
						<i class="fa fa-language"></i> 智能翻译
					</button>
					<span class="modal-close"><i class="fa fa-xmark"></i></span>
				</div>
			</div>
			<div class="modal-body">
				<form id="cate-lang-form" onsubmit="return false;">
					<input type="hidden" name="cate_id" value="0">
					<input type="hidden" name="type" value="0">
					<input type="hidden" name="cate_name" value="">
					<input type="hidden" name="opn" value="editLanguage">
					<div class="lang-source-bar">
						<span class="source-label">当前类目：</span>
						<strong class="source-name-text">-</strong>
					</div>
					<div class="language-list"></div>
					<button type="button" class="btn btn-primary btn-block save-btn">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
