<div class="container-fluid" id="site-page" data-id="<?php echo (int)$id;?>" data-name="<?php echo htmlspecialchars($site['name'] ?? '');?>">
	<?php if (empty($site)) { ?>
	<div class="panel panel-default">
		<div class="panel-body tc orange">
			<i class="glyphicon glyphicon-info-sign"></i> 未找到该站点数据，请从 <a href="<?php echo adminUrl('site/index');?>">站点列表</a> 选择站点进入配置。
		</div>
	</div>
	<?php } else {
		$usedLanCodes = !empty($siteLanguage) ? array_column($siteLanguage, 'code') : [];
		$usedCurCodes = !empty($siteCurrency) ? array_column($siteCurrency, 'code') : [];
	?>
	<div>
		<a href="<?php echo adminUrl('site/index');?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-menu-left"></i> 返回站点列表</a>
		<span class="f16 ml12 mt4"><b>#<?php echo (int)$site['site_id'];?> <?php echo htmlspecialchars($site['name']);?></b></span>
		<span class="label <?php echo ($site['type'] ?? '') === 'admin' ? 'label-warning' : 'label-info';?> ml6"><?php echo htmlspecialchars($site['type'] ?: 'home');?></span>
	</div>

	<div class="row mt10">
		<div class="col-md-7">
			<!-- 基础信息与运行开关 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>基础信息与运行状态</b>
					<span class="right text-muted f12">更新时间：<?php echo htmlspecialchars($site['update_time'] ?: ($site['add_time'] ?: '--'));?></span>
				</div>
				<table class="table table-bordered table-middle">
					<tbody>
						<tr>
							<th width="115" class="active">站点名称</th>
							<td>
								<span id="site-name" class="pointer" title="点击修改站点名称">
									<b><?php echo htmlspecialchars($site['name']);?></b>
									<i class="glyphicon glyphicon-edit text-muted ml6"></i>
								</span>
							</td>
							<th width="115" class="active">模板目录</th>
							<td>
								<span id="site-path" class="pointer" title="点击修改模板目录">
									<code><?php echo htmlspecialchars($site['path']);?></code>
									<i class="glyphicon glyphicon-edit text-muted ml6"></i>
								</span>
							</td>
						</tr>
						<tr>
							<th class="active">绑定域名</th>
							<td>
								<span id="site-domain-edit" class="pointer" title="点击修改绑定域名">
									<code><?php echo htmlspecialchars($site['domain'] ?: '--');?></code>
									<i class="glyphicon glyphicon-edit text-muted ml6"></i>
								</span>
							</td>
							<th class="active">联系邮箱</th>
							<td>
								<span id="site-email-edit" class="pointer" title="点击修改联系邮箱">
									<span><?php echo htmlspecialchars($site['email'] ?: '--');?></span>
									<i class="glyphicon glyphicon-edit text-muted ml6"></i>
								</span>
							</td>
						</tr>
						<tr>
							<th class="active">站点状态</th>
							<td>
								<div class="switch_botton site-switch" data-field="status" data-status="<?php echo (int)$site['status'];?>">
									<div class="switch_status <?php echo (int)$site['status'] === 1 ? 'on' : 'off';?>"></div>
								</div>
							</td>
							<th class="active">Debug调试</th>
							<td>
								<div class="switch_botton site-switch" data-field="debug" data-status="<?php echo (int)$site['debug'];?>">
									<div class="switch_status <?php echo (int)$site['debug'] === 1 ? 'on' : 'off';?>"></div>
								</div>
							</td>
						</tr>
						<tr>
							<th class="active">视图缓存</th>
							<td>
								<div class="switch_botton site-switch" data-field="view_cache" data-status="<?php echo (int)$site['view_cache'];?>">
									<div class="switch_status <?php echo (int)$site['view_cache'] === 1 ? 'on' : 'off';?>"></div>
								</div>
							</td>
							<th class="active">静态文件缓存</th>
							<td>
								<div class="switch_botton site-switch" data-field="static_cache" data-status="<?php echo (int)$site['static_cache'];?>">
									<div class="switch_status <?php echo (int)$site['static_cache'] === 1 ? 'on' : 'off';?>"></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- 站点多语言配置 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>站点语言配置</b>
					<span class="text-muted f12 ml6">(排在第一位的语言为默认语言)</span>
					<button class="btn btn-primary btn-xs right" id="add-language" type="button"><i class="glyphicon glyphicon-plus"></i> 管理语言</button>
				</div>
				<table class="table table-hover table-middle" id="language-table">
					<thead>
						<tr>
							<th width="65">序号</th>
							<th width="90">代码</th>
							<th>语言名称</th>
							<th width="90">排序值</th>
							<th width="180">快捷排序</th>
							<th width="75">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($siteLanguage)) { ?>
						<tr>
							<td colspan="6" class="tc orange">暂未配置站点语言</td>
						</tr>
						<?php } else {
							$lanTotal = count($siteLanguage);
							$lanIdx = 0;
							foreach ($siteLanguage as $value) {
								$lanIdx++;
								$code = $value['code'];
								$lanInfo = $languageList[$code] ?? [];
						?>
						<tr data-id="<?php echo (int)$value['item_id'];?>" data-code="<?php echo htmlspecialchars($code);?>">
							<td>
								<?php if ($lanIdx === 1) { ?>
								<span class="label label-success">默认</span>
								<?php } else { ?>
								<?php echo $lanIdx;?>
								<?php } ?>
							</td>
							<td><code><?php echo htmlspecialchars($code);?></code></td>
							<td>
								<?php echo htmlspecialchars($lanInfo['name2'] ?? ($lanInfo['name'] ?? $code));?>
								<?php if (!empty($lanInfo['name']) && !empty($lanInfo['name2']) && $lanInfo['name'] !== $lanInfo['name2']) { ?>
								<span class="text-muted f12 ml6">(<?php echo htmlspecialchars($lanInfo['name']);?>)</span>
								<?php } ?>
							</td>
							<td>
								<input type="number" class="form-control input-sm" name="sort" value="<?php echo (int)$value['sort'];?>">
							</td>
							<td>
								<div class="btn-group btn-group-xs">
									<button type="button" class="btn btn-default sort-lan-btn" data-type="top" <?php echo $lanIdx === 1 ? 'disabled' : '';?>>置顶</button>
									<button type="button" class="btn btn-default sort-lan-btn" data-type="up" <?php echo $lanIdx === 1 ? 'disabled' : '';?>>上移</button>
									<button type="button" class="btn btn-default sort-lan-btn" data-type="down" <?php echo $lanIdx === $lanTotal ? 'disabled' : '';?>>下移</button>
									<button type="button" class="btn btn-default sort-lan-btn" data-type="bottom" <?php echo $lanIdx === $lanTotal ? 'disabled' : '';?>>置底</button>
								</div>
							</td>
							<td>
								<button class="btn btn-danger btn-xs delete" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
							</td>
						</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>

			<!-- 站点多货币配置 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>站点货币配置</b>
					<span class="text-muted f12 ml6">(排在第一位的货币为默认结算货币)</span>
					<button class="btn btn-primary btn-xs right" id="add-currency" type="button"><i class="glyphicon glyphicon-plus"></i> 管理货币</button>
				</div>
				<table class="table table-hover table-middle" id="site-currency">
					<thead>
						<tr>
							<th width="65">序号</th>
							<th width="80">代码</th>
							<th width="70">符号</th>
							<th>名称 / 汇率</th>
							<th width="90">排序值</th>
							<th width="180">快捷排序</th>
							<th width="75">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($siteCurrency)) { ?>
						<tr>
							<td colspan="7" class="tc orange">暂未配置站点货币</td>
						</tr>
						<?php } else {
							$curTotal = count($siteCurrency);
							$curIdx = 0;
							foreach ($siteCurrency as $value) {
								$curIdx++;
								$code = $value['code'];
								$curInfo = $currencyList[$code] ?? [];
						?>
						<tr data-id="<?php echo (int)$value['item_id'];?>" data-code="<?php echo htmlspecialchars($code);?>">
							<td>
								<?php if ($curIdx === 1) { ?>
								<span class="label label-success">默认</span>
								<?php } else { ?>
								<?php echo $curIdx;?>
								<?php } ?>
							</td>
							<td><code><?php echo htmlspecialchars($code);?></code></td>
							<td><b><?php echo htmlspecialchars($curInfo['symbol'] ?? '');?></b></td>
							<td>
								<?php echo htmlspecialchars($curInfo['name'] ?? $code);?>
								<?php if (isset($curInfo['rate'])) { ?>
								<span class="text-muted f12 ml6">(汇率: <?php echo htmlspecialchars($curInfo['rate']);?>)</span>
								<?php } ?>
							</td>
							<td>
								<input type="number" class="form-control input-sm" name="sort" value="<?php echo (int)$value['sort'];?>">
							</td>
							<td>
								<div class="btn-group btn-group-xs">
									<button type="button" class="btn btn-default sort-cur-btn" data-type="top" <?php echo $curIdx === 1 ? 'disabled' : '';?>>置顶</button>
									<button type="button" class="btn btn-default sort-cur-btn" data-type="up" <?php echo $curIdx === 1 ? 'disabled' : '';?>>上移</button>
									<button type="button" class="btn btn-default sort-cur-btn" data-type="down" <?php echo $curIdx === $curTotal ? 'disabled' : '';?>>下移</button>
									<button type="button" class="btn btn-default sort-cur-btn" data-type="bottom" <?php echo $curIdx === $curTotal ? 'disabled' : '';?>>置底</button>
								</div>
							</td>
							<td>
								<button class="btn btn-danger btn-xs delete" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
							</td>
						</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="col-md-5">
			<!-- SEO 关键词与描述配置 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>SEO 关键词与站点描述</b>
				</div>
				<div class="panel-body text-content">
					<div class="item mb6" data-id="keyword">
						<b>站点关键词 (Keywords)</b>
						<button class="btn btn-primary btn-xs right" type="button"><i class="glyphicon glyphicon-globe"></i> 多语言配置</button>
						<div class="clear"></div>
					</div>
					<div class="mb12">
						<textarea class="form-control" name="keyword" rows="3" placeholder="输入默认站点关键词 (失去焦点自动保存)"><?php echo htmlspecialchars($site['keyword'] ?? '');?></textarea>
					</div>

					<div class="item mb6" data-id="description">
						<b>站点描述 (Description)</b>
						<button class="btn btn-primary btn-xs right" type="button"><i class="glyphicon glyphicon-globe"></i> 多语言配置</button>
						<div class="clear"></div>
					</div>
					<div>
						<textarea class="form-control" name="description" rows="4" placeholder="输入默认站点描述 (失去焦点自动保存)"><?php echo htmlspecialchars($site['description'] ?? '');?></textarea>
					</div>
				</div>
			</div>

			<!-- 已关联支付账号概览 -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>已关联支付方式</b>
					<a href="<?php echo adminUrl('payment/index');?>" class="right f12">前往支付管理 &raquo;</a>
				</div>
				<table class="table table-hover table-middle">
					<thead>
						<tr>
							<th width="60">ID</th>
							<th>支付类型</th>
							<th>支付账号</th>
							<th width="75" class="tc">状态</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($paymentUsed)) { ?>
						<tr>
							<td colspan="4" class="tc orange">暂未关联支付方式</td>
						</tr>
						<?php } else { foreach ($paymentUsed as $pay) {
							$typeName = $paymentTypeMap[$pay['type']] ?? ('类型#' . $pay['type']);
							$accInfo = $paymentAccountMap[$pay['payment_id']] ?? [];
						?>
						<tr>
							<td><?php echo (int)$pay['item_id'];?></td>
							<td><span class="label label-info"><?php echo htmlspecialchars($typeName);?></span></td>
							<td>
								<?php echo htmlspecialchars($accInfo['name'] ?? ('账号#' . $pay['payment_id']));?>
								<?php if (!empty($accInfo['is_sandbox'])) { ?>
								<span class="label label-warning ml2">沙盒</span>
								<?php } ?>
							</td>
							<td class="tc">
								<?php if (!empty($pay['status'])) { ?>
								<span class="label label-success">启用</span>
								<?php } else { ?>
								<span class="label label-default">停用</span>
								<?php } ?>
							</td>
						</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- 基础字段修改弹窗 -->
	<div id="field-info" class="dealbox hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<form class="form-horizontal">
				<button type="button" class="close" aria-hidden="true">&times;</button>
				<div class="f24 dealbox-title">修改站点配置</div>
				<input type="hidden" name="id" value="<?php echo (int)$id;?>">
				<input type="hidden" name="opn" value="modifySite">
				<div class="input-group">
					<div class="input-group-addon"><span id="field-label">字段：</span></div>
					<input class="form-control" id="field-input" name="name" value="" autocomplete="off">
				</div>
				<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认保存</button>
			</form>
		</div>
	</div>

	<!-- 管理站点语言弹窗 -->
	<div id="language-info" class="dealbox hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">管理站点语言</div>
			<table class="table table-hover table-middle">
				<thead>
					<tr>
						<th>代码</th>
						<th>语言名称</th>
						<th width="80" class="tc">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($languageList)) { foreach ($languageList as $code => $lan) {
						$inUse = in_array($code, $usedLanCodes, true);
					?>
					<tr data-code="<?php echo htmlspecialchars($code);?>">
						<td><code><?php echo htmlspecialchars($code);?></code></td>
						<td><?php echo htmlspecialchars(($lan['name2'] ?? '') . ' (' . ($lan['name'] ?? '') . ')');?></td>
						<td class="tc">
							<?php if ($inUse) { ?>
							<button type="button" class="btn btn-danger btn-xs delete">移除</button>
							<?php } else { ?>
							<button type="button" class="btn btn-primary btn-xs add">添加</button>
							<?php } ?>
						</td>
					</tr>
					<?php } } ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- 管理站点货币弹窗 -->
	<div id="currency-info" class="dealbox hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">管理站点货币</div>
			<table class="table table-hover table-middle">
				<thead>
					<tr>
						<th>代码</th>
						<th>符号</th>
						<th>货币名称</th>
						<th width="80" class="tc">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($currencyList)) { foreach ($currencyList as $code => $cur) {
						$inUse = in_array($code, $usedCurCodes, true);
					?>
					<tr data-code="<?php echo htmlspecialchars($code);?>">
						<td><code><?php echo htmlspecialchars($code);?></code></td>
						<td><b><?php echo htmlspecialchars($cur['symbol'] ?? '');?></b></td>
						<td><?php echo htmlspecialchars($cur['name'] ?? '');?></td>
						<td class="tc">
							<?php if ($inUse) { ?>
							<button type="button" class="btn btn-danger btn-xs delete">移除</button>
							<?php } else { ?>
							<button type="button" class="btn btn-primary btn-xs add">添加</button>
							<?php } ?>
						</td>
					</tr>
					<?php } } ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- 多语言文本配置弹窗 -->
	<div id="dealbox-language" class="dealbox hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<form class="form-horizontal">
				<button type="button" class="close" aria-hidden="true">&times;</button>
				<div class="f24 dealbox-title">多语言配置</div>
				<input type="hidden" name="site_id" value="<?php echo (int)$id;?>">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="source_text" value="">
				<input type="hidden" name="opn" value="editLanguage">
				<table class="table table-bordered table-middle">
					<tbody></tbody>
				</table>
				<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">保存多语言</button>
			</form>
		</div>
	</div>
	<?php } ?>
</div>