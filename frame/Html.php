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
			$matchPath = (isMobile() ? 'mobile' : 'computer').'/';
			$name || $name = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
		}
		if (!is_array($name)) {
			$name = array_map('trim', explode(',', $name));
		}
		foreach ($name as $value) {
			$this->_CSS[] = $matchPath.$value;
		}
	}

	public function addJs($name='', $match=true)
	{
		$matchPath = '';
		if ($match) {
			$matchPath = (isMobile() ? 'mobile' : 'computer').'/';
			$name || $name = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
		}
		if (!is_array($name)) {
			$name = array_map('trim', explode(',', $name));
		}
		foreach ($name as $value) {
			$this->_JS[] = $matchPath.$value;
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

	public function getLanguageJs()
	{
		$path = ROOT_PATH.'template/'.config('domain', 'template').'/';
		$file = 'js/'.(isMobile() ? 'mobile' : 'computer').'/'.strtolower(\App::get('router', 'path')).'/'.\App::get('router', 'func').'_'.lanId('code').'.js';
		if (is_file($path.$file)) {
			return $file;
		}
		return false;
	}

	protected function addStaticFile(array $arr, $name, $type)
	{
		$path = ROOT_PATH.'template/'.config('domain', 'template').'/'.(isMobile()?'mobile':'computer').'/';
		$file = 'static/'.$name.'.'.$type;
		if (\App::get('base_info', 'static_cache', false) && is_file($path.$file)) return $file;
		$str = '';
		$arr = array_unique($arr);
		foreach ($arr as $key => $value) {
			$source = $path.$type.'/'.$value.'.'.$type;
			if (is_file($source)) {
				$str .= '/* '.$name.' */'.PHP_EOL;
				$str .= trim(file_get_contents($source)).PHP_EOL;
			}
		}
		if ($str) {
			createDir($path.'static/');
			if ($type == 'css') {
				$str = $this->compressCss($str);
			} elseif ($type == 'js') {

			}
			file_put_contents($path.$file, $str);
			return $file;
		}
		return false;
	}

	protected function compressCss($css)
	{
		return str_replace(array(' {', ';}'), array('{', '}'), preg_replace('/\s*(?<=[}{:,;])\s*/', '', preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', str_replace(PHP_EOL, '', $css))));
	}
}