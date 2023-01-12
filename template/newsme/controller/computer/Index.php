<?php

namespace template\newsme\controller\computer;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addCss('slider');
		html()->addCss('clothes-icon');
		html()->addJs('slider');

		$page = iget('page', 1);
		$size = iget('size', 20);

		$cateArr = make('app/service/category/Category')->getSiteCateList(cateId());
		$hotArr = [];
		$popularCate = [];
		$leftCate = [];
		foreach ($cateArr as $value) {
			if ($value['is_hot']) {
				if ($value['icon']) {
					$popularCate[] = $value;
				} else {
					$hotArr[] = $value;
				}
			} else {
				$leftCate[] = $value;
			}
		}
		$hotArr = array_slice($hotArr, 0, 6);
		$popularCate = array_slice($popularCate, 0, 12);

		$bannerPath = ROOT_PATH.'template'.DS.path().DS;
		$arr = getDirFile($bannerPath.'image'.DS.'computer'.DS.'banner');
		$banner = [];
		foreach ($arr as $key=>$value) {
			if (!isset($hotArr[$key])) break;
			$banner[] = [
				'url' => url($hotArr[$key]['name_en'], ['c'=>$hotArr[$key]['cate_id']]),
				'image' => siteUrl(str_replace($bannerPath, '', $value)),
				'name_en' => $hotArr[$key]['name_en'],
			];
		}

		$bestSeller = make('app/service/product/Spu')->getRecommend($page, $size, $total);


		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('total', $total);
		$this->assign('banner', $banner);
		$this->assign('leftCate', $leftCate);
		$this->assign('hotArr', $hotArr);
		$this->assign('popularCate', $popularCate);
		$this->assign('bestSeller', $bestSeller);

		$this->assign('_title', appt('_title'));
		$this->assign('_desc', appt('_desc'));
		$this->assign('_keyword', appt('_keyword'));
	}
}