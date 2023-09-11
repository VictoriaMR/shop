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
			$name || $name = lcfirst(\App::get('router', 'path')).DS.\App::get('router', 'func');
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
			$name || $name = lcfirst(\App::get('router', 'path')).DS.\App::get('router', 'func');
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
		return $this->addStaticFile($this->_CSS, strtolower(\App::get('router', 'path').'_'.\App::get('router', 'func')), 'css');
	}

	public function getJs()
	{
		if (empty($this->_JS)) return [];
		return $this->addStaticFile($this->_JS, strtolower(\App::get('router', 'path').'_'.\App::get('router', 'func')), 'js');
	}

	public function getCommon($type)
	{
		return $this->addStaticFile(config($type, \App::get('router', 'class'))[isMobile() ? 'mobile' : 'computer'], 'common', $type);
	}

	public function getCommonJs()
	{
		if (empty($this->_COMMON_JS)) return false;
		return $this->addStaticFile($this->_COMMON_JS, 'common', 'js');
	}

	protected function addStaticFile(array $arr, $name, $type)
	{
		$path = ROOT_PATH.'template'.DS.template().DS;
		$file = 'static'.DS.(isMobile()?'m_':'c_').$name.'.'.$type;
		if (\App::get('base_info', 'static_cache') && is_file($path.$file)) return $file;
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