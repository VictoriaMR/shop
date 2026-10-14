<div class="currency-container" id="currency-page">
	<!-- 筛选与操作栏 -->
	<form action="<?php echo adminUrl('currency/index');?>" method="get" class="search-card">
		<div class="toolbar-top">
			<div class="summary-badge">
				<i class="fa fa-coins"></i>
				<span>共 <strong><?php echo count($list);?></strong> 个货币</span>
			</div>
			<div class="toolbar-actions">
				<button class="btn btn-primary btn-sm" id="update-rate-btn" type="button" data-loading-text="<i class='fa fa-spinner fa-spin'></i> 同步中...">
					<i class="fa fa-arrows-rotate"></i> 更新汇率
				</button>
				<button class="btn btn-success btn-sm" id="add-data-btn" type="button">
					<i class="fa fa-plus"></i> 新增货币
				</button>
			</div>
		</div>

		<div class="search-row">
			<input type="text" class="form-control search-input" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '');?>" placeholder="货币代码 / 名称 / 符号" autocomplete="off">
			<button class="btn btn-info" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 货币卡片列表 -->
	<div class="currency-list" id="data-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-coins"></i>
			<p>暂无货币数据</p>
		</div>
		<?php } else { ?>
		<?php foreach ($list as $value) {
			$isBase = ($value['code'] === 'CNY');
			$rowInfo = [
				'id'     => $value['code'],
				'code'   => $value['code'],
				'name'   => $value['name'],
				'symbol' => $value['symbol'],
				'rate'   => $value['rate'],
			];
		?>
		<div class="currency-card item" data-id="<?php echo htmlspecialchars($value['code']);?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
			<div class="card-header">
				<div class="currency-main">
					<span class="code-badge <?php echo $isBase ? 'base' : '';?>"><?php echo htmlspecialchars($value['code']);?></span>
					<span class="currency-name"><?php echo htmlspecialchars($value['name']);?></span>
					<?php if ($isBase) { ?>
					<span class="base-tag">基准货币</span>
					<?php } ?>
				</div>
				<div class="symbol-pill"><?php echo htmlspecialchars($value['symbol']);?></div>
			</div>

			<div class="card-body">
				<div class="info-row">
					<span class="info-label"><i class="fa fa-scale-balanced"></i> 汇率 (相对CNY)</span>
					<div class="rate-box">
						<span class="rate-val"><?php echo htmlspecialchars($value['rate']);?></span>
						<?php if (!$isBase) { ?>
						<span class="rate-desc">1 <?php echo htmlspecialchars($value['code']);?> ≈ <?php echo (float)$value['rate'];?> CNY</span>
						<?php } ?>
					</div>
				</div>
				<div class="info-row">
					<span class="info-label"><i class="fa fa-clock"></i> 最后更新时间</span>
					<span class="info-val"><?php echo !empty($value['update_time']) ? htmlspecialchars($value['update_time']) : '-';?></span>
				</div>
			</div>

			<div class="card-footer">
				<button class="btn btn-xs btn-primary modify" type="button">
					<i class="fa fa-pen-to-square"></i> 编辑
				</button>
				<?php if (!$isBase) { ?>
				<button class="btn btn-xs btn-danger delete" type="button">
					<i class="fa fa-trash-can"></i> 删除
				</button>
				<?php } else { ?>
				<button class="btn btn-xs btn-default disabled" type="button" disabled title="基准货币不允许删除">
					<i class="fa fa-lock"></i> 锁定
				</button>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>

	<div class="sync-tip">
		<i class="fa fa-circle-info"></i>
		<span>提示：点击“更新汇率”可在线同步中国银行最新外汇牌价</span>
	</div>
</div>

<!-- 货币管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<input type="hidden" name="id" class="no_show" value="">
			<input type="hidden" class="no_replace" name="opn" value="editCurrencyInfo">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="dealbox-title">新增货币</div>
			<div class="input-group">
				<span class="input-group-addon"><span>货币代码</span></span>
				<input type="text" class="form-control code-upper" name="code" required="required" maxlength="3" placeholder="3位大写英文 (如: USD)" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>货币名称</span></span>
				<input type="text" class="form-control" name="name" required="required" maxlength="20" placeholder="货币名称 (如: 美元)" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>货币符号</span></span>
				<input type="text" class="form-control" name="symbol" required="required" maxlength="4" placeholder="货币符号 (如: $)" autocomplete="off">
			</div>
			<div class="input-group">
				<span class="input-group-addon"><span>货币汇率</span></span>
				<input type="number" step="0.000001" min="0" class="form-control" name="rate" required="required" placeholder="相对基准币汇率 (如: 7.175300)" autocomplete="off">
			</div>
			<div class="mt20">
				<button type="button" class="btn btn-primary btn-lg btn-block save">确认保存</button>
			</div>
		</form>
	</div>
</div>
