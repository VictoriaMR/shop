<?php

namespace template\template1\controller\computer;
use app\controller\Base;

class Cart extends Base
{
	public function index()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$lanId = lanId();
		$cartService = service('cart/Cart');
		$spuService = service('product/Spu');
		$cateService = service('category/Category');
		$enMap = $cateService->getEnMap();

		$list = $cartService->getList();
		$originalPriceTotal = 0;
		$priceTotal = 0;
		$checkedCount = 0;
		$totalItemsCount = 0;
		$allChecked = !empty($list);

		if (!empty($list)) {
			$spuIdArr = array_values(array_unique(array_filter(array_column($list, 'spu_id'))));
			$collectIds = [];
			if (userId() > 0 && !empty($spuIdArr)) {
				$collectRows = service('member/Collect')->getListData([
					'mem_id' => userId(),
					'spu_id' => ['in', $spuIdArr],
				], 'spu_id');
				$collectIds = array_column($collectRows, 'spu_id');
			}

			foreach ($list as $key => $value) {
				$qty = max(1, (int)$value['quantity']);
				$totalItemsCount += $qty;
				$value['out_of_stock'] = ($qty <= 0 || $qty > (int)$value['stock']);
				$value['is_liked'] = in_array($value['spu_id'], $collectIds);
				if (!empty($value['checked'])) {
					$originalPriceTotal += (float)$value['original_price'] * $qty;
					$priceTotal += (float)$value['price'] * $qty;
					$checkedCount += $qty;
				} else {
					$allChecked = false;
				}
				$list[$key] = $value;
			}
		}

		$symbol = service('system/Currency')->priceSymbol(2) ?: '$';
		$discountTotal = max(0, $originalPriceTotal - $priceTotal);
		$shippingFee = $priceTotal > 0 ? service('order/Order')->getShippingFee($priceTotal) : 0;
		$grandTotal = $priceTotal + $shippingFee;
		$freeShipDiff = max(0, 59 - $priceTotal);
		$freeShipPercent = min(100, (int)round(($priceTotal / 59) * 100));

		$summary = [
			'checked_count'         => $checkedCount,
			'total_items_count'     => $totalItemsCount,
			'all_checked'           => $allChecked,
			'symbol'                => $symbol,
			'original_total'        => sprintf('%.2f', $originalPriceTotal),
			'original_total_format' => $symbol . sprintf('%.2f', $originalPriceTotal),
			'subtotal'              => sprintf('%.2f', $priceTotal),
			'subtotal_format'       => $symbol . sprintf('%.2f', $priceTotal),
			'discount'              => sprintf('%.2f', $discountTotal),
			'discount_format'       => '-' . $symbol . sprintf('%.2f', $discountTotal),
			'shipping'              => sprintf('%.2f', $shippingFee),
			'shipping_format'       => $shippingFee > 0 ? ($symbol . sprintf('%.2f', $shippingFee)) : 'FREE',
			'grand_total'           => sprintf('%.2f', $grandTotal),
			'grand_total_format'    => $symbol . sprintf('%.2f', $grandTotal),
			'free_shipping_diff'    => sprintf('%.2f', $freeShipDiff),
			'free_shipping_percent' => $freeShipPercent,
		];

		// 推荐商品列表 (用于空购物车或底部加购推荐)
		$recRaw = $spuService->getList(
			['status' => $spuService->getConst('STATUS_OPEN')],
			'spu_id,cate_id,attach_id,min_price,max_price,visit_total,free_ship,is_hot',
			1,
			8,
			['visit_total' => 'desc', 'is_hot' => 'desc', 'spu_id' => 'desc'],
			$lanId,
			true,
			true
		);
		$recommendList = [];
		foreach ($recRaw as $rItem) {
			$rItem['url'] = url('product', ['pid' => $rItem['spu_id']]);
			$rCid = (int)$rItem['cate_id'];
			$rItem['cate_name'] = $enMap[$rCid] ?? '';
			$minP = (float)($rItem['min_price'] ?? 0);
			$origP = (float)($rItem['original_price'] ?? 0);
			$rItem['discount_percent'] = ($origP > $minP && $origP > 0) ? (int)round((1 - $minP / $origP) * 100) : 20;
			$recommendList[] = $rItem;
		}

		$this->assign('isLogin', userId() > 0);
		$this->assign('list', $list);
		$this->assign('summary', $summary);
		$this->assign('recommendList', $recommendList);
		$this->assign('_title', distT('shopping_bag') . ' - ' . $siteName);
		$this->assign('_desc', distT('seo_desc'));
		$this->assign('_canonical', url('cart'));
	}
}
