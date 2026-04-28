<?php

namespace frame;

class View 
{
	private $_data = [];
	private $_device;

	public function display($template, $match=true, $data=[])
	{
		$data['layout_include_path'] = $this->getTemplate($template, $match);
		$this->loadFile(ROOT_PATH.'template/'.config('domain', 'template').'/layout.php', $data);
	}

	private function loadFile($template, array $data)
	{
		if (!is_file($template)) {
			echo isDebug()
				? '<pre style="color:red">[Error] Template not found: ' . $template . '</pre>'
				: '<!-- template missing -->';
			return;
		}
		$this->setData($data);
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
		if ($data) {
			$this->_data += $data;
		}
	}

	private function getTemplate($template, $match=true)
	{
		if ($match) {
			$template || $template = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
			if (!$this->_device) {
				$this->_device = isMobile() ? 'mobile' : 'computer';
			}
			$template = 'template/'.config('domain', 'template').'/'.$this->_device.'/view/'.$template;
		}
		return ROOT_PATH.$template.'.php';
	}

	public function load($template='', $data=[], $match=true)
	{
		$this->loadFile($this->getTemplate($template, $match), $data);
	}
}