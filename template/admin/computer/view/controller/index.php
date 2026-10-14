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
<div class="container-fluid" id="function-list">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-inline" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 6px;">
				<div class="form-group" style="margin-bottom: 6px;">
					<span class="text-muted">
						共 <strong><?php echo $groupCount;?></strong> 个一级功能分组，<strong><?php echo $sonControllerCount;?></strong> 个子功能模块
					</span>
				</div>
				<div class="form-group" style="margin-bottom: 6px;">
					<button class="btn btn-default btn-sm toggle-all-btn" type="button" style="margin-right: 6px;">
						<i class="fa fa-arrows-alt"></i> 全部折叠/展开
					</button>
					<button class="btn btn-success btn-sm add-btn" type="button">
						<i class="fa fa-plus-circle"></i> 新增一级分组
					</button>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="80">ID</th>
				<th width="260">功能名称 / 层级</th>
				<th width="220">控制器 / 标识</th>
				<th width="140">类型 / 子模块</th>
				<th width="130" class="tc">排序</th>
				<th width="220">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="6">
					<div class="tc orange"><i class="fa fa-info-circle"></i> 暂无功能数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach (array_values($list) as $gIdx => $value) {
				$rawIcon = (string)($value['icon'] ?? '');
				$faIcon = $groupIconMap[$value['value']] ?? ($groupIconMap[$rawIcon] ?? (strpos($rawIcon, 'fa-') === 0 ? $rawIcon : 'fa-cube'));
				$sonList = array_values($value['son'] ?? []);
				$sonCount = count($sonList);
				$isFirstGroup = ($gIdx === 0);
				$isLastGroup = ($gIdx === $groupCount - 1);
				$parentRowData = [
					'con_id'      => (int)$value['con_id'],
					'parent_id'   => 0,
					'parent_name' => '',
					'name'        => $value['name'],
					'value'       => $value['value'],
					'icon'        => $faIcon,
					'sort'        => (int)$value['sort'],
				];
			?>
			<tr class="item parent-row info" data-id="<?php echo (int)$value['con_id'];?>" data-pid="0" data-info="<?php echo htmlspecialchars(json_encode($parentRowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>" style="cursor: pointer;" title="点击折叠/展开子功能">
				<td><strong><?php echo (int)$value['con_id'];?></strong></td>
				<td>
					<div style="display: flex; align-items: center;">
						<i class="fa fa-caret-down toggle-icon text-muted" style="width: 14px; margin-right: 4px; transition: transform 0.2s;"></i>
						<i class="fa <?php echo htmlspecialchars($faIcon, ENT_QUOTES, 'UTF-8');?>" style="width: 20px; text-align: center; margin-right: 6px; color: #e7502b; font-size: 14px;"></i>
						<strong class="cate_name" style="color: #0f172a; font-size: 13px;"><?php echo htmlspecialchars($value['name']);?></strong>
					</div>
				</td>
				<td>
					<code style="color: #334155; background: #f1f5f9; padding: 2px 6px; border-radius: 3px;"><?php echo htmlspecialchars($value['value']);?></code>
				</td>
				<td>
					<span class="label label-primary" style="font-weight: normal;">一级分组 (<?php echo $sonCount;?>)</span>
				</td>
				<td class="tc">
					<div class="btn-group btn-group-xs" role="group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" <?php echo $isFirstGroup ? 'disabled' : '';?>><i class="fa fa-angle-double-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" <?php echo $isFirstGroup ? 'disabled' : '';?>><i class="fa fa-arrow-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" <?php echo $isLastGroup ? 'disabled' : '';?>><i class="fa fa-arrow-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" <?php echo $isLastGroup ? 'disabled' : '';?>><i class="fa fa-angle-double-down"></i></button>
					</div>
				</td>
				<td style="white-space: nowrap;">
					<button type="button" class="btn btn-primary btn-xs modify"><i class="fa fa-edit"></i> 修改</button>
					<button type="button" class="btn btn-success btn-xs ml4 add"><i class="fa fa-plus"></i> 子功能</button>
					<?php if ($sonCount === 0){ ?>
					<button type="button" class="btn btn-danger btn-xs ml4 delete"><i class="fa fa-trash"></i> 删除</button>
					<?php } ?>
				</td>
			</tr>
			<?php foreach ($sonList as $sIdx => $sv) {
				$svRawIcon = (string)($sv['icon'] ?? '');
				$svFaIcon = ($svRawIcon !== '' && $svRawIcon !== '0')
					? ($groupIconMap[$svRawIcon] ?? (strpos($svRawIcon, 'fa-') === 0 ? $svRawIcon : 'fa-bookmark'))
					: 'fa-bookmark';
				$isFirstSon = ($sIdx === 0);
				$isLastSon = ($sIdx === $sonCount - 1);
				$childRowData = [
					'con_id'      => (int)$sv['con_id'],
					'parent_id'   => (int)$sv['parent_id'],
					'parent_name' => $value['name'],
					'name'        => $sv['name'],
					'value'       => $sv['value'],
					'icon'        => $svFaIcon,
					'sort'        => (int)$sv['sort'],
				];
			?>
			<tr class="item child-row" data-id="<?php echo (int)$sv['con_id'];?>" data-pid="<?php echo (int)$sv['parent_id'];?>" data-info="<?php echo htmlspecialchars(json_encode($childRowData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<td class="text-muted"><?php echo (int)$sv['con_id'];?></td>
				<td>
					<div style="padding-left: 26px; display: flex; align-items: center;">
						<span class="text-muted" style="margin-right: 6px; font-family: monospace; color: #94a3b8;">└─</span>
						<i class="fa <?php echo htmlspecialchars($svFaIcon, ENT_QUOTES, 'UTF-8');?> text-muted" style="width: 18px; text-align: center; margin-right: 6px;"></i>
						<span class="cate_name" style="color: #1e293b;"><?php echo htmlspecialchars($sv['name']);?></span>
					</div>
				</td>
				<td>
					<code style="color: #0369a1; background: #f0f9ff; padding: 2px 6px; border-radius: 3px;"><?php echo htmlspecialchars($sv['value']);?></code>
				</td>
				<td>
					<span class="label label-default" style="font-weight: normal; background: #94a3b8;">子功能</span>
				</td>
				<td class="tc">
					<div class="btn-group btn-group-xs" role="group">
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="置顶" <?php echo $isFirstSon ? 'disabled' : '';?>><i class="fa fa-angle-double-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上移" <?php echo $isFirstSon ? 'disabled' : '';?>><i class="fa fa-arrow-up"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下移" <?php echo $isLastSon ? 'disabled' : '';?>><i class="fa fa-arrow-down"></i></button>
						<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="置底" <?php echo $isLastSon ? 'disabled' : '';?>><i class="fa fa-angle-double-down"></i></button>
					</div>
				</td>
				<td style="white-space: nowrap;">
					<button type="button" class="btn btn-primary btn-xs modify"><i class="fa fa-edit"></i> 修改</button>
					<button type="button" class="btn btn-danger btn-xs ml4 delete"><i class="fa fa-trash"></i> 删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>

<!-- 功能管理弹窗 -->
<div id="dealbox" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow" style="width: 460px;">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">功能管理</div>
			<input type="hidden" name="con_id" value="0">
			<input type="hidden" name="parent_id" value="0">
			<input type="hidden" name="sort" value="0">
			<input type="hidden" name="opn" value="editInfo">

			<div class="input-group parent-group" style="display: none;">
				<div class="input-group-addon"><span>所属分组：</span></div>
				<input type="text" class="form-control" name="parent_name" readonly style="background: #f8fafc;">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>功能名称：</span></div>
				<input type="text" class="form-control" name="name" required="required" autocomplete="off" placeholder="请输入显示名称" maxlength="32">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>控制器路由：</span></div>
				<input type="text" class="form-control" name="value" required="required" autocomplete="off" placeholder="例如：product 或 index/statInfo" maxlength="32">
			</div>
			<div class="input-group">
				<div class="input-group-addon">
					<span>图标：<i id="icon-preview" class="fa fa-cube" style="margin-left: 4px; color: #e7502b;"></i></span>
				</div>
				<select class="form-control" name="icon">
					<?php foreach ($iconOptions as $iconKey => $iconLabel){ ?>
					<option value="<?php echo $iconKey;?>"><?php echo $iconLabel;?></option>
					<?php } ?>
				</select>
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn mt10">确认保存</button>
		</form>
	</div>
</div>