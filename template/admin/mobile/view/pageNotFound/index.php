<?php
$appError = \App::get('app_error');
?>
<div class="not-found-page" id="page-not-found" data-home="<?php echo adminUrl('index/index');?>">
	<div class="not-found-card">
		<div class="icon-circle">
			<i class="fa fa-compass"></i>
		</div>
		<div class="error-code">404</div>
		<h3 class="error-title">抱歉，访问的页面不存在</h3>
		<p class="error-desc">请检查访问链接是否正确，或返回后台首页继续操作。</p>

		<?php if (!empty($appError)) { ?>
		<div class="error-detail">
			<?php echo $appError;?>
		</div>
		<?php } ?>

		<div class="action-row">
			<button type="button" class="btn btn-default btn-back">
				<i class="fa fa-arrow-left"></i> 返回上一页
			</button>
			<a href="<?php echo adminUrl('index/index');?>" class="btn btn-primary btn-home">
				<i class="fa fa-house"></i> 返回首页
			</a>
		</div>
	</div>
</div>
