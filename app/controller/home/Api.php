<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Api extends HomeBase
{
	public function stat()
	{
		$path = [];
		$param = ipost();
		if (!empty($param['class'])) {
			$path[] = $param['class'];
			unset($param['class']);
		}
		if (!empty($param['path'])) {
			$path[] = $param['path'];
			unset($param['path']);
		}
		if (!empty($param['func'])) {
			$path[] = $param['func'];
			unset($param['func']);
		}
		$data = [
			'path' => implode('/', $path),
			'param' => json_encode($param),
		];
		$rst = service('log/Visitor')->addLog($data);
		if ($data['path'] == 'home/Product/index') {
			if (isset($param['pid'])) {
				$spuId = $param['pid'];
			} elseif (isset($param['sid'])) {
				$spuId = service('product/Sku')->loadData($param['sid'], 'spu_id')['spu_id'] ?? 0;
			}
			if ($spuId) {
				//更新浏览人数
				$rst = service('product/Spu')->where(['spu_id'=>$spuId])->increment('visit_total');
				//浏览历史
				if ($rst && userId()) {
					service('member/History')->addHistory($spuId);
				}
			}
		}
		$data = [];
		if (!empty($param['cart'])) {
			$data['cart_count'] = service('Cart')->getCartCount();
		}
		if (!empty($param['login'])) {
			$data['member'] = userId() ? session()->get(type().'_info', []) : [];
		}
		$this->success($data);
	}

	public function upload()
	{
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error(appT('param_error'));
		}
		$cate = $_POST['cate'] ?? '';
		$fileService = service('File');

		if (!in_array($cate, $fileService::FILE_TYPE)) {
			$this->error(appT('param_error'));
		}
		$result = $fileService->upload($file, $cate, false);
		if (empty($result)) {
			$this->error('Failed to update Avatar');
		}
		$this->success($result);
	}

	public function notice()
	{
		$url = trim(ipost('url', ''));
		$priority = (int)ipost('priority');
		if (empty($url)) {
			$this->error('url 不能为空');
		}
		service('supplier/Url')->addUrl($url, $priority);
		$this->success();
	}

	public function address()
	{
		$company_name = ipost('company_name', '');
		$first_name = ipost('first_name', '');
		$last_name = ipost('last_name', '');
		$country_code2 = ipost('country_code2', '');
		$postcode = ipost('postcode', '');
		$zone_name = ipost('zone_name', '');
		$phone = ipost('phone', '');
		$city = ipost('city', '');
		$address_line1 = ipost('address_line1', '');
		$address_line2 = ipost('address_line2', '');
		$default_shipping_address = ipost('default_shipping_address/d', 0);
		$default_billing_address = ipost('default_billing_address/d', 0);
		if (empty($first_name)) {
			$this->error(appT('first_name_vaild'));
		}
		if (empty($last_name)) {
			$this->error(appT('last_name_vaild'));
		}
		if (empty($country_code2)) {
			$this->error(appT('country_vaild'));
		}
		if (empty($zone_name)) {
			$this->error(appT('zone_vaild'));
		}
		if (empty($phone)) {
			$this->error(appT('phone_vaild'));
		}
		if (empty($city)) {
			$this->error(appT('city_vaild'));
		}
		if (empty($address_line1)) {
			$this->error(appT('address_line1_vaild'));
		}
		if ($this->isLogin()) {

		} else {

		}
		dd(ipost());
	}
}