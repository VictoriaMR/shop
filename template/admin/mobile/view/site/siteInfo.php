<div id="site-page" class="site-info-mobile-page" data-id="<?php echo (int)$id;?>" data-name="<?php echo htmlspecialchars($site['name'] ?? '', ENT_QUOTES, 'UTF-8');?>" data-url="<?php echo adminUrl('site/siteInfo', ['id' => (int)$id]);?>">
	<?php if (empty($site)) { ?>
	<div class="empty-state">
		<i class="fa fa-circle-exclamation"></i>
		<p>未找到该站点数据，请从 <a href="<?php echo adminUrl('site/index');?>" class="link-primary">站点列表</a> 选择站点进入配置。</p>
	</div>
	<?php } else {
		$usedLanCodes = !empty($siteLanguage) ? array_column($siteLanguage, 'code') : [];
		$usedCurCodes = !empty($siteCurrency) ? array_column($siteCurrency, 'code') : [];
		$isAdmin = ($site['type'] ?? '') === 'admin';
	?>
	<!-- 顶部返回与标题栏 -->
	<div class="page-header-bar">
		<a href="<?php echo adminUrl('site/index');?>" class="btn btn-default btn-xs back-btn">
			<i class="fa fa-angle-left"></i> 返回列表
		</a>
		<div class="header-main">
			<span class="id-badge">#<?php echo (int)$site['site_id'];?></span>
			<span class="site-title"><?php echo htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8');?></span>
			<span class="type-tag <?php echo $isAdmin ? 'admin' : 'home';?>"><?php echo htmlspecialchars($site['type'] ?: 'home', ENT_QUOTES, 'UTF-8');?></span>
		</div>
	</div>

	<!-- 基础信息与运行状态 -->
	<div class="section-card">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-sliders"></i> 基础信息与运行状态</div>
			<span class="section-sub">更新: <?php echo htmlspecialchars($site['update_time'] ?: ($site['add_time'] ?: '--'), ENT_QUOTES, 'UTF-8');?></span>
		</div>
		<div class="section-body">
			<div class="info-edit-list">
				<div class="info-edit-row" id="site-name" title="点击修改站点名称">
					<span class="info-label">站点名称</span>
					<div class="info-val-wrap">
						<b><?php echo htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8');?></b>
						<i class="fa fa-pen-to-square edit-icon"></i>
					</div>
				</div>
				<div class="info-edit-row" id="site-path" title="点击修改模板目录">
					<span class="info-label">模板目录</span>
					<div class="info-val-wrap">
						<code><?php echo htmlspecialchars($site['path'], ENT_QUOTES, 'UTF-8');?></code>
						<i class="fa fa-pen-to-square edit-icon"></i>
					</div>
				</div>
				<div class="info-edit-row" id="site-domain-edit" title="点击修改绑定域名">
					<span class="info-label">绑定域名</span>
					<div class="info-val-wrap">
						<code><?php echo htmlspecialchars($site['domain'] ?: '--', ENT_QUOTES, 'UTF-8');?></code>
						<i class="fa fa-pen-to-square edit-icon"></i>
					</div>
				</div>
				<div class="info-edit-row" id="site-email-edit" title="点击修改联系邮箱">
					<span class="info-label">联系邮箱</span>
					<div class="info-val-wrap">
						<span><?php echo htmlspecialchars($site['email'] ?: '--', ENT_QUOTES, 'UTF-8');?></span>
						<i class="fa fa-pen-to-square edit-icon"></i>
					</div>
				</div>
			</div>

			<div class="switch-grid">
				<div class="switch-box">
					<span class="switch-label">站点状态</span>
					<div class="switch_botton site-switch" data-field="status" data-status="<?php echo (int)$site['status'];?>">
						<div class="switch_status <?php echo (int)$site['status'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
				<div class="switch-box">
					<span class="switch-label">Debug调试</span>
					<div class="switch_botton site-switch" data-field="debug" data-status="<?php echo (int)$site['debug'];?>">
						<div class="switch_status <?php echo (int)$site['debug'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
				<div class="switch-box">
					<span class="switch-label">视图缓存</span>
					<div class="switch_botton site-switch" data-field="view_cache" data-status="<?php echo (int)$site['view_cache'];?>">
						<div class="switch_status <?php echo (int)$site['view_cache'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
				<div class="switch-box">
					<span class="switch-label">静态缓存</span>
					<div class="switch_botton site-switch" data-field="static_cache" data-status="<?php echo (int)$site['static_cache'];?>">
						<div class="switch_status <?php echo (int)$site['static_cache'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 站点多语言配置 -->
	<div class="section-card" id="language-table">
		<div class="section-header">
			<div class="section-title">
				<i class="fa fa-language"></i> 站点语言配置
				<span class="section-hint">(首位默认)</span>
			</div>
			<button class="btn btn-primary btn-xs" id="add-language" type="button">
				<i class="fa fa-plus"></i> 管理语言
			</button>
		</div>
		<div class="section-body">
			<?php if (empty($siteLanguage)) { ?>
			<div class="mini-empty">暂未配置站点语言</div>
			<?php } else {
				$lanTotal = count($siteLanguage);
				$lanIdx = 0;
				foreach ($siteLanguage as $value) {
					$lanIdx++;
					$code = $value['code'];
					$lanInfo = $languageList[$code] ?? [];
			?>
			<div class="sub-card" data-id="<?php echo (int)$value['item_id'];?>" data-code="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?>">
				<div class="sub-card-top">
					<div class="sub-card-title">
						<?php if ($lanIdx === 1) { ?>
						<span class="badge-default">默认</span>
						<?php } else { ?>
						<span class="badge-idx">#<?php echo $lanIdx;?></span>
						<?php } ?>
						<code class="code-pill"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?></code>
						<span class="sub-name"><?php echo htmlspecialchars($lanInfo['name2'] ?? ($lanInfo['name'] ?? $code), ENT_QUOTES, 'UTF-8');?></span>
						<?php if (!empty($lanInfo['name']) && !empty($lanInfo['name2']) && $lanInfo['name'] !== $lanInfo['name2']) { ?>
						<span class="sub-desc">(<?php echo htmlspecialchars($lanInfo['name'], ENT_QUOTES, 'UTF-8');?>)</span>
						<?php } ?>
					</div>
					<button class="btn btn-danger btn-icon delete" type="button" title="移除语言" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-trash-can"></i>
					</button>
				</div>
				<div class="sub-card-bottom">
					<div class="sort-input-group">
						<span class="sort-label">排序</span>
						<input type="number" class="form-control sort-input" name="sort" value="<?php echo (int)$value['sort'];?>">
					</div>
					<div class="sort-btn-group">
						<button type="button" class="btn btn-default btn-icon sort-lan-btn" data-type="top" title="置顶" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $lanIdx === 1 ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-lan-btn" data-type="up" title="上移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $lanIdx === 1 ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-lan-btn" data-type="down" title="下移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $lanIdx === $lanTotal ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-lan-btn" data-type="bottom" title="置底" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $lanIdx === $lanTotal ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>
			<?php } } ?>
		</div>
	</div>

	<!-- 站点多货币配置 -->
	<div class="section-card" id="site-currency">
		<div class="section-header">
			<div class="section-title">
				<i class="fa fa-coins"></i> 站点货币配置
				<span class="section-hint">(首位默认)</span>
			</div>
			<button class="btn btn-primary btn-xs" id="add-currency" type="button">
				<i class="fa fa-plus"></i> 管理货币
			</button>
		</div>
		<div class="section-body">
			<?php if (empty($siteCurrency)) { ?>
			<div class="mini-empty">暂未配置站点货币</div>
			<?php } else {
				$curTotal = count($siteCurrency);
				$curIdx = 0;
				foreach ($siteCurrency as $value) {
					$curIdx++;
					$code = $value['code'];
					$curInfo = $currencyList[$code] ?? [];
			?>
			<div class="sub-card" data-id="<?php echo (int)$value['item_id'];?>" data-code="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?>">
				<div class="sub-card-top">
					<div class="sub-card-title">
						<?php if ($curIdx === 1) { ?>
						<span class="badge-default">默认</span>
						<?php } else { ?>
						<span class="badge-idx">#<?php echo $curIdx;?></span>
						<?php } ?>
						<code class="code-pill"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?></code>
						<?php if (!empty($curInfo['symbol'])) { ?>
						<b class="curr-symbol"><?php echo htmlspecialchars($curInfo['symbol'], ENT_QUOTES, 'UTF-8');?></b>
						<?php } ?>
						<span class="sub-name"><?php echo htmlspecialchars($curInfo['name'] ?? $code, ENT_QUOTES, 'UTF-8');?></span>
						<?php if (isset($curInfo['rate'])) { ?>
						<span class="sub-desc">(汇率: <?php echo htmlspecialchars((string)$curInfo['rate'], ENT_QUOTES, 'UTF-8');?>)</span>
						<?php } ?>
					</div>
					<button class="btn btn-danger btn-icon delete" type="button" title="移除货币" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-trash-can"></i>
					</button>
				</div>
				<div class="sub-card-bottom">
					<div class="sort-input-group">
						<span class="sort-label">排序</span>
						<input type="number" class="form-control sort-input" name="sort" value="<?php echo (int)$value['sort'];?>">
					</div>
					<div class="sort-btn-group">
						<button type="button" class="btn btn-default btn-icon sort-cur-btn" data-type="top" title="置顶" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $curIdx === 1 ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-cur-btn" data-type="up" title="上移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $curIdx === 1 ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-cur-btn" data-type="down" title="下移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $curIdx === $curTotal ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-icon sort-cur-btn" data-type="bottom" title="置底" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $curIdx === $curTotal ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>
			<?php } } ?>
		</div>
	</div>

	<!-- SEO 关键词与描述配置 -->
	<div class="section-card">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-magnifying-glass-chart"></i> SEO 关键词与站点描述</div>
		</div>
		<div class="section-body text-content">
			<div class="seo-block">
				<div class="seo-header item" data-id="keyword">
					<span class="seo-title">站点关键词 (Keywords)</span>
					<button class="btn btn-primary btn-xs lang-config-btn" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-globe"></i> 多语言配置
					</button>
				</div>
				<div class="seo-body">
					<textarea class="form-control" name="keyword" rows="3" placeholder="输入默认站点关键词 (失去焦点自动保存)"><?php echo htmlspecialchars($site['keyword'] ?? '', ENT_QUOTES, 'UTF-8');?></textarea>
				</div>
			</div>

			<div class="seo-block">
				<div class="seo-header item" data-id="description">
					<span class="seo-title">站点描述 (Description)</span>
					<button class="btn btn-primary btn-xs lang-config-btn" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
						<i class="fa fa-globe"></i> 多语言配置
					</button>
				</div>
				<div class="seo-body">
					<textarea class="form-control" name="description" rows="4" placeholder="输入默认站点描述 (失去焦点自动保存)"><?php echo htmlspecialchars($site['description'] ?? '', ENT_QUOTES, 'UTF-8');?></textarea>
				</div>
			</div>
		</div>
	</div>

	<!-- 已关联支付方式 -->
	<div class="section-card">
		<div class="section-header">
			<div class="section-title"><i class="fa fa-credit-card"></i> 已关联支付方式</div>
			<a href="<?php echo adminUrl('payment/index');?>" class="link-more">前往支付管理 <i class="fa fa-angle-right"></i></a>
		</div>
		<div class="section-body">
			<?php if (empty($paymentUsed)) { ?>
			<div class="mini-empty">暂未关联支付方式</div>
			<?php } else { foreach ($paymentUsed as $pay) {
				$typeName = $paymentTypeMap[$pay['type']] ?? ('类型#' . $pay['type']);
				$accInfo = $paymentAccountMap[$pay['payment_id']] ?? [];
			?>
			<div class="pay-item-row">
				<div class="pay-item-left">
					<span class="badge-idx">#<?php echo (int)$pay['item_id'];?></span>
					<span class="pay-type-tag"><?php echo htmlspecialchars($typeName, ENT_QUOTES, 'UTF-8');?></span>
					<span class="pay-acc-name"><?php echo htmlspecialchars($accInfo['name'] ?? ('账号#' . $pay['payment_id']), ENT_QUOTES, 'UTF-8');?></span>
					<?php if (!empty($accInfo['is_sandbox'])) { ?>
					<span class="sandbox-pill">沙盒</span>
					<?php } ?>
				</div>
				<div class="pay-item-right">
					<?php if (!empty($pay['status'])) { ?>
					<span class="status-pill on">启用</span>
					<?php } else { ?>
					<span class="status-pill off">停用</span>
					<?php } ?>
				</div>
			</div>
			<?php } } ?>
		</div>
	</div>

	<!-- 基础字段修改弹窗 -->
	<div id="field-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">修改站点配置</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form method="post">
					<input type="hidden" name="id" value="<?php echo (int)$id;?>">
					<input type="hidden" name="opn" value="modifySite">
					<div class="form-group">
						<label class="form-label" id="field-label">字段：</label>
						<input class="form-control" id="field-input" name="name" value="" autocomplete="off">
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>

	<!-- 管理站点语言弹窗 -->
	<div id="language-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-language"></i> 管理站点语言</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="manage-list">
					<?php if (!empty($languageList)) { foreach ($languageList as $code => $lan) {
						$inUse = in_array($code, $usedLanCodes, true);
					?>
					<div class="manage-item" data-code="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?>">
						<div class="manage-item-info">
							<code class="code-pill"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?></code>
							<span class="manage-item-name"><?php echo htmlspecialchars(($lan['name2'] ?? '') . ' (' . ($lan['name'] ?? '') . ')', ENT_QUOTES, 'UTF-8');?></span>
						</div>
						<?php if ($inUse) { ?>
						<button type="button" class="btn btn-danger btn-xs delete" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">移除</button>
						<?php } else { ?>
						<button type="button" class="btn btn-primary btn-xs add" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">添加</button>
						<?php } ?>
					</div>
					<?php } } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- 管理站点货币弹窗 -->
	<div id="currency-info" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title"><i class="fa fa-coins"></i> 管理站点货币</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<div class="manage-list">
					<?php if (!empty($currencyList)) { foreach ($currencyList as $code => $cur) {
						$inUse = in_array($code, $usedCurCodes, true);
					?>
					<div class="manage-item" data-code="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?>">
						<div class="manage-item-info">
							<code class="code-pill"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8');?></code>
							<b class="curr-symbol"><?php echo htmlspecialchars($cur['symbol'] ?? '', ENT_QUOTES, 'UTF-8');?></b>
							<span class="manage-item-name"><?php echo htmlspecialchars($cur['name'] ?? '', ENT_QUOTES, 'UTF-8');?></span>
						</div>
						<?php if ($inUse) { ?>
						<button type="button" class="btn btn-danger btn-xs delete" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">移除</button>
						<?php } else { ?>
						<button type="button" class="btn btn-primary btn-xs add" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">添加</button>
						<?php } ?>
					</div>
					<?php } } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- 多语言文本配置弹窗 -->
	<div id="dealbox-language" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">多语言配置</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form method="post">
					<input type="hidden" name="site_id" value="<?php echo (int)$id;?>">
					<input type="hidden" name="type" value="">
					<input type="hidden" name="source_text" value="">
					<input type="hidden" name="opn" value="editLanguage">
					<div class="translate-bar">
						<span>多语言文本列表</span>
						<button type="button" class="btn btn-info btn-xs auto-translate"><i class="fa fa-wand-magic-sparkles"></i> 一键智能翻译空项</button>
					</div>
					<div id="lang-fields-body" class="lang-fields-list"></div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">保存多语言</button>
				</form>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
