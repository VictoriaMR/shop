<div id="index-page">
	<div class="header">
		<div class="right">
			<a href="" class="glyphicon glyphicon-bell" id="message"></a>
			<a href="" class="glyphicon glyphicon-cog"></a>
			<a href="<?php echo url('login/logout');?>" class="glyphicon glyphicon-log-out"></a>
		</div>
	</div>
	<div class="body">
		<div class="nav-left">
			<div class="person">
				<div class="avatar">
					<img src="<?php echo $info['avatar'];?>">
				</div>
				<div class="info">
					<p class="e1 cf"><?php echo $info['name'];?>&nbsp;&nbsp;&nbsp;<?php echo $info['mem_id'];?></p>
					<p class="e1 cr"><?php echo $info['mobile'];?></p>
					<a href="<?php echo url('login/logout');?>" class="glyphicon glyphicon-off cr" title="退出登录"></a>
				</div>
			</div>
			<div class="left-content">
				<div class="left-one">
					<div class="toggle open" data-title="菜单切换开关">
						<span class="glyphicon glyphicon-align-justify"></span>
					</div>
					<div class="nav-content">
						<ul>
							<li data-title="概览" data-to="about-view">
								<span class="glyphicon glyphicon-eye-open"></span>
								<span class="ml6">概览</span>
							</li>
							<li data-title="管理人员" data-to="guanliyuan">
								<span class="glyphicon glyphicon-object-align-left"></span>
								<span class="ml6">管理人员</span>
							</li>
							<li data-title="产品管理" data-to="product">
								<span class="glyphicon glyphicon-th"></span>
								<span class="ml6">产品管理</span>
							</li>
							<li data-title="系统设置" data-to="systemInfo">
								<span class="glyphicon glyphicon-cog"></span>
								<span class="ml6">系统设置</span>
							</li>
						</ul>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text block c5f e1">页面标题</span>
						<span class="glyphicon glyphicon-backward" title="收起"></span>
					</div>
					<div class="nav-son-content">
						<div class="item" data-for="about-view">
							<ul>
								<li data-src="<?php echo url('index/statInfo');?>" class="selected">
									<span class="glyphicon glyphicon-user"></span>
									<span class="ml6">全部概览</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('index/statInfo');?>"></a>
								</li>
							</ul>
						</div>
						<div class="item" data-for="guanliyuan">
							<ul>
								<li data-src="<?php echo url('member');?>" class="selected">
									<span class="glyphicon glyphicon-user"></span>
									<span class="ml6">人员列表</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('member');?>"></a>
								</li>
							</ul>
						</div>
						<div class="item" data-for="systemInfo">
							<ul>
								<li data-src="<?php echo url('task');?>" class="selected">
									<span class="glyphicon glyphicon-tasks"></span>
									<span class="ml6">任务管理</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('task');?>"></a>
								</li>
								<li data-src="<?php echo url('systemInfo');?>" class="selected">
									<span class="glyphicon glyphicon-cloud"></span>
									<span class="ml6">服务器信息</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('systemInfo');?>"></a>
								</li>
								<li data-src="<?php echo url('site');?>" class="selected">
									<span class="glyphicon glyphicon-certificate"></span>
									<span class="ml6">站点管理</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('site');?>"></a>
								</li>
								<li data-src="<?php echo url('transfer');?>" class="selected">
									<span class="glyphicon glyphicon-sort"></span>
									<span class="ml6">站点文本</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('transfer');?>"></a>
								</li>
							</ul>
						</div>
						<div class="item" data-for="product">
							<ul>
								<li data-src="<?php echo url('category');?>" class="selected">
									<span class="glyphicon glyphicon-sort-by-attributes"></span>
									<span class="ml6">产品分类</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('category');?>"></a>
								</li>
								<li data-src="<?php echo url('product');?>" class="selected">
									<span class="glyphicon glyphicon-th-large"></span>
									<span class="ml6">产品管理</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('product');?>"></a>
								</li>
								<li data-src="<?php echo url('attribute');?>" class="selected">
									<span class="glyphicon glyphicon-th-list"></span>
									<span class="ml6">属性管理</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('product');?>"></a>
								</li>
								<li data-src="<?php echo url('description');?>" class="selected">
									<span class="glyphicon glyphicon-th"></span>
									<span class="ml6">描述管理</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('product');?>"></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right" style="background: transparent url(<?php echo url('login/signature');?>) repeat;">
			<iframe src="javascript:;" id="href-to-iframe" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="Yes" frameborder="0" hspace="0" vspace="0"></iframe>
		</div>
		<div class="claer"></div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	INDEX.init();
});
</script>