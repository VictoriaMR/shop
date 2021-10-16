<?php

namespace app\controller\home;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addCss();
		html()->addJs();
		html()->addJs('slider');

		//分类列表
		// $hotCategory = make('app/service/category/Category')->getHotCategory(16);
		$banner = [];
		for ($i=1;$i<6;$i++) {
			$banner[] = [
				'title' => '',
				'image' => siteUrl('image/mobile/banner/banner'.$i.'.jpg'),
				'url'=> '',
			];
		}
		$this->assign('banner', $banner);
		// $this->assign('hot_category', array_chunk($hotCategory, 2));

		$this->assign('_title', 'home');
		$this->view();
	}
}