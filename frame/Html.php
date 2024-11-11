<?php 

namespace frame;

class Html
{
	protected $_CSS = [];
	protected $_JS = [];

	public function addCss($name='')
	{
		$name || $name = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
		$this->_CSS[] = $name;
	}

	public function addJs($name='')
	{
		$name || $name = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
		$this->_JS[] = $name;
	}

	public function getCss()
	{
		return $this->_CSS ? $this->addStaticFile($this->_CSS, strtolower(\App::get('router', 'path').'_'.\App::get('router', 'func')), 'css') : [];
	}

	public function getJs()
	{
		return $this->_JS ? $this->addStaticFile($this->_JS, strtolower(\App::get('router', 'path').'_'.\App::get('router', 'func')), 'js') : [];
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
		if (\App::get('domain', 'static_cache') && is_file($path.$file)) return $file;
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
			} else {

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