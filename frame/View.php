<?php

namespace frame;

class View 
{
	protected $_data = [];

	public function display($template='', $match=true)
	{
		$this->fetch(ROOT_PATH.'template'.DS.APP_TEMPLATE_TYPE.DS.'view'.DS.'layout.php', ['layout_include_path'=>$this->getTemplate($template, $match)]);
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

	public function getContent($template, $data=[])
	{
		// 页面缓存
		ob_start();
		ob_implicit_flush(0);
		extract($data, EXTR_OVERWRITE);
		include $template;
		return ob_get_clean();
	}

	private function getTemplate($template, $match=true)
	{
		if ($match) {
			$matchPath = '';
			$matchPath = (IS_MOBILE ? 'mobile' : 'computer').DS;
			if (empty($template)) {
				$_route = \App::get('router');
				$template = lcfirst($_route['path']).DS.$_route['func'];
			}
			$template = 'template'.DS.APP_TEMPLATE_TYPE.DS.'view'.DS.$matchPath.$template;
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
		$this->fetch($this->getTemplate($template, $match), $data);
	}
}