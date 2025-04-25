<?php 

namespace app\service\purchase;
use app\service\Base;

class Spu extends Base
{
	public function addUrl($url)
	{
		$channelId = purchase()->channel()->getChannelId($url);
		if (!$channelId) {
			return false;
		}
		$itemId = purchase()->channel()->getItemId($url);
		if (!$itemId) {
			return false;
		}
		$data = [
			'channel_id' => $channelId,
			'item_id' => $itemId,
		];
		if ($this->getCountData($data)) return true;
		return $this->insert($data);
	}

	public function getStatusList()
	{
		return [
			$this->getConst('STATUS_NORMAL') => '未使用',
			$this->getConst('STATUS_SET') => '已上传',
			$this->getConst('STATUS_USED') => '已使用',
			$this->getConst('STATUS_SPU') => '转化中',
			$this->getConst('STATUS_FAIL') => '已废弃',
		];
	}

	public function url(int $channelId, int $itemId)
	{
		switch ($channelId) {
			case 6051:
				return 'https://item.taobao.com/item.htm?id='.$itemId;
			case 6052:
				return 'https://detail.tmall.com/item.htm?id='.$itemId;
			case 6053:
				return 'https://detail.1688.com/offer/'.$itemId.'.html';
		}
	}

	public function getInfo(int $id)
	{
		$info = $this->loadData($id);
		if (empty($info)) {
			return false;
		}
		$result = $this->getResult($info['channel_id'], $info['item_id']);
		if (empty($result)) {
			return false;
		}
		return array_merge($info, $result);
	}

	public function saveResult(int $channelId, int $itemId, array $data, $type='source_data')
	{
		$data = isJson($data);
		if (!$data) {
			return false;
		}
		$where = [
			'channel_id' => $channelId,
			'item_id' => $itemId,
		];
		if (purchase()->result()->getCountData($where) > 0) {
			return purchase()->result()->updateData($where, $where+[$type=>$data]);
		} else {
			$where[$type] = $data;
			return purchase()->result()->insert($where);
		}
	}

	public function getResult(int $channelId, int $itemId, $type='source_data')
	{
		$info = purchase()->result()->loadData(['channel_id' => $channelId, 'item_id' => $itemId]);
		if (empty($info)) {
			return false;
		}
		return isArray($info[$type]);
	}

	public function updateTitle($channelId, int $itemId)
	{
		$rst = $this->getResult($channelId, $itemId);
		if (empty($rst['name'])) {
			return false;
		}
		$this->updateData(['channel_id'=>$channelId, 'item_id'=>$itemId], ['name'=>$rst['name']]);
		return $rst['name'];
	}

	public function getVolume($str)
	{
		$arr = [
			'length' => 0,
			'width' => 0,
			'height' => 0,
		];
		if ($str) {
			$str = str_replace(['*','x','X','-','&',';',':'], ',', $str);
			$str = explode(',', $str);
			$arr['length'] = $str[0] ?? 0;
			$arr['width'] = $str[1] ?? 0;
			$arr['height'] = $str[2] ?? 0;
		}
		return $arr;
	}

	public function operateSpu()
	{
		$info = $this->loadData(['status'=>$this->getConst('STATUS_SPU')]);
		if (empty($info)) {
			return false;
		}
		$data = $this->getResult($info['channel_id'], $info['item_id'], 'save_data');
		if (empty($data)) {
			return false;
		}
		dd($data);
		
		//属性组
		$attrName = service('attr/Name');
		$attrValue = service('attr/Value');
		$descName = service('desc/Name');
		$descValue = service('desc/Value');
		$spuData = service('product/SpuData');
		$file = service('tool/File');

		$attrNameArr = [];
		$attrValueArr = [];
		$allImageArr = [];
		if (!empty($data['desc_img'])) {
			$allImageArr = $data['desc_img'];
		}
		//汇总属性|属性图片
		if (!empty($data['main_img'])) {
			$allImageArr = array_merge($data['main_img'], $allImageArr);
		}
		if (!empty($data['spu_image'])) {
			$allImageArr[] = $data['spu_image'];
		}

		foreach ($data['sku'] as $key => $value) {
			if (empty($value['attr'])) continue;
			if (!empty($value['img'])) {
				$allImageArr[] = $value['img'];
			}
			$attrNameArr = array_merge($attrNameArr, array_keys($value['attr']));
			$attrValueArr = array_merge($attrValueArr, $value['attr']);
			$allImageArr = array_merge($allImageArr, array_column($value['attr'], 'img'));
		}
		// 上传图片
		$allImageArr = array_unique(array_filter($allImageArr));
		$allImageArr = $file->uploadUrlImage($allImageArr, 'product');
		// 设置属性
		$attrNameArr = $attrName->addNotExist($attrNameArr);
		$attrValueArr = $attrValue->addNotExist($attrValueArr);
		dd($attrNameArr, $attrValueArr);

		//描述值
		$descNameArr = [];
		$descValueArr = [];
		foreach($data['bc_des_text'] as $value) {
			$descNameArr[] = trim(strtoupper(strTrim($value['key'])));
			$descValueArr[] = trim(strtoupper(strTrim($value['value'])));
		}
		$descNameArr = $descName->addNotExist(array_unique($descNameArr));
		$descValueArr = $descValue->addNotExist(array_unique($descValueArr));

		$where = [
			'item_id' => $data['bc_product_id'],
			'supplier' => $data['bc_site_id'],
		];	
		
		$info = $spuData->loadData($where, 'spu_id');
		$data['bc_post_fee'] = $data['bc_post_fee'] ? (float)$data['bc_post_fee'] : 0; //邮费
		if (empty($info)) {
			//价格合集
			$priceArr = [];
			foreach ($data['bc_sku'] as $key => $value) {
				if (empty($value['attr'])) continue;
				$value['price'] = (float)$value['price'];
				$priceArr[] = (float)$value['price']+$data['bc_post_fee'];
				$data['bc_sku'][$key]['price'] = $value['price'];
			}
			$insert = [
				'status' => 0,
				'site_id' => $data['bc_product_site'],
				'cate_id' => $data['bc_product_category'],
				'attach_id' => $allImageArr[$firstImage] ?? 0,
				'min_price' => $this->getPrice(min($priceArr)),
				'max_price' => $this->getPrice(max($priceArr)),
			];
			$this->start();
			$spuId = $this->insertGetId($insert);
			//spu扩展数据
			$insert = [
				'spu_id' => $spuId,
				'supplier' => $data['bc_site_id'],
				'item_id' => $data['bc_product_id'],
				'item_url' => $data['bc_product_url'],
				'shop_id' => service('supplier/Shop')->addNotExist(['url'=>$data['bc_shop_url'], 'name'=>$data['bc_shop_name']]),
				'post_fee' => (float)$data['bc_post_fee'],
				'weight' => (int)($data['bc_product_weight'] ?? 0),
				'volume' => $data['bc_product_volume'] ?? '',
			];
			$spuData->insert($insert);
			//中文语言
			service('product/Language')->insert(['spu_id'=>$spuId, 'name'=>$data['bc_product_name']]);
			//spu图片组
			$insert = [];
			$count = 1;
			foreach ($spuImageArr as $value) {
				if (isset($allImageArr[$value])) {
					$insert[$value] = [
						'spu_id' => $spuId,
						'attach_id' => $allImageArr[$value],
						'sort' => $count++,
					];
				}
			}
			if (!empty($insert)) {
				service('product/SpuImage')->addSpuImage($insert);
			}
			//sku
			$sku = service('product/Sku');
			$skuData = service('product/SkuData');
			foreach ($data['bc_sku'] as $key => $value) {
				if (empty($value['attr'])) continue;
				$price = $this->getPrice($value['price']+$data['bc_post_fee']);
				$insert = [
					'spu_id' => $spuId,
					'status' => $value['stock'] > 0 ? 1 : 0,
					'site_id' => $data['bc_product_site'],
					'attach_id' => empty($value['img']) ? 0 : $allImageArr[$value['img']] ?? 0,
					'stock' => $value['stock'],
					'price' => $price,
				];
				$skuId = $sku->insertGetId($insert);
				$insert = [
					'sku_id' => $skuId,
					'item_id' => $value['sku_id'],
					'cost_price' => $value['price'],
				];
				$skuData->insert($insert);
				//属性关联
				$insert = [];
				$count = 1;
				foreach ($value['attr'] as $k => $v) {
					$insert[] = [
						'sku_id' => $skuId,
						'attrn_id' => $attrNameArr[trim(strtoupper(strTrim($k)))],
						'attrv_id' => $attrValueArr[trim(strtoupper(strTrim($v['text'])))],
						'attach_id' => empty($v['img']) ? 0 : $allImageArr[$v['img']] ?? 0,
						'sort' => $count++,
					];
				}
				if (!empty($insert)) {
					service('product/AttrUsed')->addAttrUsed($skuId, $insert);
				}
			}
			$this->commit();
		} else {
			$spuId = $info['spu_id'];
		}
		if (empty($spuId)) {
			return false;
		}
		//spu 介绍图片
		$insert = [];
		$count = 1;
		$allImageArr = array_unique(explode(',', $data['bc_product_des_picture']));
		$allImageArr = $file->uploadUrlImage($allImageArr, 'introduce', false);
		if (!empty($allImageArr)) {
			foreach ($allImageArr as $value) {
				$insert[$spuId.'-'.$value] = [
					'spu_id' => $spuId,
					'attach_id' => $value,
					'sort' => $count++,
				];
			}
		}
		if (!empty($insert)) {
			service('product/IntroUsed')->addIntroUsed($spuId, $insert);
		}

		//spu介绍文本
		$insert = [];
		$count = 1;
		foreach ($data['bc_des_text'] as $key => $value) {
			$tempKey = trim(strtoupper(strTrim($value['key'])));
			$tempValue = trim(strtoupper(strTrim($value['value'])));
			$nameId = $descNameArr[$tempKey];
			$valueId = $descValueArr[$tempValue];
			$insert[$nameId.'-'.$valueId] = [
				'spu_id' => $spuId,
				'descn_id' => $nameId,
				'descv_id' => $valueId,
				'sort' => $count++,
			];
		}
		if (!empty($insert)) {
			service('product/DescUsed')->addDescUsed($spuId, $insert);
		}
		return true;
	}
}