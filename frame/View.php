<?php

namespace frame;

class View 
{
	private $_data = array();

	public function display($template, $match=true, $data=array())
	{
		$data['layout_include_path'] = $this->getTemplate($template, $match);
		$this->loadFile(ROOT_PATH.'template/'.config('domain', 'template').'/layout.php', $data);
	}

	private function loadFile($template, array $data)
	{
		if (is_file($template)) {
			extract($this->_data + $data, EXTR_OVERWRITE);
			include $template;
		} else {
			throw new \Exception($template.' was not exist!', 1);
		}
	}

	public function setData($data)
	{
		if (!empty($data)) {
			$this->_data = $this->_data + $data;
		}
	}

	private function getTemplate($template, $match=true)
	{
		if ($match) {
			$template || $template = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
			$template = 'template/'.config('domain', 'template').'/'.(isMobile()?'mobile':'computer').'/view/'.$template;
		}
		return ROOT_PATH.$template.'.php';
	}

	public function load($template='', $data=[], $match=true)
	{
		$this->loadFile($this->getTemplate($template, $match), $data);
	}
}