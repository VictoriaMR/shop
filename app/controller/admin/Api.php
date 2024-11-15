<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Api extends AdminBase
{
	protected $_cateArr = ['category', 'product', 'introduce'];
	protected $_helperAction = [
		[
			'title' => '产品入库',
			'name' => 'crawler',
		],
		[
			'title' => '产品维护',
			'name' => 'auto_check',
		],
	];

	public function helperData()
	{
		$data = [
			'domain' => 'https://'.$_SERVER['HTTP_HOST'],
			'version' => version(),
			'socket_domain' => 'https://'.$_SERVER['HTTP_HOST'].'/socket.io/',
			'action' => $this->_helperAction,
		];
		$this->success('获取成功', $data);
	}

	public function upload()
	{	
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error('上传数据为空');
		}
		$cate = $_POST['cate'] ?? '';
		if (!in_array($cate, $this->_cateArr)) {
			$this->error('没有权限操作'.$cate.'文件夹');
		}
		$fileService = service('File');
		$result = $fileService->upload($file, $cate);
		if ($result) {
			$this->success('上传成功', $result);
		}
		$this->error('上传失败');
	}

	public function stat()
	{
		$url = ipost('url', '');
		$url = parse_url($url);
		$data = [];
		if (isset($url['path'])) {
			$data['path'] = trim($url['path'], '/');
		}
		if (!empty($url['query'])) {
			$data['path'] .= '?'.$url['query'];
		}
		service('log/Base')->visitor()->add($data);
		$this->success('success');
	}

	public function addProduct()
	{
		$data = ipost();
		if (empty($data)) {
			$this->error('参数不能为空');
		}
		$channelId = 0;
		switch ($data['domain']) {
			case 'taobao':
				$channelId = 6051;
				break;
			case 'tmall':
				$channelId = 6052;
				break;
			case '1688':
				$channelId = 6053;
				break;
		}
		$where = [
			'purchase_channel_id' => $channelId,
			'item_id' => $data['item_id'],
		];
		$id = purchase()->product()->loadData($where, 'purchase_product_id')['purchase_product_id'] ?? 0; 
		if ($id <= 0) {
			$id = purchase()->product()->insertData($where, true);
		}
		$shopId = 0;
		if (!empty($data['seller'])) {
			// 添加店铺
			$shopId = purchase()->shop()->addShop($channelId, $data['seller']['shop_id'], $data['seller']['shop_name'], $data['seller']['shop_url'], $data['seller']['service']);
		}
		$price = 0;
		if (!empty($data['sku'])) {
			$price = min(array_column($data['sku'], 'price'));
		}
		$updateData = [
			'status' => purchase()->product()->getConst('STATUS_SET'),
			'mem_id' => userId(),
			'name' => $data['name'],
			'purchase_shop_id' => $shopId,
			'sale_count' => $data['sale_count'] ?? 0,
			'price' => $price,
		];
		$rst = purchase()->product()->updateData($where, $updateData);
		//更新采购产品
		$rst = purchase()->item()->addNotExist($id, $data['sku']);
		$rst = purchase()->product()->saveResult($channelId, $data['item_id'], $data);
		$this->success('上传成功');
	}

	public function addAfter()
	{
		service('supplier/Url')->updateData(ipost('supp_id'), ['status'=>1]);
			$this->success('操作成功');
	}

	public function notice()
	{
		foreach(ipost() as $value) {
			purchase()->product()->addUrl($value);
		}
		$this->success();
	}
}