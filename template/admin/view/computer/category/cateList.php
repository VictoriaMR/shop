<div class="container-fluid" id="category-list">
	<div class="col-md-2" style="padding-left: 0;">
		<div class="list-group">
			<?php foreach ($pList as $value){?>
			<a href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>" class="list-group-item<?php echo $value['cate_id']==$cid?' active':'';?>">
				<span class="badge"><?php echo $value['count'];?></span>
                <span><?php echo $value['name'];?></span>               
			</a>
			<?php }?>
        </div>
        <p>合计: <?php echo count($pList);?>个品类</p>
	</div>
	<div class="col-md-10" style="border-left: 1px solid #ccc;">
		<button class="btn btn-success modify" data-id="0" type="button" style="width: 150px;"><i class="glyphicon glyphicon-plus"></i> 添加类目</button>
		<table class="table table-hover mt10" id="data-list">
			<tbody>
				<tr>
					<th width="50">ID</th>
					<th width="200">名称</th>
					<th width="100">SEO配置</th>
					<th width="100">状态</th>
					<th width="100">是否展示</th>
					<th width="100">是否热门</th>
					<th width="80">头像</th>
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
							<span data-type="0" class="glyphicon glyphicon-globe" style="<?php echo $value['is_translate']==2?'color: green':($value['is_translate']==1?'color: orange':'color: red');?>"></span>
							&nbsp;
							<span class="cate_name"><?php echo $value['name'];?></span>
						</div>
					</td>
					<td>
						<span class="green">keyw:&nbsp;</span>
						<span title="keyword" data-type="1" class="glyphicon glyphicon-globe"></span>
						<br />
						<span class="orange">desc:&nbsp;</span>
						<span title="desc" data-type="2" class="glyphicon glyphicon-globe"></span>
					</td>
					<td>
						<div class="switch_botton" data-status="<?php echo $value['status'];?>" data-type="status">
	                        <div class="switch_status <?php echo $value['status']?'on':'off';?>"></div>
	                    </div>
					</td>
					<td>
						<div class="switch_botton" data-status="<?php echo $value['is_show'];?>" data-type="is_show">
	                        <div class="switch_status <?php echo $value['is_show']?'on':'off';?>"></div>
	                    </div>
					</td>
					<td>
						<div class="switch_botton" data-status="<?php echo $value['is_hot'];?>" data-type="is_hot">
	                        <div class="switch_status <?php echo $value['is_hot']?'on':'off';?>"></div>
	                    </div>
					</td>
					<td>
						<div class="avatar-hover">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload">
						</div>
					</td>
					<td>
						<a class="btn btn-primary btn-xs modify" href="<?php echo adminUrl('category/attrUsed', ['cid'=>$value['cate_id']]);?>"><span class="glyphicon glyphicon-forward"></span>&nbsp;属性</a>
						<button class="btn btn-info btn-xs modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
						<button class="btn btn-success btn-xs add"><span class="glyphicon glyphicon-plus"></span>&nbsp;增加</button>
						<button class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
					</td>
				</tr>
				<?php } ?>
				<?php }?>
			</tbody>
		</table>
		<p class="mb10 mt10">合计: <?php echo count($list);?>个类目</p>
	</div>
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
			<input type="hidden" name="type" value="0">
			<input type="hidden" name="cate_name" value="">
			<input type="hidden" name="opn" value="editLanguage">
			<table class="table table-bordered table-hover">
				<tbody></tbody>
			</table>
			<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
		</form>
	</div>
</div>