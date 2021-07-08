<?php $this->load('common/header');?>
<div class="container-fluid">
	<div class="row-item">
		<div class="right">
            <button class="btn btn-danger delete" data-id="all" type="button" style="width: 200px;"><i class="glyphicon glyphicon-trash"></i> 清除全部缓存</button>
        </div>
        <div class="clear"></div>
	</div>
	<table class="table table-hover mt20" id="data-list">
        <tbody>
	        <tr>
	            <th class="col-md-2">名称</th>
	            <th class="col-md-1">大小</th>
	            <th class="col-md-2">时间</th>
	            <th class="col-md-2">操作</th>
	        </tr>
        	<?php if (empty($list)){ ?>
        	<tr>
        		<td colspan="7">
        			<div class="tc orange">暂无数据</div>
        		</td>
        	</tr>
        	<?php } else {?>
        	<?php foreach ($list as $key => $value) { ?>
        	<tr>
        		<td class="col-md-2"><?php echo $value['name'];?></td>
        		<td class="col-md-1"><?php echo sprintf('%.2f', $value['size'] / 1024).' Kbs';?></td>
        		<td class="col-md-2"><?php echo $value['c_time'];?></td>
        		<td class="col-md-2">
                    <button class="btn btn-danger btn-sm delete mt2" type="button" data-id="<?php echo $value['name'];?>"><i class="glyphicon glyphicon-trash"></i> 删除</button>
        		</td>
        	</tr>
        	<?php } ?>
        	<?php }?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
$(function(){
	SITECACHE.init();
});
</script>
<?php $this->load('common/footer');?>