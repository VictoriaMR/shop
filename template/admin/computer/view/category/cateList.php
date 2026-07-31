<div class="container-fluid" id="category-list">
	<div class="col-md-2" style="padding-left: 0;">
		<div class="list-group">
			<?php foreach ($pList as $value){?>
			<a href="<?php echo adminUrl('category/cateList', ['cid'=>$value['cate_id']]);?>" class="list-group-item<?php echo $value['cate_id']==$cid?' active':'';?>">
				<span class="badge" title="点击添加子分类" data-id="<?php echo $value['cate_id'];?>"><?php echo $value['count'];?></span>
                <span><?php echo $value['name'];?></span>               
			</a>
			<?php }?>
        </div>
        <p>合计: <?php echo count($pList);?>个品类</p>
	</div>
	<div class="col-md-10" style="padding: 0;">
		<table class="table table-hover mt10" id="data-list">
			<tbody>
				<tr>
					<th width="40">ID</th>
					<th width="300">名称</th>
					<th width="80">SEO配置</th>
					<th width="80">状态</th>
					<th width="80">是否展示</th>
					<th width="120">排序</th>
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
							<span data-type="0" class="fa fa-globe <?php echo $value['is_translate']==2?'green':($value['is_translate']==1?'orange':'red');?>"></span>
							&nbsp;
							<span class="cate_name"><?php echo $value['name'];?></span>
						</div>
					</td>
					<td>
						<span class="green">K:&nbsp;</span>
						<span title="keyword" data-type="1" class="fa fa-globe"></span>
						<br />
						<span class="orange">D:&nbsp;</span>
						<span title="desc" data-type="2" class="fa fa-globe"></span>
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
						<div class="btn-group btn-group-xs" role="group">
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="top" title="最顶" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><span class="fa fa-arrow-up"></span></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="up" title="上一个" <?php echo !empty($value['is_first']) ? 'disabled' : '';?>><span class="fa fa-sort-up"></span></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="down" title="下一个" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><span class="fa fa-sort-down"></span></button>
							<button type="button" class="btn btn-default btn-xs sort-btn-action" data-type="bottom" title="最底" <?php echo !empty($value['is_last']) ? 'disabled' : '';?>><span class="fa fa-arrow-down"></span></button>
						</div>
					</td>
					<td>
						<div class="avatar-hover">
							<img src="<?php echo siteUrl('image/common/noimg.svg');?>" data-src="<?php echo $value['avatar'];?>" class="lazyload">
						</div>
					</td>
					<td>
						<a class="btn btn-primary btn-xs" href="<?php echo adminUrl('category/attrUsed', ['cid'=>$value['cate_id']]);?>"><span class="fa fa-forward"></span>&nbsp;属性</a>
						<button type="button" class="btn btn-info btn-xs modify"><span class="fa fa-edit"></span>&nbsp;修改</button>
						<button type="button" class="btn btn-success btn-xs add"><span class="fa fa-plus"></span>&nbsp;增加</button>
						<button type="button" class="btn btn-danger btn-xs delete"><span class="fa fa-trash"></span>&nbsp;删除</button>
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
<div id="dealbox" class="s-modal" data-mask="true">
	<i class="fa fa-times"></i>
	<div class="title">品类管理</div>
	<form class="form-horizontal">
		<input type="hidden" name="cate_id" value="0">
		<input type="hidden" name="parent_id" value="0">
		<input type="hidden" name="opn" value="editInfo">
		<div class="input-group parent-group" style="display: none;">
			<div class="input-group-addon"><span>父级：</span></div>
			<input type="text" class="form-control" name="parent_name" readonly value="">
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>名称：</span></div>
			<input type="text" class="form-control" name="name" autocomplete="off">
		</div>
		<div class="input-group">
			<div class="input-group-addon"><span>英文：</span></div>
			<input type="text" class="form-control" name="name_en" autocomplete="off">
		</div>
		<button type="button" class="btn btn-primary btn-lg w100 save-btn mt12">确认</button>
	</form>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="s-modal" data-mask="true">
	<i class="fa fa-times"></i>
	<div class="title">多语言配置</div>
	<form class="form-horizontal">
		<input type="hidden" name="cate_id" value="0">
		<input type="hidden" name="type" value="0">
		<input type="hidden" name="cate_name" value="">
		<input type="hidden" name="opn" value="editLanguage">
		<table class="table table-bordered table-hover">
			<tbody></tbody>
		</table>
		<button type="button" class="btn btn-primary btn-lg btn-block save-btn mt12">确认</button>
	</form>
</div>