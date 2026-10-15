<?php
$groupIconMap = [
	'overview'   => 'fa-gauge',
	'management' => 'fa-users',
	'finance'    => 'fa-coins',
	'product'    => 'fa-box',
	'order'      => 'fa-clipboard-list',
	'system'     => 'fa-gear',
	'0'          => 'fa-gauge',
	'53000'      => 'fa-clipboard-list',
	'53001'      => 'fa-chart-line',
	'53002'      => 'fa-box',
	'53003'      => 'fa-tags',
	'53004'      => 'fa-truck',
	'53005'      => 'fa-coins',
	'53006'      => 'fa-wallet',
	'53007'      => 'fa-shield-halved',
	'53008'      => 'fa-gear',
	'53009'      => 'fa-sliders',
	'53054'      => 'fa-bullhorn',
	'53057'      => 'fa-comments',
	'53058'      => 'fa-envelope',
	'53063'      => 'fa-users',
	'680201'     => 'fa-globe',
	'680202'     => 'fa-server',
];
$iconOptions = [
	'fa-gauge'          => '仪表盘 / 概览 (fa-gauge)',
	'fa-users'          => '人员 / 团队 (fa-users)',
	'fa-sitemap'        => '部门 / 架构 (fa-sitemap)',
	'fa-coins'          => '财务 / 货币 (fa-coins)',
	'fa-wallet'         => '钱包 / 支付 (fa-wallet)',
	'fa-box'            => '产品 / 商品 (fa-box)',
	'fa-tags'           => '分类 / 标签 (fa-tags)',
	'fa-clipboard-list' => '订单 / 列表 (fa-clipboard-list)',
	'fa-truck'          => '物流 / 配送 (fa-truck)',
	'fa-chart-line'     => '统计 / 报表 (fa-chart-line)',
	'fa-gear'           => '系统 / 设置 (fa-gear)',
	'fa-sliders'        => '参数 / 配置 (fa-sliders)',
	'fa-shield-halved'  => '安全 / 权限 (fa-shield-halved)',
	'fa-globe'          => '站点 / 全球 (fa-globe)',
	'fa-server'         => '服务器 / 节点 (fa-server)',
	'fa-bullhorn'       => '公告 / 推广 (fa-bullhorn)',
	'fa-comments'       => '消息 / 客服 (fa-comments)',
	'fa-envelope'       => '邮件 / 通知 (fa-envelope)',
	'fa-bookmark'       => '子模块书签 (fa-bookmark)',
	'fa-cube'           => '通用模块 (fa-cube)',
];
$groupCount = count($list ?? []);
$sonControllerCount = 0;
foreach ($list ?? [] as $item) {
	$sonControllerCount += count($item['son'] ?? []);
}
?>
<div id="function-list" class="controller-page" data-url="<?php echo adminUrl('controller/index');?>">
	<!-- 顶部栏 -->
	<div class="page-header-bar">
		<div class="header-title">
			<i class="fa fa-cubes"></i>
			<span>功能管理</span>
			<span class="total-badge"><?php echo $groupCount;?> 组 / <?php echo $sonControllerCount;?> 项</span>
		</div>
		<div class="header-actions">
			<button type="button" class="btn btn-default btn-xs toggle-all-btn" title="全部折叠/展开">
				<i class="fa fa-compress"></i>
			</button>
			<button type="button" class="btn btn-success btn-xs add-btn">
				<i class="fa fa-plus"></i> 新增分组
			</button>
		</div>
	</div>

	<!-- 功能卡片列表 -->
	<div class="card-list" id="data-list">
		<?php if (empty($list)) { ?>
			<div class="empty-state">
				<i class="fa fa-folder-open"></i>
				<p>暂无功能模块数据</p>
			</div>
		<?php } else { ?>
			<?php foreach (array_values($list) as $gIdx => $value) {
				$conId = (int)$value['con_id'];
				$rawIcon = (string)($value['icon'] ?? '');
				$faIcon = $groupIconMap[$value['value']] ?? ($groupIconMap[$rawIcon] ?? (strpos($rawIcon, 'fa-') === 0 ? $rawIcon : 'fa-cube'));
				$sonList = array_values($value['son'] ?? []);
				$sonCount = count($sonList);
				$isFirstGroup = ($gIdx === 0);
				$isLastGroup = ($gIdx === $groupCount - 1);
				$parentRowData = [
					'con_id'      => $conId,
					'parent_id'   => 0,
					'parent_name' => '',
					'name'        => $value['name'],
					'value'       => $value['value'],
					'icon'        => $faIcon,
					'sort'        => (int)$value['sort'],
				];
			?>
			<div class="func-card parent-card item"
				data-id="<?php echo $conId;?>"
				data-pid="0"
				data-info="<?php echo htmlspecialchars(json_encode($parentRowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<div class="card-top">
					<div class="func-info">
						<i class="fa fa-caret-down toggle-icon"></i>
						<span class="func-icon-box"><i class="fa <?php echo htmlspecialchars($faIcon, ENT_QUOTES, 'UTF-8');?>"></i></span>
						<div class="func-title-wrap">
							<div class="title-line">
								<span class="id-badge">#<?php echo $conId;?></span>
								<strong class="cate_name"><?php echo htmlspecialchars((string)$value['name'], ENT_QUOTES, 'UTF-8');?></strong>
							</div>
							<div class="meta-line">
								<code class="route-code"><?php echo htmlspecialchars((string)$value['value'], ENT_QUOTES, 'UTF-8');?></code>
								<span class="group-badge">一级分组 (<?php echo $sonCount;?>)</span>
							</div>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-primary btn-xs modify" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button type="button" class="btn btn-success btn-xs add" title="新增子功能" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-plus"></i></button>
						<?php if ($sonCount === 0) { ?>
						<button type="button" class="btn btn-danger btn-xs delete" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
						<?php } ?>
					</div>
				</div>
				<div class="card-bottom">
					<span class="sort-hint">分组排序</span>
					<div class="sort-group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirstGroup ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirstGroup ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLastGroup ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLastGroup ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>

			<?php foreach ($sonList as $sIdx => $sv) {
				$svId = (int)$sv['con_id'];
				$svPid = (int)$sv['parent_id'];
				$svRawIcon = (string)($sv['icon'] ?? '');
				$svFaIcon = ($svRawIcon !== '' && $svRawIcon !== '0')
					? ($groupIconMap[$svRawIcon] ?? (strpos($svRawIcon, 'fa-') === 0 ? $svRawIcon : 'fa-bookmark'))
					: 'fa-bookmark';
				$isFirstSon = ($sIdx === 0);
				$isLastSon = ($sIdx === $sonCount - 1);
				$childRowData = [
					'con_id'      => $svId,
					'parent_id'   => $svPid,
					'parent_name' => $value['name'],
					'name'        => $sv['name'],
					'value'       => $sv['value'],
					'icon'        => $svFaIcon,
					'sort'        => (int)$sv['sort'],
				];
			?>
			<div class="func-card child-card item"
				data-id="<?php echo $svId;?>"
				data-pid="<?php echo $svPid;?>"
				data-info="<?php echo htmlspecialchars(json_encode($childRowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<div class="card-top">
					<div class="func-info">
						<span class="child-icon-box"><i class="fa <?php echo htmlspecialchars($svFaIcon, ENT_QUOTES, 'UTF-8');?>"></i></span>
						<div class="func-title-wrap">
							<div class="title-line">
								<span class="id-badge">#<?php echo $svId;?></span>
								<strong class="cate_name"><?php echo htmlspecialchars((string)$sv['name'], ENT_QUOTES, 'UTF-8');?></strong>
							</div>
							<div class="meta-line">
								<code class="route-code"><?php echo htmlspecialchars((string)$sv['value'], ENT_QUOTES, 'UTF-8');?></code>
								<span class="child-badge">子功能</span>
							</div>
						</div>
					</div>
					<div class="card-actions">
						<button type="button" class="btn btn-primary btn-xs modify" title="修改" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-pen-to-square"></i></button>
						<button type="button" class="btn btn-danger btn-xs delete" title="删除" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-trash-can"></i></button>
					</div>
				</div>
				<div class="card-bottom">
					<span class="sort-hint">子功能排序</span>
					<div class="sort-group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirstSon ? 'disabled' : '';?>><i class="fa fa-angles-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isFirstSon ? 'disabled' : '';?>><i class="fa fa-angle-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLastSon ? 'disabled' : '';?>><i class="fa fa-angle-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" <?php echo $isLastSon ? 'disabled' : '';?>><i class="fa fa-angles-down"></i></button>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
		<?php } ?>
	</div>

	<!-- 新增/编辑功能弹窗 -->
	<div id="dealbox" class="modal-mask" style="display: none;">
		<div class="modal-panel">
			<div class="modal-header">
				<h4 class="dealbox-title">功能管理</h4>
				<span class="modal-close"><i class="fa fa-xmark"></i></span>
			</div>
			<div class="modal-body">
				<form id="func-form" onsubmit="return false;">
					<input type="hidden" name="con_id" value="0">
					<input type="hidden" name="parent_id" value="0">
					<input type="hidden" name="sort" value="0">
					<input type="hidden" name="opn" value="editInfo">

					<div class="form-group parent-group" style="display: none;">
						<label class="form-label">所属分组</label>
						<input type="text" class="form-control" name="parent_name" readonly>
					</div>
					<div class="form-group">
						<label class="form-label">功能名称 <span class="required">*</span></label>
						<input type="text" class="form-control" name="name" required="required" autocomplete="off" placeholder="请输入显示名称" maxlength="32">
					</div>
					<div class="form-group">
						<label class="form-label">控制器路由 <span class="required">*</span></label>
						<input type="text" class="form-control" name="value" required="required" autocomplete="off" placeholder="例如：product 或 index/statInfo" maxlength="32">
					</div>
					<div class="form-group">
						<label class="form-label">
							<span>图标</span>
							<span class="icon-preview-badge"><i id="icon-preview" class="fa fa-cube"></i></span>
						</label>
						<select class="form-control" name="icon">
							<?php foreach ($iconOptions as $iconKey => $iconLabel) { ?>
							<option value="<?php echo htmlspecialchars($iconKey, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($iconLabel, ENT_QUOTES, 'UTF-8');?></option>
							<?php } ?>
						</select>
					</div>
					<button type="button" class="btn btn-primary btn-block save-btn" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 保存中...">确认保存</button>
				</form>
			</div>
		</div>
	</div>
</div>
