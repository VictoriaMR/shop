<?php 

namespace app\service\payment;
use app\service\Base;

class Payment extends Base
{
	const PAYMENT_TYPE_PAYPAL = 1;
	const PAYMENT_TYPE_STRIPE = 2;

	protected $_model = 'app/model/payment/Payment';

	public function getTypeList($type=null)
	{
		$arr = [
			self::PAYMENT_TYPE_PAYPAL => 'PayPal',
			self::PAYMENT_TYPE_STRIPE => 'Stripe',
		];
		if (is_null($type)) return $arr;
		return $arr[$type] ?? '';
	}

	public function getSandBoxList($type=null)
	{
		$arr = [
			'0' => '正式',
			'1' => '测试',
		];
		if (is_null($type)) return $arr;
		return $arr[$type] ?? '';
	}

	public function getList(array $where=[], $page=1, $size=20)
	{
		$list = $this->getListData($where, 'payment_id,type,status,is_sandbox,name,remark,add_time', $page, $size, ['payment_id'=>'desc']);
		if (!empty($list)) {
			foreach ($list as $key => $value) {
				$value['type_name'] = $this->getTypeList($value['type']);
				$list[$key] = $value;
			}
		}
		return $list;
	}

	public function getSiteInfo($where)
	{
		$usedInfo = make('app/service/payment/Used')->loadData($where, 'payment_id');
		if (empty($usedInfo)) {
			return false;
		}
		return $this->loadData(['payment_id'=>$usedInfo['payment_id'], 'status'=>1, 'is_sandbox'=>config('env', 'APP_DEBUG')?1:0], 'payment_id,type,is_sandbox,name,app_key,secret_key,webhook_key');
	}
}