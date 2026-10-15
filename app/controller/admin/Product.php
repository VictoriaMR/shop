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
		$this->_init();
	}

	public function index()
	{	
		frame('Html')->addJs();
		frame('Html')->addCss();
		
		$status = iget('status/d', -1);
		$site = iget('site/d', -1);
		$cate = iget('cate/d', 0);
		$stime = iget('stime/t', '');
		$etime = iget('etime/t', '');
		$spuId = iget('spu_id/d', 0);
		$keyword = trim(iget('keyword', ''));
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		//spu状态
		$spu = service('product/Spu');
		$statusList = $spu->getStatusList();

		//分类
		$rawCateList = service('category/Category')->getListFormat(false);
		$cateArr = array_column($rawCateList, 'name', 'cate_id');
		$tempArr = [-1 => $rawCateList];
		$cateId = 0;
		foreach ($rawCateList as $value) {
			if ($value['parent_id'] == 0) {
				$cateId = $value['cate_id'];
				$tempArr[$cateId] = [];
			}
			$tempArr[$cateId][] = $value;
		}
		$cateList = $tempArr;

		$where = [];
		if ($spuId > 0) {
			$where['spu_id'] = $spuId;
		}
		if ($keyword !== '') {
			$lanSpuIds = service('product/Language')->getListData(['lan_id' => 0, 'name' => ['like', '%' . $keyword . '%']], 'spu_id');
			$lanSpuIds = array_column($lanSpuIds, 'spu_id');
			if (empty($lanSpuIds)) {
				$where['spu_id'] = 0;
			} elseif ($spuId > 0) {
				$where['spu_id'] = in_array($spuId, $lanSpuIds) ? $spuId : 0;
			} else {
				$where['spu_id'] = ['in', $lanSpuIds];
			}
		}
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
		$list = [];
		if ($total > 0) {
			$list = $spu->getList($where, '*', $page, $size, ['spu_id' => 'desc']);
			foreach ($list as $key => $value) {
				$list[$key]['status_text'] = $statusList[$value['status']] ?? '--';
			}
		}

		$this->view([
			'spuId'      => $spuId,
			'keyword'    => $keyword,
			'status'     => $status,
			'statusList' => $statusList,
			'site'       => $site,
			'cate'       => $cate,
			'cateList'   => $cateList,
			'cateArr'    => $cateArr,
			'stime'      => $stime,
			'etime'      => $etime,
			'total'      => $total,
			'size'       => $size,
			'list'       => $list,
		]);
	}

	public function purchaseList()
	{
		if (isAjax()) {
			$opn = ipost('opn');
			if (in_array($opn, ['edit'])) {
				$opn .= 'PurchaseList';
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = iget('id/d', 0);
		$status = iget('status/d', -1);
		$channelId = iget('channel_id/d', 0);
		$itemId = iget('item_id', '');
		$stime = iget('stime/t', '');
		$etime = iget('etime/t', '');
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($id > 0) {
			$where['purchase_spu_id'] = $id;
		}
		if ($status > -1) {
			$where['status'] = $status;
		}
		if ($channelId > 0) {
			$where['channel_id'] = $channelId;
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
		$total = purchase()->spu()->count($where);
		$list= [];
		if ($total > 0) {
			$list = purchase()->spu()->getListData($where, '*', $page, $size, ['purchase_spu_id' => 'desc']);
			// 用户
			$userList = array_filter(array_column($list, 'mem_id'));
			if (!empty($userList)) {
				$userList = service('member/Member')->getListData(['mem_id'=>['in', $userList]], 'mem_id,nick_name,avatar,sex');
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
				$list[$key]['url'] = purchase()->spu()->url($value['channel_id'], $value['item_id']);
				// 标题补全
				if ($value['status'] == purchase()->spu()->getConst('STATUS_SET') && !$value['name']) {
					$list[$key]['name'] = purchase()->spu()->updateTitle($value['channel_id'], $value['item_id']);
				}
			}
		}

		$channelList = purchase()->channel()->getListData();
		$channelList = array_column($channelList, 'name', 'channel_id');
		$statusList = purchase()->spu()->getStatusList();

		$this->view([
			'id' => $id,
			'status' => $status,
			'channel_id' => $channelId,
			'item_id' => $itemId,
			'stime' => $stime,
			'etime' => $etime,
			'total' => $total,
			'size' => $size,
			'list' => $list,
			'channelList' => $channelList,
			'statusList' => $statusList,
		]);
	}

	protected function editPurchaseList()
	{
		$id = ipost('id/d', 0);
		$status = ipost('status/d', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$statusList = purchase()->spu()->getStatusList();
		if (!isset($statusList[$status])) {
			$this->error('状态不正确');
		}
		$rst = purchase()->spu()->updateData($id, ['status'=>$status]);
		if ($rst) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	public function operate()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['attrMap', 'addProduct'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addCss();
		frame('Html')->addJs();

		$id = iget('id/d', 0);
		if ($id <= 0) {
			\App::error('参数不正确');
		} else {
			$info = purchase()->spu()->getInfo($id);
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

	protected function addProduct()
	{
		$id = ipost('id/d', 0);
		$site_id = ipost('site_id/d', 0);
		$cate_id = ipost('cate_id/d', 0);
		$spu_name = ipost('spu_name', '');
		if ($id <= 0) {
			$this->error('ID参数不正确');
		}
		if ($site_id <= 0) {
			$this->error('站点参数不正确');
		}
		if ($cate_id <= 0) {
			$this->error('分类参数不正确');
		}
		if (empty($spu_name)) {
			$this->error('SPU标题不能为空');
		}
		$info = purchase()->spu()->loadData($id);
		if (empty($info)) {
			$this->error('数据不存在');
		}
		$rst = purchase()->spu()->saveResult($info['channel_id'], $info['item_id'], ipost(), 'save_data');
		if ($rst) {
			purchase()->spu()->updateData($id, ['status'=>purchase()->spu()->getConst('STATUS_SPU')]);
			$this->success('保存成功');
		}
		$this->error('保存失败');
	}

	public function detail()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['editInfo', 'getSpuNameLanguage', 'transfer', 'editSpuLanguage', 'modifySpuImage', 'addSpuImage', 'deleteSpuImage', 'editSkuInfo', 'modifySkuAttrImage', 'modifySpuDesc', 'deleteSpuDesc', 'getSpuDescInfo', 'modifySpuIntroduceImage', 'deleteSpuIntroduceImage', 'addSpuIntroduceImage', 'modifySpuData', 'editDescGroupInfo'])) {
				$this->$opn();
			}
			$this->error('未知请求');
		}

		frame('Html')->addCss();
		frame('Html')->addJs();
		$spuService = service('product/Spu');
		$info = $spuService->getAdminInfo(iget('id/d', 0));
		if (!empty($info)) {
			//spu状态
			$statusList = $spuService->getStatusList();
			//站点分类
			$rawCateList = category()->getListFormat(false);
			$siteArr = service('site/Site')->getListData([], 'site_id,name,cate_id');
			$tempArr = [];
			$cateId = 0;
			foreach ($rawCateList as $value) {
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
			$info['category'] = array_reverse(category()->pCate($info['cate_id']));
			$rootCateId = $info['category'][0]['cate_id'] ?? 0;
			$siteCate = $cateArr[$info['site_id']] ?? ($tempArr[$rootCateId] ?? $rawCateList);

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
		$id = ipost('id/d', 0);
		if ($id <= 0) {
			$this->error('参数不正确');
		}
		$list = service('product/Language')->getListData(['spu_id'=>$id]);
		$list = array_column($list, null, 'lan_id');
		$languageList = sys()->language()->getListData();
		$result = [];
		foreach ($languageList as $value) {
			$lanId = $value['lan_id'];
			$result[$lanId] = [
				'lan_id'        => $lanId,
				'tr_code'       => $value['tr_code'],
				'name'          => $list[$lanId]['name'] ?? '',
				'language_name' => $lanId == 0 ? '中文名称' : ($value['name2'] ?: $value['name']),
			];
		}
		$this->success('获取成功', $result);
	}

	protected function transfer()
	{
		$trCode = ipost('tr_code');
		$name = ipost('name');
		if (empty($name) || empty($trCode)) {
			$this->error('参数不正确');
		}
		$rst = service('tool/Translate')->getText($name, $trCode);
		if ($rst !== false && $rst !== null && $rst !== '') {
			$this->success('翻译成功', $rst);
		}
		$this->error('翻译失败');
	}

	protected function editSpuLanguage()
	{
		$spuId = ipost('spu_id/d', 0);
		if ($spuId <= 0) {
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
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$sort = ipost('sort/d', 0);
		$rst = service('product/SpuImage')->updateData(['spu_id'=>$spuId, 'attach_id'=>$attachId], ['sort'=>$sort]);
		if ($rst !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$itemId = ipost('item_id/d', 0);
		if ($spuId <= 0 || $itemId <= 0) {
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
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$imageService = service('product/SpuImage');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$maxRow = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort');
		$data['sort'] = (int)($maxRow['sort'] ?? 0) + 1;
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
		$id = (int)($param['spu_id'] ?? 0);
		if ($id <= 0) {
			$this->error('ID不能为空');
		}
		if (isset($param['status'])) {
			$data['status'] = (int)$param['status'];
		}
		if (isset($param['free_ship'])) {
			$data['free_ship'] = (int)$param['free_ship'];
		}
		if (isset($param['is_hot'])) {
			$data['is_hot'] = (int)$param['is_hot'];
		}
		if (isset($param['cate_id'])) {
			$data['cate_id'] = (int)$param['cate_id'];
		}
		if (isset($param['gender'])) {
			$data['gender'] = (int)$param['gender'];
		}
		if (isset($param['attach_id'])) {
			$data['attach_id'] = (int)$param['attach_id'];
		}
		if (empty($data)) {
			$this->error('参数不正确');
		}
		$rst = service('product/Spu')->updateData(['spu_id'=>$id], $data);
		if ($rst !== false) {
			if (isset($data['attach_id'])) {
				service('product/SpuImage')->updateData(['spu_id'=>$id, 'attach_id'=>$data['attach_id']], ['sort'=>0]);
			}
			$this->success('修改成功');
		}
		$this->error('修改失败');
	}

	protected function editSkuInfo()
	{
		$skuId = ipost('sku_id');
		$spuId = ipost('spu_id/d', 0);
		if (empty($skuId) && $spuId <= 0) {
			$this->error('参数不正确');
		}
		if ($spuId > 0) {
			$skuId = service('product/Sku')->getListData(['spu_id'=>$spuId], 'sku_id');
			$skuId = array_column($skuId, 'sku_id');
		} elseif (!is_array($skuId)) {
			$skuId = [$skuId];
		}
		$param = ipost();
		$data = [];
		if (isset($param['attach_id'])) {
			$data['attach_id'] = (int)$param['attach_id'];
		}
		if (isset($param['status'])) {
			$data['status'] = (int)$param['status'];
		}
		if (isset($param['price'])) {
			$data['price'] = $param['price'];
		}
		if (isset($param['original_price'])) {
			$data['original_price'] = $param['original_price'];
		}
		if (isset($param['stock'])) {
			$data['stock'] = (int)$param['stock'];
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
		if ($rst !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function modifySkuAttrImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attrId = ipost('attrn_id/d', 0);
		$attvId = ipost('attrv_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attrId <= 0 || $attvId <= 0) {
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
		if ($rst !== false) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	protected function modifySpuDesc()
	{
		$id = ipost('item_id/d', 0);
		$param = ipost();
		$data = [];
		if (isset($param['sort'])) {
			$data['sort'] = (int)$param['sort'];
		}
		if (empty($data) && empty($param['spu_id'])) {
			$this->error('参数不正确');
		}
		if (!empty($param['spu_id'])) {
			$name = trim($param['name'] ?? '');
			$value = trim($param['value'] ?? '');
			if ($name === '') {
				$this->error('描述名不能为空');
			}
			if ($value === '') {
				$this->error('描述值不能为空');
			}
			$tempArr = service('desc/Name')->addNotExist($name);
			$data['descn_id'] = $tempArr[$name];
			$tempArr = service('desc/Value')->addNotExist($value);
			$data['descv_id'] = $tempArr[$value];
			$data['spu_id'] = (int)$param['spu_id'];
		}
		if ($id <= 0) {
			$rst = service('product/DescUsed')->addDescUsed((int)$param['spu_id'], $data);
		} else {
			$rst = service('product/DescUsed')->updateData($id, $data);
		}
		if ($rst !== false) {
			$this->success('操作成功');
		}
		$this->error('操作失败');
	}

	protected function deleteSpuDesc()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
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
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$info = service('product/DescUsed')->loadData($id);
		if ($info) {
			$tempArr = service('desc/Name')->loadData($info['descn_id'], 'name');
			$info['name'] = $tempArr['name'] ?? '';
			$tempArr = service('desc/Value')->loadData($info['descv_id'], 'name');
			$info['value'] = $tempArr['name'] ?? '';
			$this->success('获取成功', $info);
		}
		$this->error('获取失败');
	}

	protected function modifySpuIntroduceImage()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$sort = ipost('sort/d', 0);
		$rst = service('product/IntroUsed')->updateData($id, ['sort'=>$sort]);
		if ($rst !== false) {
			$this->success('排序成功');
		}
		$this->error('排序失败');
	}

	protected function deleteSpuIntroduceImage()
	{
		$id = ipost('item_id/d', 0);
		if ($id <= 0) {
			$this->error('ID值不正确');
		}
		$rst = service('product/IntroUsed')->deleteData($id);
		if ($rst) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	protected function addSpuIntroduceImage()
	{
		$spuId = ipost('spu_id/d', 0);
		$attachId = ipost('attach_id/d', 0);
		if ($spuId <= 0 || $attachId <= 0) {
			$this->error('参数不正确');
		}
		$imageService = service('product/IntroUsed');
		$data = ['spu_id'=>$spuId, 'attach_id'=>$attachId];
		if ($imageService->getCountData($data)) {
			$this->success('上传成功');
		}
		$maxRow = $imageService->loadData(['spu_id'=>$spuId], 'max(sort) as sort');
		$data['sort'] = (int)($maxRow['sort'] ?? 0) + 1;
		$rst = $imageService->insert($data);
		if ($rst) {
			$this->success('上传成功');
		}
		$this->error('上传失败');
	}

	protected function modifySpuData()
	{
		$spuId = ipost('spu_id/d', 0);
		$name = trim(ipost('name', ''));
		$value = ipost('value', '');
		if ($spuId <= 0 || $name === '') {
			$this->error('参数不正确');
		}
		$spuDataService = service('product/SpuData');
		if ($spuDataService->getCountData(['spu_id' => $spuId]) > 0) {
			$rst = $spuDataService->updateData($spuId, [$name => $value]);
		} else {
			$rst = $spuDataService->insert(['spu_id' => $spuId, $name => $value]);
		}
		if ($rst !== false) {
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
		if ($groupId === null || $groupId === '') {
			$this->error('分组ID值不正确');
		}
		$rst = service('product/DescUsed')->updateData(['item_id'=>['in', $idArr]], ['descg_id'=>(int)$groupId]);
		if ($rst !== false) {
			$this->success('更新成功');
		}
		$this->error('更新失败');
	}

	public function purchaseShop()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$status = iget('status/d', -1);
		$channelId = iget('channel_id/d', 0);
		$uniqueId = iget('unique_id', '');
		$name = trim(iget('name', ''));
		$page = iget('page/d', 1);
		$size = iget('size/d', 30);

		$where = [];
		if ($channelId > 0) {
			$where['channel_id'] = $channelId;
		}
		if ($uniqueId) {
			$where['unique_id'] = $uniqueId;
		}
		if ($name) {
			$where['name'] = ['like', '%'.$name.'%'];
		}
		$total = purchase()->shop()->count($where);
		$list = [];
		if ($total > 0) {
			$list = purchase()->shop()->getListData($where, '*', $page, $size, ['purchase_shop_id' => 'desc']);
			$shopIds = array_unique(array_column($list, 'purchase_shop_id'));
			if (!empty($shopIds)) {
				$productCount = purchase()->spu()->getListData(['purchase_shop_id'=>['in', $shopIds]], 'count(*) as c_count,purchase_shop_id', 0, 0, [], 'purchase_shop_id');
				$productCount = array_column($productCount, 'c_count', 'purchase_shop_id');
			}
			foreach ($list as $key=>$value) {
				$list[$key]['product_count'] = $productCount[$value['purchase_shop_id']] ?? 0;
			}
		}

		$channelList = service('purchase/Channel')->getListData();
		$channelList = array_column($channelList, 'name', 'channel_id');

		$this->view([
			'status' => $status,
			'channel_id' => $channelId,
			'unique_id' => $uniqueId,
			'name' => $name,
			'total' => $total,
			'size' => $size,
			'list' => $list,
			'channelList' => $channelList,
		]);
	}
}