<?php
App::uses('CrudBaseEvent', 'Crud.Controller/Event');

class ApiEvent extends CrudBaseEvent {

	/**
	 * Returns a list of all events that will fire in the controller during it's lifecycle.
	 * You can override this function to add you own listener callbacks
	 *
	 * @return array
	 */
	public function implementedEvents() {
		return array(
			'Crud.init'				=> array('callable' => 'init'),

			'Crud.beforePaginate'	=> array('callable' => 'beforePaginate', 'priority' => 100),
			'Crud.afterPaginate'	=> array('callable' => 'afterPaginate', 'priority' => 100),

			'Crud.recordNotFound'	=> array('callable' => 'recordNotFound', 'priority' => 100),
			'Crud.invalidId'		=> array('callable' => 'invalidId', 'priority' => 100),

			'Crud.beforeRender'		=> array('callable' => 'beforeRender', 'priority' => 100),
			'Crud.beforeRedirect'	=> array('callable' => 'beforeRedirect', 'priority' => 100),

			'Crud.beforeSave'		=> array('callable' => 'beforeSave', 'priority' => 100),
			'Crud.afterSave'		=> array('callable' => 'afterSave', 'priority' => 100),

			'Crud.beforeFind'		=> array('callable' => 'beforeFind', 'priority' => 100),
			'Crud.afterFind'		=> array('callable' => 'afterFind', 'priority' => 100),

			'Crud.beforeDelete'		=> array('callable' => 'beforeDelete', 'priority' => 100),
			'Crud.afterDelete'		=> array('callable' => 'afterDelete', 'priority' => 100),
		);
	}

	public function init(CakeEvent $event) {
		switch($event->subject->action) {
			case 'index':
			case 'admin_index':
				if (!$event->subject->request->is('get')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'add':
			case 'admin_add':
				if (!$event->subject->request->is('post')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'edit':
			case 'admin_edit':
				if (!$event->subject->request->is('put')) {
					throw new \MethodNotAllowedException();
				}
				break;
			case 'delete':
			case 'admin_delete':
				if (!$event->subject->request->is('delete')) {
					throw new \MethodNotAllowedException();
				}
				break;
		}
	}

	public function afterSave(CakeEvent $event) {
		$response = $event->subject->controller->render();

		if ($event->subject->success) {
			$response->statusCode(201);
			$response->header('Location', \Router::url(array('action' => 'view', $event->subject->id), true));
		}

		$event->stopPropagation();
		return $response;
	}

	public function afterDelete(CakeEvent $event) {
		$event->subject->controller->set('success', $event->subject->success);
		$event->stopPropagation();
		return $event->subject->controller->render();
	}

	public function recordNotFound(CakeEvent $event) {
		throw new \NotFoundException();
	}

	public function invalidId(CakeEvent $event) {
		throw new \NotFoundException('Invalid id specified');
	}
}
