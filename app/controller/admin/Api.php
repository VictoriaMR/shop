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
			'domain' => domain(),
			'version' => version(),
			'socket_domain' => domain().'socket.io/',
			'action' => $this->_helperAction,
		];
		$this->success($data);
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
		$fileService = make('app/service/File');
		$result = $fileService->upload($file, $cate);
		if ($result) {
			$this->success('上传成功', $result);
		}
		$this->error('上传失败');
	}

	public function stat()
	{
		make('app/service/Logger')->addLog();
	}

	public function addProduct()
	{
		$data = ipost();
		$cacheKey = 'queue-add-product:'.$data['bc_site_id'];
		if (redis(2)->hExists($cacheKey, $data['bc_product_id'])) {
			make('app/service/supplier/Url')->updateData(['name'=>$data['bc_site_id'], 'item_id'=>$data['bc_product_id']], ['status'=>2]);
			$this->success('加入队列成功');
		}
		if (empty($data['bc_product_category'])) {
			$this->error('产品分类不能为空');
		}
		if (empty($data['bc_product_site'])) {
			$this->error('站点不能为空');
		}
		$rst = make('app/service/Queue')->push([
			'class' => 'app/service/product/Spu',
			'method' => 'addProduct',
			'param' => $data,
		]);
		if ($rst) {
			redis(2)->hSet($cacheKey, $data['bc_product_id'], 1);
			//更新待入库状态
			make('app/service/supplier/Url')->updateData(['name'=>$data['bc_site_id'], 'item_id'=>$data['bc_product_id']], ['status'=>2]);
			$this->success('加入队列成功');
		} else {
			$this->error('加入队列失败');
		}
	}

	public function addAfter()
	{
		make('app/service/supplier/Url')->updateData(ipost('supp_id'), ['status'=>1]);
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