<?php 

namespace frame;

class Html
{
	protected $_CSS = [];
	protected $_JS = [];

	public function addCss($name='', $match=true)
	{
		$matchPath = '';
		if ($match) {
			if (env('APP_VIEW_MATCH')) {
				$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			}
			if (empty($name)) {
				$_route = router()->getRoute();
				$name = $_route['path'].DS.$_route['func'];
			}
		}
		if (is_array($name)) {
			foreach ($name as $value) {
				$this->_CSS[] = $matchPath.$value;
			}
		} else {
			$this->_CSS[] = $matchPath.$name;
		}
	}

	public function addJs($name='', $match=true)
	{
		$matchPath = '';
		if ($match) {
			if (env('APP_VIEW_MATCH')) {
				$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			}
			if (empty($name)) {
				$_route = router()->getRoute();
				$name = $_route['path'].DS.$_route['func'];
			}
		}
		if (is_array($name)) {
			foreach ($name as $value) {
				$this->_JS[] = $matchPath.$value;
			}
		} else {
			$this->_JS[] = $matchPath.$name;
		}
	}

	public function getCss()
	{
		$path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
		$_route = router()->getRoute();
		$file = 'static'.DS.(IS_MOBILE ? 'm_' : 'c_').$_route['path'].'_'.$_route['func'].'.css';
		if (APP_STATIC && is_file($path.DS.$file)) {
			return $file;
		}
		$cssStr = '';
		$this->_CSS = array_unique($this->_CSS);
		foreach ($this->_CSS as $key => $value) {
			$source = $path.'css'.DS.$value.'.css';
			if (is_file($source)) {
				$cssStr .= trim(file_get_contents($source));
			}
		}
		if (!is_dir($path.'static')) {
			mkdir($path.'static', 0750, true);
		}
		file_put_contents($path.DS.$file, $cssStr);
		return $file;
	}

	public function getJs()
	{
		$path = ROOT_PATH.APP_TEMPLATE_TYPE.DS;
		$_route = router()->getRoute();
		$file = 'static'.DS.(IS_MOBILE ? 'm_' : 'c_').$_route['path'].'_'.$_route['func'].'.js';
		if (APP_STATIC && is_file($path.$file)) {
			return $file;
		}
		$jsStr = '';
		$this->_JS = array_unique($this->_JS);
		foreach ($this->_JS as $key => $value) {
			$source = $path.'js'.DS.$value.'.js';
			if (is_file($source)) {
				$jsStr .= trim(file_get_contents($source));
			}
		}
		if (!is_dir($path.'static')) {
			mkdir($path.'static', 0750, true);
		}
		file_put_contents($file, $jsStr);
		return $file;
	}
}