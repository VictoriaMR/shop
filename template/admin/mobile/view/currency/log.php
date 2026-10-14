<div class="currency-log-container" id="currency-log-page">
	<!-- 筛选卡片 -->
	<form action="<?php echo adminUrl('currency/log');?>" method="get" class="search-card">
		<div class="filter-row">
			<select name="code" class="form-control">
				<option value="">全部货币</option>
				<?php foreach ($currencyList as $item) { ?>
				<option value="<?php echo htmlspecialchars($item['code']);?>" <?php echo ($search['code'] ?? '') === $item['code'] ? 'selected' : '';?>>
					<?php echo htmlspecialchars($item['code']);?> - <?php echo htmlspecialchars($item['name']);?> (<?php echo htmlspecialchars($item['symbol']);?>)
				</option>
				<?php } ?>
			</select>
		</div>

		<div class="filter-grid">
			<div class="filter-item">
				<input type="date" class="form-control" name="stime" value="<?php echo htmlspecialchars(substr($search['stime'] ?? '', 0, 10));?>" placeholder="开始日期">
			</div>
			<div class="filter-item">
				<input type="date" class="form-control" name="etime" value="<?php echo htmlspecialchars(substr($search['etime'] ?? '', 0, 10));?>" placeholder="结束日期">
			</div>
		</div>

		<div class="filter-actions">
			<button class="btn btn-info btn-block" type="submit">
				<i class="fa fa-magnifying-glass"></i> 查询
			</button>
		</div>
	</form>

	<!-- 汇率变更日志卡片列表 -->
	<div class="log-list" id="log-list">
		<?php if (empty($list)) { ?>
		<div class="empty-state">
			<i class="fa fa-chart-line"></i>
			<p>暂无汇率变更日志数据</p>
		</div>
		<?php } else { ?>
		<?php foreach ($list as $value) {
			$cCode = $value['code'];
			$curr = $currencyMap[$cCode] ?? ['name' => $cCode, 'symbol' => ''];
			$oldRate = (float)$value['old_rate'];
			$newRate = (float)$value['new_rate'];
			$diff = $newRate - $oldRate;
			$percentVal = ($oldRate > 0) ? (($diff / $oldRate) * 100) : 0;
			$percentStr = (abs($percentVal) < 0.01 && abs($percentVal) > 0) ? sprintf('%.4f', $percentVal) : sprintf('%.2f', $percentVal);
		?>
		<div class="log-card">
			<div class="log-header">
				<div class="currency-info">
					<span class="code-badge"><?php echo htmlspecialchars($cCode);?></span>
					<span class="currency-name"><?php echo htmlspecialchars($curr['name']);?></span>
					<?php if (!empty($curr['symbol'])) { ?>
					<span class="currency-symbol"><?php echo htmlspecialchars($curr['symbol']);?></span>
					<?php } ?>
				</div>
				<span class="log-id-badge">#<?php echo (int)$value['log_id'];?></span>
			</div>

			<div class="rate-compare-box">
				<div class="rate-col">
					<span class="rate-label">变动前汇率</span>
					<span class="rate-old"><?php echo htmlspecialchars($value['old_rate']);?></span>
				</div>
				<div class="rate-arrow">
					<i class="fa fa-arrow-right"></i>
				</div>
				<div class="rate-col">
					<span class="rate-label">变动后汇率</span>
					<span class="rate-new"><?php echo htmlspecialchars($value['new_rate']);?></span>
				</div>
			</div>

			<div class="log-footer">
				<div class="trend-wrap">
					<?php if ($diff > 0) { ?>
					<span class="trend-badge up">
						<i class="fa fa-arrow-trend-up"></i>
						+<?php echo number_format($diff, 6, '.', '');?> (+<?php echo $percentStr;?>%)
					</span>
					<?php } elseif ($diff < 0) { ?>
					<span class="trend-badge down">
						<i class="fa fa-arrow-trend-down"></i>
						<?php echo number_format($diff, 6, '.', '');?> (<?php echo $percentStr;?>%)
					</span>
					<?php } else { ?>
					<span class="trend-badge flat">
						<i class="fa fa-minus"></i> 0.000000 (0.00%)
					</span>
					<?php } ?>
				</div>
				<div class="log-time">
					<i class="fa fa-clock"></i>
					<span><?php echo htmlspecialchars($value['add_time'] ?? '-');?></span>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>

	<!-- 分页 -->
	<div class="mt12">
		<?php echo page($search['size'] ?? 20, $total);?>
	</div>
</div>
