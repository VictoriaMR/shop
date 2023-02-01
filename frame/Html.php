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
			$matchPath = (isMobile() ? 'mobile' : 'computer').DS;
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
			$matchPath = (isMobile() ? 'mobile' : 'computer').DS;
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
		if (empty($this->_CSS)) return [];
		$_route = \App::get('router');
		return $this->addStaticFile($this->_CSS, strtolower($_route['path'].'_'.$_route['func']), 'css');
	}

	public function getJs()
	{
		if (empty($this->_JS)) return [];
		$_route = \App::get('router');
		return $this->addStaticFile($this->_JS, strtolower($_route['path'].'_'.$_route['func']), 'js');
	}

	public function getCommonCss()
	{
		$arr = config('css', type())[isMobile()?'mobile':'computer'];
		return $this->addStaticFile($arr, 'common', 'css');
	}

	public function getCommonJs()
	{
		$arr = config('js', type())[isMobile()?'mobile':'computer'];
		return $this->addStaticFile($arr, 'common', 'js');
	}

	protected function addStaticFile(array $arr, $name, $type)
	{
		$path = ROOT_PATH.'template'.DS.path().DS;
		$file = 'static'.DS.(isMobile()?'m_':'c_').$name.'.'.$type;
		if (\App::get('base_info', 'static_cache') && is_file($file)) return $file;
		$str = '';
		$arr = array_unique($arr);
		foreach ($arr as $key => $value) {
			$source = $path.$type.DS.$value.'.'.$type;
			if (is_file($source)) {
				$str .= '/* '.$name.' */'.PHP_EOL;
				$str .= trim(file_get_contents($source)).PHP_EOL;
			}
		}
		if (!is_dir($path.'static')) mkdir($path.'static', 0755, true);
		file_put_contents($path.$file, trim($str, PHP_EOL));
		return $file;
	}
}