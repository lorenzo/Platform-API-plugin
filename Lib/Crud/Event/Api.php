<?php
namespace Crud\Event;

\App::uses('BaseFormDecorator', 'Crud.Form');

class Api extends Base {

	public function init(\Controller $controller, $action) {
		switch($action) {
			case 'index':
			case 'admin_index':
				if (!$controller->request->is('get')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'add':
			case 'admin_add':
				if (!$controller->request->is('post')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'edit':
			case 'admin_edit':
				if (!$controller->request->is('put')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'delete':
			case 'admin_delete':
				if (!$controller->request->is('delete')) {
					throw new \MethodNotAllowedException();
				}
				break;
		}
	}

	public function recordNotFound($controller, $action) {
		throw new \FileNotFoundException();
	}
}