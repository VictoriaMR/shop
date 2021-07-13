<?php

namespace frame;

class View 
{
	protected $_data = [];

	public function display($template='', $match=true)
	{
		$this->fetch(ROOT_PATH.APP_TEMPLATE_TYPE.DS.'view'.DS.'layout.php', ['layout_include_path'=>$this->getTemplate($template, $match)]);
	}

	protected function fetch($template, array $data=[])
	{
		if (is_file($template)) {
			extract(array_merge($this->_data, $data), EXTR_OVERWRITE);
			include $template;
		} else {
			throw new \Exception($template.' was not exist!', 1);
		}
	}

	private function getTemplate($template, $match = true)
	{
		if ($match) {
			$matchPath = '';
			if (env('APP_VIEW_MATCH')) {
				$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			}
			if (empty($template)) {
				$_route = router()->getRoute();
				$template = $_route['path'].DS.$_route['func'];
			}
			$template = APP_TEMPLATE_TYPE.DS.'view'.DS.$matchPath.$template;
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

	public function load($template = '', $match = true)
	{
		$this->fetch($this->getTemplate($template, $match));
	}
}