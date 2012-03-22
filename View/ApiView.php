<?php
class ApiView extends View {

	protected $apiFormat;

	public function __construct($controller) {
		parent::__construct($controller);

		if ($this->request->is('json')) {
			$this->apiFormat = 'json';
		} else {
			throw new Exception('Unknown API request format');
		}
	}

	protected function _paths($plugin = null, $cached = true) {
		if ($plugin === null && $cached === true && !empty($this->_paths)) {
			return $this->_paths;
		}

		$paths = parent::_paths($plugin, $cached);
		$paths[] = App::pluginPath('Api') . 'View' . DS;

		return $this->_paths = $paths;
	}

	protected function _getViewFileName($name = null) {
		$name = $name ?: $this->view;

		// Search relative path for the api format (json / xml)
		try {
			//debug($this->viewPath . DS . $this->apiFormat . DS . $name);
			return parent::_getViewFileName($this->viewPath . DS . $this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		// Search relative path
		try {
			//debug($this->viewPath . DS . 'api' . DS . $name);
			return parent::_getViewFileName($this->viewPath . DS . 'api' . DS . $name);
		} catch (MissingViewException $e) { }

		// Search relative path for the api format (json / xml)
		try {
			//debug($this->apiFormat . DS . $name);
			return parent::_getViewFileName($this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		// Search relative path
		try {
			//debug('api' . DS . $name);
			return parent::_getViewFileName('api' . DS . $name);
		} catch (MissingViewException $e) { }

		// Search aboslute path for the api format (json / xml)
		try {
			//debug(DS . $this->apiFormat . DS . $name);
			return parent::_getViewFileName(DS . $this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		// Search aboslute path
		try {
			//debug(DS . 'api' . DS . $name);
			return parent::_getViewFileName(DS . 'api' . DS . $name);
		} catch (MissingViewException $e) { }

		// We couldn't find any API view, don't try any further
		throw new MissingViewException('Could not find API view');
	}
}