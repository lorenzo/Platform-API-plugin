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

	/**
	 * _getViewFileName
	 *
	 * Search relative and absolute (to the view folder) paths for which view to use for the given api call
	 *
	 * @param mixed $name
	 * @return void
	 */
	protected function _getViewFileName($name = null) {
		$name = $name ?: $this->view;

		try {
			//debug($this->viewPath . DS . $this->apiFormat . DS . $name);
			return parent::_getViewFileName($this->viewPath . DS . $this->apiFormat . DS . $name);
		} catch (MissingViewException $e) { }

		$files[] = $this->viewPath . DS . 'api' . DS . $name;
		try {
			return parent::_getViewFileName(end($files));
		} catch (MissingViewException $e) { }

		$files[] = $this->apiFormat . DS . $name;
		try {
			return parent::_getViewFileName(end($files));
		} catch (MissingViewException $e) { }

		$files[] = 'api' . DS . $name;
		try {
			return parent::_getViewFileName(end($files));
		} catch (MissingViewException $e) { }

		$files[] = DS . $this->apiFormat . DS . $name;
		try {
			return parent::_getViewFileName(end($files));
		} catch (MissingViewException $e) { }

		$files[] = DS . 'api' . DS . $name;
		try {
			return parent::_getViewFileName(end($files));
		} catch (MissingViewException $e) { }

		foreach($files as &$file) {
			$file .= '.ctp';
		}
		throw new MissingViewException("Could not find API view for $name, create one of " . implode(', ', $files));
	}
}
