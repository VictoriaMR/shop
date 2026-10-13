<div id="index-page">
	<div class="header">
		<div class="left">
			<a href="/" title="系统首页">
				<img src="<?php echo siteUrl('/image/common/erp.svg');?>" height="35" alt="ERP">
			</a>
		</div>
		<div class="right">
			<a href="javascript:;" title="通知">
				<img src="<?php echo siteUrl('/image/icon/notice.png');?>" height="16" alt="通知">
			</a>
			<a href="javascript:;" title="消息">
				<img src="<?php echo siteUrl('/image/icon/email.png');?>" height="16" alt="消息">
			</a>
			<a href="<?php echo adminUrl('login/logout');?>" title="退出系统">
				<img src="<?php echo siteUrl('/image/icon/exit.png');?>" height="16" alt="退出">
			</a>
		</div>
	</div>
	<div class="body">
		<div class="nav-left<?php echo empty($leftInfo['left_type']) ? '' : ' left-close';?>">
			<div class="person">
				<div class="avatar">
					<img src="<?php echo !empty($info['avatar']) ? $info['avatar'] : siteUrl('/image/common/male.jpg');?>" alt="avatar">
				</div>
				<div class="info">
					<p class="e1 cf">
						<span class="name"><?php echo $info['nick_name'] ?? '';?></span><span class="user-id">【<?php echo $info['mem_id'] ?? '';?>】</span>
					</p>
					<p class="e1 cr"><?php echo $info['mobile'] ?? '';?></p>
				</div>
			</div>
			<div class="left-content">
				<div class="left-one">
					<div class="toggle open" data-title="菜单切换开关">
						<span class="fa fa-list-ul"></span>
					</div>
					<div class="nav-content">
						<ul>
							<?php 
							$lastGroup = $leftInfo['last_group'] ?? '';
							foreach ($funcList ?? [] as $value){ 
								$isSelected = ($lastGroup === $value['value']);
							?>
							<li data-title="<?php echo $value['name'];?>" data-to="<?php echo $value['value'];?>" <?php echo $isSelected ? 'class="auto-select"' : '';?>>
								<img src="<?php echo siteUrl('/image/feature/' . ($value['icon'] ?? 'default') . '.png');?>" alt="<?php echo $value['name'];?>" height="20">
								<span><?php echo $value['name'];?></span>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text">页面标题</span>
						<a href="/" title="返回首页" class="fa fa-home" style="float: right;margin-right: 6px;margin-top: 13px;"></a>
					</div>
					<div class="nav-son-content">
						<?php 
						$lastUrl = $leftInfo['last_url'] ?? '';
						foreach ($funcList ?? [] as $value){ 
							if (!empty($value['son'])){
						?>
						<div class="item" data-for="<?php echo $value['value'];?>">
							<ul>
								<?php 
								foreach ($value['son'] as $sv){ 
									$sonUrl = adminUrl($sv['value']);
									$isUrlSelected = ($lastUrl === $sonUrl);
								?>
								<li data-src="<?php echo $sonUrl;?>" <?php echo $isUrlSelected ? 'class="auto-select"' : '';?>>
									<span class="fa fa-bookmark left"></span>
									<span class="ml2"><?php echo $sv['name'];?></span>
									<a class="fa fa-link right" title="新窗口打开" target="_blank" href="<?php echo $sonUrl;?>"></a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php 
							} 
						} 
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right" style="background: transparent url('<?php echo adminUrl('login/signature');?>') repeat;">
			<iframe src="about:blank" id="href-to-iframe" width="100%" height="100%" frameborder="0" title="管理内容"></iframe>
		</div>
		<div class="clear"></div>
	</div>
</div>