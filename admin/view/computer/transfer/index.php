<div class="container-fluid">
    <form action="<?php echo url();?>" class="form-inline">
		<div class="col-md-12 pt10">
			<div class="form-group mt10 mr20">
				<label for="short_name">关键字:</label>
				<input type="text" class="form-control" name="keyword" value="<?php echo $keyword;?>" placeholder="名称关键字" autocomplete="off">
			</div>
			<div class="mr20 form-group mt10">
				<button class="btn btn-info" type="submit"><i class="glyphicon glyphicon-search"></i> 查询</button>
			</div>
			<div class="bottom15 right">
		        <button class="btn btn-primary reload" type="button"><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;重构缓存</button>
		    </div>
		</div>
		<div class="clear"></div>
	</form>
   	<table class="table table-hover table-middle mt20">
        <tr>
        	<th class="col-md-1 col-1">ID</th>
            <th class="col-md-1 col-1">类型/名称</th>
            <th class="col-md-4 col-4">名称</th>
            <th class="col-md-4 col-4">翻译</th>
            <th class="col-md-2 col-2">操作</th>
        </tr>
        <?php if (empty($list)) { ?>
    	<tr>
    		<td colspan="5" style="text-align: center;"><span style="color: orange;">暂无数据</span></td>
    	</tr>
    	<?php } else { ?>
        <?php foreach ($list as $key => $value){ ?>
        <tr data-id="<?php echo $value['tran_id'];?>">
            <td class="col-md-1 col-1"><?php echo $value['tran_id'];?></td>
            <td class="col-md-1 col-1"><?php echo $value['type'].'/'.$value['type_name'];?></td>
            <td class="col-md-4 col-4"><?php echo $value['name'];?></td>
            <td class="col-md-4 col-4"><?php echo $value['value'];?></td>
            <td class="col-md-2 col-2">
                <button class="btn btn-primary btn-xs modify" type="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
            </td>
        </tr>
        <?php } ?>
    	<?php } ?>
    </table>
	<?php echo page($size, $total);?>
</div>
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	    	<button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">编辑翻译&nbsp;<span class="glyphicon glyphicon-transfer f16" title="自动翻译"></span></div>
	    	<input type="hidden" class="form-control" name="tran_id" value="0">
	    	<input type="hidden" name="opn" value="editInfo">
	    	<input type="hidden" class="form-control" name="type" value="">
	        <input type="hidden" class="form-control" name="name" value="">
	        <div class="input-group">
	            <div class="input-group-addon"><span>类型：</span></div>
	            <input type="text" class="form-control" name="type_name" required="required" maxlength="30" disabled="disabled" value="">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>名称：</span></div>
	            <textarea class="form-control" name="name" required="required" value="" disabled="disabled"></textarea>
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>翻译：</span></div>
	            <textarea class="form-control" name="value" required="required" value=""></textarea>
	        </div>
	        <div class="col-md-8 col-8 right hidden" style="padding: 0;">
	        	<button type="button" class="btn btn-success btn-sm btn-block translate-saved" data-loading-text="<span class='glyphicon glyphicon-refresh'></span>">获取智能翻译</button>
	        </div>
	        <div class="clear"></div>
	        <div class="margin-top-15">
	        	<button type="button" class="btn btn-primary btn-lg btn-block save" data-loading-text="loading...">确认</button>
	        </div>
	    </form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	TRANSFER.init();
})
</script>