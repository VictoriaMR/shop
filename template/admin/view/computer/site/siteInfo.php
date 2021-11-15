<div class="container-fluid" id="site-page" data-id="<?php echo $id;?>">
	<?php if (empty($site)){?>
	<p class="tc orange mt32">找不到站点信息</p>
	<?php } else {?>
	<div class="w100">
		<div class="w60 left">
			<div class="basic-header">
				<h3 class="f20">基础信息</h3>
			</div>
			<div class="w50 left pl16">
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">ID:</dt>
					<dd class="cls-dt cls-dt-id"><?php echo $site['site_id'];?></dd>
					<dd id="site-status">
						<div class="switch_botton" data-status="<?php echo $site['status'];?>">
							<div class="switch_status <?php echo $site['status'] == 1 ? 'on' : 'off';?>"></div>
						</div>
					</dd>
				</div>
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">名称:</dt>
					<dd class="cls-dt cls-dt-id"><?php echo $site['name'];?></dd>
					<dd>
						<button class="btn btn-primary btn-xs" id="site-name">修改</button>
					</dd>
				</div>
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">语言:</dt>
					<dd>
						<button class="btn btn-success btn-xs" id="add-language">添加</button>
					</dd>
				</div>
				<div class="item">
					<table class="table table-bordered table-hover table-wrapper j-lan-table" id="language-table">
						<tbody>
							<tr>
								<th width="40%">语言</th>
								<th width="40%">排序</th>
								<th width="20%">操作</th>
							</tr>
							<?php if (empty($siteLanguage)){?>
							<tr>
								<td colspan="3" class="orange tc">暂无数据</td>
							</tr>
							<?php } else {?>
							<?php foreach ($siteLanguage as $value){?>
							<tr data-id="<?php echo $value['item_id'];?>" data-code="<?php echo $value['code'];?>">
								<td><?php echo $languageList[$value['code']]['name2'];?></td>
								<td>
									<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>">
								</td>
								<td>
									<button class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
								</td>
							</tr>
							<?php } ?>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="w50 left pl16">
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">添加时间:</dt>
					<dd class="cls-dt cls-dt-id"><?php echo $site['add_time'];?></dd>
				</div>
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">模板:</dt>
					<dd class="cls-dt cls-dt-id"><?php echo $site['path'];?></dd>
					<dd>
						<button class="btn btn-primary btn-xs" id="site-path">修改</button>
					</dd>
				</div>
				<div class="item flex">
					<dt class="cls-dt cls-dt-name">货币:</dt>
					<dd>
						<button class="btn btn-success btn-xs" id="add-currency">添加</button>
					</dd>
				</div>
				<div class="item">
					<table class="table table-bordered table-hover table-wrapper j-lan-table" id="site-currency">
						<tbody>
							<tr>
								<th width="40%">货币</th>
								<th width="40%">排序</th>
								<th width="20%">操作</th>
							</tr>
							<?php if (empty($siteCurrency)){?>
							<tr>
								<td colspan="3" class="orange tc">暂无数据</td>
							</tr>
							<?php } else {?>
							<?php foreach ($siteCurrency as $value){?>
							<tr data-id="<?php echo $value['item_id'];?>" data-code="<?php echo $value['code'];?>">
								<td><?php echo $currencyList[$value['code']]['name'];?></td>
								<td>
									<input type="text" name="sort" class="form-control" value="<?php echo $value['sort'];?>">
								</td>
								<td>
									<button class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
								</td>
							</tr>
							<?php } ?>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="clear"></div>
			<div class="pl16">
				<div class="text-content">
					<div class="item flex" data-id="keyword">
						<dt class="cls-dt cls-dt-name">Keyword:</dt>
						<dd class="cls-dt cls-dt-id">
							<button class="btn btn-primary btn-xs">配置多语言</button>
						</dd>
					</div>
					<div class="item">
						<textarea class="form-control" rows="3" name="keyword"><?php echo $site['keyword'];?></textarea>
					</div>
					<div class="item flex" data-id="description">
						<dt class="cls-dt cls-dt-name">Description:</dt>
						<dd class="cls-dt cls-dt-id">
							<button class="btn btn-primary btn-xs">配置多语言</button>
						</dd>
					</div>
					<div class="item">
						<textarea class="form-control" rows="3" name="description"><?php echo $site['description'];?></textarea>
					</div>
					<div class="item flex">
						<dt class="cls-dt cls-dt-name">备注:</dt>
					</div>
					<div class="item">
						<textarea class="form-control" rows="3" name="remark"><?php echo $site['remark'];?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="w40 left pl16">
			<div class="basic-header">
				<h3 class="f20">支付方式显示顺序</h3>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<!-- 修改名字 -->
	<div id="name-info" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<input type="hidden" name="id" value="<?php echo $id;?>">
			<input type="hidden" name="opn" value="modifySite">
			<div class="f24 dealbox-title">站点名称配置</div>
			<div class="input-group">
				<div class="input-group-addon"><span>站点名称</span>：</div>
				<input class="form-control" name="name" required="required" autocomplete="off" placeholder="站点名称" value="<?php echo $site['name'];?>" />
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
		</div>
	</div>
	<!-- 修改模板 -->
	<div id="path-info" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<input type="hidden" name="id" value="<?php echo $id;?>">
			<input type="hidden" name="opn" value="modifySite">
			<div class="f24 dealbox-title">站点模板配置</div>
			<div class="input-group">
				<div class="input-group-addon"><span>站点模板</span>：</div>
				<input class="form-control" name="path" required="required" autocomplete="off" placeholder="站点模板" value="<?php echo $site['path'];?>" />
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
		</div>
	</div>
	<!-- 多语言弹窗 -->
	<div id="language-info" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">站点多语言</div>
			<table class="table table-bordered table-hover table-wrapper j-lan-table">
				<tbody>
					<tr>
						<th width="60%">语言</th>
						<th width="40%">操作</th>
					</tr>
					<?php if (empty($languageList)){?>
					<tr>
						<td colspan="2" class="orange tc">暂无数据</td>
					</tr>
					<?php } else { $selectLanguage = array_column($siteLanguage, 'code');?>
					<?php foreach ($languageList as $value){?>
					<tr data-code="<?php echo $value['code'];?>">
						<td><?php echo $value['name2'];?></td>
						<td>
							<?php if (in_array($value['code'], $selectLanguage)){?>
							<button class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
							<?php } else {?>
							<button class="btn btn-success btn-xs add"><span class="glyphicon glyphicon-plus"></span>&nbsp;添加</button>
							<?php }?>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- 增加域名弹窗 -->
	<div id="domain-info" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">添加域名</div>
			<input type="hidden" name="domain_id" value="0">
			<input type="hidden" name="site_id" value="<?php echo $id;?>">
			<input type="hidden" name="opn" value="editDomain">
			<div class="input-group">
				<div class="input-group-addon"><span>域名</span>：</div>
				<input class="form-control" name="domain" required="required" autocomplete="off" maxlength="64" placeholder="只要主体部分" />
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>状态</span>：</div>
				<select class="form-control" name="status">
					<option value="0">关闭</option>
					<option value="1">启用</option>
				</select>
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>备注</span>：</div>
				<textarea class="form-control" name="remark" required="required" autocomplete="off" rows="2" placeholder="内部使用备注"></textarea>
			</div>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
		</div>
	</div>
	<!-- 多货币弹窗 -->
	<div id="currency-info" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">站点货币</div>
			<table class="table table-bordered table-hover table-wrapper j-lan-table">
				<tbody>
					<tr>
						<th width="60%">货币</th>
						<th width="40%">操作</th>
					</tr>
					<?php if (empty($currencyList)){?>
					<tr>
						<td colspan="2" class="orange tc">暂无数据</td>
					</tr>
					<?php } else { $selectCurrency = array_column($siteCurrency, 'code');?>
					<?php foreach ($currencyList as $value){?>
					<tr data-code="<?php echo $value['code'];?>">
						<td><?php echo $value['name'];?></td>
						<td>
							<?php if (in_array($value['code'], $selectCurrency)){?>
							<button class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
							<?php } else {?>
							<button class="btn btn-success btn-xs add"><span class="glyphicon glyphicon-plus"></span>&nbsp;添加</button>
							<?php }?>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- 多语言弹窗 -->
	<div id="dealbox-language" class="hidden">
		<div class="mask"></div>
		<div class="centerShow">
			<form class="form-horizontal">
				<button type="button" class="close" aria-hidden="true">&times;</button>
				<div class="f24 dealbox-title">多语言配置</div>
				<input type="hidden" name="site_id" value="<?php echo $id;?>">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="source_text" value="">
				<input type="hidden" name="opn" value="editLanguage">
				<table class="table table-bordered table-hover">
					<tbody></tbody>
				</table>
				<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
			</form>
		</div>
	</div>
	<?php }?>
</div>