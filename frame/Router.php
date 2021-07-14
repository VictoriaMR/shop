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
			} else {
				$pathInfo['path'] = str_replace(env('TEMPLATE_SUFFIX'), '', $pathInfo['path']);
				if (strpos($pathInfo['path'], '-') === false) {
					$pathInfo = explode(DS, $pathInfo['path']);
			        switch (count($pathInfo)) {
			        	case 0:
			        		$this->_route['path'] = 'index';
				        	$this->_route['func'] = 'index';
			        		break;
			        	case 1:
			        		$this->_route['path'] = implode(DS, $pathInfo);
				        	$this->_route['func'] = 'index';
			        		break;
			        	default:
			        		$func = array_pop($pathInfo);
			        		$this->_route['path'] = implode(DS, $pathInfo);
			        		$this->_route['func'] = $func;
			        		break;
			        }
				} else {
					$pathInfo = explode('-', $pathInfo['path']);
					$temp = array_pop($pathInfo);
					//是页码
					if (strlen($temp) > 1 && strpos($temp, 'p') === 0) {
						$_GET['page'] = (int)substr($temp, 1);
						$temp = array_pop($pathInfo);
					}
					$id = (int) $temp;
					$temp = array_pop($pathInfo);
					switch ($temp) {
						case 'c':
							$this->_route['path'] = 'category';
							$_GET['cid'] = $id;
							break;
						case 'p':
						case 'k':
							$this->_route['path'] = 'product';
							$_GET['s'.$temp.'u_id'] = $id;
							break;
						default:
							$this->_route['path'] = 'index';
							break;
					}
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

	public function getRoute()
	{
		return $this->_route;
	}

	public function buildUrl($url=null, $param=null)
	{
		if (is_null($url)) {
			$url = $this->_route['path'].DS.$this->_route['func'];
		}
		if (!empty($url)) {
			$url .= empty(env('TEMPLATE_SUFFIX')) ? '' : '.'.env('TEMPLATE_SUFFIX');
		}
		if (empty($param)) {
			$param = iget();
		}
		if (!empty($param)) {
			$url .= '?' . http_build_query($param);
		}
		return env('APP_DOMAIN').$url;
	}
}