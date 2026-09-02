<?php

namespace frame;

class View 
{
	private $_data = [];
	private $_device;
	private $_templateDir;
	private $_tplCache = [];

	public function display($template = '', $match = true, $data = [])
	{
		$data['layout_include_path'] = $this->getTemplate($template, $match);
		$this->loadFile($this->getTemplateDir() . 'layout.php', $data);
	}

	private function loadFile($template, array $data = [])
	{
		if (!is_file($template)) {
			echo isDebug()
				? '<pre style="color:red">[Error] Template not found: ' . $template . '</pre>'
				: '<!-- template missing -->';
			return;
		}
		if (!empty($data)) {
			$this->_data = array_merge($this->_data, $data);
		}
		extract($this->_data, EXTR_OVERWRITE);
		try {
			include $template;
		} catch (\Throwable $e) {
			echo isDebug()
				? '<pre style="color:red">[Error] ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . '</pre>'
				: '<!-- render error -->';
		}
	}

	public function setData($data)
	{
		if (!empty($data)) {
			$this->_data = array_merge($this->_data, $data);
		}
	}

	private function getTemplateDir()
	{
		if ($this->_templateDir === null) {
			$this->_templateDir = ROOT_PATH . 'template/' . config('domain', 'template') . '/';
		}
		return $this->_templateDir;
	}

	private function getTemplate($template, $match = true)
	{
		$cacheKey = $template . ($match ? '_1' : '_0');
		if (isset($this->_tplCache[$cacheKey])) {
			return $this->_tplCache[$cacheKey];
		}

		if ($match) {
			if ($template === '') {
				$template = lcfirst(\App::get('router', 'path', 'Index')) . '/' . \App::get('router', 'func', 'index');
			}
			if ($this->_device === null) {
				$this->_device = isMobile() ? 'mobile' : 'computer';
			}
			$path = $this->getTemplateDir() . $this->_device . '/view/' . $template . '.php';
		} else {
			$path = ROOT_PATH . $template . '.php';
		}

		return $this->_tplCache[$cacheKey] = $path;
	}

	public function load($template = '', $data = [], $match = true)
	{
		$this->loadFile($this->getTemplate($template, $match), $data);
	}
}