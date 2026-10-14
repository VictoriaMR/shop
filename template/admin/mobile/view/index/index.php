<div id="index-page">
	<div class="header">
		<div class="header-left">
			<button type="button" class="menu-toggle" id="menu-toggle" aria-label="打开菜单">
				<i class="fa fa-bars"></i>
			</button>
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
		<div class="drawer-mask" id="drawer-mask"></div>
		<div class="nav-left" id="nav-drawer">
			<div class="person">
				<div class="avatar">
					<?php if (!empty($info['avatar']) && strpos($info['avatar'], 'image/common/') === false) { ?>
					<img src="<?php echo htmlspecialchars($info['avatar'], ENT_QUOTES, 'UTF-8');?>" alt="avatar">
					<?php } else { ?>
					<i class="fa fa-user"></i>
					<?php } ?>
				</div>
				<div class="info">
					<p class="e1 cf">
						<span class="name"><?php echo htmlspecialchars($info['nick_name'] ?? '', ENT_QUOTES, 'UTF-8');?></span>
						<span class="user-id">【<?php echo htmlspecialchars((string)($info['mem_id'] ?? ''), ENT_QUOTES, 'UTF-8');?>】</span>
					</p>
					<p class="e1 cr"><?php echo htmlspecialchars($info['mobile'] ?? '', ENT_QUOTES, 'UTF-8');?></p>
				</div>
				<button type="button" class="drawer-close" id="drawer-close" aria-label="关闭菜单">
					<i class="fa fa-xmark"></i>
				</button>
			</div>

			<div class="left-content">
				<div class="left-one">
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
							foreach ($funcList ?? [] as $value) {
								$isSelected = ($lastGroup === $value['value']);
								$groupName  = htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');
								$groupVal   = htmlspecialchars($value['value'], ENT_QUOTES, 'UTF-8');
								$rawIcon    = (string)($value['icon'] ?? '');
								$faIcon     = $groupIconMap[$value['value']] ?? ($groupIconMap[$rawIcon] ?? (strpos($rawIcon, 'fa-') === 0 ? $rawIcon : 'fa-cube'));
							?>
							<li data-title="<?php echo $groupName;?>" data-to="<?php echo $groupVal;?>" <?php echo $isSelected ? 'class="auto-select"' : '';?>>
								<i class="fa <?php echo htmlspecialchars($faIcon, ENT_QUOTES, 'UTF-8');?> group-icon"></i>
								<span class="e1"><?php echo $groupName;?></span>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>

				<div class="left-two">
					<div class="title">
						<span class="text e1">页面标题</span>
						<a href="/" title="返回首页" class="fa fa-home home-link"></a>
					</div>
					<div class="nav-son-content">
						<?php
						$lastUrl = $leftInfo['last_url'] ?? '';
						foreach ($funcList ?? [] as $value) {
							if (!empty($value['son'])) {
								$groupVal = htmlspecialchars($value['value'], ENT_QUOTES, 'UTF-8');
						?>
						<div class="item" data-for="<?php echo $groupVal;?>">
							<ul>
								<?php
								foreach ($value['son'] as $sv) {
									$sonUrl        = adminUrl($sv['value']);
									$isUrlSelected = ($lastUrl === $sonUrl);
									$sonName       = htmlspecialchars($sv['name'], ENT_QUOTES, 'UTF-8');
								?>
								<li data-src="<?php echo htmlspecialchars($sonUrl, ENT_QUOTES, 'UTF-8');?>" data-title="<?php echo $sonName;?>" <?php echo $isUrlSelected ? 'class="auto-select"' : '';?>>
									<span class="fa fa-bookmark item-icon"></span>
									<span class="item-name e1"><?php echo $sonName;?></span>
									<a class="fa fa-up-right-from-square item-ext" title="新窗口打开" target="_blank" href="<?php echo htmlspecialchars($sonUrl, ENT_QUOTES, 'UTF-8');?>"></a>
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

		<div class="content-right">
			<iframe src="about:blank" id="href-to-iframe" width="100%" height="100%" frameborder="0" title="管理内容"></iframe>
		</div>
	</div>
</div>