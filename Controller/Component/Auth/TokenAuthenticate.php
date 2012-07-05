<?php
App::uses('ApiUtility', 'Api.Lib');
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 * Token Authenticator
 *
 * Allows people with access tokens to authenticate as a normal user
 *
 * An authentication adapter for AuthComponent. Provides the ability to authenticate by token.
 * Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate setting.
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Api.Token' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 *
 * When configuring TokenAuthenticate you can pass in settings to which fields, model and additional conditions
 * are used. See TokenAuthenticate::$settings for more information.
 *
 * @package Plugin.Api.Controller.Component.Auth
 * @copyright Nodes ApS 2010-2012 <tech@nodes.dk>
 */
class TokenAuthenticate extends BaseAuthenticate {

	/**
	 * Settings for this object.
	 *
	 * - `fields` The fields to use to identify a user by.
	 * - `userModel` The model name of the User, defaults to User.
	 * - `scope` Additional conditions to use when looking up and authenticating users,
	 *    i.e. `array('User.is_active' => 1).`
	 * - `recursive` The value of the recursive key passed to find(). Defaults to -1
	 *
	 * @var array
	 */
	public $settings = array(
		'fields'	=> array(
			'access_token' => 'access_token'
		),
		'userModel' => 'User',
		'scope'		=> array(),
		'recursive'	=> -1
	);

	/**
	 * Authentication callback
	 *
	 * Its called by Cake's AuthComponent, and it expects it to either return false
	 * or an array with user data that will be cached from the entire session
	 *
	 * @cakephp
	 * @param CakeRequest	$request
	 * @param CakeResponse	$response
	 * @return boolean|array
	 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		return $this->getUser($request);
	}

	public function getUser($request) {
		// Find the token from the request
		$token = ApiUtility::getRequestToken($request);
		if (empty($token)) {
			return false;
		}

		// Each access to fields
		$fields = $this->settings['fields'];

		// Find the user model
		$userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);

		// build conditions
		$conditions = array(
			$model . '.' . $fields['access_token'] => $token,
		);

		// Apply additional scope
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}

		// Try and find the user
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions'	=> $conditions,
			'recursive'		=> $this->settings['recursive']
		));

		// Check the result
		if (empty($result) || empty($result[$model])) {
			return false;
		}

		// Return the data
		return $result[$model];
	}

	/**
	 * Logout callback
	 *
	 * Its called by Cake's AuthComponent when you request a logout, it expects to return an
	 * url to redirect the client to.
	 *
	 * But we also invalidate any tokens we might have cached to make sure it cannot be reused
	 *
	 * @return array
	 */
	public function logout($user) {
		return '/';
	}
}
