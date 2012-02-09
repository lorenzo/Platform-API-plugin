<?php
/**
 * API component
 *
 * Handles the automatic transformation of HTTP requests to API responses
 *
 * @see https://wiki.ournodes.com/display/platform/Api+Plugin
 * @see http://book.cakephp.org/2.0/en/controllers/components.html#Component
 * @copyright Nodes ApS, 2011
 */
class ApiComponent extends Component {

	/**
	* Reference to the current controller
	*
	* @var Controller
	*/
	protected $Controller;

	/**
	* Reference to the current request
	*
	* @var CakeRequest
	*/
	protected $request;

	/**
	* Reference to the current response
	*
	* @var CakeResponse
	*/
	protected $response;

	public function initialize(Controller $controller) {
		$this->controller	= $controller;
		$this->request		= $controller->request;
		$this->response		= $controller->response;

		// Ensure we can detect API requests
		$this->configureRequestDetectors();
		$this->setup();
	}

	public function startup(Controller $controller) {
		$this->setup();
	}

	protected function setup() {
		// Don't do additional work if its'n not an API request
		if (!$this->request->is('api')) {
			return;
		}

		Configure::write('ResponseObject', $this->response);

		// Switch to the API view class
		$this->controller->viewClass = 'Api.Api';

		// Read out the API token
		$this->configureApiToken();

		// Enforce API authentication
		$this->configureApiAccess();
	}

	public function hasError() {
		return get_class($this->controller) == 'CakeErrorController';
	}

	public function beforeRedirect($controller, $url, $status = null, $exit = true) {
		if ($controller->request->is('api')) {
			if (empty($status)) {
				$status = 302;
			}

			// Make sure URls always is absolute
			$url = Router::url($url, true);

			$controller->view = 'redirect';
			switch($status) {
				case 404:
					$controller->response->statusCode(404);
					$controller->response->send();
					die;
				case 301:
				case 302:
					$controller->response->statusCode($status);
					$controller->response->header(array('location' => $url));
					break;
				default:
					break;
			}

			// Render the redirect view
			$controller->set(compact('url', 'status'));
			$controller->render();

			// Send the result and stop the request
			$controller->response->send();
			$this->_stop();
		}
	}

	/**
	* Ensures that the current request is validated for Authentication
	*
	*/
	protected function configureApiAccess() {
		// Do not enforce authentication if the request isn't API
		if (!$this->request->is('api')) {
			return;
		}

		// Don't enforce API access check if the request is public
		if ($this->hasError()) {
			return;
		}

		// If its a public action, do not enforce API security checks
		if (in_array($this->controller->action, $this->controller->publicActions)) {
			return;
		}

		// Do not enforce authentication if the request is already authenticated
		if ($this->controller->Auth->user()) {
			return;
		}

		// Deny access if no AccessToken is provided
		if (!Configure::read('Platform.AccessToken')) {
			throw new ForbiddenException('Permission denied, missing access token');
		}

		// Deny access if the AccessToken isn't valid
		if (!$this->controller->Auth->login()) {
			throw new ForbiddenException('Permission denied, invalid access token');
		}
	}

	/**
	* Tries to find the AccessToken from a long range of entry points
	*
	*/
	protected function configureApiToken() {
		$token = null;

		// Check if the token is posted to the action
		if (!empty($this->request->data['token'])) {
			$token = $this->request->data['token'];
		}
		// Check if the token is passed as a query argument (?token=$token)
		elseif (!empty($this->request->query['token'])) {
			$token = $this->request->query['token'];
		}
		// Check if the token is passed as named argument (/token:$token)
		elseif (!empty($this->request->params['named']['token'])) {
			$token = $this->request->params['named']['token'];
		}
		// Check if the token is passed as a HTTP header
		elseif (!empty($_SERVER['HTTP_PLATFORM_TOKEN'])) {
			$token = $_SERVER['HTTP_PLATFORM_TOKEN'];
		}

		// Write the found AccessToken (if any)
		Configure::write('Platform.AccessToken', $token);
	}

	protected function configureRequestDetectors() {
		// Add detector for json
		$this->request->addDetector('json', array('callback' => function(CakeRequest $request) {
			// The sure solution is to check if the extension is "json"
			if (isset($request->params['ext']) && $request->params['ext'] === 'json') {
				return true;
			}

			// Or try to sniff out the accept header
			return $request->accepts('json');
		}));

		// Generic API check
		$this->request->addDetector('api', array('callback' => function(CakeRequest $request) {
			// Currently only checks if a request is JSON - but allows us to easily add other request formats
			return $request->is('json');
		}));
	}
}