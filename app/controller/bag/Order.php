<?php

namespace app\controller\bag;
use app\controller\Base;

class Order extends Base
{
	public function index()
	{	
		$this->view();
	}

	protected function getOrderList()
	{
		$status = input('status');
		if (!is_null($status)) {
			
		}
	}

	public function getOrderAddress()
	{
		$orderId = (int)ipost('order_id');
		if (empty($orderId)) {
			$this->error('Sorry, we can\' find any order param here, Please try agin.');
		}
		if (!make('app/service/order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Sorry, we can\' find any order info here, Please try agin.');
		}
		$info = make('app/service/order/Address')->loadData(['order_id'=>$orderId, 'type'=>1]);
		$this->success($info, 'success');
	}

	public function editOrderAddress()
	{
		$orderId = (int)ipost('order_id');
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
		if (empty($orderId)) {
			$this->error('Sorry, we can\' find any order param here, Please try agin.');
		}
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
		if (!make('app/service/order/Order')->getCountData(['mem_id'=>userId(), 'order_id'=>$orderId])) {
			$this->error('Sorry, we can\' find any order info here, Please try agin.');
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
		];
		$countryInfo = make('app/service/address/Country')->loadData($country_code2, 'dialing_code');
		$data['phone'] = '+'.$countryInfo['dialing_code'].' '.$phone;

		$rst = make('app/service/order/Address')->updateData(['order_id'=>$orderId, 'type'=>1], $data);
		if ($rst) {
			$this->success('Update your billing address success.');
		} else {
			$this->error('Update your billing address failed.');
		}
	}
}