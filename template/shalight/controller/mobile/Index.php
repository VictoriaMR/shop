<?php

namespace template\shalight\controller\mobile;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addJs('slider');
		$banner = [];
		for ($i=1;$i<6;$i++) {
			$banner[] = [
				'title' => '',
				'image' => siteUrl('image/mobile/banner/banner'.$i.'.jpg'),
				'url'=> 'https://www.baidu.com',
			];
		}
		//获取热门分类
		$cateList = 
		$this->assign('banner', $banner);
	}
}