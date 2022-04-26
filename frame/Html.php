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
			$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			if (empty($name)) {
				$_route = \App::get('router');
				$name = lcfirst($_route['path']).DS.$_route['func'];
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
			$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			if (empty($name)) {
				$_route = \App::get('router');
				$name = lcfirst($_route['path']).DS.$_route['func'];
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
		if (empty($this->_CSS)) {
			return [];
		}
		$_route = \App::get('router');
		return $this->addStaticFile($this->_CSS, lcfirst($_route['path']).'_'.$_route['func'], 'css');
	}

	public function getJs()
	{
		if (empty($this->_JS)) {
			return [];
		}
		$_route = \App::get('router');
		return $this->addStaticFile($this->_JS, lcfirst($_route['path']).'_'.$_route['func'], 'js');
	}

	public function getCommonCss()
	{
		$arr = config('css', APP_TEMPLATE_TYPE)[IS_MOBILE?'mobile':'computer'];
		return $this->addStaticFile($arr, 'common', 'css');
	}

	public function getCommonJs()
	{
		$arr = config('js', APP_TEMPLATE_TYPE)[IS_MOBILE?'mobile':'computer'];
		return $this->addStaticFile($arr, 'common', 'js');
	}

	protected function addStaticFile(array $arr, $name, $type)
	{
		$path = ROOT_PATH.'template'.DS.APP_TEMPLATE_PATH.DS;
		$file = 'static'.DS.(IS_MOBILE?'m_':'c_').$name.'.'.$type;
		if (\App::get('base_info', 'cache') && is_file($path.$file)) {
			return $file;
		}
		$str = '';
		$arr = array_unique($arr);
		foreach ($arr as $key => $value) {
			$source = $path.$type.DS.$value.'.'.$type;
			if (is_file($source)) {
				$str .= trim(file_get_contents($source));
			}
		}
		if (!is_dir($path.'static')) mkdir($path.'static', 0755, true);
		file_put_contents($path.$file, $str);
		return $file;
	}
}