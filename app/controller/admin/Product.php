<?php

namespace app\controller\admin;
use app\controller\Base;

class Product extends Base
{
	public function __construct()
	{
        $this->_arr = [
            'index' => 'SPU列表',
        ];
		$this->_default = '产品管理';
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
		$spu = make('app/service/product/Spu');
		$statusList = $spu->getStatusList();
		//站点
		$siteList = make('app/service/Site')->getListData([], 'site_id,name');
		$siteList = array_column($siteList, 'name', 'site_id');
		//分类
		$cateList = make('app/service/Category')->getListFormat();
		$cateList = array_column($cateList, null, 'cate_id');
		$where = [];
		if (in_array($status, array_keys($statusList), true)) {
			$where['status'] = $status;
		}
		if ($site >= 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$spuIdArr = make('app/service/Category')->getSpuIdByCateId($cate);
			if (empty($spuIdArr)) {
				$where = ['spu_id' => 0];
			} else {
				$where['spu_id'] = ['in', $spuIdArr];
			}
		}

		$total = $spu->getCountData($where);
		if ($total > 0) {
			$list = $spu->getAdminList($where, $page, $size);
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
		$this->_init();
		
		$this->view();
	}

	public function detail()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		$this->_arr['detail'] = 'SPU详情';
		$this->_init();

		html()->addCss();
		html()->addJs();
		$id = (int)iget('id');
		$spu = make('app/service/product/Spu');
		$info = $spu->getAdminInfo($id);
		if (empty($info)) {
			$this->error('产品不存在');
		}
		//spu状态
		$spu = make('app/service/product/Spu');
		$statusList = $spu->getStatusList();
		//产品分类
		$cateList = make('app/service/Category')->getListFormat();
		$cateList = array_column($cateList, null, 'cate_id');

		$cateInfo = $this->getCateInfo($cateList, $info['cate_id']);
		
		$this->assign('info', $info);
		$this->assign('statusList', $statusList);
		$this->assign('cateList', $cateList);
		$this->assign('cateInfo', $cateInfo);
		$this->view();
	}

	protected function getCateInfo($cateList, $cateId)
	{
		$returnData = [];
		$returnData[] = $cateList[$cateId];
		if ($cateList[$cateId]['parent_id'] > 0) {
			$returnData = array_merge($this->getCateInfo($cateList, $cateList[$cateId]['parent_id']), $returnData);
		}
		return $returnData;
	}

	protected function editInfo()
	{
		$data = [];
		$param = ipost();
		$id = (int)$param['spu_id'];
		if ($id <= 0) {
			$this->error('ID不能为空');
		}
		if (isset($param['status'])) {
			$data['status'] = (int)$param['status'];
		}
		if (isset($param['free_ship'])) {
			$data['free_ship'] = (int)$param['free_ship'];
		}
		if (isset($param['cate_id'])) {
			$data['cate_id'] = (int)$param['cate_id'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = make('app/service/product/Spu')->updateData(['spu_id'=>$id], $data);
		if ($rst) {
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}
}