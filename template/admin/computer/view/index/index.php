<div id="index-page">
	<div class="header">
		<div class="left">
			<a href="/">
				<img src="<?php echo siteUrl('/image/common/erp.svg');?>" height="35">
			</a>
		</div>
		<div class="right">
			<a href="javascript:;">
				<img src="<?php echo siteUrl('/image/icon/notice.png');?>" height="16">
			</a>
			<a href="javascript:;">
				<img src="<?php echo siteUrl('/image/icon/email.png');?>" height="16">
			</a>
			<a href="<?php echo frame('Router')->adminUrl('login/logout');?>">
				<img src="<?php echo siteUrl('/image/icon/exit.png');?>" height="16">
			</a>
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
						<span class="name"><?php echo $info['nick_name']??'';?></span><span class="user-id">【<?php echo $info['mem_id'];?>】</span></p>
					<p class="e1 cr"><?php echo $info['mobile'];?></p>
				</div>
			</div>
			<div class="left-content">
				<div class="left-one">
					<div class="toggle open" data-title="菜单切换开关">
						<span class="fa fa-list-ul"></span>
					</div>
					<div class="nav-content">
						<ul>
							<?php foreach ($funcList as $value){?>
							<li data-title="<?php echo $value['name'];?>" data-to="<?php echo $value['value'];?>" <?php echo ($leftInfo['last_group']??'')==$value['value']?'class="auto-select"':'';?>>
								<img src="<?php echo siteUrl('/image/feature/'.$value['icon'].'.png');?>" alt="" height="20">
								<span><?php echo $value['name'];?></span>
							</li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text">页面标题</span>
						<a href="/" title="返回首页" class="fa fa-home" style="float: right;margin-right: 6px;margin-top: 13px;"></a>
					</div>
					<div class="nav-son-content">
						<?php foreach ($funcList as $value){?>
						<?php if (!empty($value['son'])){?>
						<div class="item" data-for="<?php echo $value['value'];?>">
							<ul>
								<?php foreach ($value['son'] as $sv){?>
								<li data-src="<?php echo frame('Router')->adminUrl($sv['value']);?>" <?php echo ($leftInfo['last_url']??'')==frame('Router')->adminUrl($sv['value'])?'class="auto-select"':'';?>>
									<span class="fa fa-bookmark left"></span>
									<span class="ml2"><?php echo $sv['name'];?></span>
									<a class="fa fa-link right" title="新窗口打开" target="_blank" href="<?php echo frame('Router')->adminUrl($sv['value']);?>"></a>
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
		<div class="content-right" style="background: transparent url('<?php echo frame('Router')->adminUrl('login/signature');?>') repeat;">
			<iframe src="javascript:;" id="href-to-iframe" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="Yes" frameborder="0" hspace="0" vspace="0"></iframe>
		</div>
		<div class="claer"></div>
	</div>
</div>