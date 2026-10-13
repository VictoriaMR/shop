<?php 

namespace frame;

class Html
{
	protected $_CSS = [];
	protected $_JS = [];

	public function addCss($name = '')
	{
		if ($name === '') {
			$name = lcfirst(\App::get('router', 'path', 'Index')) . '/' . \App::get('router', 'func', 'index');
		}
		$this->_CSS[] = $name;
	}

	public function addJs($name = '')
	{
		if ($name === '') {
			$name = lcfirst(\App::get('router', 'path', 'Index')) . '/' . \App::get('router', 'func', 'index');
		}
		$this->_JS[] = $name;
	}

	public function getCss()
	{
		if (empty($this->_CSS)) {
			return [];
		}
		$routeKey = strtolower(\App::get('router', 'path', 'Index') . '_' . \App::get('router', 'func', 'index'));
		return $this->addStaticFile($this->_CSS, $routeKey, 'css');
	}

	public function getJs()
	{
		if (empty($this->_JS)) {
			return [];
		}
		$routeKey = strtolower(\App::get('router', 'path', 'Index') . '_' . \App::get('router', 'func', 'index'));
		return $this->addStaticFile($this->_JS, $routeKey, 'js');
	}

	public function getCommon($type)
	{
		$class = \App::get('router', 'class', 'home');
		$config = config($type, $class);
		$device = isMobile() ? 'mobile' : 'computer';
		$files = $config[$device] ?? [];
		return $this->addStaticFile($files, 'common', $type);
	}

	public function getLanguageJs()
	{
		$device = isMobile() ? 'mobile' : 'computer';
		$path = ROOT_PATH . 'template/' . \App::get('domain', 'template') . '/';
		$file = 'js/' . $device . '/' . strtolower(\App::get('router', 'path', 'Index')) . '/' . \App::get('router', 'func', 'index') . '_' . lanId('code') . '.js';
		if (is_file($path . $file)) {
			return $file;
		}
		return false;
	}

	protected function addStaticFile(array $arr, $name, $type)
	{
		if (empty($arr)) {
			return false;
		}
		$device = isMobile() ? 'mobile' : 'computer';
		$path = ROOT_PATH . 'template/' . \App::get('domain', 'template') . '/' . $device . '/';
		$file = 'static/' . $name . '.' . $type;

		if (\App::get('domain', 'static_cache') && is_file($path . $file)) {
			return $file;
		}

		$str = '';
		$arr = array_unique($arr);
		foreach ($arr as $value) {
			$source = $path . $type . '/' . $value . '.' . $type;
			if (is_file($source)) {
				$str .= '/* ' . $name . ' */' . PHP_EOL . trim(file_get_contents($source)) . PHP_EOL;
			}
		}

		if ($str !== '') {
			createDir($path . 'static/');
			if ($type === 'css') {
				$str = $this->compressCss($str);
			}
			file_put_contents($path . $file, $str);
			return $file;
		}
		return false;
	}

	protected function compressCss($css)
	{
		// 移除注释
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		// 移除空白与换行
		$css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
		$css = preg_replace('/\s+/', ' ', $css);
		$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
		$css = str_replace(';}', '}', $css);
		return trim($css);
	}
}
