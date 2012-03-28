<?php
App::uses('ExceptionRenderer', 'Error');

class ApiExceptionRenderer extends ExceptionRenderer {
	/**
	 * A safer way to render error messages, replaces all helpers, with basics
	 * and doesn't call component methods.
	 *
	 * @param string $template The template to render
	 * @return void
	 */
	protected function _outputMessageSafe($template) {
		$this->controller->layoutPath = '';
		$this->controller->subDir = '';
		$this->controller->viewPath = 'Errors/';
		$this->controller->viewClass = 'View';
		$this->controller->helpers = array('Form', 'Html', 'Session');

		$this->controller->render($template);
		$this->controller->response->send();
	}
}