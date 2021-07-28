<?php

namespace frame;

final class Router
{
	static private $_instance;
	protected $_route = []; //路由

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
		$this->_route['class'] = APP_TEMPLATE_TYPE;
		if (empty($pathInfo)) {
			$this->_route['path'] = 'index';
			$this->_route['func'] = 'index';
		} else {
			$pathInfo = parse_url($pathInfo);
			if (empty($pathInfo['path'])) {
				$this->_route['path'] = 'index';
				$this->_route['func'] = 'index';
			} elseif(defined('TEMPLATE_SUFFIX') && strpos($pathInfo['path'], TEMPLATE_SUFFIX) !== false){
				$pathInfo['path'] = str_replace('.'.TEMPLATE_SUFFIX, '', $pathInfo['path']);
				$routerArr = explode('-', $pathInfo['path']);
				if (!empty($routerArr) && count($routerArr) > 1) {
					$routerArr = array_reverse($routerArr);
					if (count($routerArr) > 1) {
						foreach ($routerArr as $key => $value) {
							if ($key % 2 > 0) {
								if (in_array($value, ['page', 'size'])) {
									$_GET[$value] = $routerArr[$key-1];
								} else {
									$this->_route['path'] = $value;
									$_GET['id'] = $routerArr[$key-1];
									break;
								}
							}
						}
					} else {
						$this->_route['path'] = $routerArr[0];
					}
					$this->_route['func'] = 'index';
				}
			}

			if (empty($this->_route['path'])) {
				$temp = explode('/', $pathInfo['path']);
				if (count($temp) > 1) {
					$this->_route['func'] = array_pop($temp);
					$this->_route['path'] = implode('/', $temp);
				} else {
					$this->_route['path'] = $temp[0];
					$this->_route['func'] = 'index';
				}
			}
		}
		array_shift($_GET);
		if (count($this->_route) != 3) {
			throw new \Exception(' router analyed error', 1);
		}
		return $this;
	}

	public function getRoute($name='')
	{
		if (empty($name)) {
			return $this->_route;
		}
		return $this->_route[$name] ?? '';
	}

	public function buildUrl($url=null, $param=null)
	{
		if (is_null($url)) {
			$url = $this->_route['path'].DS.$this->_route['func'];
		}
		if (!empty($url)) {
			$url .= defined('TEMPLATE_SUFFIX') ? '.'.TEMPLATE_SUFFIX : '';
		}
		if (empty($param)) {
			$param = iget();
		}
		if (!empty($param)) {
			$url .= '?' . http_build_query($param);
		}
		return env('APP_DOMAIN').$url;
	}

	public function siteUrl($name, $type, $param=[])
	{
		$name = preg_replace('/[^-A-Za-z0-9 ]/', '', strtolower($name));
		$name = preg_replace('/( ){2,}/', ' ', $name);
		$name = str_replace(' ', '-' , $name);
		$name .= '-'.$type;
		if (isset($param['page'])) {
			$name .= '-page-'.$param['page'];
		}
		if (isset($param['size'])) {
			$name .= '-size-'.$param['size'];
		}
		if (defined('TEMPLATE_SUFFIX')) {
			$name .= '.'.TEMPLATE_SUFFIX;
		}
		return env('APP_DOMAIN').$name;
	}
}