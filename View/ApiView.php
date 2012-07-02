<?php
App::uses('View', 'View');

/**
 * ApiView
 */
class ApiView extends View {

/**
 * apiFormat
 *
 * @var string
 */
	protected $apiFormat = 'json';

/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin Optional plugin name to scan for view files.
 * @param boolean $cached Set to true to force a refresh of view paths.
 * @return array paths
 */
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
 * @throws \MissingViewException
 * @param mixed $name
 * @return void
 */
	protected function _getViewFileName($name = null) {
		$name = $name ?: $this->view;

		$this->subDir = $this->apiFormat;

		try {
			return parent::_getViewFileName($name);
		} catch (MissingViewException $exception) {
		}

		try {
			return parent::_getViewFileName(DS . $this->apiFormat . DS . $name);
		} catch (MissingViewException $e) {
			if (isset($this->viewVars['success']) || isset($this->viewVars['data'])) {
				return parent::_getViewFileName(DS . $this->apiFormat . DS . 'fallback_template');
			}
			throw $e;
		}
	}
}
