<?php

namespace frame;

final class Router
{
	public function analyze()
	{
		$pathInfo = trim($_SERVER['REQUEST_URI'], '/');
		$router = [];
		if (empty($pathInfo)) {
			$router['path'] = 'Index';
			$router['func'] = 'index';
		} else {
			$pathInfo = parse_url($pathInfo);
			if (!empty($pathInfo['path'])) {
				$pathInfo = explode('/', $pathInfo['path']);
				$pathInfo[0] = str_replace('.html', '', $pathInfo[0]);
				$tempInfo = array_reverse(explode('-', $pathInfo[0]));
				if (count($tempInfo) > 1) {
					$c = $this->getPath($tempInfo[1]);
					if ($c == 'page') {
						$_GET['page'] = $tempInfo[0];
						$_GET['id'] = $tempInfo[2] ?? 0;
						$router['path'] = empty($tempInfo[3])?'Index':ucfirst($this->getPath($tempInfo[3]));
					} else {
						$_GET['id'] = $tempInfo[0] ?? 0;
						$router['path'] = empty($tempInfo[1])?'Index':ucfirst($this->getPath($tempInfo[1]));
					}
				} else {
					$router['path'] = ucfirst($tempInfo[0]);
				}
				if (!isset($router['func'])) {
					$router['func'] = empty($pathInfo[1])?'index':lcfirst($pathInfo[1]);;
				}
			}
		}
		return $router;
	}

	protected function getPath($path)
	{
		$arr = [
			'spu' => 'Product',
			'p' => 'Product',
			'sku' => 'Product',
			's' => 'Product',
			'cate' => 'Category',
			'c' => 'Category',
		];
		return $arr[$path] ?? $path;
	}

	public function buildUrl($url=null)
	{
		if (empty($url)) return '/';
		$router = \App::get('router');
		if (is_null($url)) $url = $router['path'].DS.$router['func'];
		if (!empty($url) && $router['view_suffix']) $url .= '.'.$router['view_suffix'];
		return APP_DOMAIN.$url;
	}

	public function urlFormat($name, $type, $param=[], $domain=null)
	{
		$name = preg_replace('/[^-A-Za-z0-9\s]/', '', strtolower($name));
		$name = preg_replace('/( ){2,}/', ' ', $name);
		$name = str_replace(' ', '-' , $name);
		$name .= $name ? '-'.$type : $type;
		$name = preg_replace('/-{1,}/', '-', $name);
		$name = ltrim($name, '-');
		if (isset($param['id'])) $name .= '-'.$param['id'];
		if (isset($param['page'])) $name .= '-page-'.$param['page'];
		if (isset($param['size'])) $name .= '-size-'.$param['size'];
		if (defined('TEMPLATE_SUFFIX')) $name .= '.'.TEMPLATE_SUFFIX;
		return APP_DOMAIN.$name;
	}

	public function setParam($param=[], $url=null)
	{
		if (is_null($url)) {
			$url = APP_DOMAIN.$_SERVER['REQUEST_URI'];
		}
		$url = str_replace('.html', '', $url);
		foreach ($param as $key=>$value) {
			if (strpos($url, $key.'-') === false) {
				$url .= $key.'-'.$value;
			} else {
				$str = $key.'-'.$value;
				$url = preg_replace('/'.$key.'-(\d+)/', $str, $url);
			}
		}
		return $url.'.html';
	}
}