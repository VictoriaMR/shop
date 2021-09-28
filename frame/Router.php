<?php

namespace frame;

final class Router
{
	static private $_instance;

	public static function instance() 
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function analyze()
	{
		$pathInfo = trim($_SERVER['REQUEST_URI'], DS);
		$router['class'] = APP_TEMPLATE_TYPE;
		if (empty($pathInfo)) {
			$router['path'] = 'index';
			$router['func'] = 'index';
		} else {
			$pathInfo = parse_url($pathInfo);
			if (empty($pathInfo['path'])) {
				$router['path'] = 'index';
				$router['func'] = 'index';
			} else {
				$pathInfo['path'] = explode('.', $pathInfo['path'])[0];
				$routerArr = explode('-', $pathInfo['path']);
				if (APP_TEMPLATE_TYPE != 'admin' && !empty($routerArr) && count($routerArr) > 1) {
					$routerArr = array_reverse($routerArr);
					if (count($routerArr) > 1) {
						foreach ($routerArr as $key => $value) {
							if ($key % 2 > 0) {
								if (in_array($value, ['page', 'size'])) {
									$_GET[$value] = $routerArr[$key-1];
								} else {
									if ($value == 'sku' || $value == 's') {
										$_GET['sid'] = $routerArr[$key-1];
									} else {
										$_GET['id'] = $routerArr[$key-1];
									}
									$router['path'] = $value;
									break;
								}
							}
						}
					} else {
						$router['path'] = $routerArr[0];
					}
					$router['func'] = 'index';
					$router['path'] = $this->getPath($router['path']);
				}
			}
			if (empty($router['path'])) {
				$temp = explode('/', $pathInfo['path']);
				if (count($temp) > 1) {
					$router['func'] = array_pop($temp);
					$router['path'] = implode('/', $temp);
				} else {
					$router['path'] = $temp[0];
					$router['func'] = 'index';
				}
			}
		}
		array_shift($_GET);
		if (count($router) != 3) {
			throw new \Exception(' router analyed error', 1);
		}
		\App::set('router', $router);
		return true;
	}

	protected function getPath($path)
	{
		$arr = [
			'spu' => 'product',
			'p' => 'product',
			'cate' => 'category',
			'c' => 'category',
			'sku' => 'product',
			's' => 'product',
		];
		return $arr[$path] ?? $path;
	}

	public function buildUrl($url=null, $param=null, $domain=null)
	{
		$router = \App::get('router');
		if (is_null($url)) $url = $router['path'].DS.$router['func'];
		if (!empty($url)) $url .= defined('TEMPLATE_SUFFIX') ? '.'.TEMPLATE_SUFFIX : '';
		if (!empty($param)) $url .= '?' . http_build_query($param);
		return (is_null($domain) ? config('env.APP_DOMAIN') : $domain).$url;
	}

	public function urlFormat($name, $type, $param=[])
	{
		$name = preg_replace('/[^-A-Za-z0-9\s]/', '', strtolower($name));
		$name = preg_replace('/( ){2,}/', ' ', $name);
		$name = str_replace(' ', '-' , $name);
		$name .= '-'.$type;
		$name = preg_replace('/-{1,}/', '-', $name);
		if (isset($param['id'])) $name .= '-'.$param['id'];
		if (isset($param['page'])) $name .= '-page-'.$param['page'];
		if (isset($param['size'])) $name .= '-size-'.$param['size'];
		if (defined('TEMPLATE_SUFFIX')) $name .= '.'.TEMPLATE_SUFFIX;
		return config('env.APP_DOMAIN').$name;
	}
}