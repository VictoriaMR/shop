<?php

namespace template\newsme\controller\mobile;
use app\controller\Base;

class Index extends Base
{
	public function index()
	{
		html()->addCss('common/productList');
		html()->addCss('clothes-icon');
		html()->addJs('slider');
		$page = iget('page', 1);
		$size = iget('size', 20);

		if ($page <= 1) {
			$cateArr = make('app/service/category/Category')->getSiteCateList(cateId());
			$hotArr = [];
			$popularCate = [];
			$cateList = [];
			foreach ($cateArr as $value) {
				if ($value['is_hot']) {
					if ($value['icon']) {
						$popularCate[] = $value;
					} else {
						$hotArr[] = $value;
					}
				} else {
					$cateList[] = $value;
				}
			}
			$hotArr = array_slice($hotArr, 0, 6);
			$popularCate = array_chunk(array_slice($popularCate, 0, 12), 2);

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
			$this->assign('popularCate', $popularCate);
		}
		//获取SPU列表
		$size = 20;
		$bestSeller = make('app/service/product/Spu')->getRecommend($page, $size, $total);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('page', $page);
		$this->assign('bestSeller', $bestSeller);
		$this->assign('banner', $banner ?? []);

		$this->assign('_title', appt('_title'));
		$this->assign('_desc', appt('_desc'));
		$this->assign('_keyword', appt('_keyword'));
	}
}