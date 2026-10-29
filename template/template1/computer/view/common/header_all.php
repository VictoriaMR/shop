<?php
$siteName = $siteName ?? \App::get('domain', 'name', 'Shop');
$curCurrencyCode = $curCurrencyCode ?? currencyId('code');
$curCurrencySymbol = $curCurrencySymbol ?? '$';
$curLanCode = $curLanCode ?? lanId('code');
$curLanName = $curLanName ?? 'English';
$currencyList = $currencyList ?? [];
$languageList = $languageList ?? [];
$navCategories = $navCategories ?? [];
$hotKeywords = $hotKeywords ?? ['Dress', 'Sweater', 'T-Shirt', 'Chandelier', 'Nordic'];
$currentKeyword = htmlspecialchars(trim((string)iget('keyword', '')), ENT_QUOTES, 'UTF-8');
$currentCid = (int)iget('cid', 0);
$currentPath = strtolower(\App::get('router', 'path', 'Index'));
$__headerMem = session()->get(type() . '_info') ?: [];
$__isLogged = !empty($__headerMem['mem_id']);
$__headerDisplayName = $__isLogged
	? (!empty($__headerMem['first_name']) ? $__headerMem['first_name'] : (!empty($__headerMem['nickname']) ? $__headerMem['nickname'] : (!empty($__headerMem['email']) ? $__headerMem['email'] : 'Account')))
	: appT('sign_in');
$__headerGreeting = $__isLogged ? 'Hi, Member' : 'Welcome';
$__headerUserUrl = $__isLogged ? url('userInfo') : url('login');
?>
<header class="site-header" id="site-header">
	<!-- Top Announcement & Locale Bar -->
	<div class="topbar">
		<div class="container flex-between">
			<div class="topbar-promo flex-align">
				<svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<path d="M5 12h14M12 5l7 7-7 7"/>
				</svg>
				<span><?php echo appT('free_shipping_banner');?></span>
			</div>
			<div class="topbar-tools flex-align">
				<!-- Language Switcher -->
				<?php if (!empty($languageList)) { ?>
				<div class="dropdown-item">
					<button type="button" class="dropdown-trigger flex-align" aria-label="Language selector" aria-haspopup="true">
						<svg class="icon-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
						</svg>
						<span><?php echo htmlspecialchars($curLanName, ENT_QUOTES, 'UTF-8');?></span>
						<svg class="icon-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<ul class="dropdown-menu">
						<?php foreach ($languageList as $lan) {
							$lCode = $lan['code'] ?? 'en';
							$isActive = ($lCode === $curLanCode);
						?>
						<li>
							<a href="?lan=<?php echo urlencode($lCode);?>" class="<?php echo $isActive ? 'active' : '';?>" rel="nofollow">
								<span><?php echo htmlspecialchars($lan['name'] ?? strtoupper($lCode), ENT_QUOTES, 'UTF-8');?></span>
								<span class="code-pill"><?php echo strtoupper(htmlspecialchars($lCode, ENT_QUOTES, 'UTF-8'));?></span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>

				<span class="topbar-divider" aria-hidden="true"></span>

				<!-- Currency Switcher -->
				<?php if (!empty($currencyList)) { ?>
				<div class="dropdown-item">
					<button type="button" class="dropdown-trigger flex-align" aria-label="Currency selector" aria-haspopup="true">
						<span><?php echo htmlspecialchars($curCurrencyCode . ' (' . $curCurrencySymbol . ')', ENT_QUOTES, 'UTF-8');?></span>
						<svg class="icon-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<ul class="dropdown-menu">
						<?php foreach ($currencyList as $cur) {
							$cCode = $cur['code'] ?? 'USD';
							$cSym = $cur['symbol'] ?? '$';
							$isActive = ($cCode === $curCurrencyCode);
						?>
						<li>
							<a href="?cur=<?php echo urlencode($cCode);?>" class="<?php echo $isActive ? 'active' : '';?>" rel="nofollow">
								<span><?php echo htmlspecialchars($cCode, ENT_QUOTES, 'UTF-8');?></span>
								<span class="code-pill"><?php echo htmlspecialchars($cSym, ENT_QUOTES, 'UTF-8');?></span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>

				<span class="topbar-divider" aria-hidden="true"></span>

				<a href="<?php echo url('faq');?>" class="topbar-link" title="<?php echo appT('help_center');?>"><?php echo appT('help_center');?></a>
				<a href="<?php echo url('contact');?>" class="topbar-link" title="Contact Us">Contact</a>
			</div>
		</div>
	</div>

	<!-- Sticky Main Header -->
	<div class="header-main-wrap" id="sticky-header">
		<div class="container header-main flex-between">
			<!-- Brand Logo -->
			<a href="<?php echo url();?>" class="brand-logo flex-align" title="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?> Home">
				<span class="brand-mark" aria-hidden="true">
					<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
					</svg>
				</span>
				<span class="brand-text">
					<strong class="brand-name"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');?></strong>
					<small class="brand-sub">MODERN LIVING &amp; STYLE</small>
				</span>
			</a>

			<!-- Search Box & Hot Keywords -->
			<div class="header-search-wrap">
				<form class="search-form" action="<?php echo url('search');?>" method="get" role="search">
					<label for="header-search-input" class="sr-only">Search products</label>
					<input
						id="header-search-input"
						class="search-input"
						type="search"
						name="keyword"
						value="<?php echo $currentKeyword;?>"
						placeholder="<?php echo htmlspecialchars(appT('search_placeholder'), ENT_QUOTES, 'UTF-8');?>"
						autocomplete="off"
					>
					<button type="submit" class="search-btn" aria-label="Search">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
						</svg>
						<span>Search</span>
					</button>
				</form>
				<?php if (!empty($hotKeywords)) { ?>
				<div class="hot-keywords flex-align">
					<span class="hot-label"><?php echo appT('hot_searches');?></span>
					<?php foreach ($hotKeywords as $kw) { ?>
					<a href="<?php echo url('search', ['keyword' => strtolower($kw)]);?>" class="hot-tag" title="Search <?php echo htmlspecialchars($kw, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($kw, ENT_QUOTES, 'UTF-8');?></a>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<!-- User Account, Wishlist & Cart Actions -->
			<div class="header-actions flex-align">
				<div class="action-item account-dropdown" id="header-account">
					<a href="<?php echo $__headerUserUrl;?>" class="action-link flex-align" id="header-user-link" title="<?php echo appT('my_account');?>">
						<span class="action-icon">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
							</svg>
						</span>
						<span class="action-meta">
							<small class="action-sub" id="header-user-greeting"><?php echo htmlspecialchars($__headerGreeting, ENT_QUOTES, 'UTF-8');?></small>
							<strong class="action-title" id="header-user-name"><?php echo htmlspecialchars($__headerDisplayName, ENT_QUOTES, 'UTF-8');?></strong>
						</span>
					</a>
					<div class="account-popover">
						<div class="account-popover-head<?php echo $__isLogged ? ' hidden' : '';?>" id="account-guest-cta">
							<a href="<?php echo url('login');?>" class="btn btn-primary btn-sm w-full"><?php echo appT('sign_in');?> / <?php echo appT('register');?></a>
						</div>
						<ul class="account-menu-list">
							<li>
								<a href="<?php echo url('userInfo');?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
									<span><?php echo appT('my_account');?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo url('order');?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
									<span><?php echo appT('my_orders');?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo url('userInfo/wishList');?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
									<span><?php echo appT('wishlist');?></span>
								</a>
							</li>
							<li>
								<a href="<?php echo url('userInfo/history');?>">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
									<span><?php echo appT('recently_viewed');?></span>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<a href="<?php echo url('userInfo/wishList');?>" class="action-link flex-align" title="<?php echo appT('wishlist');?>">
					<span class="action-icon">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
						</svg>
					</span>
					<span class="action-meta">
						<small class="action-sub">Saved</small>
						<strong class="action-title"><?php echo appT('wishlist');?></strong>
					</span>
				</a>

				<a href="<?php echo url('cart');?>" class="action-link cart-link flex-align" id="header-cart" title="<?php echo appT('shopping_bag');?>">
					<span class="action-icon relative">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
						</svg>
						<span class="cart-badge" id="header-cart-count">0</span>
					</span>
					<span class="action-meta">
						<small class="action-sub">Checkout</small>
						<strong class="action-title"><?php echo appT('shopping_bag');?></strong>
					</span>
				</a>
			</div>
		</div>

		<!-- Primary Category Navigation Bar -->
		<nav class="header-nav" aria-label="Main Category Navigation">
			<div class="container">
				<ul class="nav-list flex-align">
					<li class="nav-item">
						<a href="<?php echo url();?>" class="nav-link <?php echo ($currentPath === 'index') ? 'active' : '';?>">Home</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo url('newIn');?>" class="nav-link nav-link-highlight <?php echo ($currentPath === 'newin') ? 'active' : '';?>">
							<span><?php echo appT('new_in');?></span>
							<span class="nav-pill">NEW</span>
						</a>
					</li>
					<?php foreach ($navCategories as $cate) {
						$hasChildren = !empty($cate['children']);
						$isCateActive = ($currentPath === 'category' && ($currentCid === (int)$cate['cate_id'] || (!empty($rootCateId) && (int)$rootCateId === (int)$cate['cate_id'])));
					?>
					<li class="nav-item <?php echo $hasChildren ? 'has-submenu' : '';?>">
						<a href="<?php echo $cate['url'];?>" class="nav-link <?php echo $isCateActive ? 'active' : '';?>" title="<?php echo htmlspecialchars($cate['name'], ENT_QUOTES, 'UTF-8');?>">
							<span><?php echo htmlspecialchars($cate['name'], ENT_QUOTES, 'UTF-8');?></span>
							<?php if ($hasChildren) { ?>
							<svg class="icon-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
							<?php } ?>
						</a>
						<?php if ($hasChildren) { ?>
						<div class="nav-submenu">
							<div class="submenu-title"><?php echo htmlspecialchars($cate['name'], ENT_QUOTES, 'UTF-8');?></div>
							<ul class="submenu-grid">
								<?php foreach ($cate['children'] as $child) { ?>
								<li>
									<a href="<?php echo $child['url'];?>" title="<?php echo htmlspecialchars($child['name'], ENT_QUOTES, 'UTF-8');?>">
										<?php echo htmlspecialchars($child['name'], ENT_QUOTES, 'UTF-8');?>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>
					</li>
					<?php } ?>
				</ul>
			</div>
		</nav>
	</div>
</header>
