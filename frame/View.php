<?php

namespace frame;

class View
{
	private array $_data = [];
	private ?string $_device = null;
	private ?string $_templateDir = null;
	private array $_tplCache = [];

	public function display($template = '', $match = true, array $data = []): void
	{
		$data['layout_include_path'] = $this->getTemplate($template, $match);
		$this->loadFile($this->getTemplateDir() . 'layout.php', $data);
	}

	private function loadFile(string $template, array $data = []): void
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

	public function setData(array $data): void
	{
		if (!empty($data)) {
			$this->_data = array_merge($this->_data, $data);
		}
	}

	private function getTemplateDir(): string
	{
		if ($this->_templateDir === null) {
			$this->_templateDir = ROOT_PATH . 'template/' . \App::get('domain', 'template') . '/';
		}
		return $this->_templateDir;
	}

	private function getTemplate(string $template, bool $match = true): string
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
			$cleanTemplate = str_starts_with($template, ROOT_PATH)
				? substr($template, strlen(ROOT_PATH))
				: $template;
			if (!str_ends_with($cleanTemplate, '.php')) {
				$cleanTemplate .= '.php';
			}
			$path = ROOT_PATH . ltrim($cleanTemplate, '/\\');
		}

		return $this->_tplCache[$cacheKey] = $path;
	}

	public function load(string $template = '', array $data = [], bool $match = true): void
	{
		$this->loadFile($this->getTemplate($template, $match), $data);
	}

	public function getContent(string $template = '', array $data = [], bool $match = false): string
	{
		ob_start();
		$this->load($template, $data, $match);
		return (string)ob_get_clean();
	}
}
