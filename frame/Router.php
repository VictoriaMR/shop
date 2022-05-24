<?php

namespace frame;

final class Router
{
	public function analyze()
	{
		array_shift($_GET);
		$pathInfo = trim($_SERVER['REQUEST_URI'], '/');
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
					$pathInfo[0] = str_replace('.html', '', $pathInfo[0]);
					$tempInfo = explode('-', $pathInfo[0]);
					if (count($tempInfo) > 1) {
						$router['path'] = isset($tempInfo[0]) ? ucfirst($tempInfo[0]) : 'Index';
						$router['func'] = $tempInfo[1] ?? 'index';
						if (isset($tempInfo[2])) {
							if (in_array($tempInfo[2], ['ape', 'flac', 'wav', 'chinese', 'western'])) $_GET['key'] = $tempInfo[2];
							else $_GET['id'] = $tempInfo[2];
						}
						$tempIndex = array_search('page', $tempInfo);
						if ($tempIndex !== false) {
							$_GET['page'] = $tempInfo[$tempIndex+1]??1;
						}
						$tempIndex = array_search('size', $tempInfo);
						if ($tempIndex !== false) {
							$_GET['size'] = $tempInfo[$tempIndex+1]??20;
						}
					} else {
						$router['path'] = ucfirst($tempInfo[0]);
						$router['func'] = 'index';
					}
				}
			}
		}
		return $router;
	}

	public function buildUrl($url=null, $param=null, $name=null)
	{
		if (empty($url)) return '/';
		if (IS_ADMIN) {
			$url = lcfirst($url);
			if (!empty($param)) {
				if (is_array($param)) {
					$param = http_build_query($param);
				}
				$url .= '?'.$param;
			}
		} else {
			if (!empty($param)) {
				if (is_array($param))
					$url .= $this->setParam($param);
				else
					$url .= $this->nameFormat($param);
			}
			if (!empty($name)) {
				$url .= $this->nameFormat($name);
			}
			$viewSuffix = \App::get('router', 'view_suffix');
			if (!empty($url) && $viewSuffix) $url .= '.'.$viewSuffix;
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
		return '-'.$name;
	}

	public function setParam($param=[])
	{
		$str = '-';
		foreach ($param as $key=>$value) {
			$str .= $key.'-'.$value;
		}
		return $str;
	}
}