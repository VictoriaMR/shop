<div id="index-page">
	<div class="header">
		<div class="header-left">
			<a href="/" class="brand-logo" title="系统首页">
				<i class="fa fa-cubes"></i>
				<span>ERP</span>
			</a>
		</div>
		<div class="header-right">
			<a href="javascript:;" id="iframe-refresh" title="刷新当前页">
				<i class="fa fa-rotate-right"></i>
			</a>
			<a href="javascript:;" title="通知">
				<i class="fa fa-bell"></i>
			</a>
			<a href="javascript:;" title="消息">
				<i class="fa fa-envelope"></i>
			</a>
			<a href="<?php echo adminUrl('login/logout');?>" title="退出系统">
				<i class="fa fa-right-from-bracket"></i>
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
							$groupIconMap = [
								'overview'   => 'fa-gauge',
								'management' => 'fa-users',
								'finance'    => 'fa-coins',
								'product'    => 'fa-box',
								'order'      => 'fa-clipboard-list',
								'system'     => 'fa-gear',
								'0'          => 'fa-gauge',
								'53000'      => 'fa-clipboard-list',
								'53001'      => 'fa-chart-line',
								'53002'      => 'fa-box',
								'53003'      => 'fa-tags',
								'53004'      => 'fa-truck',
								'53005'      => 'fa-coins',
								'53006'      => 'fa-wallet',
								'53007'      => 'fa-shield-halved',
								'53008'      => 'fa-gear',
								'53009'      => 'fa-sliders',
								'53054'      => 'fa-bullhorn',
								'53057'      => 'fa-comments',
								'53058'      => 'fa-envelope',
								'53063'      => 'fa-users',
								'680201'     => 'fa-globe',
								'680202'     => 'fa-server',
							];
							foreach ($funcList ?? [] as $value){ 
								$isSelected = ($lastGroup === $value['value']);
								$rawIcon = (string)($value['icon'] ?? '');
								$faIcon = $groupIconMap[$value['value']] ?? ($groupIconMap[$rawIcon] ?? (strpos($rawIcon, 'fa-') === 0 ? $rawIcon : 'fa-cube'));
							?>
							<li data-title="<?php echo $value['name'];?>" data-to="<?php echo $value['value'];?>" <?php echo $isSelected ? 'class="auto-select"' : '';?>>
								<i class="fa <?php echo $faIcon;?> group-icon"></i>
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