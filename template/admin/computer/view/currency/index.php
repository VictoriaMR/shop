<div class="container-fluid" id="currency-page">
    <form action="<?php echo adminUrl('currency/index');?>" method="get" class="form-inline">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                    <input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword ?? '');?>" placeholder="货币代码 / 名称 / 符号" autocomplete="off">
                </div>
                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                    <button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
                </div>
                <div class="form-group right" style="margin-bottom: 10px;">
                    <button class="btn btn-primary" id="update-rate-btn" type="button" style="margin-right: 8px;" title="在线同步中国银行实时汇率"><i class="glyphicon glyphicon-refresh"></i> 更新汇率</button>
                    <button class="btn btn-success" id="add-data-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增货币</button>
                </div>
            </div>
        </div>
    </form>

    <table class="table table-hover table-middle mt6" id="data-list">
        <thead>
            <tr>
                <th width="110">货币代码</th>
                <th width="150">货币名称</th>
                <th width="80" class="tc">符号</th>
                <th width="200">汇率 (相对CNY)</th>
                <th width="180">最后更新时间</th>
                <th width="130">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="6">
                    <div class="tc orange">
                        <i class="glyphicon glyphicon-info-sign"></i> 暂无货币数据
                    </div>
                </td>
            </tr>
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
            <tr class="item" data-id="<?php echo htmlspecialchars($value['code']);?>" data-info="<?php echo htmlspecialchars(json_encode($rowInfo, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');?>">
                <td>
                    <span class="label <?php echo $isBase ? 'label-success' : 'label-primary';?>" style="font-family: monospace; font-size: 13px; font-weight: 600; padding: 3px 8px; letter-spacing: 0.5px;">
                        <?php echo htmlspecialchars($value['code']);?>
                    </span>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($value['name']);?></strong>
                    <?php if ($isBase){ ?>
                    <span class="label label-default" style="margin-left: 4px; font-size: 11px; background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">基准货币</span>
                    <?php } ?>
                </td>
                <td class="tc">
                    <span style="display: inline-block; min-width: 24px; font-size: 15px; font-weight: 600; color: #374151;"><?php echo htmlspecialchars($value['symbol']);?></span>
                </td>
                <td>
                    <span style="font-family: monospace; font-size: 13px; font-weight: bold; color: #111827;"><?php echo htmlspecialchars($value['rate']);?></span>
                    <?php if (!$isBase){ ?>
                    <span class="text-muted f12" style="margin-left: 4px;">(1 <?php echo htmlspecialchars($value['code']);?> ≈ <?php echo (float)$value['rate'];?> CNY)</span>
                    <?php } ?>
                </td>
                <td>
                    <span class="text-muted"><i class="glyphicon glyphicon-time f12"></i> <?php echo !empty($value['update_time']) ? $value['update_time'] : '-';?></span>
                </td>
                <td>
                    <button class="btn btn-primary btn-xs modify" type="button"><i class="glyphicon glyphicon-edit"></i> 编辑</button>
                    <?php if (!$isBase){ ?>
                    <button class="btn btn-danger btn-xs delete" type="button" style="margin-left: 4px;"><i class="glyphicon glyphicon-trash"></i> 删除</button>
                    <?php } else { ?>
                    <button class="btn btn-default btn-xs disabled" type="button" style="margin-left: 4px; opacity: 0.5;" title="基准货币不允许删除"><i class="glyphicon glyphicon-ban-circle"></i> 锁定</button>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <div class="row mt12">
        <div class="col-xs-12 text-muted" style="line-height: 28px;">
            共 <strong style="color: #333;"><?php echo count($list);?></strong> 个货币
            <span style="margin-left: 12px; color: #888; font-size: 12px;"><i class="glyphicon glyphicon-info-sign"></i> 提示：点击“更新汇率”可在线同步中国银行最新外汇牌价</span>
        </div>
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
                <input type="text" class="form-control" name="code" required="required" maxlength="3" placeholder="3位大写英文代码 (如: USD)" autocomplete="off" style="text-transform: uppercase;">
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
            <div style="margin-top: 20px;">
                <button type="button" class="btn btn-primary btn-block save">确认保存</button>
            </div>
        </form>
    </div>
</div>