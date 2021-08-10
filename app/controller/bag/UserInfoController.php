<?php

namespace app\controller\bag;
use app\controller\Controller;

class UserInfoController extends Controller
{
	public function index()
	{	
		$this->view();
	}

	public function collect()
	{
		$spuId = ipost('spu_id', 0);
		if (empty($spuId)) {
			$this->error('param error');
		}
		$rst = make('app/service/member/CollectService')->collectProduct($spuId);
		if ($rst) {
			$this->success($rst, 'success');
		} else {
			$this->error('collect failed');
		}
	}

	public function address()
	{
		html()->addCss();
		html()->addJs();

		$page = iget('page', 1);
		$size = iget('size', 10);
		$addressService = make('app/service/member/AddressService');
		$list = $addressService->getListData(['mem_id'=>userId()], '*', $page, $size, ['default'=>'desc','address_id'=>'desc']);
		if (!empty($list)) {
			$countryList = array_unique(array_column($list, 'country_code2'));
			$countryList = make('app/service/CountryService')->getListData(['code2'=>['in', $countryList]], 'code2,name_en');
			$countryList = array_column($countryList, 'name_en', 'code2');
			foreach ($list as $key => $value) {
				$value['country'] = $countryList[$value['country_code2']] ?? '';
				$list[$key] = $value;
			}
		}

		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', 'My address - '.site()->getName());
		$this->view();
	}

	public function setAddressDefault()
	{
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/AddressService');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->updateData(['mem_id'=>$memId], ['default'=>0]);
		$service->updateData($id, ['default'=>1]);
		$this->success();
	}

	public function deleteAddress()
	{
		$id = (int)ipost('id');
		$memId = userId();
		$where = [
			'address_id' => $id,
			'mem_id' => $memId,
		];
		$service = make('app/service/member/AddressService');
		if (!$service->getCountData($where)) {
			$this->error('That address was not exist!');
		}
		$service->deleteData($id);
		$this->success();
	}
}