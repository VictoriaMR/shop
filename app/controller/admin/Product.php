<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Product extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => 'SPU列表',
			'purchaseList' => '采购产品列表',
			'detail' => 'SPU详情',
			'operate' => '添加产品',
			'purchaseShop' => '供应商店铺',
		];
		$this->_ignore = ['detail', 'operate'];
		$this->_default = '产品管理';
		parent::_init();
	}

	public function index()
	{	
		html()->addJs();
		html()->addCss();
		
		$status = iget('status', -1);
		$site = iget('site', -1);
		$cate = iget('cate', 0);
		$stime = iget('stime');
		$etime = iget('etime');
		$spuId = iget('spu_id');
		$page = iget('page', 1);
		$size = iget('size', 30);
		//spu状态
		$spu = service('product/Spu');
		$statusList = $spu->getStatusList();
		//分类
		$cateList = service('category/Category')->getListFormat(false);
		$cateArr = array_column($cateList, 'name', 'cate_id');
		$tempArr = [];
		$cateId = 0;
		foreach ($cateList as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
		}
		$cateList = [];
		$where = [];
		if ($status > -1) {
			$where['status'] = $status;
		}
		if ($site >= 0) {
			$where['site_id'] = $site;
		}
		if ($cate > 0) {
			$where['cate_id'] = $cate;
		}
		if ($stime && $etime) {
			$where['add_time'] = ['between', [date('Y-m-d', strtotime($stime)).' 00:00:00', date('Y-m-d', strtotime($etime)).' 23:59:59']];
		} elseif ($stime) {
			$where['add_time'] = ['>=', date('Y-m-d', strtotime($stime)).' 00:00:00'];
		} elseif ($etime) {
			$where['add_time'] = ['<=', date('Y-m-d', strtotime($etime)).' 23:59:59'];
		}
		$total = $spu->getCountData($where);
		if ($total > 0) {
			$list = $spu->getList($where, '*', $page, $size);
			foreach ($list as $key=>$value) {
				$list[$key]['status_text'] = $statusList[$value['status']];
			}
		}

		$this->assign('spuId', $spuId);
		$this->assign('status', $status);
		$this->assign('statusList', $statusList);
		$this->assign('site', $site);
		$this->assign('cate', $cate);
		$this->assign('cateList', $cateList);
		$this->assign('cateArr', $cateArr);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->view();
	}

	public function purchaseList()
	{
		html()->addCss();
		html()->addJs();

		$id = iget('id/d', 0);
		$status = iget('status/d', -1);
		$channelId = iget('purchase_channel_id/d', 0);
		$itemId = iget('item_id', '');
		$stime = iget('stime/t', '');
		$etime = iget('etime/t', '');
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($id > 0) {
			$where['purchase_product_id'] = $id;
		}
		if ($status > -1) {
			$where['status'] = $status;
		}
		if ($channelId > 0) {
			$where['purchase_channel_id'] = $channelId;
		}
		if ($itemId) {
			$where['item_id'] = $itemId;
		}
		if ($stime && $etime) {
			$where['add_time'] = ['between', [date('Y-m-d', strtotime($stime)).' 00:00:00', date('Y-m-d', strtotime($etime)).' 23:59:59']];
		} elseif ($stime) {
			$where['add_time'] = ['>=', date('Y-m-d', strtotime($stime)).' 00:00:00'];
		} elseif ($etime) {
			$where['add_time'] = ['<=', date('Y-m-d', strtotime($etime)).' 23:59:59'];
		}
		$total = purchase()->product()->count($where);
		if ($total > 0) {
			$list = purchase()->product()->getListData($where, '*', $page, $size, ['purchase_product_id' => 'desc']);
			// 用户
			$userList = array_filter(array_column($list, 'mem_id'));
			if (!empty($userList)) {
				$userList = service('Member')->getListData(['mem_id'=>['in', $userList]], 'mem_id,nick_name,avatar,sex');
				$userList = array_column($userList, null, 'mem_id');
			}
			// 店铺
			$shopList = array_filter(array_column($list, 'purchase_shop_id'));
			if (!empty($shopList)) {
				$shopList = purchase()->shop()->getListData(['purchase_shop_id'=>['in', $shopList]]);
				$shopList = array_column($shopList, null, 'purchase_shop_id');
			}
			foreach ($list as $key=>$value) {
				$list[$key]['user_info'] = $this->avatar($userList[$value['mem_id']] ?? '');
				$list[$key]['shop_info'] = $shopList[$value['purchase_shop_id']] ?? [];
				$list[$key]['url'] = purchase()->product()->url($value['purchase_channel_id'], $value['item_id']);
				// 标题补全
				if ($value['status'] == purchase()->product()->getConst('STATUS_SET') && !$value['name']) {
					$list[$key]['name'] = purchase()->product()->updateTitle($value['purchase_channel_id'], $value['item_id']);
				}
			}
		}

		$channelList = service('purchase/Channel')->getListData();
		$channelList = array_column($channelList, 'name', 'purchase_channel_id');
		$statusList = purchase()->product()->getStatusList();

		$this->assign('id', $id);
		$this->assign('status', $status);
		$this->assign('purchase_channel_id', $channelId);
		$this->assign('item_id', $itemId);
		$this->assign('stime', $stime);
		$this->assign('etime', $etime);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('channelList', $channelList);
		$this->assign('statusList', $statusList);
		$this->view();
	}

	public function operate()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['attrMap'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		html()->addCss();
		html()->addJs();

		$id = iget('id/d', 0);
		if ($id <= 0) {
			\App::error('参数不正确');
		} else {
			$info = purchase()->product()->getInfo($id);
			if (empty($info['sku'])) {
				\App::error('数据不存在, 请重新上传');
			} else {
				$shopInfo = purchase()->shop()->loadData($info['purchase_shop_id']);
				// 映射属性名
				$attrNs = attr()->nameMap()->getMapList(array_column($info['attr'], 'name'));
				$attrNs = array_column($attrNs, null, 'name');
				// 映射属性值
				$attrVs = [];
				foreach ($info['attr'] as $value) {
					$attrVs = array_merge($attrVs, array_column($value['value'], 'name'));
				}
				$attrVs = attr()->valueMap()->getMapList($attrVs);
				$attrVs = array_column($attrVs, null, 'name');

				$siteList = site()->getListData(['site_id'=>['>', 80]], 'site_id,name');
				$cateList = category()->getListFormat(false);
				
				$this->assign('attrNs', $attrNs);
				$this->assign('attrVs', $attrVs);
				$this->assign('info', $info);
				$this->assign('shopInfo', $shopInfo);
				$this->assign('siteList', $siteList);
				$this->assign('cateList', $cateList);	
			}
		}
		$this->view();
	}

	protected function attrMap()
	{
		$name = ipost('name', '');
		$fromName = ipost('from_name', '');
		$type = ipost('type/d', 0);
		$attr = ipost('attr/a', []);
		if (empty($name) || empty($fromName) || !in_array($type, [1, 2])) {
			$this->error('参数不正确');
		}
		if ($type == 1) {
			$rst = attr()->nameMap()->addMap($fromName, $name);
		} else {
			$rst = attr()->valueMap()->addMap($fromName, $name, $attr);
		}
		if ($rst) {
			$this->success('添加属性映射成功');
		}
		$this->error('添加属性映射失败');
	}

	public function detail()
	{
		if (request()->isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo', 'getSpuNameLanguage', 'transfer', 'editSpuLanguage', 'modifySpuImage', 'addSpuImage', 'deleteSpuImage', 'editSkuInfo', 'modifySkuAttrImage', 'modifySpuDesc', 'deleteSpuDesc', 'getSpuDescInfo', 'modifySpuIntroduceImage', 'deleteSpuIntroduceImage', 'addSpuIntroduceImage', 'modifySpuData', 'editDescGroupInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		html()->addCss();
		html()->addJs();
		$spuService = service('product/Spu');
		$info = $spuService->getAdminInfo(iget('id'));
		if (!empty($info)) {
			//spu状态
			$statusList = $spuService->getStatusList();
			//站点分类
			$cateArr = category()->getListFormat();
			$siteArr = service('site/Site')->getListData([], 'site_id,name,cate_id');
			$tempArr = [];
			$cateId = 0;
			foreach ($cateArr as $value) {
				if ($value['parent_id'] == 0) {
					$cateId = $value['cate_id'];
					$tempArr[$cateId] = [];
				}
				$tempArr[$cateId][] = $value;
			}
			$cateArr = [];
			foreach ($siteArr as $value) {
				$cateArr[$value['site_id']] = $tempArr[$value['cate_id']] ?? [];
			}
			$siteCate = $cateArr[$info['site_id']];

			$info['category'] = array_reverse(category()->pCate($info['cate_id']));

			$tempArr = [];
			foreach ($info['desc'] as $value) {
				if (!isset($tempArr[$value['descg_id']])) {
					$tempArr[$value['descg_id']] = [
						'descg_id' => $value['descg_id'],
						'group' => $value['group'],
						'list' => [],
					];
				}
				$tempArr[$value['descg_id']]['list'][] = $value;
			}
			$info['desc'] = $tempArr;

			//分组列表
			$groupList = service('desc/Group')->getListData();
			$this->assign('info', $info);
			$this->assign('statusList', $statusList);
			$this->assign('groupList', $groupList);
			$this->assign('siteCate', $siteCate);
		}
		$this->view();
	}

	protected function getSpuNameLanguage()
	{
		$id = ipost('id');
		$list = service('product/Language')->getListData(['spu_id'=>$id]);
		$list = array_column($list, null, 'lan_id');
		$list[0]['language_name'] = '名称';
		$languageList = service('Language')->getListData();
		foreach ($languageList as $key => $value) {
			$list[$value['lan_id']] = [
				'lan_id' => $value['lan_id'],
				'tr_code' => $value['tr_code'],
				'name' => empty($list[$value['lan_id']]) ? '' : $list[$value['lan_id']]['name'],
				'language_name' => $value['name2'],
			];
		}
		$this->success($list, '');
	}

	protected function editSpuLanguage()
	{
		$spuId = ipost('spu_id');
		if (empty($spuId)) {
			$this->error('ID值不正确');
		}
		$language = ipost('language');
		if (!empty($language)) {
			$services = service('product/Language');
			foreach ($language as $key => $value) {
				$services->setNxLanguage($spuId, $key, strTrim($value));
			}
		}
		$this->addLog('修改产品语言-'.$spuId);
		$this->success('操作成功');
	}

	protected function modifySpuImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort');
		$rst = service('product/SpuImage')->updateData(['spu_id'=>$spuId, 'attach_id'=>$attachId], ['sort'=>$sort]);
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuImage()
	{
		$spuId = ipost('spu_id');
		$itemId = ipost('item_id');
		if (empty($spuId) || empty($itemId)) {
			$this->error('参数不正确');
		}
		$rst = service('product/SpuImage')->deleteData(['spu_id'=>$spuId, 'item_id'=>$itemId]);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$imageService = service('product/SpuImage');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$data['sort'] = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort')['sort']+1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function editInfo()
	{
		$data = [];
		$param = ipost();
		$id = $param['spu_id'];
		if ($id <= 0) {
			$this->error('ID不能为空');
		}
		if (isset($param['status'])) {
			$data['status'] = $param['status'];
		}
		if (isset($param['free_ship'])) {
			$data['free_ship'] = $param['free_ship'];
		}
		if (isset($param['cate_id'])) {
			$data['cate_id'] = $param['cate_id'];
		}
		if (isset($param['gender'])) {
			$data['gender'] = $param['gender'];
		}
		if (isset($param['attach_id'])) {
			$data['attach_id'] = $param['attach_id'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = service('product/Spu')->updateData(['spu_id'=>$id], $data);
		if ($rst) {
			if (isset($data['attach_id'])) {
				service('product/SpuImage')->updateData(['spu_id'=>$id, 'attach_id'=>$data['attach_id']], ['sort'=>0]);
			}
			$this->success('修改成功');
		} else {
			$this->error('修改失败');
		}
	}

	protected function editSkuInfo()
	{
		$skuId = ipost('sku_id');
		$spuId = ipost('spu_id');
		if (empty($skuId) && empty($spuId)) {
			$this->error('参数不正确');
		}
		if (!empty($spuId)) {
			$skuId = service('product/Sku')->getListData(['spu_id'=>$spuId], 'sku_id');
			$skuId = array_column($skuId, 'sku_id');
		} elseif (!is_array($skuId)) {
			$skuId = [$skuId];
		}
		$param = ipost();
		$data = [];
		if (isset($param['attach_id'])) {
			$data['attach_id'] = $param['attach_id'];
		}
		if (isset($param['status'])) {
			$data['status'] = $param['status'];
		}
		if (isset($param['price'])) {
			$data['price'] = $param['price'];
		}
		if (isset($param['original_price'])) {
			$data['original_price'] = $param['original_price'];
		}
		if (isset($param['stock'])) {
			$data['stock'] = $param['stock'];
		}
		if (isset($param['cost_price'])) {
			$data['cost_price'] = $param['cost_price'];
		}
		if (isset($param['volume'])) {
			$data['volume'] = $param['volume'];
		}
		if (isset($param['weight'])) {
			$data['weight'] = $param['weight'];
		}
		if (empty($data)) {
			$this->error('更新的参数为空');
		}
		if (isset($data['cost_price'])) {
			$service = service('product/SkuData');
		} else {
			$service = service('product/Sku');
		}
		$rst = $service->updateData(['sku_id'=>['in', $skuId]], $data);
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function modifySkuAttrImage()
	{
		$spuId = ipost('spu_id');
		$attrId = ipost('attrn_id');
		$attvId = ipost('attrv_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attrId) || empty($attvId)) {
			$this->error('参数不正确');
		}
		$skuService = service('product/Sku');
		$skuId = $skuService->getListData(['spu_id'=>$spuId], 'sku_id');
		if (empty($skuId)) {
			$this->error('SKU ID不能为空');
		}
		$skuId = array_column($skuId, 'sku_id');
		$attrUsed = service('product/AttrUsed');
		$list = $attrUsed->getListData(['sku_id'=>['in', $skuId], 'attrn_id'=>$attrId, 'attrv_id'=>$attvId], 'item_id,sku_id');
		if (empty($list)) {
			$this->error('找不到sku属性');
		}
		$rst = $attrUsed->updateData(['item_id'=>['in', array_column($list, 'item_id')]], ['attach_id'=>$attachId]);
		$skuService->updateData(['sku_id'=>['in', array_unique(array_column($list, 'sku_id'))]], ['attach_id'=>$attachId]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function modifySpuDesc()
	{
		$id = ipost('item_id');
		$param = ipost();
		$data = [];
		if (isset($param['sort'])) {
			$data['sort'] = $param['sort'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		if (!empty($param['spu_id'])) {
			if (empty($param['name'])) {
				$this->error('描述名不能为空');
			}
			if(empty($param['value'])) {
				$this->error('描述值不能为空');
			}
			$tempArr = service('desc/Name')->addNotExist($param['name']);
			$data['descn_id'] = $tempArr[$param['name']];
			$tempArr = service('desc/Value')->addNotExist($param['value']);
			$data['descv_id'] = $tempArr[$param['value']];
			$data['spu_id'] = $param['spu_id'];
		}
		if (empty($id)) {
			$rst = service('product/DescUsed')->addDescUsed($param['spu_id'], $data);
		} else {
			$rst = service('product/DescUsed')->updateData($id, $data);
		}
		if ($rst) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuDesc()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$rst = service('product/DescUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function getSpuDescInfo()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$info = service('product/DescUsed')->loadData($id);
		if ($info) {
			$tempArr = service('desc/Name')->loadData($info['descn_id'], 'name');
			$info['name'] = $tempArr['name'];
			$tempArr = service('desc/Value')->loadData($info['descv_id'], 'name');
			$info['value'] = $tempArr['name'];
			$this->success($info, '获取成功');
		}
		$this->error('获取失败');
	}

	protected function modifySpuIntroduceImage()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort');
		$rst = service('product/IntroduceUsed')->updateData($id, ['sort'=>$sort]);
		if ($rst) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteSpuIntroduceImage()
	{
		$id = ipost('item_id');
		if (empty($id)) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort');
		$rst = service('product/IntroduceUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuIntroduceImage()
	{
		$spuId = ipost('spu_id');
		$attachId = ipost('attach_id');
		if (empty($spuId) || empty($attachId)) {
			$this->error('参数不正确');
		}
		$imageService = service('product/IntroduceUsed');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$data['sort'] = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort')['sort']+1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function modifySpuData()
	{
		$spuId = ipost('spu_id');
		$name = ipost('name');
		$value = ipost('value');
		if (empty($spuId) || empty($name)) {
			$this->error('参数不正确');
		}
		$rst = service('product/SpuData')->updateData($spuId, [$name=>$value]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function editDescGroupInfo()
	{
		$idArr = ipost('id');
		$groupId = ipost('group_id');
		if (empty($idArr)) {
			$this->error('ID值不正确');
		}
		if (empty($groupId)) {
			$this->error('分组ID值不正确');
		}
		$rst = service('product/DescUsed')->updateData(['item_id'=>['in', $idArr]], ['descg_id'=>$groupId]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	public function purchaseShop()
	{

		html()->addCss();
		html()->addJs();

		$status = iget('status/d', -1);
		$channelId = iget('purchase_channel_id/d', 0);
		$uniqueId = iget('unique_id', '');
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($channelId > 0) {
			$where['purchase_channel_id'] = $channelId;
		}
		if ($uniqueId) {
			$where['unique_id'] = $uniqueId;
		}
		$total = purchase()->shop()->count($where);
		if ($total > 0) {
			$list = purchase()->shop()->getListData($where, '*', $page, $size, ['purchase_shop_id' => 'desc']);
			$shopIds = array_unique(array_column($list, 'purchase_shop_id'));
			if (!empty($shopIds)) {
				$productCount = service('purchase/Product')->getListData(['purchase_shop_id'=>['in', $shopIds]], 'count(*) as c_count,purchase_shop_id', 0, 0, [], 'purchase_shop_id');
				$productCount = array_column($productCount, 'c_count', 'purchase_shop_id');
			}
			foreach ($list as $key=>$value) {
				$list[$key]['product_count'] = $productCount[$value['purchase_shop_id']] ?? 0;
			}
		}

		$channelList = service('purchase/Channel')->getListData();
		$channelList = array_column($channelList, 'name', 'purchase_channel_id');

		$this->assign('status', $status);
		$this->assign('purchase_channel_id', $channelId);
		$this->assign('unique_id', $uniqueId);
		$this->assign('total', $total);
		$this->assign('size', $size);
		$this->assign('list', $list ?? []);
		$this->assign('channelList', $channelList);
		$this->view();
	}
}