<?php

namespace app\controller\admin;
use app\controller\Controller;

class ProductController extends Controller
{
	public function __construct()
	{
        $this->_arr = [
            'index' => 'SPU列表',
        ];
		$this->_default = '产品管理';
		$this->_init();
	}

	public function index()
	{	
		html()->addCss();
		
		$status = (int)iget('status', -1);
		$site = (int)iget('site', -1);
		$cate = (int)iget('cate', 0);
		$stime = trim(iget('stime'));
		$etime = trim(iget('etime'));
		$spuId = (int)iget('spu_id');
		$page = (int)iget('page', 1);
		$size = (int)iget('size', 20);
		//spu状态
		$spuService = make('app/service/product/SpuService');
		$statusList = $spuService->getStatusList();
		//站点
		$siteList = make('app/service/SiteService')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//分类
		$cateList = make('app/service/CategoryService')->getListFormat();
		$cateList = array_column($cateList, null, 'cate_id');
		$where = [];
		if (in_array($status, array_keys($statusList), true)) {
			$where['status'] = $status;
		}
		if ($site >= 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$spuIdArr = make('app/service/CategoryService')->getSpuIdByCateId($cate);
			if (empty($spuIdArr)) {
				$where = ['spu_id' => 0];
			} else {
				$where['spu_id'] = ['in', $spuIdArr];
			}
		}

		$total = $spuService->getCountData($where);
		if ($total > 0) {
			$list = $spuService->getAdminList($where, $page, $size);
		}

		$this->assign('spuId', $spuId);
		$this->assign('status', $status);
		$this->assign('statusList', $statusList);
		$this->assign('site', $site);
		$this->assign('siteList', $siteList);
		$this->assign('cate', $cate);
		$this->assign('cateList', $cateList);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);

		$this->view();
	}

	public function detail()
	{
		html()->addCss();
		html()->addJs();
		$id = (int)iget('id');
		$spuService = make('app/service/product/SpuService');
		$info = $spuService->getAdminInfo($id);
		
		$this->assign('info', $info);

		$this->_arr['detail'] = 'SPU详情';
		$this->_init();
		$this->view();
	}
}