<?php

namespace frame;

class View 
{
	protected $_data = [];

	public function display($template, $match=true)
	{
		return $this->fetch(ROOT_PATH.'template'.DS.path().DS.'view'.DS.'layout.php', ['layout_include_path'=>$this->getTemplate($template, $match)]);
	}

	protected function fetch($template, array $data=[])
	{	
		ob_start();
		ob_implicit_flush(0);
		$this->loadFile($template, $data);
		echo ob_get_clean();
		return true;
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
		ob_implicit_flush(0);
		$this->loadFile($template, $data);
		return ob_get_clean();
	}

	private function getTemplate($template, $match=true)
	{
		if ($match) {
			$matchPath = (isMobile()?'mobile':'computer').DS;
			if (empty($template)) {
				$_route = \App::get('router');
				$template = lcfirst($_route['path']).DS.$_route['func'];
			}
			$template = 'template'.DS.path().DS.'view'.DS.$matchPath.$template;
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