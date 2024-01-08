<div id="index-page">
	<div class="header">
		<div class="right">
			<a href="" class="glyphicon glyphicon-bell" id="message"></a>
			<a href="" class="glyphicon glyphicon-cog"></a>
			<a href="<?php echo adminUrl('login/logout');?>" class="glyphicon glyphicon-log-out"></a>
		</div>
	</div>
	<div class="body">
		<div class="nav-left<?php echo empty($leftInfo['left_type'])?'':' left-close';?>">
			<div class="person">
				<div class="avatar">
					<img src="<?php echo $info['avatar'];?>">
				</div>
				<div class="info">
					<p class="e1 cf">
						<span class="name"><?php echo $info['nick_name']??'';?></span><span class="user-id">&nbsp;&nbsp;&nbsp;<?php echo $info['mem_id'];?></span></p>
					<p class="e1 cr"><?php echo $info['mobile'];?></p>
				</div>
			</div>
			<div class="left-content">
				<div class="left-one">
					<div class="toggle open" data-title="菜单切换开关">
						<span class="glyphicon glyphicon-align-justify"></span>
					</div>
					<div class="nav-content">
						<ul>
							<?php foreach ($funcList as $value){?>
							<li data-title="<?php echo $value['name'];?>" data-to="<?php echo $value['value'];?>" <?php echo ($leftInfo['last_group']??'')==$value['value']?'class="auto-select"':'';?>>
								<span class="glyphicon glyphicon-<?php echo $value['icon'];?>"></span>
								<span class="ml6"><?php echo $value['name'];?></span>
							</li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text block e1 c2">页面标题</span>
					</div>
					<div class="nav-son-content">
						<?php foreach ($funcList as $value){?>
						<?php if (!empty($value['son'])){?>
						<div class="item" data-for="<?php echo $value['value'];?>">
							<ul>
								<?php foreach ($value['son'] as $sv){?>
								<li data-src="<?php echo adminUrl($sv['value']);?>" <?php echo ($leftInfo['last_url']??'')==adminUrl($sv['value'])?'class="auto-select"':'';?>>
									<span class="glyphicon glyphicon-<?php echo $sv['icon'];?>"></span>
									<span class="ml2"><?php echo $sv['name'];?></span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo adminUrl($sv['value']);?>"></a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right" style="background: transparent url('<?php echo adminUrl('login/signature');?>') repeat;">
			<iframe src="<?php echo $leftInfo['last_url']??'javascript:;';?>" id="href-to-iframe" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="Yes" frameborder="0" hspace="0" vspace="0"></iframe>
		</div>
		<div class="claer"></div>
	</div>
</div>