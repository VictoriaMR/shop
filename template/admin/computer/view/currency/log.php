<div class="container-fluid" id="currency-log-page">
    <form action="<?php echo adminUrl('currency/log');?>" method="get" class="form-inline">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                    <select name="code" class="form-control">
                        <option value="">全部货币</option>
                        <?php foreach ($currencyList as $item) { ?>
                        <option value="<?php echo htmlspecialchars($item['code']);?>" <?php echo ($search['code'] ?? '') === $item['code'] ? 'selected' : '';?>>
                            <?php echo htmlspecialchars($item['code']);?> - <?php echo htmlspecialchars($item['name']);?> (<?php echo htmlspecialchars($item['symbol']);?>)
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                    <input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['stime'] ?? '');?>" name="stime" placeholder="开始日期" autocomplete="off" style="width: 130px;">
                    <span class="text-muted">-</span>
                    <input class="form-control form_datetime" type="text" value="<?php echo htmlspecialchars($search['etime'] ?? '');?>" name="etime" placeholder="结束日期" autocomplete="off" style="width: 130px;">
                </div>
                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                    <button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
                </div>
            </div>
        </div>
    </form>

    <table class="table table-hover table-middle mt6" id="log-list">
        <thead>
            <tr>
                <th width="80">ID</th>
                <th width="220">货币信息</th>
                <th width="150">变动前汇率</th>
                <th width="150">变动后汇率</th>
                <th width="220">变动差值 / 趋势</th>
                <th width="180">记录时间</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="6">
                    <div class="tc orange">
                        <i class="glyphicon glyphicon-info-sign"></i> 暂无汇率变更日志数据
                    </div>
                </td>
            </tr>
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
            <tr>
                <td><strong><?php echo $value['log_id'];?></strong></td>
                <td>
                    <span class="label label-primary" style="font-family: monospace; font-size: 13px; font-weight: 600; padding: 2px 7px; letter-spacing: 0.5px;">
                        <?php echo htmlspecialchars($cCode);?>
                    </span>
                    <strong style="margin-left: 6px;"><?php echo htmlspecialchars($curr['name']);?></strong>
                    <?php if (!empty($curr['symbol'])){ ?>
                    <span class="text-muted" style="margin-left: 4px; font-weight: bold;"><?php echo htmlspecialchars($curr['symbol']);?></span>
                    <?php } ?>
                </td>
                <td>
                    <span style="font-family: monospace; font-size: 13px; color: #555;"><?php echo htmlspecialchars($value['old_rate']);?></span>
                </td>
                <td>
                    <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #111;"><?php echo htmlspecialchars($value['new_rate']);?></span>
                </td>
                <td>
                    <?php if ($diff > 0){ ?>
                    <span class="text-success" style="font-family: monospace; font-weight: 600;">
                        <i class="glyphicon glyphicon-arrow-up"></i> +<?php echo number_format($diff, 6, '.', '');?>
                        <span class="f12" style="font-weight: normal;">(+<?php echo $percentStr;?>%)</span>
                    </span>
                    <?php } elseif ($diff < 0){ ?>
                    <span class="text-danger" style="font-family: monospace; font-weight: 600;">
                        <i class="glyphicon glyphicon-arrow-down"></i> <?php echo number_format($diff, 6, '.', '');?>
                        <span class="f12" style="font-weight: normal;">(<?php echo $percentStr;?>%)</span>
                    </span>
                    <?php } else { ?>
                    <span class="text-muted" style="font-family: monospace;">
                        <i class="glyphicon glyphicon-minus"></i> 0.000000
                        <span class="f12">(0.00%)</span>
                    </span>
                    <?php } ?>
                </td>
                <td>
                    <span class="text-muted"><i class="glyphicon glyphicon-time f12"></i> <?php echo $value['add_time'];?></span>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <div class="row mt12">
        <div class="col-xs-12">
            <?php echo page($search['size'], $total);?>
        </div>
    </div>
</div>
