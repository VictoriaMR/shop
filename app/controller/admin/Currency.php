<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Currency extends AdminBase
{
	public function __construct()
	{
		$this->_arr = [
			'index' => '货币管理',
		];
		$this->_default = '财务管理';
		parent::_init();
	}

	public function index()
	{
		if (isPost()) {
			$opn = ipost('opn');
			if (in_array($opn, ['getCurrencyInfo', 'updateCurrencyRate'])) {
				$this->$opn();
			}
			$this->error('非法请求');
		}
		frame('Html')->addJs();
		
		$this->view([
			'list' => sys()->currency()->getListData(),
		]);
	}

	protected function getCurrencyInfo()
	{
		$id = trim(ipost('id', ''));
		if (empty($id)) {
			$this->error('参数不正确, ID不能为空');
		}
		$info = sys()->currency()->loadData($id, 'code,name,rate,symbol');
		$this->success('获取成功', $info);
	}

	protected function updateCurrencyRate()
	{
		$rst = sys()->currency()->updateRate();
		if ($rst) {
			$this->success('更新汇率成功');
		}
		$this->error('更新汇率失败');
	}
}