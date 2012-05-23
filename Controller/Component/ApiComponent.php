<?php
App::uses('ApiUtility', 'Api.Lib');
App::uses('ApiEvent', 'Api.Controller/Event');

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
	protected $controller;

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

	/**
	* List of actions that can be accessed without authentication
	*
	* @var array
	*/
	protected $publicActions = array();

	/**
	* initialize callback
	*
	* @param Controller $controller
	* @return void
	*/
	public function initialize(Controller $controller) {
		// Ensure we can detect API requests
		$this->setup($controller);
	}

	/**
	* startup method
	*
	* @param Controller $controller
	* @return void
	*/
	public function startup(Controller $controller) {
		$this->setup($controller);

		// Enforce API authentication
		$this->configureApiAccess();
	}

	/**
	* Allow public access to an action
	*
	* @param string $action
	* @return void
	*/
	public function allowPublic($action) {
		$this->publicActions[] = $action;
	}

	/**
	* Deny public access to an action
	*
	* @param string $action
	* @return boolean
	*/
	public function denyPublic($action) {
		$pos = array_search($action, $this->publicActions);
		if (false === $pos) {
			return false;
		}
		unset($this->publicActions[$pos]);
		return true;
	}

	/**
	* beforeRender callback
	*
	* @return void
	*/
	public function beforeRender() {
		if (!$this->request->is('api')) {
			return;
		}

		Configure::write('ResponseObject', $this->response);

		// Switch to the API view class
		$this->controller->viewClass = 'Api.Api';

		// Ensure we output data as JSON
		if ($this->hasError()) {
			$this->controller->layout = 'Api.json/error';
		} else {
			$this->controller->layout = 'Api.json/default';
		}

		// Override RequestHandler messing around with my layoutPaths
		// If not set to null it may do json/json/default.ctp as layout in non-crud actions
		$this->controller->layoutPath = null;

		// Always repond as JSON
		$this->controller->response->type('json');

		// Publish the token
		$token = ApiUtility::getRequestToken($this->request);
		$this->controller->set('apiAccessToken', $token);
	}

	/**
	* Is the current controller an Error controller?
	*
	* @return boolean
	*/
	public function hasError() {
		return get_class($this->controller) == 'CakeErrorController';
	}

	/**
	* beforeRedirection
	*
	* @param Controller $controller
	* @param mixed $url
	* @param integer $status
	* @param boolean $exit
	* @return void
	*/
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
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
					$this->_stop();
				case 301:
				case 302:
					$controller->response->statusCode($status);
					$controller->response->header(array('location' => $url));
					break;
				default:
					break;
			}

			$success = true;

			// Render the redirect view
			$controller->set(compact('success', 'url', 'status'));
			$controller->render();

			// Send the result and stop the request
			$controller->response->send();
			$this->_stop();
		}
	}

	/**
	* Setup method
	*
	* @param Controller $controller
	* @return void
	*/
	protected function setup(Controller $controller) {
		// Cache local properties from the controller
		$this->controller	= $controller;
		$this->request		= $controller->request;
		$this->response		= $controller->response;

		// Configure detectors
		$this->configureRequestDetectors();

		// Don't do anything if the request isn't considered API
		if (!$this->request->is('api')) {
			return;
		}

		// Bind Crud Event Api
		$this->controller->getEventManager()->attach(new ApiEvent());

		// Copy publicActions from the controller if set and no actions has been defined already
		// @todo: This is legacy, remove it
		if (isset($this->controller->publicActions) && empty($this->publicActions)) {
			$this->publicActions = $this->controller->publicActions;
		}

		// Change Exception.renderer so output isn't forced to HTML
		Configure::write('Exception.renderer', 'Api.ApiExceptionRenderer');
	}

	/**
	* Ensures that the current request is validated for Authentication
	*
	* @return void
	*/
	protected function configureApiAccess() {
		if ($this->hasError()) {
			return;
		}

		// Do not require authentication if the request isn't considered API
		if (!$this->request->is('api')) {
			return;
		}

		// If its a public action, do not enforce API security checks
		if (in_array($this->controller->action, $this->publicActions)) {
			return;
		}

		// If the user has a isAuthorizedApi method, call it and don't check anything else
		if (method_exists($this->controller, 'isAuthorizedApi')) {
			if (!$this->controller->isAuthorizedApi()) {
				throw new ForbiddenException('Permission denied');
			}

			return;
		}

		// Do not enforce authentication if the request is already authenticated
		if (isset($this->controller->Auth) && $this->controller->Auth->user()) {
			return;
		}

		// Get the access token, if any
		$token = ApiUtility::getRequestToken($this->request);

		// Deny access if no AccessToken is provided
		if (empty($token)) {
			throw new ForbiddenException('Permission denied, missing access token');
		}

		// Deny access if the AccessToken isn't valid
		if (!$this->controller->Auth->login()) {
			throw new ForbiddenException('Permission denied, invalid access token');
		}
	}

	/**
	* Configure detectors for API requests
	*
	* Add detectors for ->is('api') and ->is('json') on CakeRequest
	*
	* @return void
	*/
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