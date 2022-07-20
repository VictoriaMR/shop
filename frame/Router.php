<?php

namespace frame;

final class Router
{
	public function analyze()
	{
		array_shift($_GET);
		$pathInfo = trim(str_replace('.html', '', $_SERVER['REQUEST_URI']), '/');
		$router = [];
		if (empty($pathInfo)) {
			$router['path'] = 'Index';
			$router['func'] = 'index';
		} else {
			$pathInfo = parse_url($pathInfo);
			if (!empty($pathInfo['path'])) {
				$pathInfo = explode('/', $pathInfo['path']);
				if (IS_ADMIN) {
					$router['path'] = isset($pathInfo[0]) ? ucfirst($pathInfo[0]) : 'Index';
					$router['func'] = $pathInfo[1] ?? 'index';
				} else {
					$tempInfo = explode('-', $pathInfo[0]);
					if (count($tempInfo) > 1) {
						$tempInfo = array_reverse($tempInfo);
						$index = array_search('page', $tempInfo);
						if ($index === false) {
							$index = 0;
							$_GET['id'] = $tempInfo[0];
						} else {
							$_GET['page'] = $tempInfo[$index-1] ?? 1;
						}
						$index++;
						if (isset($tempInfo[$index])) {
							if (is_numeric($tempInfo[$index])) {
								$router['path'] = $tempInfo[$index+1] ?? 'Index';
								if ($router['path'] == 's') {
									$_GET['sid'] = $tempInfo[$index];
								} else {
									$_GET['id'] = $tempInfo[$index];
								}
							} else {
								$router['path'] = $tempInfo[$index];
							}
						} else {
							$router['path'] = 'Index';
						}
						$router['path'] = $this->formatPath($router['path']);
						$router['func'] = 'index';
					} else {
						$router['path'] = ucfirst($pathInfo[0]);
						$router['func'] = $pathInfo[1] ?? 'index';
					}
				}
			}
		}
		return $router;
	}

	protected function formatPath($type)
	{
		$arr = [
			'c'=>'Category',
			'p' => 'Product',
			's' => 'Product',
		];
		return $arr[$type] ?? ucfirst($type);
	}

	public function buildUrl($url=null, $param=null, $suffix=false)
	{
		if ($suffix) {
			if (empty($url)) return '/';
			if (!empty($param)) {
				if (is_array($param))
					$url .= $this->setParam($param);
				else
					$url .= $this->nameFormat($param);
			}
			$url .= trim($url, '-').'.'.\App::get('router', 'view_suffix');
		} else {
			$url = lcfirst($url);
			if (!empty($param)) {
				if (is_array($param)) {
					$param = http_build_query($param);
				}
				$url .= '?'.$param;
			}
		}
		return APP_DOMAIN.$url;
	}

	public function nameFormat($name, $param=[])
	{
		$name = preg_replace('/[^-A-Za-z0-9\s]/', '', strtolower($name));
		$name = preg_replace('/( ){2,}/', ' ', $name);
		$name = str_replace(' ', '-' , $name);
		$name = preg_replace('/-{1,}/', '-', $name);
		$name = ltrim($name, '-');
		if (isset($param['id'])) $name .= '-'.$param['id'];
		if (isset($param['page'])) $name .= '-page-'.$param['page'];
		if (isset($param['size'])) $name .= '-size-'.$param['size'];
		return $name;
	}

	public function setParam($param=[])
	{
		$str = '';
		foreach ($param as $key=>$value) {
			if (in_array($key, ['page', 'size'])) {
				$str .= '-'.$key.'-'.$value;
			} else {
				$str .= '-'.$value;
			}
		}
		return $str;
	}

	public function urlFormat($name, $type='', $params=[], $domain='')
	{
		$name = $this->nameFormat($name.'-'.$type, $params);
		return $domain.$name.'.html';
	}
}