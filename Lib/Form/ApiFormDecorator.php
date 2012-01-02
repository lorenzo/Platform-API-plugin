<?php
App::uses('BaseFormDecorator', 'Core.Form');

class ApiFormDecorator extends BaseFormDecorator {

    public function init($Controller, $action) {
        switch($action) {
            case 'index':
                if (!$Controller->request->is('get')) {
                    $Controller->response->statusCode(405); // Method Not Allowed
                    $Controller->response->send();
                    die;
                }
                break;
            case 'add':
                if (!$Controller->request->is('post')) {
                    $Controller->response->statusCode(405); // Method Not Allowed
                    $Controller->response->send();
                    die;
                }
                break;
            case 'edit':
                if (!$Controller->request->is('put')) {
                    $Controller->response->statusCode(405); // Method Not Allowed
                    $Controller->response->send();
                    die;
                }
                break;
            case 'delete':
                if (!$Controller->request->is('delete')) {
                    $Controller->response->statusCode(405); // Method Not Allowed
                    $Controller->response->send();
                    die;
                }
        }
    }

    public function recordNotFound($Controller, $action) {
        $Controller->response->statusCode(404);
        $Controller->response->send();
        die;
    }
}