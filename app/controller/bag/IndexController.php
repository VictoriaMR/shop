<?php

namespace app\controller\bag;
use app\controller\Controller;

class IndexController extends Controller
{
	public function index()
	{
		make('app/task/main/QueueTask')->run();
		dd('123123');
		html()->addCss();
		html()->addJs();
		html()->addJs('slider');

		//分类列表
		$hotCategory = make('app/service/CategoryService')->getHotCategory(16);
		$banner = [];
		for ($i=1;$i<6;$i++) {
			$banner[] = [
				'title' => '',
				'image' => siteUrl('image/mobile/banner/banner'.$i.'.jpg'),
				'url'=> '',
			];
		}
		$this->assign('banner', $banner);
		$this->assign('hot_category', array_chunk($hotCategory, 2));
		$this->view();
	}
}