<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Login extends Base
{
	public function index()
	{
		if (userId() > 0) {
			redirect(url('userInfo'));
		}

		$siteName = \App::get('domain', 'name', 'Shop');
		$rawTab = strtolower(trim((string)iget('tab', 'signin')));
		$validTabs = ['signin', 'register', 'forgot'];
		$activeTab = in_array($rawTab, $validTabs, true) ? $rawTab : 'signin';

		$prefillEmail = trim((string)iget('email', ''));
		$prefillCode = trim((string)iget('verify_code', ''));

		$memberBenefits = [
			[
				'icon'  => 'gift',
				'title' => 'Welcome Member Perks',
				'desc'  => 'Unlock member-only launch prices and seasonal voucher drops',
			],
			[
				'icon'  => 'truck',
				'title' => 'Real-Time Order Tracking',
				'desc'  => 'Track every package from studio dispatch to doorstep delivery',
			],
			[
				'icon'  => 'heart',
				'title' => 'Synced Wishlist & Bag',
				'desc'  => 'Save your favorite furniture, lighting & apparel across devices',
			],
			[
				'icon'  => 'shield',
				'title' => '30-Day Buyer Protection',
				'desc'  => 'Fast, hassle-free returns and dedicated 24/7 order support',
			],
		];

		$this->assign('activeTab', $activeTab);
		$this->assign('prefillEmail', $prefillEmail);
		$this->assign('prefillCode', $prefillCode);
		$this->assign('memberBenefits', $memberBenefits);

		$this->assign('_title', distT('seo_title') . ' - ' . $siteName);
		$this->assign('_keyword', distT('seo_keyword'));
		$this->assign('_desc', distT('seo_desc'));
		$this->assign('_canonical', url('login'));
	}
}
