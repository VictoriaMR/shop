<?php
$curPath = strtolower(\App::get('router', 'path', ''));
$curFunc = strtolower(\App::get('router', 'func', 'index'));
$activeKey = $curPath . '/' . $curFunc;
$memInfo = session()->get(type() . '_info') ?: [];
$memEmail = $memInfo['email'] ?? 'Guest Shopper';
$memName = trim(($memInfo['first_name'] ?? '') . ' ' . ($memInfo['last_name'] ?? ''));
if ($memName === '') {
	$memName = !empty($memInfo['email']) ? explode('@', $memInfo['email'])[0] : 'Welcome Guest';
}

$navLinks = [
	[
		'key'   => 'userinfo/index',
		'label' => 'Account Overview',
		'url'   => url('userInfo'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
	],
	[
		'key'   => 'order/index',
		'match' => ['order/index', 'order/detail', 'order/search', 'order/trash'],
		'label' => 'My Orders',
		'url'   => url('order'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>',
	],
	[
		'key'   => 'userinfo/wishlist',
		'label' => 'Saved Wishlist',
		'url'   => url('userInfo/wishList'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>',
	],
	[
		'key'   => 'userinfo/address',
		'label' => 'Address Book',
		'url'   => url('userInfo/address'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
	],
	[
		'key'   => 'userinfo/coupon',
		'label' => 'My Coupons',
		'url'   => url('userInfo/coupon'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>',
	],
	[
		'key'   => 'userinfo/history',
		'label' => 'Recently Viewed',
		'url'   => url('userInfo/history'),
		'icon'  => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
	],
];
?>
<aside class="member-sidebar" aria-label="Account Navigation">
	<div class="member-user-card">
		<div class="member-avatar" aria-hidden="true">
			<?php echo strtoupper(substr($memName, 0, 1)); ?>
		</div>
		<div class="member-user-meta">
			<strong class="member-user-name text-ellipsis"><?php echo htmlspecialchars($memName, ENT_QUOTES, 'UTF-8'); ?></strong>
			<span class="member-user-email text-ellipsis"><?php echo htmlspecialchars($memEmail, ENT_QUOTES, 'UTF-8'); ?></span>
		</div>
	</div>

	<nav class="member-nav-list">
		<?php foreach ($navLinks as $nav) {
			$matches = $nav['match'] ?? [$nav['key']];
			$isCurrent = in_array($activeKey, $matches, true);
		?>
		<a
			href="<?php echo htmlspecialchars($nav['url'], ENT_QUOTES, 'UTF-8'); ?>"
			class="member-nav-item<?php echo $isCurrent ? ' active' : ''; ?>"
			<?php echo $isCurrent ? 'aria-current="page"' : ''; ?>
		>
			<?php echo $nav['icon']; ?>
			<span><?php echo htmlspecialchars($nav['label'], ENT_QUOTES, 'UTF-8'); ?></span>
		</a>
		<?php } ?>

		<?php if (userId() > 0) { ?>
		<a href="<?php echo url('login/logout'); ?>" class="member-nav-item logout">
			<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
			<span>Sign Out</span>
		</a>
		<?php } else { ?>
		<a href="<?php echo url('login'); ?>" class="member-nav-item">
			<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
			<span>Sign In / Register</span>
		</a>
		<?php } ?>
	</nav>
</aside>
