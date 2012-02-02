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
			return parent::_getViewFileName($this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		// Search relative path
		try {
			return parent::_getViewFileName('api/' . $name);
		} catch (MissingViewException $e) { }

		// Search aboslute path for the api format (json / xml)
		try {
			return parent::_getViewFileName(DS . $this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		// Search aboslute path
		try {
			return parent::_getViewFileName('/api/' . $name);
		} catch (MissingViewException $e) { }

		// Default to the normal view if everything else fails
		return parent::_getViewFileName($name);
	}
}