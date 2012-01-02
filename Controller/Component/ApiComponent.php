<?php
class ApiComponent extends Component {

    protected $Controller;

    public function initialize(Controller $Controller) {
        $this->controller   = $Controller;
        $this->request      = $Controller->request;
        $this->response     = $Controller->response;

        $this->configureRequestDetectors();
        $this->configureApiToken();

        if ($this->request->is('api')) {
            $this->controller->viewClass = 'Api.Api';
        }
    }

    public function beforeRedirect($Controller, $url, $status = null, $exit = true) {
        if ($this->request->is('api')) {
            if (empty($status)) {
                $status = 302;
            }

            $url = Router::url($url, true);

            $Controller->view = 'redirect';
            switch($status) {
                case 404:
                    $this->response->statusCode(404);
                    $this->response->send();
                    die;
                case 301:
                case 302:
                    $this->response->statusCode($status);
                    $this->response->header(array('location' => $url));
                    break;
                default:
                    break;
            }

            $Controller->set(compact('url', 'status'));
            $Controller->render();

            $this->response->send();
            die;
        }
    }

    public function startup() {
        // Public actions is visible to everyone
        if (!in_array($this->action, $this->controller->publicActions)) {
            $this->configureApiAccess();
        }
    }

    protected function configureApiAccess() {
        if (!$this->request->is('api')) {
            return;
        }

        if ($this->controller->Auth->user()) {
            return;
        }

        if (!Configure::read('Platform.AccessToken')) {
            throw new ForbiddenException('Permission denied');
        }

        if (!$this->controller->Auth->login()) {
            throw new ForbiddenException('Permission denied');
        }
    }

    protected function configureApiToken() {
        $token = null;
        if (!empty($this->request->data['token'])) {
            $token = $this->request->data['token'];
        } elseif (!empty($this->request->query['token'])) {
            $token = $this->request->query['token'];
        } elseif (!empty($this->request->params['named']['token'])) {
            $token = $this->request->params['named']['token'];
        } elseif (!empty($_SERVER['HTTP_PLATFORM_TOKEN'])) {
            $token = $_SERVER['HTTP_PLATFORM_TOKEN'];
        }
        Configure::write('Platform.AccessToken', $token);
    }

    protected function configureRequestDetectors() {
        // Add detector for json
        $this->request->addDetector('json', array('callback' => function(CakeRequest $request) {
            if (isset($request->params['ext']) && $request->params['ext'] === 'json') {
                return true;
            }
            return $request->accepts('json');
        }));

        $this->request->addDetector('api', array('callback' => function(CakeRequest $request) {
            return $request->is('json');
        }));
    }
}