<?php
class ApiView extends View {

	protected $apiFormat = 'json';

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
			return parent::_getViewFileName($this->apiFormat . DS . $name);
		} catch (MissingViewException $e) {
			if (isset($this->viewVars['success']) || isset($this->viewVars['data'])) {
				return parent::_getViewFileName(DS . $this->apiFormat . DS . 'fallback_template');
			}
			throw $e;
		}

	}
}
