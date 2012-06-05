<?php
App::uses('ExceptionRenderer', 'Error');

class ApiExceptionRenderer extends ExceptionRenderer {

/**
 * Generate the response using the controller object.
 *
 * If there is no specific template for the error raised (normally there will
 * not be) swallow the missing view exception and just use the standard
 * error format. This prevents throwing a RandomException and seeing instead
 * a MissingView exception
 *
 * @param string $template The template to render.
 * @return void
 */
	protected function _outputMessage($template) {
		try {
			$this->controller->render($template);
			$this->controller->afterFilter();
			$this->controller->response->send();
		} catch (MissingViewException $e) {
			$this->_outputMessageSafe('error500');
		} catch (Exception $e) {
			$this->controller->set(array(
				'error' => $e,
				'name' => $e->getMessage(),
				'code' => $e->getCode(),
			));
			$this->_outputMessageSafe('error500');
		}
	}

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
