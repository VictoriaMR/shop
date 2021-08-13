<?php 

namespace app\service\order;
use app\service\Base;

class OrderService extends Base
{
	protected function getModel()
	{
		$this->baseModel = make('app/model/order/Order');
	}

	public function createOrder($skuIdArr, $shippingAddressId, $billingAddressId, $couponId=0, $insurance=false)
	{
		//获取sku信息
		$skuService = make('app/service/product/SkuService');
		$where = [
			'site_id' => $this->siteId(),
			'sku_id' => ['in', array_keys($skuIdArr)],
			'status' => $skuService->getConst('STATUS_OPEN'),
		];
		$skuList = $skuService->getListData($where, 'sku_id,stock');
		$skuList = array_column($skuList, 'stock', 'sku_id');

		$list = [];
		$productTotal = 0;
		$originalPriceTotal = 0;
		$orderProduct = [];
		$orderProductAttr = [];
		foreach ($skuIdArr as $key => $value) {
			if (empty($skuList[$key])) {
				return false;
			}
			if ($skuList[$key] < $value) {
				return false;
			}
			$info = $skuService->getInfoCache($key, $this->lanId());
			$productTotal = $info['price'] * $value;
			$originalPriceTotal = $info['original_price'] * $value;
			$orderProduct[] = [
				'sku_id' => $key,
				'name' => $info['name'],
				'quantity' => $value,
				'price' => $info['price'],
				'original_price' => $info['original_price'],
			];
			foreach ($info['attrMap'] as $k => $v) {
				foreach ($v as $kk => $vv) {
					$orderProductAttr[$key][] = [
						'attr_id' => $k,
						'attr_name' => $info['attr'][$k],
						'attv_id' => $vv,
						'attv_name' => $info['attv'][$vv],
					];
				}
			}
		}
		$couponId = (int)$couponId;
		$couponFree = 0;
		if (!empty($couponId)) {
			//todo 优惠券使用
		}
		//当前货币
		$currency = make('app/service/LanguageService')->currency();
		//保险
		$insuranceFree = $insurance ? sprintf('%.2f', $productTotal*0.05) : 0;
		//运费
		$shippingFee = 0;
		$insert = [
			'order_no' => substr(getUniqueName(), 2, -2),
			'site_id' => $this->siteId(),
			'mem_id' => $this->userId(),
			'coupon_id' => $couponId,
			'lan_id' => $this->lanId(),
			'currency' => $currency,
			'insurance_free' => $insuranceFree,
			'coupon_free' => $couponFree,
			'shipping_fee' => $shippingFee,
			'product_total' => $productTotal,
			'order_total' => $productTotal + $couponFree + $insuranceFree + $shippingFee,
		];
		$orderProductService = make('app/service/order/ProductService');
		$orderProductAttrService = make('app/service/order/ProductAttributeService');
		$this->start();
		$orderId = $this->insertGetId($insert);
		$insert = [];
		foreach ($orderProduct as $value) {
			$value['order_id'] = $orderId;
			$orderProductId = $orderProductService->insertGetId($value);
			//同步减少库存
			$skuService->where('sku_id', $value['sku_id'])->decrement('stock', $value['quantity']);
			$insert = array_merge($insert, array_map(function($value) use ($orderProductId){
				$value['order_product_id'] = $orderProductId;
				return $value;
			}, $orderProductAttr[$value['sku_id']]));
		}
		make('app/service/order/ProductAttributeService')->insert($insert);
		$this->commit();
		return $orderId;
	}
}