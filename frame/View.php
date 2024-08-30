<?php

namespace frame;

class View 
{
	protected $_data = [];

	public function display($template, $match=true)
	{
		return $this->fetch(ROOT_PATH.'template/'.config('domain', 'template').'/layout.php', [
			'layout_include_path'=>$this->getTemplate($template, $match)
		]);
	}

	protected function fetch($template, array $data=[])
	{	
		echo $this->getContent($template, $data);
	}
	private function loadFile($template, array $data)
	{
		if (is_file($template)) {
			extract(array_merge($this->_data, $data), EXTR_OVERWRITE);
			return include $template;
		}
		throw new \Exception($template.' was not exist!', 1);
	}

	private function getContent($template, $data=[])
	{
		ob_start();
		$this->loadFile($template, $data);
		return ob_get_clean();
	}

	private function getTemplate($template, $match=true)
	{
		if ($match) {
			$template || $template = lcfirst(\App::get('router', 'path')).'/'.\App::get('router', 'func');
			$template = 'template/'.config('domain', 'template').'/'.(isMobile()?'m':'c').'/view/'.$template;
		}
		return ROOT_PATH.$template.'.php';
	}

	public function assign($name, $value = null)
	{
		if (is_array($name)) {
			$this->_data = array_merge($this->_data, $name);
		} else {
			$this->_data[$name] = $value;
		}
		return $this;
	}

	public function load($template='', $data=[], $match=true)
	{
		$this->loadFile($this->getTemplate($template, $match), $data);
	}
}