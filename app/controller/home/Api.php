<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Api extends HomeBase
{
	public function stat()
	{
		$path = [];
		$param = ipost();
		unset($param['is_ajax']);
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
		$needCart = !empty($param['cart']);
		$needLogin = !empty($param['login']);
		unset($param['cart'], $param['login']);

		$pathStr = implode('/', $path);
		$data = [
			'path' => $pathStr,
		];
		$rst = service('log/Visitor')->addLog($data);
		if ($pathStr === 'home/Product/index') {
			$spuId = 0;
			if (isset($param['pid'])) {
				$spuId = (int)$param['pid'];
			} elseif (isset($param['sid'])) {
				$spuId = (int)(service('product/Sku')->loadData($param['sid'], 'spu_id')['spu_id'] ?? 0);
			}
			if ($spuId) {
				//更新浏览人数
				$rst = service('product/Spu')->where(['spu_id' => $spuId])->increment('visit_total');
				//浏览历史
				if ($rst && userId()) {
					service('member/History')->addHistory($spuId);
				}
			}
		}
		$resData = [];
		if ($needCart) {
			$resData['cart_count'] = service('cart/Cart')->getCartCount();
		}
		if ($needLogin) {
			$resData['member'] = userId() ? session()->get(type() . '_info', []) : [];
		}
		header('Cache-Control: private, no-cache, must-revalidate');
		$this->success($resData);
	}

	public function upload()
	{
		$file = $_FILES['file'] ?? [];
		if (empty($file)) {
			$this->error(appT('param_error'));
		}
		$cate = $_POST['cate'] ?? '';
		$fileService = service('tool/File');

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
		$url = trim((string)ipost('url', ''));
		if (empty($url)) {
			$this->error('url 不能为空');
		}
		purchase()->spu()->addUrl($url);
		$this->success();
	}

	public function address()
	{
		$id = ipost('address_id/d', 0);
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
		$data = [
			'company_name' => $company_name,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'country_code2' => $country_code2,
			'postcode' => $postcode,
			'state' => $zone_name,
			'phone' => $phone,
			'city' => $city,
			'address1' => $address_line1,
			'address2' => $address_line2,
			'is_default' => $default_shipping_address,
			'is_bill' => $default_billing_address,
		];
		if ($this->isLogin()) {
			if ($id > 0) {
				if (!service('member/Address')->getCountData(['address_id'=>$id, 'mem_id'=>userId()])) {
					$this->error(appT('address_not_exist'));
				}
				$rst = service('member/Address')->updateData($id, $data);
			} else {
				$data['mem_id'] = userId();
				$data['site_id'] = siteId();
				$rst = service('member/Address')->insert($data);
			}
		} else {
			$rst = frame('Session')->set('guest_address', $data);
		}
		if ($rst) {
			$this->success(appT('address_success'));
		} else {
			$this->error(appT('address_error'));
		}
	}

	public function aiCatalog()
	{
		$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
		$host = strtolower(strstr($host, ':', true) ?: $host);
		$siteName = \App::get('domain', 'name', 'Shop');
		$baseUrl = 'https://' . $host;

		$manifest = [
			'specVersion' => '1.0',
			'host' => [
				'displayName' => $siteName,
				'identifier' => 'did:web:' . $host,
				'documentationUrl' => $baseUrl . '/',
				'logoUrl' => $baseUrl . '/computer/image/logo.png',
			],
			'entries' => [
				[
					'identifier' => 'urn:air:' . $host . ':commerce:product-catalog',
					'displayName' => $siteName . ' Product Catalog & Search API',
					'type' => 'application/openapi+json',
					'url' => $baseUrl . '/api/openapi.json',
					'description' => 'Search and browse products, categories, pricing, and new arrivals on ' . $siteName . '.',
					'version' => '1.0.0',
					'updatedAt' => '2026-01-01T00:00:00Z',
					'tags' => ['ecommerce', 'shopping', 'catalog', 'products', 'search'],
					'representativeQueries' => [
						'Search for products and check prices on ' . $siteName,
						'Browse new arrivals and trending products',
						'Find products by category or keyword',
					],
				],
			],
		];

		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: public, max-age=3600');
		echo json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		\App::runOver(true);
	}

	public function openapi()
	{
		$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
		$host = strtolower(strstr($host, ':', true) ?: $host);
		$siteName = \App::get('domain', 'name', 'Shop');
		$baseUrl = 'https://' . $host;

		$spec = [
			'openapi' => '3.1.0',
			'info' => [
				'title' => $siteName . ' Storefront Catalog API',
				'version' => '1.0.0',
				'description' => 'Public product search, category browsing, and product detail endpoints for ' . $siteName . '.',
			],
			'servers' => [
				['url' => $baseUrl],
			],
			'paths' => [
				'/search.html' => [
					'get' => [
						'operationId' => 'searchProducts',
						'summary' => 'Search products by keyword',
						'parameters' => [
							[
								'name' => 'keyword',
								'in' => 'query',
								'required' => true,
								'schema' => ['type' => 'string'],
								'description' => 'Search keyword for matching product titles and tags',
							],
						],
						'responses' => [
							'200' => ['description' => 'Search results page'],
						],
					],
				],
				'/category.html' => [
					'get' => [
						'operationId' => 'browseCategory',
						'summary' => 'Browse products by category ID',
						'parameters' => [
							[
								'name' => 'cid',
								'in' => 'query',
								'required' => true,
								'schema' => ['type' => 'integer'],
								'description' => 'Category ID',
							],
						],
						'responses' => [
							'200' => ['description' => 'Category product listing page'],
						],
					],
				],
				'/newIn.html' => [
					'get' => [
						'operationId' => 'listNewArrivals',
						'summary' => 'Browse latest new arrival products',
						'responses' => [
							'200' => ['description' => 'New arrivals product listing page'],
						],
					],
				],
			],
		];

		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: public, max-age=3600');
		echo json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		\App::runOver(true);
	}
}
