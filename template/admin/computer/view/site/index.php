<?php
$status = (int)($search['status'] ?? -1);
$keyword = $search['keyword'] ?? '';
?>
<div class="container-fluid" id="site-page">
	<form action="<?php echo adminUrl('site/index');?>" method="get" class="form-inline">
		<input type="hidden" name="status" value="<?php echo $status;?>">
		<div class="row">
			<div class="col-xs-12">
				<div class="btn-group left status-filter" role="group">
					<button type="button" data-status="-1" class="btn <?php echo ($status === 1 || $status === 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
					<button type="button" data-status="1" class="btn <?php echo $status === 1 ? 'btn-primary' : 'btn-default';?>">已启用</button>
					<button type="button" data-status="0" class="btn <?php echo $status === 0 ? 'btn-primary' : 'btn-default';?>">未启用</button>
				</div>
				<div class="form-group ml12">
					<input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');?>" placeholder="站点名称 / 域名 / 模板路径" autocomplete="off">
				</div>
				<div class="form-group ml6">
					<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
				</div>
				<div class="form-group right">
					<button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增站点</button>
				</div>
			</div>
		</div>
	</form>

	<table class="table table-hover table-middle mt6" id="data-list">
		<thead>
			<tr>
				<th width="65">ID</th>
				<th width="190">站点名称 / 类型</th>
				<th width="200">域名 / 模板路径</th>
				<th>关联语言</th>
				<th>关联货币</th>
				<th width="160" class="tc">缓存 / 调试</th>
				<th width="80" class="tc">状态</th>
				<th width="165">时间记录</th>
				<th width="145">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="9">
					<div class="tc orange"><i class="glyphicon glyphicon-info-sign"></i> 暂无站点数据</div>
				</td>
			</tr>
			<?php } else { ?>
			<?php foreach ($list as $value) {
				$siteId = (int)$value['site_id'];
				$isAdmin = ($value['type'] === 'admin');
				$langs = !empty($value['language']) && is_array($value['language']) ? array_column($value['language'], 'name2') : [];
				$currs = !empty($value['currency']) && is_array($value['currency']) ? array_column($value['currency'], 'code') : [];
				$rowInfo = [
					'site_id'      => $siteId,
					'name'         => $value['name'] ?? '',
					'type'         => $value['type'] ?? 'home',
					'path'         => $value['path'] ?? '',
					'domain'       => $value['domain'] ?? '',
					'email'        => $value['email'] ?? '',
					'view_cache'   => (int)($value['view_cache'] ?? 0),
					'static_cache' => (int)($value['static_cache'] ?? 0),
					'debug'        => (int)($value['debug'] ?? 0),
					'status'       => (int)($value['status'] ?? 0),
				];
			?>
			<tr class="item" data-id="<?php echo $siteId;?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
				<td><strong><?php echo $siteId;?></strong></td>
				<td>
					<div>
						<strong><?php echo htmlspecialchars($value['name']);?></strong>
						<span class="label <?php echo $isAdmin ? 'label-warning' : 'label-info';?> ml2"><?php echo htmlspecialchars($value['type'] ?: 'home');?></span>
					</div>
					<?php if (!empty($value['email'])){ ?>
					<div class="text-muted f12 mt2"><i class="fa fa-envelope-o"></i> <?php echo htmlspecialchars($value['email']);?></div>
					<?php } ?>
				</td>
				<td>
					<div><code><?php echo htmlspecialchars($value['domain'] ?: '--');?></code></div>
					<div class="text-muted f12 mt2">模板: <?php echo htmlspecialchars($value['path']);?></div>
				</td>
				<td>
					<?php if (empty($langs)){ ?>
					<span class="text-muted f12">--</span>
					<?php } else { foreach ($langs as $lName) { ?>
					<span class="label label-default mr2"><?php echo htmlspecialchars($lName);?></span>
					<?php } } ?>
				</td>
				<td>
					<?php if (empty($currs)){ ?>
					<span class="text-muted f12">--</span>
					<?php } else { foreach ($currs as $cCode) { ?>
					<span class="label label-primary mr2"><?php echo htmlspecialchars($cCode);?></span>
					<?php } } ?>
				</td>
				<td class="tc">
					<span class="label <?php echo !empty($value['view_cache']) ? 'label-success' : 'label-default';?>" title="模板缓存">视图</span>
					<span class="label <?php echo !empty($value['static_cache']) ? 'label-success' : 'label-default';?> ml2" title="静态文件缓存">静态</span>
					<span class="label <?php echo !empty($value['debug']) ? 'label-warning' : 'label-default';?> ml2" title="Debug调试">Debug</span>
				</td>
				<td class="tc">
					<div class="switch_botton site-status-switch" data-status="<?php echo (int)$value['status'];?>" title="点击切换站点状态">
						<div class="switch_status <?php echo (int)$value['status'] === 1 ? 'on' : 'off';?>"></div>
					</div>
				</td>
				<td class="f12 text-muted">
					<div>增: <?php echo $value['add_time'] ?: '--';?></div>
					<div>更: <?php echo $value['update_time'] ?: '--';?></div>
				</td>
				<td>
					<a href="<?php echo adminUrl('site/siteInfo', ['id' => $siteId]);?>" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-wrench"></i> 配置</a>
					<button class="btn btn-info btn-xs modify ml6" type="button"><i class="glyphicon glyphicon-edit"></i> 编辑</button>
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>

	<div class="row mt12">
		<div class="col-xs-12 text-muted">
			当前共 <strong><?php echo count($list ?? []);?></strong> 个站点
		</div>
	</div>
</div>

<!-- 新增/编辑站点弹窗 -->
<div id="dealbox-info" class="dealbox hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">编辑站点</div>
			<input type="hidden" name="site_id" value="0">
			<input type="hidden" name="opn" value="editSite">

			<div class="input-group">
				<div class="input-group-addon"><span>站点名称：</span></div>
				<input class="form-control" name="name" required="required" maxlength="64" placeholder="请输入站点名称" autocomplete="off">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>站点类型：</span></div>
				<select class="form-control" name="type">
					<option value="home">前台商城 (home)</option>
					<option value="admin">管理后台 (admin)</option>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>绑定域名：</span></div>
				<input class="form-control" name="domain" maxlength="64" placeholder="例如: clothestome.com" autocomplete="off">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>模板目录：</span></div>
				<input class="form-control" name="path" required="required" maxlength="32" placeholder="template 下的目录名，如 newsme" autocomplete="off">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>联系邮箱：</span></div>
				<input class="form-control" name="email" maxlength="64" placeholder="support@example.com（选填）" autocomplete="off">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>开关项：</span></div>
				<div class="form-control">
					<label class="checkbox-inline">
						<input type="checkbox" name="view_cache" value="1"> 视图缓存
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="static_cache" value="1"> 静态缓存
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="debug" value="1"> Debug模式
					</label>
				</div>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>站点状态：</span></div>
				<div class="form-control pt6">
					<div class="switch_botton modal-status-switch" data-status="1">
						<div class="switch_status on"></div>
					</div>
					<input type="hidden" name="status" value="1">
				</div>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认保存</button>
		</form>
	</div>
</div>