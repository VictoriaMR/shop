<?php

namespace app\controller\bag;
use app\controller\Controller;

class UserInfoController extends Controller
{
	public function index()
	{	
		html()->addCss();
		html()->addJs();
		dd(session()->get(APP_TEMPLATE_TYPE.'_info'));
		$this->assign('info', session()->get(APP_TEMPLATE_TYPE.'_info'));
		$this->assign('_title', 'My info page - '.site()->getName());
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
		html()->addCss('common/address');
		html()->addJs();
		html()->addJs('common/address');

		$page = iget('page', 1);
		$size = iget('size', 10);
		
		$list = $this->getAddressList($page, $size);

		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('size', $size);
		$this->assign('_title', 'My address - '.site()->getName());
		$this->view();
	}

	protected function getAddressList($page=1, $size=10)
	{
		$list = make('app/service/member/AddressService')->getListData(['mem_id'=>userId()], '*', $page, $size, ['`default`'=>'desc','address_id'=>'desc']);
		if (!empty($list)) {
			$countryList = array_unique(array_column($list, 'country_code2'));
			$countryList = make('app/service/address/CountryService')->getListData(['code2'=>['in', $countryList]], 'code2,name_en');
			$countryList = array_column($countryList, 'name_en', 'code2');
			foreach ($list as $key => $value) {
				$value['country'] = $countryList[$value['country_code2']] ?? '';
				$list[$key] = $value;
			}
		}
		return $list;
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

	public function getAddress()
	{
		$page = ipost('page', 1);
		$size = ipost('size', 10);
		$list = $this->getAddressList($page, $size);
		$this->success($list);
	}

	public function getAddressInfo()
	{
		$id = (int)ipost('id');
		$where = [
			'address_id' => $id,
			'mem_id' => userId(),
		];
		$info = make('app/service/member/AddressService')->loadData($id);
		if (empty($info)) {
			$this->error('Sorry, That address was not exist!');
		} else {
			$this->success($info);
		}
	}

	public function editAddress()
	{
		$address_id = ipost('address_id');
		$country_code2 = ipost('country_code2');
		$tax_number = ipost('tax_number');
		$first_name = ipost('first_name');
		$last_name = ipost('last_name');
		$phone = ipost('phone');
		$postcode = ipost('postcode');
		$city = ipost('city');
		$zone_id = (int)ipost('zone_id');
		$state = ipost('state');
		$address1 = ipost('address1');
		$address2 = ipost('address2');
		$default = (int)ipost('default');
		if (empty($country_code2)) {
			$this->error('Country is required');
		}
		if (empty($first_name)) {
			$this->error('First Name is required');
		}
		if (empty($phone)) {
			$this->error('Phone Number is required');
		}
		if (empty($postcode)) {
			$this->error('ZIP Code is required');
		}
		if (empty($city)) {
			$this->error('City is required');
		}
		if (empty($state)) {
			$this->error('State / Region is required');
		}
		if (empty($address1)) {
			$this->error('Address is required');
		}
		$data = [
			'country_code2' => substr($country_code2, 0, 2),
			'tax_number' => substr($tax_number, 0, 32),
			'first_name' => substr($first_name, 0, 32),
			'last_name' => substr($last_name, 0, 32),
			'postcode' => substr($postcode, 0, 10),
			'city' => substr($city, 0, 32),
			'zone_id' => $zone_id,
			'state' => substr($state, 0, 32),
			'address1' => substr($address1, 0, 64),
			'address2' => substr($address2, 0, 64),
			'default' => $default == 1 ? 1 : 0,
		];
		if (empty($address_id)) {
			$data['mem_id'] = userId();
			$rst = make('app/service/member/AddressService')->insert($data);
		} else {
			$rst = make('app/service/member/AddressService')->updateData(['address_id'=>$address_id, 'mem_id'=>userId()], $data);
		}
		if ($rst) {
			$this->success($address_id ? 'Edit address success' : 'Add address success');
		} else {
			$this->error($address_id ? 'Edit address failed' : 'Add address failed');
		}
	}
}