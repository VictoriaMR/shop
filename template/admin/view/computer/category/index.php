<div class="container-fluid" id="category-list">
	<button class="btn btn-success modify" data-id="0" type="button" style="width: 150px;"><i class="glyphicon glyphicon-plus"></i> 添加类目</button>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
			<tr>
				<th width="100">ID</th>
				<th width="300">名称</th>
				<th width="300">英文名称</th>
				<th width="120">语言配置</th>
				<th width="150">状态</th>
				<th width="150">是否展示</th>
				<th width="150">头像</th>
				<th width="250">操作</th>
			</tr>
			<?php if (empty($list)){ ?>
			<tr>
				<td colspan="8">
					<div class="tc orange">暂无数据</div>
				</td>
			</tr>
			<?php } else {?>
			<?php foreach ($list as $key => $value) { ?>
			<tr class="item<?php echo $value['level']==0 ? ' info' : '';?>" data-lev="<?php echo $value['level'];?>" data-id="<?php echo $value['cate_id'];?>" data-pid="<?php echo $value['parent_id'];?>">
				<td><?php echo $value['cate_id'];?></td>
				<td>
					<div class="left text-content" <?php echo $value['level'] ? 'style="padding-left:'.($value['level']*20).'px;"' : '';?>>
						<span class="glyphicon glyphicon-globe"></span>
						&nbsp;
						<span class="cate_name"><?php echo $value['name'];?></span>
					</div>
				</td>
				<td><?php echo $value['name_en'];?></td>
				<td>
					<?php if ($value['is_translate'] == 2){?>
					<span class="green">已配置</span>
					<?php } elseif ($value['is_translate'] == 1){?>
					<span class="orange">部分配置</span>
					<?php } else {?>
					<span class="red">未配置</span>
					<?php } ?>
				</td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status">
                        <div class="switch_status <?php echo $value['status']?'on':'off';?>"></div>
                    </div>
				</td>
				<td>
					<div class="switch_botton" data-status="<?php echo $value['show'];?>" data-type="show">
                        <div class="switch_status <?php echo $value['show']?'on':'off';?>"></div>
                    </div>
				</td>
				<td>
					<div class="avatar-hover">
						<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload">
					</div>
				</td>
				<td>
					<button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
					<button class="btn btn-success btn-xs ml4 add"><span class="glyphicon glyphicon-plus"></span>&nbsp;增加</button>
					<button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
				</td>
			</tr>
			<?php } ?>
			<?php }?>
		</tbody>
	</table>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">品类管理</div>
			<input type="hidden" name="cate_id" value="0">
			<input type="hidden" name="parent_id" value="0">
			<input type="hidden" name="opn" value="editInfo">
			<div class="input-group">
				<div class="input-group-addon"><span>名称：</span></div>
				<input type="text" class="form-control" name="name" autocomplete="off">
			</div>
			<div class="input-group">
				<div class="input-group-addon"><span>英文：</span></div>
				<input type="text" class="form-control" name="name_en" autocomplete="off">
			</div>
			<button type="button" class="btn btn-primary btn-lg w100 save-btn">确认</button>
		</form>
	</div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
		<form class="form-horizontal">
			<button type="button" class="close" aria-hidden="true">&times;</button>
			<div class="f24 dealbox-title">多语言配置</div>
			<input type="hidden" name="cate_id" value="0">
			<input type="hidden" name="cate_name" value="">
			<input type="hidden" name="opn" value="editLanguage">
			<table class="table table-bordered table-hover">
				<tbody></tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>