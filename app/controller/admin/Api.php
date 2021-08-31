<?php

namespace app\controller\admin;
use app\controller\Base;

class Api extends Base
{
	protected $_cateArr = ['category'];

	public function getHelperData()
	{
		$data = [
			'version' => config('version.admin'),
			'category' => make('app/service/Category')->getListFormat(),
			'site' => make('app/service/Site')->getListData(['site_id'=>['<>', '00']]),
		];
		$this->success($data);
	}

	public function getHelperFunction()
	{
		$data = [
			[
				'title' => '数据爬取',
				'name' => 'crawler',
			]
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
		$file = make('app/service/File');
		$result = $file->upload($file, $cate);
		if (empty($result)) {
			$this->error('上传失败');
		}
		$this->success($result);
	}

	public function stat()
	{
		make('app/service/Logger')->addLog();
	}

	public function addProduct()
	{
		$data = ipost();
		$cacheKey = 'queue-add-product:'.ipost('bc_site_id');
		if (redis(2)->hExists($cacheKey, ipost('bc_product_id'))) {
			$this->error('队列已存在');
		}
		if (empty(ipost('bc_product_category'))) {
			$this->error('产品分类不能为空');
		}
		if (empty(ipost('bc_product_site'))) {
			$this->error('站点不能为空');
		}
		$data = [
			'class' => 'app/service/product/Spu',
			'method' => 'addProduct',
			'param' => ipost(),
		];
		$rst = make('app/service/Queue')->push($data);
		if ($rst) {
			redis(2)->hSet($cacheKey, ipost('bc_product_id'), 1);
			$this->success('加入队列成功');
		} else {
			$this->error('加入队列失败');
		}
	}
}